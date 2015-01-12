<?
/*
   File name         : createcontact.php
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
        
	$txtemail = str_replace(";", "", $txtemail);
	$abentry = explode(" &lt", $txtemail);
	$txtnamesurname = $abentry[0];
	$txtemail = $abentry[1];
	$txtemail = str_replace("&gt", "", $txtemail);
	if ($txtemail == "") { $txtemail = $txtnamesurname; }

	$txtnamesurname = trim($txtnamesurname);
	if ($txtnamesurname == "") {
		$txtnamesurname = "-----------";
	}

  $dbq = $db->execute("select item_id from tblAdressbook order by item_id desc");
  $max_item_id   = $dbq->fields['item_id'];
  $dbq->close();
  $max_item_id++;
 
  $txtnamesurname = clean_data($txtnamesurname);
  $txtemail = clean_data($txtemail);
  $txttelephone = clean_data($txttelephone);

  $dbq = $db->execute("insert into tblAdressbook values($max_item_id,$user_id,'$txtnamesurname','$txtemail','','$txttelephone')");
  $dbq->close();

  if ($rm == 1) {
    //$mbox_id = rawurlencode($mbox_id);
  	Header("Location: readmessage.php?ek=1&mbox_id=$mbox_id&msg_no=$msg_no");
  } else {
  	Header("Location: addressbook.php");
  }

}

?>
