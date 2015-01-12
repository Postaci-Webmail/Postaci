<?
/*
   File name         : sendmail.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : Used for sending mail. User will type the text content here.
   Last modified     : 28 Feb 2005
*/

session_start();
include ("includes/global.inc");
include ("includes/functions.inc");

// ID comparison between logged hash and session. If they are both the same, let the user to go on...
$dbq = $db->execute("select log_id from tblLoggedUsers where hash = '$ID'");
$log_id = $dbq->fields['log_id'];
$dbq->close();
if ($log_id == ""){

  Header("Location: index.php?error_id=1");
} else {

  $mbox_id = rawurldecode($mbox_id);

  include ("$postaci_directory" . "classes/imap_pop3.inc");

  // Check to see if the mbox_id is a valid one.
  $dbq = $db->execute("select user_id,username from tblLoggedUsers where hash='$ID'");
  $username = $dbq->fields['username'];
  $user_id = $dbq->fields['user_id'];
  $dbq->close();

  $dbq = $db->execute("select signature from tblUsers where user_id=$user_id");
  $signature = $dbq->fields['signature'];
  $dbq->close();

if ($default_protocol == "pop3" && $mbox_id != "INBOX" && $post_act != 0 && $post_act !=4 && $post_act !=8) {                   // Security Check
$msg_id = clean_data($msg_id);
$dbq = $db->execute("select user_id from tblMessages where message_id=$msg_id");
$auth_user = $dbq->fields['user_id'];
$dbq->close();
if ($auth_user != $user_id) {
Header("Location: index.php?error_id=1");
}
}

$email=new imap_pop3($default_port,$default_protocol,$default_host,$username,$password,$mbox_id);

include ("$postaci_directory" . "includes/commonhead.inc");
include ("$postaci_directory" . "includes/stylesheets.inc");
include ("$postaci_directory" . "includes/javascripts.inc");
include ("$postaci_directory" . "includes/finish_spell.php");


if ($default_protocol == "imap" || $mbox_id == "INBOX") {   // Message is being forwarded and it may have attachments from POP3 INBOX or an IMAP folder.
if($post_act == 2) {
prepare_imap_attachments($msg_id);
}
}

if ($default_protocol == "pop3" && $mbox_id != "INBOX") {   // Message is being forwarded and it may have attachments from POP3 DB simulation
if($post_act == 6) {
prepare_pop3db_attachments($msg_id);
}
}
?>

<script language='JavaScript'>

function spellCheck() {
  document.myForm.msgbody.value = document.myForm.msgbody.value.replace(/^\s*|\s*$/g,"");
  if (document.myForm.msgbody.value != "") {
    document.myForm.action = "spellcheck.php";
    document.myForm.submit();
  } else {
  alert("<? echo $text108; ?>");
  }
}

qF = 'to';

function setIt(H) {
  if (H != qF) {
    document.myForm[qF].style.backgroundColor = "<? echo $nscolor; ?>";
  }
  qF = H;
  document.myForm[qF].style.backgroundColor = "<? echo $scolor; ?>";
}

function MIT(qaName){

  if (document.myForm[qF].value == "" && qF != "subject" && qF != "msgbody") {
    document.myForm[qF].value = qaName;
  } else if (qF != "subject" && qF != "msgbody") {
    document.myForm[qF].value += ',';
    document.myForm[qF].value += qaName;
  }
}
</script>

</head>

<body bgcolor='#F3F3F3' leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" background="images/background.gif" text="#000000" link="#1F4687" vlink="#1F4687" alink="#1F4687" onLoad="MM_preloadImages('images/turkish_version2.gif','images/english_version2.gif')">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="1%" bgcolor="<? echo $bgc; ?>"><img src="images/empty.gif" width="15" height="1"></td>
<td width="1%" bgcolor="<? echo $bgc; ?>" background="images/line_vertical_blue_back.gif"><img src="images/empty.gif" width="152" height="1"></td>
<td width="98%" bgcolor="<? echo $bgc; ?>" background="images/line_vertical_blue_back.gif" align="right"><img src="images/postaci_webmail.jpg" width="349" height="32" alt="ver : <? echo $postaci_version; ?>"></td>
</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="1">
<tr>
<td height="1" width="1%" bgcolor="<? echo $bgc; ?>"><img src="images/empty.gif" width="15" height="1"></td>
<td height="1" width="1%"><img src="images/line_horizantal_shadow.gif" width="165" height="1"></td>
<td height="1" width="98%"><img src="images/empty.gif" width="10" height="1"></td>
</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="400">
<tr>
<td width="1%"><img src="images/empty.gif" width="15" height="1"></td>
<td width="1%" valign="top" align="left"><img src="images/company_logo2.jpg" width="152" height="152"><br>
<table width="120" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td>
<img src="images/dot.gif" width="10" height="10"> <a href="sendmail.php?mbox_id=<? echo $mbox_id; ?>"><? echo "$text25"; ?></a>
</td>
</tr>
<tr>
<td>
<img src="images/dot.gif" width="10" height="10"> <a href="mailbox.php?mbox_id=INBOX"><? echo "$text26"; ?></a>
</td>
</tr>
<tr>
<td>
<img src="images/dot.gif" width="10" height="10"> <a href="folders.php"><? echo "$text27"; ?></a>
</td>
</tr>
<tr>
<td>
<table border="0" cellpadding="0" cellspacing="0" width="111">
<tr>
  <td width="8">
    <img src="images/empty.gif" height="1" width="15">
  </td>
  <td width="112">
    <?
      if ($default_protocol == "pop3") {
	echo "- <a href=\"mailbox.php?mbox_id=INBOX\">INBOX</a><br>";
      }
      $folders = $email->getMailboxes();
      $folder_names = array();
      $folder_counter=-1;
      while (list($key,$val) = each($folders["name"])) {
	$folder_counter++;
	$folder_names[$folder_counter] = $val;
      }
      $ids = array();
      $folder_counter=-1;
      while (list($key,$val) = each($folders["id"])) {
	$folder_counter++;
	$ids[$folder_counter] = $val;
      }

      for($i=0;$i<count($ids);$i++) {
	echo "- <a href=\"mailbox.php?mbox_id=$ids[$i]\">$folder_names[$i]</a><br>";
      }
    ?>
  </td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<img src="images/dot.gif" width="10" height="10"> <a href="preferences.php"><? echo "$text28"; ?></a>
</td>
</tr>
<tr>
<td>
<img src="images/dot.gif" width="10" height="10"> <a href="index.php"><? echo "$text29"; ?></a>
</td>
</tr>
<tr valign="bottom" align="center">
<?
if($show_sponsor) {
echo "<td height=\"70\" class=\"style=\"8\">$text78</td>\n";
} else {
echo "<td height=\"70\" class=\"style=\"8\">&nbsp;</td>\n";
}
?>
</tr>
<tr>
<td>
<div align="center">
<table width="111" border="0" cellspacing="0" cellpadding="1" bgcolor="#333333">
<tr>
  <?
    if ($show_sponsor) {
      echo "<td><a href=\"http://www.yimpas.net.tr\"><img src=\"images/sponsor.gif\" width=\"109\" height=\"30\" border=\"0\"></a></td>\n";
    }
  ?>
</tr>
</table>
</div>
</td>
</tr>
</table>
</td>
<td width="98%" valign="top">

<table width="100%" border="0" cellspacing="0" cellpadding="0" height="150">
<? include ($postaci_directory . "includes/headinside.inc"); ?>
<tr>
<td width="10" height="180" valign="top">
<p><img src="images/empty.gif" width="1" height="180"></p>
</td>
<td valign="top" align="left">
<hr size="1">

<?

// Delete (if there are) the zombie attachments. 

if ($attached != 1) {
$handle=opendir($attach_directory);
while ($file = readdir($handle)) {
  $attach_id = substr($file,1,46);
  $idvar = strstr($file,$ID);
  if ($idvar) {
    unlink($attach_directory . $file);
  }
}
}

if ($post_act != 0 && $post_act != 4 && $post_act != 5 && $post_act != 6) {
$msg_no_temp = $msg_no;
$msg_no = $msg_id;
$start=0;

$email->empty_mailbox();
$structure=imap_fetchstructure($email->mbox,$msg_no);

if (eregi("MIXED",$structure->subtype) )  {                       // Is the message a multipart one???
  $body = imap_fetchbody($email->mbox,$msg_no,1);
  $fullheader = imap_fetchheader($email->mbox,$msg_no);
  if (eregi("Content-Type: text/html",$fullheader) ) {            // message is simply text/plain
    $showhtml=1;
  } else {
    $showhtml=0;
  }
  $start=1;
}
if (eregi("RELATED",$structure->subtype) )  {                     // MS Outlook compatibility
  $body = imap_fetchbody($email->mbox,$msg_no,1);
  $showhtml=1;
  $start=1;
}
if ( $start== 0) {																					// Multipart deðil ama html ise...
  $fullheader = imap_fetchheader($email->mbox,$msg_no);
  if ( eregi("Content-Type: text/html",$fullheader) ) {
    $showhtml=1;
  } else {
    $showhtml=0;
  }
  $body = imap_fetchbody($email->mbox,$msg_no,1);
  $start=0;
}

// Message is HTML or not. If it is we crop the unnecessary tags.

$msg_enc  = 4;
$msg_part = $structure->parts[0];
$msg_enc  = $msg_part->encoding;
if ($msg_enc == 3) {
  $body = imap_base64($body);
}
if ($msg_enc == 4) {
  $body = imap_qprint($body);
}

if ($showhtml == 1) {
  $htmlbody = do_html_staff($body);
} else {
  if ($mesajencoding == 4) {
    $qp = 1;
  } else {
    $qp = 0;
  }
    $sending = true;
    $htmlbody=do_body_staff($body);
}

// Stop or continue decision

$related = 0;
if (eregi("RELATED",$structure->subtype) ) {
  $related = 1;
}
if (! eregi("MIXED",$structure->subtype) ) {
  if ($related != 1) {
    // exit;
  }
}

$msg_no = $msg_no_temp;
}

switch ($post_act) {
case 0 :                                      // It is coming back from attachments.php but a new message was being compesed
 $to      = stripslashes(rawurldecode($to));
 $cc      = stripslashes(rawurldecode($cc));
 $bcc     = stripslashes(rawurldecode($bcc));
 $subject = stripslashes(rawurldecode($subject));
 if (trim($msgbody) == "") {
   $msgbody = "\n\n\n\n$signature\n";
 }
 $msgbody = "\n" . rawurldecode($msgbody);
 break;
case 1 :                                       // It is INBOX a reply message
 $postaci_headers = $email->postaci_get_headers();
 for ($i = 0; $i<=$email->messagecount - 1;$i++) {
   if ($postaci_headers["msg_no"][$i] == $msg_id) {
     $to       = $postaci_headers["from"][$i];
     $cc       = $postaci_headers["cc"][$i];
     $subject  = "Re : " . $postaci_headers["subject"][$i];
     $msg_date = $postaci_headers["msg_date"][$i];
   }
 }

 $msgbody = ereg_replace("\n","\n>",$htmlbody);
 $ek = "\n\n\n\n$signature\n--------------------------------------------------------------------------\n$msg_date\n$to\n--------------------------------------------------------------------------\n";
 $msgbody = $ek . ">" . $msgbody;
 $msgbody = stripslashes($msgbody);
 break;
case 2 :                                       // It is INBOX a forward message
 //$email->empty_mailbox();
 $postaci_headers = $email->postaci_get_headers();
 for ($i = 0; $i<=$email->messagecount -1;$i++) {
   if ($postaci_headers["msg_no"][$i] == $msg_id) {
     $from       = $postaci_headers["from"][$i];
     $subject = "Fwd : " . $postaci_headers["subject"][$i];
     $msg_date = $postaci_headers["msg_date"][$i];
   }
 }

 $msgbody = ereg_replace("\n","\n>",$htmlbody);
 $ek = "\n\n\n\n$signature\n--------------------------------------------------------------------------\n$msg_date\n$from\n--------------------------------------------------------------------------\n";
 $msgbody = $ek . ">" . $msgbody;
 $msgbody = stripslashes($msgbody);
 break;
case 3 :                                      // It is INBOX and reply but from Sent Items folder
 $email->empty_mailbox();
 $postaci_headers = $email->postaci_get_headers();
 for ($i = 0; $i<=$email->messagecount - 1;$i++) {
   if ($postaci_headers["msg_no"][$i] == $msg_id) {
     $to      = $postaci_headers["to"][$i];
     $cc      = $postaci_headers["cc"][$i];
     $subject = "Re : " . $postaci_headers["subject"][$i];
     $msg_date = $postaci_headers["msg_date"][$i];
   }
 }
 $msgbody = ereg_replace("\n","\n>",$html_body);
 $ek = "\n\n\n\n$signature\n--------------------------------------------------------------------------\n$msg_date\n$to\n--------------------------------------------------------------------------\n";
 $msgbody = $ek . ">" . $msgbody;
 $msgbody = stripslashes($msgbody);
 break;
case 4 :                                      // It is coming back from attachments.php
 $to      = stripslashes(rawurldecode($to));
 $cc      = stripslashes(rawurldecode($cc));
 $bcc     = stripslashes(rawurldecode($bcc));
 $subject = stripslashes(rawurldecode($subject));
 $msgbody = "\n" . rawurldecode($msgbody);
 break;
case 5 :                                      // It is a DB folder and coming from Sent Items to REPLY
 $dbq = $db->execute("select * from tblMessages where message_id=$msg_id and user_id = $user_id");
 $from     = $dbq->fields['header_from'];
 $to       = $dbq->fields['header_to'];
 $cc       = $dbq->fields['header_cc'];
 $subject  = "Re: " . $dbq->fields['header_subject'];
 $msg_date = $dbq->fields['header_date'];
 $msg_body = $dbq->fields['msg_body'];
 $dbq->close();

 if ($msg_body == ""){
   $msg_body = $text88;
 }
 $msgbody = ereg_replace("\n","\n>",$msg_body);
 $ek = "\n\n\n\n$signature\n--------------------------------------------------------------------------\n$msg_date\n$from\n--------------------------------------------------------------------------\n";
 $msgbody = $ek . ">" . $msgbody;
 $msgbody = stripslashes($msgbody);
 break;
case 6 :                                      // It is a DB folder and coming from Sent Items to FORWARD
 $dbq = $db->execute("select * from tblMessages where message_id=$msg_id and user_id = $user_id");
 $from     = $dbq->fields['header_from'];
 $subject  = "Fwd: " . $dbq->fields['header_subject'];
 $msg_date = $dbq->fields['header_date'];
 $msg_body = $dbq->fields['msg_body'];
 $dbq->close();

 if ($msg_body == ""){
   $msg_body = $text88;
 }
 $msgbody = ereg_replace("\n","\n>",$msg_body);
 $ek = "\n\n\n\n$signature\n--------------------------------------------------------------------------\n$msg_date\n$from\n--------------------------------------------------------------------------\n";
 $msgbody = $ek . ">" . $msgbody;
 $msgbody = stripslashes($msgbody);
 break;
case 7 :                                      // It is a DB folder and coming to REPLY
 $dbq = $db->execute("select * from tblMessages where message_id=$msg_id and user_id = $user_id");
 $from     = $dbq->fields['header_from'];
 $to       = $dbq->fields['header_from'];
 $cc       = $dbq->fields['header_cc'];
 $subject  = "Re : " . $dbq->fields['header_subject'];
 $msg_date = $dbq->fields['header_date'];
 $msg_body = $dbq->fields['msg_body'];
 $dbq->close();
 if ($msg_body == ""){
   $msg_body = $text88;
 }
 $msgbody = ereg_replace("\n","\n>",$msg_body);
 $ek = "\n\n\n\n$signature\n--------------------------------------------------------------------------\n$msg_date\n$from\n--------------------------------------------------------------------------\n";
 $msgbody = $ek . ">" . $msgbody;
 $msgbody = stripslashes($msgbody);
 break;

case 8 :                                      // It is returning from spellcheck.php 
 $to       = stripslashes($spellto);
 $cc       = stripslashes($spellcc);
 $bcc      = stripslashes($spellbcc);
 $subject  = stripslashes($spellsubject);
 $msgbody  = stripslashes($newtext);
 $msgbody  = stripslashes($msgbody);
 
 break;

}

$to      = turkcelestir($to);
$from    = turkcelestir($from);
$subject = turkcelestir($subject);
$cc      = turkcelestir($cc);
$msgbody = turkcelestir($msgbody);
$msgbody = strip_tags($msgbody);
?>

<div align='center'>&nbsp;<br>
<form name='myForm' method='post' action='postmail.php' onSubmit='submitonce(this)'>
<input type="hidden" name="mbox_id" value="<? echo $mbox_id; ?>">
	<input type='hidden' name='post_act' value='<? echo $post_act; ?>'>
	<table width='550' border='0' cellspacing='0' cellpadding='1' bgcolor='#999999'>
	  <tr bgcolor="#F3F3F3">
	    <td></td><td><? print "<center><b><font color='#4E4E4E'>".ucwords($text22)."</font></b></center>"; ?></td>
	  </tr>
	  <tr>
	    <td>
	      <table width='100%' border='0' cellspacing='0' cellpadding='1'>
		<tr bgcolor='#DFDFDF'>
		  <td align='left' nowrap height='12'><img src='images/empty.gif' width='20' height='12'></td>
		  <td align='left' nowrap height='12'><img src='images/empty.gif' width='110' height='12'></td>
		  <td align='left' nowrap height='12'><img src='images/empty.gif' width='10' height='12'></td>
		  <td width='370' align='left' nowrap height='12'><img src='images/empty.gif' width='370' height='12'></td>
		</tr>
		<tr bgcolor='#DFDFDF'>
		  <td width='20' align='left' bgcolor='#DFDFDF' nowrap>&nbsp;</td>
		  <td width='110' align='left' bgcolor='#DFDFDF' nowrap><b><font color='#4E4E4E'><? echo $text32; ?></font></b></td>
		  <td width='10' align='center'><b><font color='#4E4E4E'>:</font></b></td>
		  <td width='370' bgcolor='#DFDFDF'>
		    <input type='text' name='to' class='styletextfield' size='45' value='<? echo $to; ?>' onFocus="setIt('to')" style='background-color:<? echo $scolor; ?>'>
		  </td>
		</tr>
		<tr bgcolor='#DFDFDF'>
		  <td width='20' align='left' nowrap>&nbsp;</td>
		  <td width='110' align='left' nowrap bgcolor='#DFDFDF'><b><font color='#4E4E4E'>CC</font></b></td>
		  <td width='10' align='center'><b><font color='#4E4E4E'>:</font></b></td>
		  <td width='370'>
		    <input type='text' name='cc' class='styletextfield' size='45' value='<? echo $cc; ?>' onFocus="setIt('cc')" style=
'background-color:<? echo $nscolor; ?>'>
		  </td>
		</tr>
		<tr bgcolor='#DFDFDF'>
		  <td width='20' align='left' valign='top' nowrap>&nbsp;</td>
		  <td width='110' align='left' nowrap bgcolor='#DFDFDF'><b><font color='#4E4E4E'>BCC</font></b></td>
		  <td width='10' align='center'><b><font color='#4E4E4E'>:</font></b></td>
		  <td width='370'>
		    <input type='text' name='bcc' class='styletextfield' size='45' value="<? echo $bcc; ?>" onFocus="setIt('bcc')" style=
'background-color:<? echo $nscolor; ?>'>
	  </td>
		</tr>
                        <tr bgcolor='#DFDFDF'>
                          <td width='20' align='left' nowrap>&nbsp;</td>
                          <td width='110' align='left' nowrap bgcolor='#DFDFDF'><b><font color='#4E4E4E'><? echo $text13; ?></font></b></td>
                          <td width='10' align='center' nowrap><b><font color='#4E4E4E'>:</font></b></td>
                          <td width='370' nowrap>
                            <input type='text' name='subject' class='styletextfield' size='45' value='<? echo $subject; ?>' style='background-color:<? echo $nscolor; ?>' onFocus="setIt('subject')">
                          </td>
                        </tr>
                        <tr bgcolor='#DFDFDF'>
                          <td width='20' align='left' valign='top' nowrap>&nbsp;</td>
                          <td width='110' align='left' valign='top' nowrap bgcolor='#DFDFDF'><input type='submit' name='submitX' value='<? echo $text33; ?>' class='stilbutton' onClick="formURL('1')" ></font></td>
                          <td width='10' align='center' valign='top'><b><font color='#4E4E4E'>:</font></b></td>
                          <td width='400' nowrap>
                            <?
                              $handle=opendir($attach_directory);
                              while ($file = readdir($handle)) {
                                $attach_id = substr($file,1,46);
                                $idvar = strstr($file,$ID);
                                if ($idvar) {
                                  $attachedfile = strstr($file,"######");
                                  $attach_name = substr($attachedfile,6);
                                  $ebat = filesize($attach_directory . $file);
                                  $ebat = ceil($ebat / 1024);
                                  echo "<font color='#1F4687'><img src='images/attach.gif'>&nbsp;$attach_name ($ebat K)</font><br>\n";
                                }
                              }
                              closedir($handle);
                            ?>
                          </td>
                        </tr>
                      </table>
                    </td>
		    <td rowspan="3" bgcolor="#F3F3F3" valign="top">
		      <select name="ab" size=20 style="width:180px;border:1px" width="170px" onclick="MIT(this.options[this.selectedIndex].value);">
		      <?
			$dbq = $db->execute("select email1  from tblAdressbook where user_id = $user_id order by email1");
                	while (!$dbq->EOF) {
                  	$email1 = $dbq->fields['email1'];
			print "<option value='".$email1."'>".$email1;
			$dbq->nextRow();
			}
		      ?>
		      </select>
		      <center><b><font color='#4E4E4E'><? echo $text106; ?></font></b></center>
		    </td>
                  </tr>
                  <tr>
                    <td bgcolor='#F3F3F3'>
                      <textarea name='msgbody' cols='74' rows='18' wrap='PHYSICAL' class='stylemsgbody' style='background-color:<? echo $nscolor; ?>' onFocus="setIt('msgbody')"><? echo $msgbody; ?></textarea><br>
                    </td>
                  </tr>
                  <tr>
                    <td bgcolor='#F3F3F3' align='right'>
		      <?
			if ($spellcheck_on) {
		          print "<input type='button' name='spellcheck' value='".$text109."' onClick='spellCheck()'>";
			}
		      ?>
                      &nbsp;
                      <input type='submit' name='submitX' value='<? echo $text43; ?>' onClick="formURL('2')">
                    </td>
                  </tr>
                </table>
              </form>
            </div>
            <p>&nbsp; </p>
          </td>
          <td valign="top" width="10">&nbsp;</td>
        </tr>
        <tr>
          <td width="10" height="80" valign="top">&nbsp;</td>
          <td valign="bottom" align="right">
            <hr size="1">
            <div align="right"><span class="style8"><? echo $footer; ?></span></div>
            <p>
          </td>
          <td>&nbsp;</td>
        </tr>
      </table>
      </td>
    </tr>
  </table>
  </body>
  </html>

<?
}
?>
