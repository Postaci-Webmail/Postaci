<?
/*
   File name         : createfolder.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Last modified     : 28 Feb 2005
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
  exit;
} else {
  if (trim($newfolder) != ""){
    if(isset($pnspace)) {
      $newfolder = $pnspace.$newfolder;
    }
    if ($default_protocol == "imap") {   // protocol = imap
      include ("$postaci_directory" . "classes/imap_pop3.inc");
      $newfolder = imap_utf7_encode($newfolder);
      $email=new imap_pop3($default_port,$default_protocol,$default_host,$username,$password,"INBOX");
      $sonuc = imap_createmailbox($email->mbox, "{" . $default_host . "}" . $newfolder);
      imap_subscribe($email->mbox, "{" . $default_host . "}" . $newfolder);

      if (!$sonuc) {
        Header("Location: folders.php?error_id=4");
        exit;
      }
    } else {                             // protocol = pop3
      $dbq = $db->execute("select mbox_id from tblMailBoxes order by mbox_id desc");
      $max_mbox_id   = $dbq->fields['mbox_id'];
      $max_mbox_id++;
      $dbq->close();

      $newfolder = clean_data($newfolder);
      $dbq = $db->execute("select mbox_id from tblMailBoxes where user_id = $user_id and mboxname = '$newfolder'");
      $mbox_exists   = $dbq->fields['mbox_id'];
      if ($mbox_exists == 0) {
        $dbq = $db->execute("insert into tblMailBoxes values('$max_mbox_id','$user_id','$newfolder',3)");
	$dbq->close();
      } else {
        Header("Location: folders.php?error_id=4");
        exit;
      }
    } // end if
  } else {
    Header("Location: folders.php?error_id=5");
    exit;
  }

  Header("Location: folders.php");
}


?>
