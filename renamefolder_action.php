<?
/*
   File name         : renamefolder_action.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : Deleting attachments from the temporary place before sending them. 
   Last modified     : 28 Feb 2005 
*/

session_start();
include ("includes/global.inc");
include ("includes/functions.inc");

$mbox_id = clean_data($mbox_id);
$newfoldername = clean_data($newfoldername);

// ID comparison between logged hash and session. If they are both the same, let the user to go on...
$dbq = $db->execute("select log_id,user_id,username from tblLoggedUsers where hash = '$ID'");
$log_id   = $dbq->fields['log_id'];
$user_id  = $dbq->fields['user_id'];
$username = $dbq->fields['username'];
$rmbox_id = $mbox_id;

$dbq->close();
if ($log_id == ""){
  Header("Location: index.php?error_id=1");
} else {
  if(isset($pnspace)) {
    $newfoldername = $pnspace.$newfoldername;
  }
  if ($default_protocol == "imap") {   // protocol = imap
    include ("$postaci_directory" . "classes/imap_pop3.inc");
    $newfoldername = imap_utf7_encode($newfoldername);
    $email=new imap_pop3($default_port,$default_protocol,$default_host,$username,$password,"INBOX");

    imap_unsubscribe($email->mbox, "{" . $default_host . "}$mbox_id");
    $sonuc = imap_renamemailbox($email->mbox, "{" . $default_host . ":" . $default_port . "}$mbox_id","{".$default_host. ":" . $default_port . "}$newfoldername");

    imap_subscribe($email->mbox, "{" . $default_host . "}$newfoldername");
    if (!$sonuc)  {
      Header("Location: folders.php?error_id=7");
      exit;
    }
  } else {
    $dbq = $db->execute("select user_id from tblMailBoxes where mbox_id = $mbox_id"); // security check
    $auth_user   = $dbq->fields['user_id'];
    if ($auth_user != $user_id) {
      Header("Location: index.php?error_id=1");
    }

    $dbq = $db->execute("select mbox_id from tblMailBoxes where mboxname = '$newfoldername'");
    $mbox_id   = $dbq->fields['mbox_id'];
    if ($mbox_id != 0) {
      Header("Location: folders.php?error_id=7");
      exit;
    }

    $dbq = $db->execute("update tblMailBoxes set mboxname = '$newfoldername' where mbox_id = $rmbox_id and user_id = $user_id");
    $dbq->close();
  }

  Header("Location: folders.php");
}

?>
