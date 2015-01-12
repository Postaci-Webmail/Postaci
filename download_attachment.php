<?
/*
   File name         : download_attachments.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : This script is a generic one which can download any part of a message. 
   Last modified     : 28 Feb 2005 
*/

session_start();
include ("includes/global.inc");
include ("includes/functions.inc");

include ("$postaci_directory" . "classes/imap_pop3.inc");

$mbox_id = clean_data($mbox_id);
$msg_no = clean_data($msg_no);
$attach_id = clean_data($attach_id);

// ID comparison between logged hash and session. If they are both the same, let the user to go on...
$dbq = $db->execute("select log_id,user_id,username from tblLoggedUsers where hash = '$ID'");
$log_id = $dbq->fields['log_id'];
$username = $dbq->fields['username'];
$user_id = $dbq->fields['user_id'];
$dbq->close();

if ($log_id == ""){
  Header("Location: index.php?error_id=1");
} else {
  $real_name = rawurldecode($real_name);
  if ($default_protocol == "imap" || $mbox_id == "INBOX") {  // real mailbox, IMAP or POP3
    $mbox_id = rawurldecode($mbox_id);
    $email=new imap_pop3($default_port,$default_protocol,$default_host,$username,$password,$mbox_id);
    $email->empty_mailbox();
    $body=imap_fetchbody($email->mbox,$msg_no,$part);
    if ($encoding==3){
      $body = imap_base64($body);
    }
    if ($encoding==4){
      $body = imap_qprint($body);
    }

    // Find the MIME type from the extension...
    $tersname = strrev($real_name);
    $dosya_isim = stristr($tersname,".");
    $lenisim = strlen($dosya_isim);
    $extension=trim(strtolower(substr($real_name,$lenisim)));

    $mime_type = "application/octet-stream";
    $dbq = $db->execute("select mime_type from tblMIME where mime_ext = '$extension'");
    $mime_type = $dbq->fields['mime_type'];
    $dbq->close();

    Header("Content-type: $mime_type; name=$real_name");
    Header("Content-Disposition: attachment; filename=$real_name");
    echo $body;
  } else {                                                 // pop3 database simulation
    if ($attach_type != 1){

      $dbq = $db->execute("select user_id from tblMessages where message_id=$msg_no");
      $auth_user = $dbq->fields['user_id'];
      $dbq->close();
      if ($auth_user != $user_id) {
        Header("Location: index.php?error_id=1");
      }

      $dbq = $db->execute("select file_actual_name from tblAttachments where message_id = $msg_no and user_id = $user_id and attach_id=$attach_id");
      $file_actual_name = $dbq->fields['file_actual_name'];
      $dbq->close();

      // Find the MIME type from the extension...
      $tersname = strrev($real_name);
      $dosya_isim = stristr($tersname,".");
      $lenisim = strlen($dosya_isim);
      $extension=trim(strtolower(substr($real_name,$lenisim)));

      $mime_type = "application/octet-stream";
      $dbq = $db->execute("select mime_type from tblMIME where mime_ext = '$extension'");
      $mime_type = $dbq->fields['mime_type'];
      $dbq->close();

      $fp=fopen($file_actual_name,"r");
      $body = fread($fp, filesize($file_actual_name));
      fclose($fp);
      Header("Content-type: $mime_type; name=$real_name");
      Header("Content-Disposition: attachment; filename=$real_name");
      echo $body;
    } else {

      $dbq = $db->execute("select user_id from tblMessages where message_id=$msg_no");  // security check
      $auth_user = $dbq->fields['user_id'];
      $dbq->close();
      if ($auth_user != $user_id) {
        Header("Location: index.php?error_id=1");
      }

      $dbq = $db->execute("select msg_body from tblMessages where message_id = $msg_no and user_id = $user_id and mbox_id=$mbox_id");
      $body = $dbq->fields['msg_body'];
      $dbq->close();
      $mime_type = "plain/text";
      Header("Content-type: $mime_type; name=$real_name");
      Header("Content-Disposition: attachment; filename=$real_name");
      echo $body;
    }
  }
}

?>
