<?
/*
   File name         : imap.inc
   Version           : 1.1.0
   Author            : Umut Gokbayrak
   e-mail            : umut@trlinux.com
   Purpose           : It automates any IMAP function. It is included from th imap_pop3 class.
   Last modified     : 10 Sep 2000
*/

/*******************************************************************************/
/*                         Functions IMAP and POP3 share                       */
/*******************************************************************************/


  class imap_pop3 {
    var $port;
    var $protocol;
    var $hostname;
    var $username;
    var $password;
    var $mbox;
    var $mbox_id;
    var $message_count;

    function imap_pop3($port, $protocol, $hostname, $username, $password,$mbox_id) {
      global $text80;

      $this->port     = $port;
      $this->protocol = $protocol;
      $this->hostname = $hostname;
      $this->username = $username;
      $this->password = $password;
      $this->mbox     = false;
      $this->mbox_id  = $mbox_id;
      $this->messagecount = 0;

      if (trim($port) == "") {
         switch ($protocol) {
           case "imap" :
              $this->port = "143";
              break;
           case "pop3" :
              $this->port = "110";
              break;
           default :
              echo "&nbsp;<p>&nbsp;<p>&nbsp;<p><center><font color='red'><p><b>$text80</b></p></font></conter>";
              die;
         } // end switch
      } // end if

      $this->makealive();
    } // end function

    function authenticate() {
      $this->mbox = imap_open("{" . $this->hostname . "/" . $this->protocol . "}", $this->username, $this->password);
      if ($this->mbox) {
        imap_close($this->mbox);
        return true;
      } else {
        return false;
      } // end if

    }  // end function

    function decode_mime_string ($string) {
       if (eregi("=?([A-Z,0-9,-]+)?([A-Z,0-9,-]+)?([A-Z,0-9,-,=,_]+)?=", $string)) {
          $coded_strings = explode('=?', $string);
          $counter = 1;
          $string = $coded_strings[0];
          while ($counter < sizeof($coded_strings)) {
             $elements = explode('?', $coded_strings[$counter]);
             if (eregi("Q", $elements[1])) {
                $elements[2] = str_replace('_', ' ', $elements[2]);
                $elements[2] = eregi_replace("=([A-F,0-9]{2})", "%\\1", $elements[2]);
                $string .= urldecode($elements[2]);
             } else {
                $elements[2] = str_replace('=', '', $elements[2]);
                if ($elements[2]) { $string .= base64_decode($elements[2]); }
             }
             if (isset($elements[3]) && $elements[3] != '') {
                $elements[3] = ereg_replace("^=", '', $elements[3]);
                $string .= $elements[3];
             }
    				 $string .= " ";
             $counter++;
          }
       }
       return $string;
    }

/*******************************************************************************/
/*                         Only IMAP specific functions                        */
/*******************************************************************************/

    function makealive() {             // checks to see if imap stream is active. If not it does.
      if (!$this->mbox) {
        $this->mbox = imap_open("{" . $this->hostname . "/" . $this->protocol . ":" . $this->port . "}$this->mbox_id", $this->username, $this->password) or die ("<meta http-equiv=\"refresh\" content=\"0; url=index.php?error_id=1\">");
      } else {
        $active = imap_ping($this->mbox);
        if (!$active) {
          $this->mbox = imap_open("{" . $this->hostname . "/" . $this->protocol . ":" . $this->port . "}$this->mbox_id", $this->username, $this->password) or die ("<meta http-equiv=\"refresh\" content=\"0; url=index.php?error_id=1\">");
        } // end if
      } // end if
    } // end function


/*******************************************************************************/
/*                   IMAP specific functions with shared names                 */
/*******************************************************************************/

    function getMailboxes() {  // returns an array of subscribed imap folders
      $this->makealive();
      if ($this->mbox ){
        $list = imap_getsubscribed($this->mbox, "{" . $this->hostname . ":" . $this->port . "}", "*");
        if (is_array($list)) {
          $folders = array("name" => array(),
                           "id"   => array());

          $folder_counter=-1;
          reset($list);
          while (list($key, $val) = each($list)) {
            $folder_counter++;
            $folders["name"][$folder_counter] = substr(stristr(imap_utf7_decode($val->name),"}"), 1);
            $folders["id"][$folder_counter] = rawurlencode(substr(stristr($val->name,"}"), 1));
          } // end while
        } // end if
      }

      return $folders;
    } // end function


    function mbox_exists($mbox_id) { // Checks to see if the mbox_id is a valid mailbox or not.
      if (trim($mbox_id) != "INBOX") {
        $this->makealive();
        if ($this->mbox){
          $it_exists = false;
          $list = imap_getsubscribed($this->mbox, "{" . $this->hostname . ":" . $this->port . "}", "*");
          if (is_array($list)) {
            while (list($key, $val) = each($list)) {
              if (substr(stristr(imap_utf7_decode($val->name),"}"), 1) == trim($mbox_id)) {
                $it_exists = true;
              } // end if
            } // end while
          } // end if
        } else {
          Header("Location: index.php?error_id=2");
        } // end if
        if (!$it_exists) {
          Header("Location: index.php?error_id=3");
        }
      }
    } // end function


    function postaci_get_headers() {
      global $text40, $topmsg, $seperator;
      $postaci_headers= array("msg_no"  => array(),
                              "attach"  => array(),
                              "from"    => array(),
                              "to"      => array(),
                              "subject" => array(),
                              "size"    => array(),
                              "status"  => array(),
                              "cc"      => array(),
                              "msg_date"=> array());

      $this->makealive();
      if ($this->mbox ){
        $mailbox_headers = array();
        $mailbox_headers=imap_headers($this->mbox);

        if ($topmsg >= $this->messagecount) {
          $topmsg = $this->messagecount;
          $bottommsg = $this->messagecount - $seperator +1;
        }
        if ($topmsg <= 0) {
          $topmsg = $this->messagecount;
          $bottommsg = $this->messagecount - $seperator +1;
        } else {
          $bottommsg = $topmsg - $seperator +1;
        }
        if ($bottommsg <= 0) {
          $bottommsg = 1;
        }

        $simple_counter = -1;

        for ($i = $topmsg; $i>=$bottommsg;$i = $i -1) {

          $simple_counter++;

          $size=$mailbox_headers[$i-1];
          $size=ereg_replace(".*\(","",$size);
          $size=ereg_replace(" .*$"," ",$size);
          $htmlsize=ceil($size/1024). "K";

          $message_header=imap_header($this->mbox,$i);

          $message_date=ereg_replace(" ","&nbsp;", date("Y M d - H:i",$message_header->udate));
          if ($message_header->Unseen == "U" || $message_header->Recent == "N") {
            $message_is_new = 1;
          } else {
            $message_is_new = 0;
          }
          $htmlfrom = htmlspecialchars($this->decode_mime_string($message_header->fromaddress));
          $htmlcc = htmlspecialchars($this->decode_mime_string($message_header->ccaddress));
          $htmlto = htmlspecialchars($this->decode_mime_string($message_header->toaddress));
          $mysubject = $this->decode_mime_string($message_header->subject);
          if (strlen($mysubject) <= 0 ) {
            $mysubject="$text40";
          }
          $message_number=trim($message_header->Msgno);
          $structure=imap_fetchstructure($this->mbox,$message_number);
          $c=count($structure->parts);
          $attachment_exists = 0;
          if ($c>1 ) {
            $attachment_exists = 1;
          }

          $postaci_headers["msg_no"][$simple_counter] = $message_number;
          if ($attachment_exists == 0) {
            $postaci_headers["attach"][$simple_counter] = 0;
          } else {
            $postaci_headers["attach"][$simple_counter] = 1;
          }
          $postaci_headers["from"][$simple_counter] = $htmlfrom;
          $postaci_headers["to"][$simple_counter] = $htmlto;
          $postaci_headers["subject"][$simple_counter] = $mysubject;
          $postaci_headers["size"][$simple_counter] = $htmlsize;
          $postaci_headers["status"][$simple_counter] = $message_is_new;
          $postaci_headers["cc"][$simple_counter] = $htmlcc;
          $postaci_headers["msg_date"][$simple_counter] = $message_date;
        }

      } else {
        Header("Location: index.php?error_id=2");
      } // end if

      return $postaci_headers;
    } // end function

    function empty_mailbox() {
      $this->makealive();
      if ($this->mbox){
        $mboxstatus=imap_check($this->mbox);
        $this->messagecount=$mboxstatus->Nmsgs;
        if ($this->messagecount == 0) {
          return true;
        } else {
          return false;
        }
      } else {
        Header("Location: index.php?error_id=2");
      } // end if
    } // end function

    function close_mailbox() {
      if ($this->mbox){
        imap_close($this->mbox);
      }
    } // end function


  } // end class

?>
