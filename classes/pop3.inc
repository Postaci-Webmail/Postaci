<?
/*
   File name         : pop3.inc
   Version           : 1.1.0
   Author            : Umut Gokbayrak
   e-mail            : umut@trlinux.com
   Purpose           : It automates any POP3 function. It is included from th imap_pop3 class.
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
              echo "&nbsp;<p>&nbsp;<p>&nbsp;<p><center><font color='red'><p><b>$text80</b></p></font></center>";
              die;
         } // end switch
      } // end if

    } // end function

    function authenticate() {
      $this->mbox = imap_open("{" . $this->hostname . "/" . $this->protocol . "}", $this->username, $this->password);
      if ($this->mbox) {
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
/*                         Only POP3 specific functions                        */
/*******************************************************************************/



/*******************************************************************************/
/*                     POP3 specific functions with shared names               */
/*******************************************************************************/

    function getMailboxes() {  // returns an array of subscribed pop3 folders
      global $db,$ID;
      $folders = array("name" => array(),
                       "id"   => array());

      $folder_counter=-1;

      $dbq = $db->execute("select user_id from tblLoggedUsers where hash='$ID'");
      $user_id = $dbq->fields['user_id'];
      $dbq->close();
      $dbq = $db->execute("select mbox_id,mboxname from tblMailBoxes where user_id=$user_id");
	    $numsR = $dbq->getNumOfRows();
  	  $dbq->firstRow();

      for ($r=0;$r<$numsR;$r++) {
        $mbox_name = $dbq->fields[1];
        $mbox_id = $dbq->fields[0];

        $folders["name"][$r] = "$mbox_name";
        $folders["id"][$r] = "$mbox_id";

        $dbq->nextRow();
      } // end for
      $dbq->close();

      return $folders;
    } // end function

    function mbox_exists($mbox_id) {
      global $db,$mbox_id,$ID;

      $dbq = $db->execute("select user_id from tblLoggedUsers where hash='$ID'");
      $user_id = $dbq->fields['user_id'];
      $dbq->close();



      $it_exists = false;
      if (trim($mbox_id) != "INBOX") {
        $dbq = $db->execute("select mbox_id from tblMailBoxes where mbox_id='$mbox_id' and user_id = '$user_id'");
        $mbox_id = $dbq->fields['mbox_id'];
        $dbq->close();
        if ($mbox_id != 0) {
          $it_exists = true;
        }
      } else {
        $it_exists = true;
      }

      if (!$it_exists) {
        Header("Location: index.php?error_id=1");
      }

    } // end function


    function postaci_get_headers() {
      global $text40, $topmsg, $seperator,$db,$mbox_id, $ID;

      $postaci_headers= array("msg_no"  => array(),
                              "attach"  => array(),
                              "from"    => array(),
                              "to"      => array(),
                              "subject" => array(),
                              "size"    => array(),
                              "status"  => array(),
                              "cc"      => array(),
                              "msg_date"=> array());

      if (trim($mbox_id) == "INBOX") {                    // Real POP3 INBOX, not a database one...
       if ($this->mbox) {
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
            $message_is_new = 0;

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
        }
      } else {                                            // A database POP3 folder.
        $attachment_exists = 0;
        $dbq = $db->execute("select user_id from tblLoggedUsers where hash='$ID'");
        $user_id = $dbq->fields['user_id'];
        $dbq->close();
/*
        $dbq = $db->execute("select * from tblMessages where mbox_id=$mbox_id and user_id = $user_id and message_id <= $topmsg order by message_id desc");
        $simple_counter = -1;

        while (!$dbq->EOF && $simple_counter < $seperator - 1) {
*/
        $dbq = $db->execute("select message_id from tblMessages where mbox_id=$mbox_id and user_id = $user_id order by message_id desc");
        $max_msg_id = $dbq->fields['message_id'];
        $dbq->close();
        if ($topmsg == 0) {
          $topmsg = $max_msg_id;
        }

        $dbq = $db->execute("select * from tblMessages where mbox_id=$mbox_id and user_id = $user_id and message_id <= $topmsg order by message_id desc");
        $simple_counter = -1;

        while (!$dbq->EOF && $simple_counter < $seperator - 1) {

          $simple_counter++;
          $message_id     = $dbq->fields['message_id'];
          $header_from    = $dbq->fields['header_from'];
          $header_to      = $dbq->fields['header_to'];
          $header_cc      = $dbq->fields['header_cc'];
          $header_subject = $dbq->fields['header_subject'];
          $header_size    = $dbq->fields['header_size'];
          $header_date    = $dbq->fields['header_date'];

          $dbq2 = $db->execute("select * from tblAttachments where message_id=$message_id and user_id = $user_id");
          $attach_id     = $dbq2->fields['attach_id'];
          $dbq2->close();
          if ($attach_id != 0) {
            $attachment_exists = 1;
          } else { $attachment_exists = 0; }

          $postaci_headers["msg_no"][$simple_counter] = $message_id;
          if ($attachment_exists == 0) {
            $postaci_headers["attach"][$simple_counter] = 0;
          } else {
            $postaci_headers["attach"][$simple_counter] = 1;
          }
          $postaci_headers["from"][$simple_counter] = htmlspecialchars($header_from);
          $postaci_headers["to"][$simple_counter] = htmlspecialchars($header_to);
          $postaci_headers["subject"][$simple_counter] = $header_subject;
          $postaci_headers["size"][$simple_counter] = htmlspecialchars($header_size);
          $postaci_headers["status"][$simple_counter] = 0;
          $postaci_headers["cc"][$simple_counter] = htmlspecialchars($header_cc);
          $postaci_headers["msg_date"][$simple_counter] = $header_date;

          $dbq->nextRow();
        } // end while
        $dbq->close();
      } // end if

      return $postaci_headers;

    } // end function

    function empty_mailbox() {
      global $ID,$mbox_id,$db;

      if (trim($mbox_id) == "INBOX") {                    // Real POP3 INBOX, not a database one...
        $this->mbox = imap_open("{" . $this->hostname . "/" . $this->protocol . "}INBOX", $this->username, $this->password);
        if ($this->mbox) {
          $mboxstatus=imap_check($this->mbox);
          $this->messagecount=$mboxstatus->Nmsgs;
          if ($this->messagecount == 0) {
            return true;
          } else {
            return false;
          }
        }
      } else {                                            // A database simulation
        $dbq = $db->execute("select user_id from tblLoggedUsers where hash='$ID'");
        $user_id = $dbq->fields['user_id'];
        $dbq->close();
        $dbq = $db->execute("select * from tblMessages where mbox_id='$mbox_id' and user_id = $user_id");
        $this->messagecount = $dbq->getNumOfRows();
        if ($this->messagecount == 0) {
          return true;
        } else {
          return false;
        }
      }
    } // end function

    function close_mailbox() {
      if ($this->mbox){
        imap_close($this->mbox);
      }
    } // end function

  } // end class

?>
