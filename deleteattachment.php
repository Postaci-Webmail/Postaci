<?
/*
   File name         : deleteattachment.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Last modified     : 28 Feb 2005
*/

include ("includes/global.inc");
session_start();

// ID comparison between logged hash and session. If they are both the same, let the user to go on...
$dbq = $db->execute("select log_id from tblLoggedUsers where hash = '$ID'");
$log_id = $dbq->fields['log_id'];
$dbq->close();
if ($log_id == ""){
  Header("Location: index.php?error_id=1");
} else {

  // Saving the message body to a temporary file.
  $dosyaadi = "msgbody.txt";
  $fp = fopen($attach_directory . $ID . "#####" . "msgbody.txt", "w+");
  if ($fp) {
    $sonuc = fputs($fp, $msgbody);
    fclose($fp);
  } //end if

  // Deleting the actual file.
  unlink($attach_directory . $slcsil);

  Header("Location: attachments.php?to=$to&cc=$cc&bcc=$bcc&subject=$subject&attached=1&mbox_id=$mbox_id&post_act=1");

}

?>
