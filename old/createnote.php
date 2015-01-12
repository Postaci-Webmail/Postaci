<?
/*
   File name         : createnote.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Last modified     : 28 Feb 2005
*/

session_start();
include ("includes/global.inc");
include ("includes/functions.inc");

if (trim($txttitle) == "") {
  $txttitle = $text40;
}

// ID comparison between logged hash and session. If they are both the same, let the user to go on...
$dbq = $db->execute("select log_id,user_id,username from tblLoggedUsers where hash = '$ID'");
$log_id   = $dbq->fields['log_id'];
$user_id  = $dbq->fields['user_id'];
$username = $dbq->fields['username'];

$dbq->close();
if ($log_id == ""){
  Header("Location: index.php?error_id=1");
} else {

  $dbq = $db->execute("select note_id from tblNotebook order by note_id desc");
  $max_note_id   = $dbq->fields['note_id'];
  $dbq->close();
  $max_note_id++;

  $tarih = date('Y-m-d');

  $txttitle = addslashes(clean_data($txttitle));
  $txtnote = addslashes(strip_tbls($txtnote));

  $dbq = $db->execute("insert into tblNotebook values($max_note_id,$user_id,'$txttitle','$txtnote','$tarih')");
  Header("Location: notebook.php");
}

?>
