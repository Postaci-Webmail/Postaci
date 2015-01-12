<?
/*
   File name         : favorites.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
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
                <td>&nbsp;<font color="#4E4E4E"><b> <? echo htmlspecialchars($mailadress) . " - " . $text23; ?></b></font></td>
              </tr>
            </table>&nbsp;<p>

            <?

              echo "      <div align='center'><br>\n";
              echo "        <table width='100%' border='0' cellspacing='2' cellpadding='0'>\n";
              echo "          <tr>\n";
              echo "            <td width='50%' bgcolor='#D1D1D1'><b><font color='#4E4E4E'>$text68</font></b></td>\n";
              echo "            <td width='43%' bgcolor='#D1D1D1'><b><font color='#4E4E4E'>$text69</font></b></td>\n";
              echo "            <td width='7%' bgcolor='#D1D1D1'>&nbsp;</td>\n";
              echo "          </tr>\n";

              $contactcount = 0;
              $dbq = $db->execute("select favorite_id from tblFavorites where user_id = $user_id");
              $favoritecount = $dbq->getNumOfRows();

            	if ($favoritecount == 0) {
                echo "          <tr>\n";
                echo "            <td nowrap colspan='3'><div align='center'>&nbsp;<br><b><font color='#304167'>$text70</font></b><hr size='1'><p></div></td>\n";
                echo "          </tr>\n";
            	} else {

                $dbq = $db->execute("select * from tblFavorites where user_id = $user_id order by url_title");
                while (!$dbq->EOF) {
                  $favorite_id = $dbq->fields['favorite_id'];
                  $url         = $dbq->fields['url'];
                  $url_title   = $dbq->fields['url_title'];
                  echo "          <tr>\n";
                  echo "            <td width='50%' nowrap bgcolor='#E3E7E8'><a href='favoritedetails.php?favorite_id=$favorite_id'>$url_title</a></td>\n";
                  echo "            <td width='43%' nowrap bgcolor='#E3E7E8'><a href='$url' target='_blank'>$url</a></td>\n";
                  echo "            <td width='7%' nowrap bgcolor='#E3E7E8'>\n";
                  echo "              <div align='center'><a href='deletefavorites.php?favorite_id=$favorite_id'>$text57</a></div>\n";
                  echo "            </td>\n";
                  echo "          </tr>\n";
                  $dbq->nextRow();
                }
                $dbq->close();
              }

              echo "        </table>\n";
              echo "        <br>\n";
              echo "        <form method='post' action='createfavorites.php'>\n";
              echo "          <div align='center'>\n";
              echo "      </div>\n";
              echo "          <table width='550' border='0' cellspacing='0' cellpadding='0' align='center' bgcolor='#999999'>\n";
              echo "            <tr>\n";
              echo "              <td>\n";
              echo "                <table width='550' border='0' cellspacing='1' cellpadding='0' align='center'>\n";
              echo "                  <tr bgcolor='#D1D1D1'>\n";
              echo "                    <td><b><font color='#4E4E4E'>$text71</font></b></td>\n";
              echo "                  </tr>\n";
              echo "                  <tr align='center' bgcolor='#F3F3F3'>\n";
              echo "                    <td>\n";
              echo "                      <table width='100%' border='0' cellspacing='0' cellpadding='0'>\n";
              echo "                        <tr>\n";
              echo "                          <td width='23%'>$text68</td>\n";
              echo "                          <td width='2%'>:</td>\n";
              echo "                          <td width='75%'>\n";
              echo "                            <input type='text' name='txttitle' size='45' class='stiltextfield'>\n";
              echo "                        </td>\n";
              echo "                        </tr>\n";
              echo "                        <tr>\n";
              echo "                          <td width='23%'>$text69</td>\n";
              echo "                          <td width='2%'>:</td>\n";
              echo "                          <td width='75%'>\n";
              echo "                            <input type='text' name='txturl' value='http://' size='45' class='stiltextfield'>\n";
              echo "                        </td>\n";
              echo "                        </tr>\n";
              echo "                        <tr>\n";
              echo "                          <td width='23%'>&nbsp;</td>\n";
              echo "                          <td width='2%'>&nbsp;</td>\n";
              echo "                          <td width='75%'>\n";
              echo "                            <input type='submit' name='Submit' value='$text65'>\n";
              echo "                        </td>\n";
              echo "                        </tr>\n";
              echo "                      </table>\n";
              echo "                    </td>\n";
              echo "                  </tr>\n";
              echo "                </table>\n";
              echo "              </td>\n";
              echo "            </tr>\n";
              echo "          </table>\n";
              echo "        </form>\n";
              echo "        <br>\n";
              echo "      </div>\n";

            ?>
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
