<?
/*
   File name         : folders.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : Lists all folders. user can add, delete, rename folders at this interface.
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
  include ("$postaci_directory" . "classes/imap_pop3.inc");

  // Check to see if the mbox_id is a valid one.
  $dbq = $db->execute("select user_id,username from tblLoggedUsers where hash='$ID'");
  $username = $dbq->fields['username'];
  $user_id = $dbq->fields['user_id'];
  $dbq->close();
  $mbox_id = "INBOX";
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
              <table width='100%' border='0' cellspacing='0' cellpadding='0' height="18">
                <tr bgcolor='#D1D1D1'>
                  <?
                    $mailadress = getMailAdress($user_id);
                  ?>
                  <td align='left'><font color='#4E4E4E'>&nbsp;&nbsp;<b><? echo htmlspecialchars($mailadress) . " - " . $text27; ?></b></font></td>
                </tr>
              </table>&nbsp;<p>
              <div align="center">
              <?
                 switch ($error_id) {
                   case 4:
                     echo "<b class='styleerror'>$text92</b><br>";
                     break;
                   case 5:
                     echo "<b class='styleerror'>$text93</b><br>";
                     break;
                   case 6:
                     echo "<b class='styleerror'>$text94</b><br>";
                     break;
                   case 7:
                     echo "<b class='styleerror'>$text95</b><br>";
                     break;
                   default:
                     echo "<b class='styleerror'>&nbsp;</b><br>";
                 }
              ?>
              </div>


              <table width='550' border='0' cellspacing='1' cellpadding='0' align='center' bgcolor='#999999'>
                <tr>
                  <td>
                    <table width='100%' border='0' cellspacing='1' cellpadding='1'>
                      <tr bgcolor='#DFDFDF'>
                        <td width='26%'><b><font color='#4E4E4E'><? echo $text27; ?></font></b></td>
                        <td width='33%'>
                          <div align='center'><b><font color='#4E4E4E'><? echo $text19; ?></font></b></div>
                        </td>
                        <td width='41%' nowrap>&nbsp;</td>
                      </tr>
                      <?
                        if ($default_protocol == "imap") { // imap protocol
                          $mbox_id = "INBOX";
                          $email=new imap_pop3($default_port,$default_protocol,$default_host,$username,$password,$mbox_id);
                          $email->empty_mailbox();
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

                          $email->close_mailbox();
                          for($i=0;$i<count($ids);$i++) {
                            $email=new imap_pop3($default_port,$default_protocol,$default_host,$username,$password,"$folder_names[$i]");
                            $email->empty_mailbox();

                            if ($folder_names[$i] == "INBOX" || $folder_names[$i] == $to_folder) {
                              echo "<tr bgcolor='#F3F3F3'>\n";
                              echo "  <td width='26%'><a href='mailbox.php?mbox_id=$ids[$i]'>$folder_names[$i]</a></td>\n";
                              echo "  <td width='33%'>\n";
                              echo "    <div align='center'>$email->messagecount</div>\n";
                              echo "  </td>\n";
                              echo "  <td width='41%' nowrap>&nbsp;</td>\n";
                              echo "</tr>\n";
                            } else {               // regular imap folder
                              echo "<tr bgcolor='#F3F3F3'>\n";
                              echo "  <td width='26%'><a href='mailbox.php?mbox_id=$ids[$i]'>$folder_names[$i]</a></td>\n";
                              echo "  <td width='33%'>\n";
                              echo "    <div align='center'>$email->messagecount</div>\n";
                              echo "  </td>\n";
                              echo "  <td width='41%' nowrap>\n";
                              echo "    <table width='100%' border='0' cellspacing='0' cellpadding='0'>\n";
                              echo "      <tr>\n";
                              echo "        <td width='50%' nowrap>\n";
                              echo "           <div align='right'><a href='deletefolder.php?mbox_id=$ids[$i]'>$text51</a></div>\n";
                              echo "        </td>\n";
                              echo "        <td width='4%'>\n";
                              echo "           <div align='center'>-</div>\n";
                              echo "        </td>\n";
                              echo "        <td width='46%'><a href='renamefolder.php?mbox_id=$ids[$i]'>$text52</a></td>\n";
                              echo "      </tr>\n";
                              echo "    </table>\n";
                              echo "  </td>\n";
                              echo "</tr>\n";
                            } // end if

                            $email->close_mailbox();

                          } // end for
                        } else {                               // POP3 protocol
                          echo "              <tr bgcolor='#F3F3F3'>\n";
                          echo "                <td width='26%'><a href='mailbox.php?mbox_id=INBOX'>INBOX</a></td>\n";
                          echo "                <td width='33%'>\n";
                          $mbox = imap_open("{".$default_host."/pop3:110}INBOX", "$username", "$password");
                          if ($mbox) {
                            $mboxstatus=imap_check($mbox);
                            $msgcount=$mboxstatus->Nmsgs;
                          }
                          imap_close($mbox);
                          echo "                  <div align='center'>$msgcount</div>\n";
                          echo "                </td>\n";
                          echo "                <td width='41%' nowrap>&nbsp;</td>\n";
                          echo "              </tr>\n";

                          $dbq = $db->execute("select mbox_id, mboxname, mbox_type from tblMailBoxes where user_id = $user_id order by mbox_id asc");
                          while (!$dbq->EOF) {
                            $mbox_type = $dbq->fields['mbox_type'];
                            $mbox_id   = $dbq->fields['mbox_id'];
                            $mboxname  = $dbq->fields['mboxname'];

                            $dbq2 = $db->execute("select * from tblMessages where mbox_id=$mbox_id and user_id = $user_id");
                            $msgcount = $dbq2->getNumOfRows();

                            if ($mbox_type == 1) {         // Sent Items
                              echo "<tr bgcolor='#F3F3F3'>\n";
                              echo "  <td width='26%'><a href='mailbox.php?mbox_id=$mbox_id'>$mboxname</a></td>\n";
                              echo "  <td width='33%'>\n";
                              echo "    <div align='center'>$msgcount</div>\n";
                              echo "  </td>\n";
                              echo "  <td width='41%' nowrap>&nbsp;</td>\n";
                              echo "</tr>\n";
                            } else {                      // Regular POP3 folder simulation
                              echo "<tr bgcolor='#F3F3F3'>\n";
                              echo "  <td width='26%'><a href='mailbox.php?mbox_id=$mbox_id'>$mboxname</a></td>\n";
                              echo "  <td width='33%'>\n";
                              echo "    <div align='center'>$msgcount</div>\n";
                              echo "  </td>\n";
                              echo "  <td width='41%' nowrap>\n";
                              echo "    <table width='100%' border='0' cellspacing='0' cellpadding='0'>\n";
                              echo "      <tr>\n";
                              echo "        <td width='50%' nowrap>\n";
                              echo "           <div align='right'><a href='deletefolder.php?mbox_id=$mbox_id'>$text51</a></div>\n";
                              echo "        </td>\n";
                              echo "        <td width='4%'>\n";
                              echo "           <div align='center'>-</div>\n";
                              echo "        </td>\n";
                              echo "        <td width='46%'><a href='renamefolder.php?mbox_id=$mbox_id'>$text52</a></td>\n";
                              echo "      </tr>\n";
                              echo "    </table>\n";
                              echo "  </td>\n";
                              echo "</tr>\n";
                            } // end if
                          $dbq->nextRow();
                          } // end while
                          $dbq->close();
                       } // end if
                      ?>


                      <tr bgcolor='#DFDFDF'>
                        <td width='26%'>&nbsp;</td>
                        <td width='33%'>&nbsp;</td>
                        <td width='41%' nowrap>&nbsp;</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <table width='550' border='0' cellspacing='0' cellpadding='0' align='center'>
                <tr align='right'>
                  <td>
                    <form method='post' action='createfolder.php' onSubmit='submitonce(this)'>
                      <? echo $text54; ?> :
                      <?
                        if($courier_imap_support) {
                          echo '<select size="1" name="pnspace">
                          <option selected>'.$personal_namespace.'</option>
                          </select>';
                        }
                      ?>
                        
                      <input type='text' name='newfolder'>
                      <input type='submit' name='Submit' value='<? echo $text53; ?>'>
                    </form>
                    </td>
                </tr>
                <tr>
                  <td>
                    <hr size='1'>
                  </td>
                </tr>
              </table>

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
