<?
/*
   File name         : contactdetails.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
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

  include ("$postaci_directory" . "includes/commonhead.inc");
  include ("$postaci_directory" . "includes/stylesheets.inc");
  include ("$postaci_directory" . "includes/javascripts.inc");

  // security check
  $item_id = clean_data($item_id);
  $dbq = $db->execute("select user_id from tblAdressbook where item_id=$item_id");
  $auth_user = $dbq->fields['user_id'];
  if ($auth_user != $user_id) {
    Header("Location: index.php?error_id=1");
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
            <hr size="1">&nbsp;<br>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" height="18">
              <tr bgcolor="#D1D1D1">
                <?
                  $mailadress = getMailAdress($user_id);
                ?>
                <td>&nbsp;<font color="#4E4E4E"><b> <? echo htmlspecialchars($mailadress) . " - " . $text22; ?></b></font></td>
              </tr>
            </table>&nbsp;<p><div align="center">

            <?

              $dbq = $db->execute("select * from tblAdressbook where user_id = $user_id and item_id = $item_id");
              $txtnamesurname = $dbq->fields['real_name'];
              $txtemail   = $dbq->fields['email1'];
              $txtnotes   = $dbq->fields['notes'];
              $txttelephone   = $dbq->fields['telephone'];
              $dbq->close();

              echo "      <form method='post' action='contactupdate.php?item_id=$item_id'>\n";
              echo "        <table width='550' border='0' cellspacing='1' cellpadding='0' bgcolor='#999999'>\n";
              echo "          <tr>\n";
              echo "            <td>\n";
              echo "              <table width='550' border='0' cellspacing='0' cellpadding='0'>\n";
              echo "                <tr bgcolor='#F3F3F3'>\n";
              echo "                  <td>&nbsp;</td>\n";
              echo "                  <td width='126'>&nbsp;</td>\n";
              echo "                  <td width='7'>&nbsp;</td>\n";
              echo "                  <td width='398'>&nbsp;</td>\n";
              echo "                </tr>\n";
              echo "                <tr bgcolor='#F3F3F3'>\n";
              echo "                  <td width='19'>&nbsp;</td>\n";
              echo "                  <td width='126'><font color='#4E4E4E'><b>$text61</b></font></td>\n";
              echo "                  <td width='7'><b><font color='#4E4E4E'>:</font></b></td>\n";
              echo "                  <td width='398'>\n";
              echo "                    <input type='text' name='txtnamesurname' class='stiltextfield' value='$txtnamesurname'>\n";
              echo "          				</td>\n";
              echo "                </tr>\n";
              echo "                <tr bgcolor='#F3F3F3'>\n";
              echo "                  <td width='19'>&nbsp;</td>\n";
              echo "                  <td width='126'><b><font color='#4E4E4E'>$text20</font></b></td>\n";
              echo "                  <td width='7'><b><font color='#4E4E4E'>:</font></b></td>\n";
              echo "                  <td width='398'>\n";
              echo "                    <input type='text' name='txtemail' class='stiltextfield' size='30' value='$txtemail'>\n";
              echo "          				</td>\n";
              echo "                </tr>\n";
              echo "                <tr bgcolor='#F3F3F3'>\n";
              echo "                  <td width='19'>&nbsp;</td>\n";
              echo "                  <td width='126'><b><font color='#4E4E4E'>$text62</font></b></td>\n";
              echo "                  <td width='7'><b><font color='#4E4E4E'>:</font></b></td>\n";
              echo "                  <td width='398'>\n";
              echo "                    <input type='text' name='txttelephone' class='stiltextfield' value='$txttelephone'>\n";
              echo "          </td>\n";
              echo "                </tr>\n";
              echo "                <tr bgcolor='#F3F3F3'>\n";
              echo "                  <td width='19' valign='top'>&nbsp;</td>\n";
              echo "                  <td width='126' valign='top'><b><font color='#4E4E4E'>$text66</font></b></td>\n";
              echo "                  <td width='7' valign='top'><b><font color='#4E4E4E'>:</font></b></td>\n";
              echo "                  <td width='398'>\n";
              echo "                    <textarea name='txtnotes' cols='30' rows='4' wrap='PHYSICAL' class='stiltextfield'>$txtnotes</textarea>\n";
              echo "          </td>\n";
              echo "                <tr bgcolor='#F3F3F3'>\n";
              echo "                  <td width='19' valign='top'>&nbsp;</td>\n";
              echo "                  <td width='126' valign='top'>&nbsp;</td>\n";
              echo "                  <td width='7' valign='top'>&nbsp;</td>\n";
              echo "                  <td width='398'>\n";
              echo "                    <input type='submit' name='Submit' value='$text67'>\n";
              echo "          </td>\n";
              echo "                </tr>\n";
              echo "                <tr bgcolor='#F3F3F3'>\n";
              echo "                  <td width='19'>&nbsp;</td>\n";
              echo "                  <td width='126'>&nbsp;</td>\n";
              echo "                  <td width='7'>&nbsp;</td>\n";
              echo "                  <td width='398'>&nbsp;</td>\n";
              echo "                </tr>\n";
              echo "              </table>\n";
              echo "            </td>\n";
              echo "          </tr>\n";
              echo "        </table>\n";
              echo "      </form>\n";


            ?>
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
