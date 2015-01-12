<?
/*
   File name         : message_move.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : Moves a message from one to another. Possible paths are : 
                       IMAP     -> IMAP
                       POP3     -> DATABASE
                       DATABASE -> DATABASE
   Last modified     : 28 Feb 2005 
*/

include ("includes/global.inc");
session_start();
include ("$postaci_directory" . "classes/imap_pop3.inc");
include ("$postaci_directory" . "includes/functions.inc");

// ID comparison between logged hash and session. If they are both the same, let the user to go on...
$dbq = $db->execute("select log_id,user_id,username from tblLoggedUsers where hash = '$ID'");
$log_id   = $dbq->fields['log_id'];
$user_id  = $dbq->fields['user_id'];
$username = $dbq->fields['username'];
$dbq->close();

if ($log_id == ""){
  Header("Location: index.php?error_id=1");
} else {
  $from_folder_id = rawurldecode($mbox_id);
  $to_folder_id = rawurldecode($slcmbox);
  $to_folder_id = clean_data($to_folder_id);

  if ($default_protocol == "imap") {
	error_reporting(E_ALL);
ini_set(display_errors,1);

    $email=new imap_pop3($default_port,$default_protocol,$default_host,$username,$password,$from_folder_id);

    $count=count($chk);

    for($i=0; $i<$count; $i++) {
      $chk[$i] = clean_data($chk[$i]);
      list($chk[$i], $message_size[$i]) = split("[A]", $chk[$i]);
    }

    for($i=0; $i<$count; $i++) {
      $message_no = $chk[$i];
	print $message_no;
      imap_mail_move($email->mbox,$message_no,$to_folder_id);
    }
    imap_expunge($email->mbox);
  } // end if

  if ($default_protocol == "pop3") {
    $dbq = $db->execute("select user_id from tblMailBoxes where mbox_id=$to_folder_id");  // security check
    $auth_user = $dbq->fields['user_id'];
    $dbq->close();
    if ($auth_user != $user_id) {
      Header("Location: index.php?error_id=1");
    }

    if ($from_folder_id == "INBOX") {
      $email=new imap_pop3($default_port,$default_protocol,$default_host,$username,$password,$from_folder_id);
      $email->empty_mailbox();
      $count=count($chk);

      for($i=0; $i<$count; $i++) {
	$chk[$i] = clean_data($chk[$i]);
        list($chk[$i], $message_size[$i]) = split("[A]", $chk[$i]);
      }

      for($j=0; $j<$count; $j++) {
        $msg_no = $chk[$j];

        $header=imap_header($email->mbox,$msg_no);
        $from	= decode_mime_string($header->fromaddress);
        $subject = decode_mime_string($header->subject);
        $to	= decode_mime_string($header->toaddress);
        $replyto = decode_mime_string($header->reply_toaddress);
        $cc	= decode_mime_string($header->ccaddress);
        $msg_date= date("Y M d - H:i",$header->udate);
        $body = imap_body($email->mbox,$msg_no);
        $msize = $message_size[$j];

        $start=0;
        $structure=imap_fetchstructure($email->mbox,$msg_no);


        if (eregi("MIXED",$structure->subtype) )  {                       // Is the message a multipart one???
          $body = imap_fetchbody($email->mbox,$msg_no,1);
          $fullheader = imap_fetchheader($email->mbox,$msg_no);
          if (eregi("Content-Type: text/html",$fullheader) ) {            // message is simply text/plain
            $showhtml=1;
          } else {
            $showhtml=0;
          }
          $start=1;
        }
        if (eregi("RELATED",$structure->subtype) )  {                     // MS Outlook compatibility
          $body = imap_fetchbody($email->mbox,$msg_no,1);
          $showhtml=1;
          $start=1;
        }
        if ( $start== 0) {																					// Multipart deðil ama html ise...
          $fullheader = imap_fetchheader($email->mbox,$msg_no);
          if ( eregi("Content-Type: text/html",$fullheader) ) {
            $showhtml=1;
          } else {
            $showhtml=0;
          }
          $body = imap_fetchbody($email->mbox,$msg_no,1);
          $start=0;
        }

        // Message is HTML or not. ýf it is we crop the unnecessary tags.

        $msg_enc  = 4;
        $msg_part = $structure->parts[0];
        $msg_enc  = $msg_part->encoding;
        $htmlbody = $body;
        if ($showhtml == 1) {
          $htmlbody = do_html_staff($body);
        } else {
          if ($msg_enc == 4) {
            $qp = 1;
          } else {
            $qp = 0;
          }
          $htmlbody=do_body_staff($body);
        }

        if ($msg_enc == 3) {
          $htmlbody = imap_base64($htmlbody);
        }
        // Stop or continue decision

        $related = 0;
        if (eregi("RELATED",$structure->subtype) ) {
          $related = 1;
        }

        $dbq = $db->execute("select message_id from tblMessages order by message_id desc");
        $max_msg_no   = $dbq->fields['message_id'];
        $dbq->close();
        $max_msg_no++;

        $from = addslashes($from);
        $to = addslashes($to);
        $cc = addslashes($cc);
	$replyto = addslashes($replyto);
        $msg_date = addslashes($msg_date);
        $subject = addslashes($subject);
        $msize = addslashes($msize);
        $htmlbody = addslashes($htmlbody);

        $dbq = $db->execute("insert into tblMessages values($max_msg_no,$to_folder_id,$user_id,'$from','$to','$cc','$replyto','$msg_date','$subject','$msize','$htmlbody')");
        $dbq->close();

        $structure=imap_fetchstructure($email->mbox,$msg_no);
        $c=count($structure->parts);
        if ($c<=1 ) {
          $no_attachments = 1;
        }



        if ($no_attachments == 0) {
          for ($i=$start; $i<$c; $i++) {
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
            $val=rawurlencode(eregi_replace(" ","_",$val));
            $size=sprintf("%0.2f",$part0->bytes / 1024);

         	$att_body=imap_fetchbody($email->mbox,$msg_no,$part);
          	if ($encoding == 3){
          		$att_body =imap_base64($att_body);
          	}
          	if ($encoding == 4){
          		$att_body =imap_qprint($att_body);
          	}

            $real_name = $val;
            // Find the MIME type from the extension...
            $tersname = strrev($real_name);
            $dosya_isim = stristr($tersname,".");
            $lenisim = strlen($dosya_isim);
            $extension=trim(strtolower(substr($real_name,$lenisim)));

            $mime_type = "application/octet-stream";
            $dbq = $db->execute("select mime_type from tblMIME where mime_ext = '$extension'");
            $mime_type = $dbq->fields['mime_type'];
            $dbq->close();

            $dbq = $db->execute("select attach_id from tblAttachments order by attach_id desc");
            $max_att_no   = $dbq->fields['attach_id'];
            $dbq->close();
            $max_att_no++;

            mt_srand((double)microtime()*10000);
            $id_name = md5(mt_rand(1,6000));
            $id_name=ereg_replace("/","X",$id_name);
            $act_name = $pop3_attach_dir . $id_name . "######" . $real_name;

            $dbq = $db->execute("insert into tblAttachments values($max_att_no,$max_msg_no,$user_id,'$mime_type','$real_name','$act_name')");
            $dbq->close();

            $fd = fopen($act_name, "w");
            fwrite($fd, $att_body);
            fclose($fd);

          } // end for
        } // end if
      
        @imap_delete($email->mbox, $msg_no);
      } // end for
      @imap_close($email->mbox,CL_EXPUNGE);
    } else {          // copy from pop3db to pop3db

      $count=count($chk);

      for($i=0; $i<$count; $i++) {
        list($chk[$i], $message_size[$i]) = split("[A]", $chk[$i]);
      }

      for ($i=0; $i<$count; $i++) {
        $msg_no = $chk[$i];
        $dbq = $db->execute("update tblMessages set mbox_id=$to_folder_id where message_id = $msg_no");
        $dbq->close();
      }
    } // end if
  } // end if

  Header("Location: mailbox.php?mbox_id=$from_folder_id");
}

?>
