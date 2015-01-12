<?
/*
   File name         : import_addressbook.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Last modified     : 28 Feb 2005
*/

session_start();
include ("includes/global.inc");
include ("includes/functions.inc");

$dbq = $db->execute("select user_id from tblLoggedUsers where hash = '$ID'");
$user_id  = $dbq->fields['user_id'];
$dbq->close();

$uploaddir = "/tmp/postaci/";
$filename = $uploaddir . $ID;

if (copy($userfile, $filename)) {
  $fp = fopen($filename, "r");

  // We want to remove all of the possible column headings, if we don't the name of the first record will
  // include the last column heading name.
  $column_headings = array("First Name", "Last Name", "Middle Name", "Name", "Nickname", "E-mail Address", "Home Street", "Home City", "Home Postal Code", "Home State", "Home Country/Region", "Home Phone", "Home Fax", "Mobile Phone", "Personal Web Page", "Business Street", "Business City", "Business Postal Code", "Business State", "Business Country/Region", "Business Phone", "Business Fax", "Pager", "Company", "Job Title", "Department", "Office Location", "Notes");

  $addresses = fread($fp, filesize($filename));
  fclose($fp);
  $addresses = str_replace($column_headings, "", $addresses);
  $list = explode(",", $addresses); // Divide $addresses into an array called $list

  $dbq = $db->execute("select item_id from tblAdressbook order by item_id desc");
  $max_item_id   = $dbq->fields['item_id'];
  $dbq->close();
  $max_item_id++;

  // When an email address is detectcted, the record just before it will be the name that corresponds to it.
  // The name and email address are then written to the database
  for($i=0; $i<=sizeof($list); $i++) {
    if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $list[$i])) {
      $list[$i-1] = clean_data($list[$i-1]);
      $list[$i] = clean_data($list[$i]);
      $dbq = $db->execute("insert into tblAdressbook values($max_item_id,$user_id,'".$list[$i-1]."','".$list[$i]."','','')");
      $max_item_id ++;
    }
  }
  $dbq->close();
  unlink($filename);
  Header("Location: addressbook.php?note=1");
} else {
  Header("Location: addressbook.php?note=2"); 
}
?>
