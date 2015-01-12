<?
/*
   File name         : createfavorites.php
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
} else {

	$txttitle = trim($txttitle);
	if ($txttitle == "") {
		$txttitle = "-----------";
	}

  $dbq = $db->execute("select favorite_id from tblFavorites order by favorite_id desc");
  $max_favorite_id   = $dbq->fields['favorite_id'];
  $dbq->close();
  $max_favorite_id++;

  $txturl = clean_data($txturl);
  $txttitle = clean_data($txttitle);

  $dbq = $db->execute("insert into tblFavorites values($max_favorite_id,$user_id,'$txturl','$txttitle','')");
  Header("Location: favorites.php");
}

?>
