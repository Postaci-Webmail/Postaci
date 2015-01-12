<?
/*
   File name         : mailbox.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : This is a generic file that lists all the mail
                       headers in the current mailbox. Clicking on a header will
                       redirect the user to a POP3 or IMAP stream.
   Last modified     : 28 Feb 2005 
*/
session_start();
include ("includes/global.inc");

// If first login redirect to preferences screen
if ($f == 1) {
  Header("Location: preferences.php?f=1");
}

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

  // Get page seperator value and see if the notebook and favorites are enabled.
  $dbq = $db->execute("select rsrv_int1 from tblUsers where user_id='$user_id'");          
  $seperator = $dbq->fields['rsrv_int1'];
  $dbq->close();

  if ($seperator == 0) {
    $seperator = 15;
  }
  session_register('seperator');

  $email=new imap_pop3($default_port,$default_protocol,$default_host,$username,$password,$mbox_id);
  if ($f !=1 && $mbox_id != "INBOX") {$email->mbox_exists($mbox_id);}
  include ("$postaci_directory" . "includes/commonhead.inc");
  include ("$postaci_directory" . "includes/stylesheets.inc");
  include ("$postaci_directory" . "includes/javascripts.inc");
  include ("$postaci_directory" . "includes/functions.inc");

?>
  <meta http-equiv="refresh" content="180; url=mailbox.php?mbox_id=<? echo $mbox_id; ?>">
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
              <img src="images/dot.gif" width="10" height="10"> <a href="sendmail.php?post_act=0&mbox_id=<? echo $mbox_id; ?>"><? echo "$text25"; ?></a>
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
            <hr size="1">
            <form method="post" action="message_move.php" name="mesajlar" onSubmit="submitonce(this)">
              <input type="hidden" name="mbox_id" value="<? echo $mbox_id; ?>">

            <table width="100%" border="0" cellspacing="0" cellpadding="0" height="18">
              <tr bgcolor="#D1D1D1">
                <?
                  $mailadress = getMailAdress($user_id);
                  if ($email->protocol == "pop3") {
                    if (trim(strtoupper($mbox_id)) != "INBOX") {
                      $current_folder = getFolderName($mbox_id);
                    } else {
                      $current_folder = $mbox_id;
                    }
                  } else {
                    $current_folder = $mbox_id;
                  }
                ?>
                <td>&nbsp;<font color="#4E4E4E"><b> <? echo htmlspecialchars($mailadress) . " - " . $current_folder; ?></b></font></td>
              </tr>
            </table>
            &nbsp;<br>
            &nbsp;<br>
              <table width="100%" border="0" cellspacing="2" cellpadding="0">
                <tr align="right">
                  <td colspan="6">
                    <div align="right">

                    <?
                      echo "$text16 ";
                      echo "<select name=\"slcmbox\">\n";
                      for($i=0;$i<count($ids);$i++) {
                        if (trim($folder_names[$i]) != "INBOX" && trim($folder_names[$i]) != $current_folder) {
                          $folder_id = getFolderID($folder_names[$i],$user_id);
                          echo "<option value=\"$folder_id\">$folder_names[$i]</option>\n";
                        }
                      }
                      echo "</select>\n";
                      echo $text17;
                      echo "<input type=\"submit\" name=\"Submit\" value=\"$text18\">\n";
                     ?>

                      </div>
                  </td>
                </tr>
                <tr>
                  <td width="3%" bgcolor="#D1D1D1">
                    <input type="checkbox" name="allbox" value="checkbox" onClick="ToggleAll();">
                  </td>
                  <td width="2%" bgcolor="#D1D1D1"><img src="images/empty.gif" width="17" height="8"></td>
                  <?
                  if ($default_protocol == "pop3") {
                    $dbq = $db->execute("select mbox_id from tblMailBoxes where user_id=$user_id and mbox_type = 1");
                    $POP3toFolder = $dbq->fields['mbox_id'];
                    $dbq->close();
                  }

                  if ($mbox_id == "$to_folder" || $mbox_id == $POP3toFolder) {
                    echo "<td width=\"29%\" bgcolor=\"#D1D1D1\"><font color=\"#4E4E4E\"><b>$text32</b></font></td>";
                  } else {
                    echo "<td width=\"29%\" bgcolor=\"#D1D1D1\"><font color=\"#4E4E4E\"><b>$text12</b></font></td>";
                  }
                  ?>
                  <td width="46%" bgcolor="#D1D1D1"><font color="#4E4E4E"><b><? echo "$text13"; ?></b></font></td>
                  <td width="12%" bgcolor="#D1D1D1"><font color="#4E4E4E"><b><? echo "$text14"; ?></b></font></td>
                  <td width="8%" bgcolor="#D1D1D1"><font color="#4E4E4E"><b><? echo "$text15"; ?></b></font></td>
                </tr>
                <?
                  if ($email->empty_mailbox()) {
                    echo "  <tr>\n";
                    echo "    <td colspan='6' align='center'>\n";
                    echo "      &nbsp;<br><b><font color=\"#304167\">$text24</font></b>&nbsp;<p><hr size=\"1\">\n";
                    echo "    </td>\n";
                    echo "  </tr>\n";
                  } else {
                    $postaci_headers = $email->postaci_get_headers();

                    $message_count_right_now = count($postaci_headers["status"]);

                    for ($i = 0; $i < $message_count_right_now ;$i++) {
                      if ($postaci_headers["status"][$i] == 1) {
                        $message_new_tag_begin = "<b>";
                        $message_new_tag_end = "</b>";
                      } else {
                        $message_new_tag_begin = "";
                        $message_new_tag_end = "";
                      }

                      echo "<tr>\n";
                      $mesaj_numerosu = $postaci_headers["msg_no"][$i];
                      echo "  <td width='3%' bgcolor='#E4E4E4'><font color='#000000'><input type='checkbox' name='chk[]' value='".$mesaj_numerosu."A".$postaci_headers["size"][$i]."'></font></td>\n";


                      if ($postaci_headers["attach"][$i] == 1) {
                        echo "  <td width='3%' bgcolor='#E4E4E4' nowrap align='center'><img src='images/attach.gif'></td>\n";
                      } else {
                        echo "  <td width='3%' bgcolor='#E4E4E4' nowrap align='center'>&nbsp;</td>\n";
                      }

                      $urlenc_mbox_id = rawurlencode($mbox_id);
                      if ($mbox_id == "$to_folder" || $mbox_id == $POP3toFolder) {
                          if ($default_protocol == "pop3" && $mbox_id != "INBOX") {
                            $post_act = 5;
                          } else {
                            $post_act = 3;
                          }
                          echo "<td width=\"29%\" bgcolor=\"#E4E4E4\" nowrap><a href=\"sendmail.php?topmsg=" . $topmsg . "&msg_id=" . $postaci_headers["msg_no"][$i] . "&mbox_id=$urlenc_mbox_id&post_act=" . $post_act . "\">";
                          echo "$message_new_tag_begin" . mychop($postaci_headers["to"][$i],25) . "$message_new_tag_end";
                          echo "</a>&nbsp;&nbsp;</td>";
                      } else {
                          if ($default_protocol == "pop3" && $mbox_id != "INBOX") {
                            $post_act = 7;
                          } else {
                            $post_act = 1;
                          }
                          echo "<td width=\"29%\" bgcolor=\"#E4E4E4\" nowrap>";
                          echo "$message_new_tag_begin" . mychop($postaci_headers["from"][$i],45) . "$message_new_tag_end";
                          echo "</a>&nbsp;&nbsp;</td>";
                      }
                      echo "<td width=\"46%\" bgcolor=\"#E4E4E4\" nowrap><a href=\"readmessage.php?topmsg=" . $topmsg . "&mbox_id=$urlenc_mbox_id&msg_no=" . $postaci_headers["msg_no"][$i] . "\">";
                      if (strip_tags($postaci_headers["subject"][$i]) == "") {
                        $postaci_headers["subject"][$i] = $text40;
                      }
                      echo "$message_new_tag_begin" . mychop(turkcelestir($postaci_headers["subject"][$i]),45) . "$message_new_tag_end";
                      echo "</a>&nbsp;&nbsp;</td>";
                      echo "<td width=\"12%\" bgcolor=\"#E4E4E4\" nowrap>" . $postaci_headers["msg_date"][$i];
                      echo "</td>";
                      echo "<td width=\"8%\" bgcolor=\"#E4E4E4\" nowrap align=\"center\">" . $postaci_headers["size"][$i] . "</td>";
                      echo "<input type=\"hidden\" name=\"size[]\" value=\"".$postaci_headers["size"][$i]."\">";
		      echo "</tr>\n";
                    } // end for
                  } // end if
                ?>
              </table>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td align="right">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>
                            <b><font color="#304167"><? echo "$text19 : $email->messagecount"; ?></font></b>
                          </td>
                          <td align="right">
                            <input type="submit" name="Submit2" value="<? echo $text57; ?>" onClick="formMailBox('1')">
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </form>
              <?
                // Page identification with numbers.

                $how_many = calculateHowManyPages($email->messagecount);

                if ($how_many !=1) {
                  echo "<div align='center'>\n";
                  if ($default_protocol == "imap" || $mbox_id == "INBOX") { // IMAP or POP3 inbox
                    $tmptop = $email->messagecount;
                    for ($j=$how_many ; $j>0 ; $j = $j-1) {
                      $tmpbottom = $tmptop - $seperator +1;
                      if ($tmpbottom <= 0) {
                        $tmpbottom = 1;
                      }
                      if ($tmptop != $tmpbottom) {
                        echo "[ <b><a href='mailbox.php?mbox_id=$mbox_id&topmsg=$tmptop'>$tmptop-$tmpbottom</a></b> ] ";
                      } else {
                        echo "[ <b><a href='mailbox.php?mbox_id=$mbox_id&topmsg=$tmptop'>$tmptop</a></b> ]";
                      }
                      $tmptop = $tmpbottom -1;
                    }
                  } else {                                                 // pop3 db folder
                    if ($tmptop == 0) {
                      $dbq = $db->execute("select message_id from tblMessages where mbox_id=$mbox_id and user_id = $user_id order by message_id desc");
                      $max_msg_id = $dbq->fields['message_id'];
                      $dbq->close();
                      $topmsg = $max_msg_id;
                    }
                    $tmptop = $email->messagecount;
                    for ($j=$how_many ; $j>0 ; $j = $j-1) {
                      if ($topmsg2 != 0) {
                        $topmsg = $topmsg2;
                      }
                      $tmpbottom = $tmptop - $seperator +1;
                      $dbq = $db->execute("select message_id from tblMessages where mbox_id=$mbox_id and user_id = $user_id and message_id <= $topmsg order by message_id desc");
                      for ($n = 1;$n <= $seperator; $n++) {
                        $dbq->nextRow();
                      }
                      $topmsg2 = $dbq->fields['message_id'];
                      $dbq->close();

                      if ($tmpbottom <= 0) {
                        $tmpbottom = 1;
                      }
                      if ($tmptop != $tmpbottom) {
                        echo "[ <b><a href='mailbox.php?mbox_id=$mbox_id&topmsg=$topmsg'>$tmptop-$tmpbottom</a></b> ] ";
                      } else {
                        echo "[ <b><a href='mailbox.php?mbox_id=$mbox_id&topmsg=$topmsg'>$tmptop</a></b> ]";
                      }
                      $tmptop = $tmpbottom -1;

                    }
                  }
                  echo "</div>\n";
                }
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
