<?
/*
   File name         : attachments.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : Adding and deleting attachments to the e-mail to be send.
   Last modified     : 29 Sept 2006 
*/

session_start();
include ("includes/global.inc");
include ("includes/functions.inc");

// ID comparison between logged hash and session. If they are both the same, let the user to go on...
$dbq = $db->execute("select log_id,user_id,username from tblLoggedUsers where hash = '$ID'");
$log_id   = $dbq->fields['log_id'];
$user_id  = $dbq->fields['user_id'];
$username = $dbq->fields['username'];
$dbq->close();

if ($log_id == ""){
  Header("Location: index.php?error_id=1");
} else {

  if ($emailspp > 100) {
    $emailspp = 100;
  }

  if ($emailspp < 15 || !is_numeric($emailspp)) {
    $emailspp = 15;
  }

  $txtreal_name = clean_data($txtreal_name);
  $txtsignature = clean_data($txtsignature);
  $txtemail = clean_data($txtemail);
  $emailspp = clean_data($emailspp);
  $txtsmtp_user = clean_data($txtsmtp_user);
  $txtsmtp_pass = clean_data($txtsmtp_pass);

  $dbq = $db->execute("update tblUsers set real_name='$txtreal_name', signature='$txtsignature', rsrv_int1='$emailspp', rsrv_char1='$txtemail',rsrv_char2='$txtsmtp_user',rsrv_char3='$txtsmtp_pass' where user_id = $user_id");
  $dbq->close();

  Header("Location: mailbox.php?mbox_id=INBOX");
}

?>
