<?
/*
   File name         : favoritesupdate.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Last modified     : 28 Feb 2005
*/

include ("includes/global.inc");
include ("includes/functions.inc");
session_start();

// ID comparison between logged hash and session. If they are both the same, let the user to go on...
$dbq = $db->execute("select log_id,user_id,username from tblLoggedUsers where hash = '$ID'");
$log_id   = $dbq->fields['log_id'];
$user_id  = $dbq->fields['user_id'];
$username = $dbq->fields['username'];
$dbq->close();

// security check
$dbq = $db->execute("select user_id from tblFavorites where favorite_id=$favorite_id");
$auth_user = $dbq->fields['user_id'];
if ($auth_user != $user_id) {
  Header("Location: index.php?error_id=1");
}
$dbq->close();

if ($log_id == ""){
  Header("Location: index.php?error_id=1");
} else {

  $txturl = clean_data($txturl);
  $txttitle = clean_data($txttitle);
  $txtnotes = clean_data($txtnotes);

  $dbq = $db->execute("update tblFavorites set url='$txturl',url_title = '$txttitle',notes='$txtnotes' where user_id = $user_id and favorite_id = $favorite_id");
  $dbq->close();

  Header("Location: favorites.php");
}

?>
