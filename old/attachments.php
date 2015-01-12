<?
/*
   File name         : attachments.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : Adding and deleting attachments to the e-mail to be send.
   Last modified     : 28 Feb 2005 
*/

session_start();
include ("includes/global.inc");

// ID comparison between logged hash and session. If they are both the same, let the user to go on...
$dbq = $db->execute("select log_id from tblLoggedUsers where hash = '$ID'");
$log_id = $dbq->fields['log_id'];
$dbq->close();
if ($log_id == ""){

  Header("Location: index.php?error_id=1");
} else {

  function mychop($thistring,$length) {
    $result="";
    $mystring=trim($thistring);
    if (strlen($mystring)>$length) {
      $result=substr($mystring,0,$length);
      $result = "$result...";
    }else {
      $result=$mystring;
    }

    return($result);
  }

  $mbox_id = rawurldecode($mbox_id);

  include ("$postaci_directory" . "classes/imap_pop3.inc");

  // Check to see if the mbox_id is a valid one.
  $dbq = $db->execute("select user_id,username from tblLoggedUsers where hash='$ID'");
  $username = $dbq->fields['username'];
  $user_id = $dbq->fields['user_id'];
  $dbq->close();

  $email=new imap_pop3($default_port,$default_protocol,$default_host,$username,$password,$mbox_id);

  //$email->mbox_exists($mbox_id);
  include ("$postaci_directory" . "includes/commonhead.inc");
  include ("$postaci_directory" . "includes/stylesheets.inc");
  include ("$postaci_directory" . "includes/javascripts.inc");
  include ("$postaci_directory" . "includes/functions.inc");

  $to=rawurlencode($to);
  $cc=rawurlencode($cc);
  $bcc=rawurlencode($bcc);
  $subject=rawurlencode($subject);
  if ($attached ==1) {												// Attachment eklendi
    $dosyaadi = "$attach_directory$ID#####msgbody.txt";
    $msgbody = fread(fopen($dosyaadi, "r"), filesize($dosyaadi));
    fclose($fp);
    unlink("$dosyaadi");
  } else {
    $msgbody=stripslashes($msgbody);
    $msgbody=rawurlencode($msgbody);
  }
?>
  </head>

  <body bgcolor='#F3F3F3' leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" background="images/background.gif" text="#000000" link="#1F4687" vlink="#1F4687" alink="#1F4687" onLoad="MM_preloadImages('images/turkish_version2.gif','images/english_version2.gif')">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="1%" bgcolor="<? echo $bgc; ?>"><img src="images/empty.gif" width="15" height="1"></td>
      <td width="1%" bgcolor="<? echo $bgc; ?>" background="images/line_vertical_blue_back.gif"><img src="images/empty.gif" width="152" height="1"></td>
      <td width="98%" bgcolor="<? echo $bgc; ?>" background="images/line_vertical_blue_back.gif" align="right"><img src="images/postaci_webmail.jpg" width="349" height="32" alt="ver :  <? echo $postaci_version; ?>"></td>
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
              <img src="images/dot.gif" width="10" height="10">
              <a href="http://login.postini.com/exec/login?email=<? echo $txtusername; ?>" target="_blank"><? echo $text103; ?></a>
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
              <br>
              <br> 
              <!-- BEGIN PHP Live! code, (c) OSI Codes Inc. -->
              <script language="JavaScript" src="http://chat.infowest.com/phplive/js/status_image.php?base_url=http://chat.infowest.com/phpliv
e&l=infowest&x=1&deptid=0&"><a href="http://www.phplivesupport.com"></a></script>
              <!-- END PHP Live! code : (c) OSI Codes Inc. -->
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
            <hr size="1">&nbsp;<br>

            <div align="center">



       <table width='500' border='0' cellspacing='0' cellpadding='1' align='center'>
         <tr bgcolor='#999999'>
          <td>
            <table width='100%' border='0' cellspacing='0' cellpadding='0' height='200'>
              <tr bgcolor='#DFDFDF'>
                <td height='20' width='4%'>&nbsp;</td>
                <td height='20' width='92%'>&nbsp;</td>
                <td height='20' width='4%'>&nbsp;</td>
              </tr>
              <tr bgcolor='#DFDFDF'>
                <td height='25' width='4%'>&nbsp;</td>
                <td height='25' width='92%'>
                  <form enctype="multipart/form-data" action="upload.php" method="post" onSubmit="submitonce(this)">
                    <input type="hidden" name="to" value="<? echo $to; ?>">
                    <input type="hidden" name="cc" value="<? echo $cc; ?>">
                    <input type="hidden" name="bcc" value="<? echo $bcc; ?>">
                    <input type="hidden" name="subject" value="<? echo $subject; ?>">
                    <input type='hidden' name='msgbody' value='<? echo $msgbody; ?>'>
                    <input type='hidden' name='mbox_id' value='<? echo $mbox_id; ?>'>
                    <input type='hidden' name='post_act' value='<? echo $post_act; ?>'>
                    <input name='userfile' type='file' size='30'>
                    <input type='submit' value='<? echo $text44; ?>' name='submit'>
                  </form>
                </td>
                <td height='25' width='4%'>&nbsp;</td>
              </tr>
              <tr bgcolor='#DFDFDF'>
                <td width='4%'>&nbsp;</td>
                <td width='92%' valign='top'>
                  <hr size='1'>
                  <form method="post" action="deleteattachment.php" onSubmit="submitonce(this)">
                    <select name='slcsil' size='5'>
                      <option value='0'>-- <? echo $text45; ?> --</option>
                        <?
                        // lists the attached files in a listbox.

                          $handle=opendir($attach_directory);
                          while ($file = readdir($handle)) {
                            $attach_id = substr($file,1,46);
                            $idvar = strstr($file,$ID);
                            if ($idvar) {
                              $attachedfile = strstr($file,"######");
                              $attach_name = substr($attachedfile,6);
                              $ebat = filesize($attach_directory . $file);
                              $ebat = ceil($ebat / 1024);
                              echo "<option value='$file'>$attach_name ($ebat K)</option>\n";
                            } // end if
                          } // end while
                          closedir($handle);
                        ?>
                    </select>
                    <input type="hidden" name="to" value="<? echo $to; ?>">
                    <input type="hidden" name="cc" value="<? echo $cc; ?>">
                    <input type="hidden" name="bcc" value="<? echo $bcc; ?>">
                    <input type="hidden" name="subject" value="<? echo $subject; ?>">
                    <input type='hidden' name='msgbody' value='<? echo $msgbody; ?>'>
                    <input type='hidden' name='mbox_id' value='<? echo $mbox_id; ?>'>
                    <input type='hidden' name='post_act' value='<? echo $post_act; ?>'>
                    <input type='submit' name='eklentisil' value='<? echo $text46; ?>'>
                  </form>
                  <form method="post" action="sendmail.php" onSubmit="submitonce(this)">
                    <input type='hidden' name="attached" value="1">
                    <input type="hidden" name="to" value="<? echo $to; ?>">
                    <input type="hidden" name="cc" value="<? echo $cc; ?>">
                    <input type="hidden" name="bcc" value="<? echo $bcc; ?>">
                    <input type="hidden" name="subject" value="<? echo $subject; ?>">
                    <input type='hidden' name='msgbody' value='<? echo $msgbody; ?>'>
                    <input type='hidden' name='mbox_id' value='<? echo $mbox_id; ?>'>

                    <input type='submit' name='geridon' value='<- <? echo $text47; ?>'>
                  </form>
                </td>
                <td width='4%'>&nbsp;</td>
              </tr>

            </table>
            </td>
          </tr>
        </table>
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
