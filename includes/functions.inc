<?
/*
   File name         : functions.inc
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : Some commonly used functions needed at various places.
   Last modified     : 29 Sept 2006 
*/


    // Returns the full e-mail adress of a user by user_id
    function getMailAdress($user_id) {
      global $db,$qmail_support;

      $dbq = $db->execute("select rsrv_char1 from tblUsers where user_id=$user_id");
      $mailaddress = $dbq->fields['rsrv_char1'];
      $dbq->close();

      return $mailaddress;
    }  // end function


    // Returns the folder_name from tblMailBoxes with folder_id
    function getFolderName($mbox_id) {
      global $db;

      $dbq = $db->execute("select mboxname from tblMailBoxes where mbox_id=$mbox_id");
      $mboxname = $dbq->fields['mboxname'];
      $dbq->close();

      return $mboxname;
    } // end function


    function do_body_staff($body)  {
      global $qp, $text88,$sending;
      $user =  $_SESSION['txtusername'];

      if ( strlen($body) <= 0 ) {
        $body= $text88;
      }

      if ($qp == 1) {
        $xbody=imap_qprint($body);
        $mylen=strlen($xbody);
        if ( $mylen > 10 )  {
          $body=$xbody;
        }
      }

      $htmlbody = $body;
      $htmlbody = htmlspecialchars($htmlbody);

      if (!$sending) {
	$htmlbody = str_replace("&gt;", ">", $htmlbody);
	$htmlbody = str_replace("&lt;", "<", $htmlbody);
	$htmlbody = str_replace("amp;", "", $htmlbody);
	$htmlbody = str_replace($user, "hidetheusername", $htmlbody);
        $htmlbody = ereg_replace("([A-Za-z0-9_.]+\@[[:alnum:].]+[a-zA-Z]+)","<a href=\"sendmail.php?to=\\1\">\\1</a>",$htmlbody);
        $htmlbody = eregi_replace("(http|https|ftp)://([[:alnum:]/\n+-=&%:_.~?]+[#[:alnum:]+]*)","<a href=\"\\1://\\2\" target='yeni'>\\1://\\2</a>",	$htmlbody);
	$htmlbody = str_replace("hidetheusername", $user, $htmlbody); 
      }
      return $htmlbody;
    }

    function do_html_staff($body)  {

      $tmpbody = stristr ($body,"<html>");
      if ($tmpbody) {
        $body = substr ($tmpbody, 7);
      }
      $tmpbody = stristr ($body,"<body");
      if ($tmpbody) {
       $body = substr ($tmpbody, 7);
       $tmpbody2 = stristr ($body,">");
       $body = substr ($tmpbody2, 1);
      }

      return $body;
    }

    function calculateHowManyPages($messagecount) {
      global $seperator;

      $tmp_page = floor($messagecount / $seperator);
      $total_page = $tmp_page * $seperator;
      if ($messagecount != $total_page) {
        $how_many = $tmp_page + 1;
      } else {
        $how_many = $tmp_page;
      }

      return $how_many;
    }


    function prepare_imap_attachments($msg_no) {
      global $email,$ID,$attach_directory;

     // $email->empty_mailbox();
      $structure=imap_fetchstructure($email->mbox,$msg_no);
      $c=count($structure->parts);
      if ($c<=1 ) {
        $no_attachments = 1;
      }

      if ($no_attachments == 0) {
        for ($i=1; $i<$c; $i++) {
          $part0=$structure->parts[$i];
          $part=$i+1;
          $parameters=$part0->parameters;
          $attach_type=$part0->subtype;
          $mytype=$part0->type;
          $encoding=$part0->encoding;
          $text_encoding=$mime_encoding[$encoding];

          if (empty($text_encoding)) {
            $text_encoding="unknown";
          }
          if (eregi("RFC822",$attach_type)) {
            $att="RFC822 Message";
            $val="message.txt";
          } else {
            $att=$parameters[0]->attribute;
            $val=$parameters[0]->value;
          }
          $val=eregi_replace(" ","_",$val);

          $raw_data=imap_fetchbody($email->mbox,$msg_no,$part);

          if ($encoding==3){
            $raw_data = imap_base64($raw_data);
          }
          if ($encoding==4){
            $raw_data = imap_qprint($raw_data);
          }

          $fp=fopen($attach_directory . $ID . "######" . $val,"w");
          fwrite($fp,$raw_data);
          fclose($fp);
        } // end for
      } // end if
    } // end function


    function prepare_pop3db_attachments($msg_id) {
      global $db, $ID, $attach_directory;

      $dbq = $db->execute("select * from tblAttachments where message_id = $msg_id");
      $no_of_attachments = $dbq->getNumOfRows();
      if ($no_of_attachments != 0) {
        while (!$dbq->EOF) {
          $file_name = $dbq->fields['file_name'];
          $file_actual_name = $dbq->fields['file_actual_name'];
          copy($file_actual_name , $attach_directory . $ID . "######" . $file_name);
          $dbq->nextRow();
        } // end while
        $dbq->close();
      } // end if

   } // end function

   function getFolderID($folder_name,$user_id) {
      global $db, $default_protocol;

      if ($default_protocol == "imap" || $folder_name == "INBOX") {
        $folder_id = rawurlencode($folder_name);
      } else {
        $dbq = $db->execute("select mbox_id from tblMailBoxes where user_id=$user_id and mboxname = '$folder_name'");
        $folder_id = $dbq->fields['mbox_id'];
		$dbq->close();
	      } // end if

	      return $folder_id;
	   } // end function


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

	function turkcelestir($string) {
	  $string = str_replace("&amp;#305;","ý",$string);
	  $string = str_replace("&amp;#350;","Þ",$string);
	  $string = str_replace("&amp;#351;","þ",$string);
	  $string = str_replace("&amp;#304;","Ý",$string);
	  $string = str_replace("&amp;#287;","ð",$string);
	  $string = str_replace("&amp;#286;","Ð",$string);

	  $string = str_replace("&#305;","ý",$string);
	  $string = str_replace("&#350;","Þ",$string);
	  $string = str_replace("&#351;","þ",$string);
	  $string = str_replace("&#304;","Ý",$string);
	  $string = str_replace("&#287;","ð",$string);
	  $string = str_replace("&#286;","Ð",$string);


	  return $string;
	} // end function

    function clean_data($data) {
      $banlist = "(insert|select|update|delete|distinct|having|truncate|replace|handler|procedure|limit|order by|group by|asc|desc|;|#|=)";

      $data = eregi_replace($banlist, "", $data);
      $data = trim($data);
      $data = strip_tbls($data);
      return $data;
    }

    function strip_tbls($data) {
      $tbl_names = "(tblAdressbook|tblAttachments|tblDomains|tblFavorites|tblLoggedUsers|tblMIME|tblMailBoxes|tblMessages|tblNotebook|tblSpamList|tblUserDomains|tblUsers|;)";
      $data = eregi_replace($tbl_names, "", $data);
      $data = trim($data);
      return $data;
    }
?>
