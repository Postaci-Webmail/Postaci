<?
/*
   File name         : notebookupdate.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : Update script for notebook.
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

// security check
$note_id = clean_data($note_id);
$txttitle = addslashes(clean_data($txttitle));
$txtnote = addslashes(strip_tbls($txtnote));

$dbq = $db->execute("select user_id from tblNotebook where note_id=$note_id");
$auth_user = $dbq->fields['user_id'];
if ($auth_user != $user_id) {
  Header("Location: index.php?error_id=1");
}
$dbq->close();


if ($log_id == ""){
  Header("Location: index.php?error_id=1");
} else {
  $dbq = $db->execute("update tblNotebook set notetitle='$txttitle',notes = '$txtnote' where user_id = $user_id and note_id = $note_id");
  $dbq->close();

  Header("Location: notebook.php");
}

?>
