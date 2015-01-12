<?
/*
   File name         : validate_login.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : Connects imap/pop3 port to see if
                       given username, password information is
                       valid. If it is the first login for any
                       POP3 account it creates the needed rows in the database for
                       the folder simulation. If all is OK, it
                       redirects user to mailbox.php
   Last modified     : 28 Feb 2005 
*/
session_start();
include ("includes/global.inc");
include ("includes/functions.inc");
include ("$postaci_directory" . "classes/imap_pop3.inc");
include ("$postaci_directory" . "classes/userclass.inc");
include ("$postaci_directory" . "classes/mailbox.inc");

$txtusername = clean_data($txtusername);
$txtpassword = clean_data($txtpassword);

$tmbox_id = "INBOX";  
$email=new imap_pop3($default_port,$default_protocol,$default_host,$txtusername,$txtpassword,$tmbox_id);

$password = $txtpassword;
session_register('password');

session_register('txtusername');
session_register('lang_choice'); 

// Generating a random unique!!! alphanumeric
mt_srand((double)microtime()*10000);
//$ID = md5(mt_rand(1,60000));
$ID = $txtusername . md5(microtime());
$ID = ereg_replace("/","X",$ID);

// Session Management
session_register('ID');

// Authenticating user
$mbox_id = "INBOX";
$email=new imap_pop3($default_port,$default_protocol,$default_host,$txtusername,$txtpassword,$mbox_id);
if ($email->authenticate()) {

  //Check to see if this is the first login.
  $user_id = 0;
  $dbq = $db->execute("select user_id from tblUsers where username = '$txtusername'");
  $user_id = $dbq->fields['user_id'];
  $dbq->close();

  $dbq = $db->execute("select " . $default_protocol . "_count from tblUsers where username = '$txtusername'");
  $visit_count = $dbq->fields[$default_protocol . '_count'];
  $dbq->close();

  $qdate = date('Y-m-d');
  $qtime = date('H:i');

  if ($visit_count == 0) {
    if ($user_id == 0) {               // User doesn't exist in tblUsers... (s)he didn' t log in before.
      $user = new userclass();
      $dbq = $db->execute("select user_id from tblUsers order by user_id asc");
      $dbq->lastRow();
      $user_id = $dbq->fields['user_id'];
      $dbq->close();
      $user_id++;

      // Find user' s domain_id
      $domain_id = $user->findUserDomainID($txtusername);
      // adding user to tblUsers with default values.
      $dbq = $db->execute("insert into tblUsers values($user_id,'$txtusername','','',$domain_id,'$qdate','$REMOTE_ADDR',0,0,'',0,0,0,0,0,0,'','','','','','','','','','','','')");
      $dbq->close();
    }

    if ($default_protocol == "pop3") {
      // creating default database mailboxes for POP3 access.
      $mbox = new mailbox();
      $mbox_id = $mbox->findMaxMboxID() + 1;
      $dbq = $db->execute("insert into tblMailBoxes values($mbox_id,$user_id,'$text7',1)");
      $mbox_id++;
      $dbq = $db->execute("insert into tblMailBoxes values($mbox_id,$user_id,'$text8',2)");
      $dbq->close();
    } else {
      $mbox2 = imap_open("{" . $default_host . "/" . $default_protocol . "}", $txtusername, $txtpassword);

      if ($mbox2) {

        $folders = $email->getMailboxes();
        $folder_names = array();
        $folder_counter=-1;
        $sent_item_exists = 0;
        $draft_exists = 0;
        while (list($key,$val) = each($folders["name"])) {
          $folder_counter++;
          $folder_names[$folder_counter] = $val;
          if ($folder_names[$folder_counter] == "Sent Items") {
            $sent_item_exists = 1;
          }
          if ($folder_names[$folder_counter] == "Drafts") {
            $draft_exists = 1;
          }
        }

        $sonuc = imap_createmailbox($mbox2, "{" . $default_host . "}INBOX");
        $sonuc = imap_subscribe($mbox2, "{" . $default_host . "}INBOX");

        if ($sent_item_exists == 0) {
          imap_createmailbox($mbox2, "{" . $default_host . "}Sent Items");
          imap_subscribe($mbox2, "{" . $default_host . "}Sent Items");
        }
        if ($draft_exists == 0) {
          imap_createmailbox($mbox2, "{" . $default_host . "}Drafts");
          imap_subscribe($mbox2, "{" . $default_host . "}Drafts");
        }
      }
    }

    $f = 1;                        // First time visitors shall see preferences screen when they enter...
  } // end if


  // We take a log of user details in tblLoggedUsers. This table will be used for gathering username and password.
  $dbq = $db->execute("select log_id from tblLoggedUsers");
  $dbq->lastrow();
  $log_id = $dbq->fields['log_id'];
  $dbq->close();
  $log_id++;
  $dbq = $db->execute("insert into tblLoggedUsers values($log_id,'$txtusername','','$ID','$REMOTE_ADDR','$qdate','$qtime','$user_id',0,0,'','')");
  $dbq->close();

  // Update the database mailbox names in tblMailboxes for POP3
  if ($default_protocol == "pop3") {
    $dbq = $db->execute("update tblMailBoxes set mboxname='$text7' where user_id = $user_id and mbox_type = 1");
    $dbq = $db->execute("update tblMailBoxes set mboxname='$text8' where user_id = $user_id and mbox_type = 2");
  }

  $dbq = $db->execute("select " . $default_protocol . "_count from tblUsers where username = '$txtusername'");
  $visit_count = $dbq->fields[$default_protocol . '_count'];
  $visit_count++;
  $dbq = $db->execute("update tblUsers set " . $default_protocol . "_count=$visit_count where username = '$txtusername'");
  $dbq->close();

  Header("Location: mailbox.php?mbox_id=INBOX&f=$f");

} else {
    Header("Location: index.php?error_id=1");
}

?>
