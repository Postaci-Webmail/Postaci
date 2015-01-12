<?
/*
   File name         : preferences.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : User preferences. user can change his/her details from here.
   Last modified     : 29 Sept 2006 
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

  $dbq = $db->execute("select rsrv_int1 from tblUsers where user_id='$user_id'");  
  $seperator = $dbq->fields['rsrv_int1'];
  $favorites = $dbq->fields['rsrv_int2'];
  $notebook = $dbq->fields['rsrv_int3'];
  $dbq->close();

  if ($seperator == 0) {
    $seperator = 15; 
  }

  $email=new imap_pop3($default_port,$default_protocol,$default_host,$username,$password,$mbox_id);

  include ("$postaci_directory" . "includes/commonhead.inc");
  include ("$postaci_directory" . "includes/stylesheets.inc");
  include ("$postaci_directory" . "includes/javascripts.inc");
  include ("$postaci_directory" . "includes/functions.inc");

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
              echo "<td height=\"75\" class=\"style=\"8\">$text78</td>\n";
            } else {
              echo "<td height=\"75\" class=\"style=\"8\">&nbsp;</td>\n";
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
            <table width="100%" border="0" cellspacing="0" cellpadding="0" height="18">
              <tr bgcolor="#D1D1D1">
                <?
                  $mailadress = getMailAdress($user_id);
                ?>
                <td>&nbsp;<font color="#4E4E4E"><b> <? echo htmlspecialchars($mailadress) . " - " . $text28; ?></b></font></td>
              </tr>
            </table>&nbsp;<p>

            <?
              $dbq = $db->execute("select real_name,signature,rsrv_char1,rsrv_char2,rsrv_char3 from tblUsers where user_id=$user_id");
              $real_name = $dbq->fields['real_name'];
              $signature = $dbq->fields['signature'];
	      $email     = $dbq->fields['rsrv_char1'];
	      $smtp_user = $dbq->fields['rsrv_char2'];
	      $smtp_pass = $dbq->fields['rsrv_char3'];

              $dbq->close();
            ?>
            <div align="center">
            <form method="post" action="save_preferences.php"><table width="502" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="502" colspan="3">
                    <hr size="1">
                  </td>
                </tr>
                <tr>
                  <td width="125"><? echo $text100; ?></td>
                  <td width="10">:</td>
                  <td width="367">
                    <input type="text" name="txtreal_name" maxlength="100" size="40" value="<? echo $real_name; ?>">
                  </td>
                </tr>
		<tr>
                  <td width="502" colspan="3">
                    <hr size="1">
                  </td>
                </tr>
		<tr>
                  <td width="125"><? echo ucfirst($text20); ?></td>
                  <td width="10">:</td>
                  <td width="367">
                    <input type="text" name="txtemail" maxlength="100" size="40" value="<? echo $email; ?>">
                  </td>
                </tr>
                <tr>
                  <td width="502" colspan="3">
                    <hr size="1">
                  </td>
                </tr>
                <tr>
                  <td width="125" valign="top"><? echo $text101; ?></td>
                  <td width="10" valign="top">:</td>
                  <td width="367">
                    <textarea name="txtsignature" cols="35" rows="3" wrap="PHYSICAL"><? echo $signature; ?></textarea>
                  </td>
                </tr>
                <tr>
                  <td width="502" colspan="3">
                    <hr size="1">
                  </td>
                </tr>
                <tr>
                  <td width="125" align="right"><? echo $text104; ?></td>
                  <td width="10">:</td>
		  <td>
		    <input type='text' size='4'  name='emailspp' value='<? echo $seperator; ?>'>
		    <? echo $text105; ?>
                  </td>
                </tr>
           <?  if ($smtp_auth == "yes" && $smtp_same == "no" && $smtp_imap == "no") { ?>
		<tr>
                  <td width="502" colspan="3">
                    <hr size="1">
                  </td>
                </tr>
                <tr>
                  <td width="125"><? echo "SMTP $text2"; ?></td>
                  <td width="10">:</td>
                  <td width="367">
                    <input type="text" name="txtsmtp_user" maxlength="100" size="30" value="<? echo $smtp_user; ?>">
                  </td>
                </tr>
		<tr>
                  <td width="502" colspan="3">
                    <hr size="1">
                  </td>
                </tr>
                <tr>
                  <td width="125"><? echo "SMTP $text3"; ?></td>
                  <td width="10">:</td>
                  <td width="367">
                    <input type="password" name="txtsmtp_pass" maxlength="100" size="30" value="<? echo $smtp_pass; ?>">
                  </td>
                </tr>
	   <?  }  ?>	

                <tr>
                  <td width="502" colspan="3">
                    <hr size="1">
                  </td>
                </tr>
                <tr>
                  <td width="135" colspan="2">&nbsp;</td>
                  <td width="367">
		    <br>
                    <input type="submit" name="Submit" value="<? echo $text53; ?>">
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
