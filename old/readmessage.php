<?
/*
   File name         : readmessage.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : This script is for reading e-mail. Output format is the same but, because
                       of protocol independency on of three (readimap.inc, readpop3.inc, readpop3db.inc)
                       include files.
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

  $mbox_id = rawurldecode($mbox_id);
  include ("$postaci_directory" . "classes/imap_pop3.inc");

  // Check to see if the mbox_id is a valid one.
  $dbq = $db->execute("select user_id,username from tblLoggedUsers where hash='$ID'");
  $username = $dbq->fields['username'];
  $user_id = $dbq->fields['user_id'];
  $dbq->close();

  if ($default_protocol == "pop3" && $mbox_id != "INBOX") {                             // Security Check
    $dbq = $db->execute("select user_id from tblMessages where message_id=$msg_no");
    $auth_user = $dbq->fields['user_id'];
    $dbq->close();
    if ($auth_user != $user_id) {
      Header("Location: index.php?error_id=1");
    }
  }

  $email=new imap_pop3($default_port,$default_protocol,$default_host,$username,$password,$mbox_id);

  $email->mbox_exists($mbox_id);
  include ("$postaci_directory" . "includes/commonhead.inc");
  include ("$postaci_directory" . "includes/stylesheets.inc");
  include ("$postaci_directory" . "includes/javascripts.inc");
  include ("$postaci_directory" . "includes/functions.inc");

/*
  if ($default_protocol == "imap") {       // If mailbox is an IMAP folder.

    $email->empty_mailbox();

    $postaci_headers = $email->postaci_get_headers();
    for ($i = 0; $i<=$email->messagecount -1;$i++) {
      if ($postaci_headers["msg_no"][$i] == $msg_no) {
        $postaci_from    = $postaci_headers["from"][$i];
        $postaci_to      = $postaci_headers["to"][$i];
        $postaci_size    = $postaci_headers["size"][$i];
        $postaci_cc      = $postaci_headers["cc"][$i];
        $postaci_date    = $postaci_headers["msg_date"][$i];
        $postaci_subject = $postaci_headers["subject"][$i];
      }
    }
*/

$mbox_id = clean_data($mbox_id);
$msg_id = clean_data($msg_id);
$topmsg = clean_data($topmsg);

if ($default_protocol == "imap" || $mbox_id == "INBOX") {
  $email->empty_mailbox();
  $mailbox_headers = array();
  $mailbox_headers=imap_headers($email->mbox);
  $size=$mailbox_headers[$msg_no-1];
  $size=ereg_replace(".*\(","",$size);
  $size=ereg_replace(" .*$"," ",$size);
  $htmlsize=ceil($size/1024). "K";
  $message_header = imap_header($email->mbox,$msg_no);
  $postaci_from = htmlspecialchars($email->decode_mime_string($message_header->fromaddress));
  $postaci_cc = htmlspecialchars($email->decode_mime_string($message_header->ccaddress));
  $postaci_to = htmlspecialchars($email->decode_mime_string($message_header->toaddress));
  $postaci_subject = $email->decode_mime_string($message_header->subject);
  $postaci_date = ereg_replace(" ","&nbsp;", date("Y M d - H:i",$message_header->udate));
} else {
  $dbq = $db->execute("select * from tblMessages where mbox_id=$mbox_id and user_id = $user_id and message_id = $msg_no");
  $postaci_from    = $dbq->fields['header_from'];
  $postaci_to      = $dbq->fields['header_to'];
  $postaci_cc      = $dbq->fields['header_cc'];
  $postaci_subject = $dbq->fields['header_subject'];
  $postaci_size    = $dbq->fields['header_size'];
  $postaci_date    = $dbq->fields['header_date'];
}
/*
  } else {                                 // If mailbox is a POP3 folder
    if ($mbox_id == "INBOX") {
      $email->empty_mailbox();
      $postaci_headers = $email->postaci_get_headers();
      for ($i = 0; $i<=$email->messagecount -1;$i++) {
        if ($postaci_headers["msg_no"][$i] == $msg_no) {
          $postaci_from    = $postaci_headers["from"][$i];
          $postaci_to      = $postaci_headers["to"][$i];
          $postaci_size    = $postaci_headers["size"][$i];
          $postaci_cc      = $postaci_headers["cc"][$i];
          $postaci_date    = $postaci_headers["msg_date"][$i];
          $postaci_subject = $postaci_headers["subject"][$i];
        }
      }
    } else {                               // If mailbox is a POP3 but database simulation folder.
      $dbq = $db->execute("select * from tblMessages where mbox_id=$mbox_id and user_id = $user_id and message_id = $msg_no");
      $postaci_from    = htmlspecialchars($dbq->fields['header_from']);
      $postaci_to      = htmlspecialchars($dbq->fields['header_to']);
      $postaci_size    = "";
      $postaci_date    = htmlspecialchars($dbq->fields['header_date']);
      $postaci_cc      = htmlspecialchars($dbq->fields['header_cc']);
      $postaci_subject = $dbq->fields['header_subject'];
      $dbq->close();
    }
  }
*/
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
      <td width="1%" valign="top" align="left"><img src="images/company_logo3.jpg" width="152" height="152"><br>
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
              <form method='post' action='message_move.php' name='mesajlar' onSubmit='submitonce(this)'>
                <input type='hidden' name='mbox_id' value='<? echo rawurlencode($mbox_id); ?>'>
                <input type='hidden' name='chk[0]' value='<? echo $msg_no; ?>'>

                <table width='100%' border='0' cellspacing='0' cellpadding='0' height="18">
                  <tr bgcolor='#D1D1D1'>
                    <td align='left'><font color='#4E4E4E'>&nbsp;&nbsp;<b><? echo $postaci_subject; ?></b></font></td>
                  </tr>
                </table>

                &nbsp;<br>
                &nbsp;<br>
                <table width="100%" border="0" cellspacing="2" cellpadding="0">
                  <tr align="right">
                    <td>
                      <table cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                          <td width="10%" align="left" valign="bottom" nowrap>
                            <?
                              $mbox_id = rawurlencode($mbox_id);
                              if ($default_protocol == "imap" || $mbox_id=="INBOX") {
                                $max_msg_id = $email->messagecount;
                                $min_msg_id = 1;
                                $prev_msg = $msg_no - 1;
                                $next_msg = $msg_no + 1;
                                if ($next_msg > $max_msg_id) {
                                  $next_msg = $max_msg_id;
                                }
                                if ($prev_msg < $min_msg_id) {
                                  $prev_msg = $min_msg_id;
                                }
                              } else {
                                $dbq = $db->execute("select message_id from tblMessages where user_id = $user_id and mbox_id = $mbox_id order by message_id desc");
                                $max_msg_id = $dbq->fields['message_id'];
                                $dbq->close();
                                $dbq = $db->execute("select message_id from tblMessages where user_id = $user_id and mbox_id = $mbox_id order by message_id asc");
                                $min_msg_id = $dbq->fields['message_id'];
                                $dbq->close();

                                $dbq = $db->execute("select message_id from tblMessages where message_id > $msg_no and user_id = $user_id and mbox_id = $mbox_id order by message_id");
                                $next_msg = $dbq->fields['message_id'];
                                $dbq->close();
                                $dbq = $db->execute("select message_id from tblMessages where message_id < $msg_no and user_id = $user_id and mbox_id = $mbox_id order by message_id desc");
                                $prev_msg = $dbq->fields['message_id'];
                                $dbq->close();
                              }
                              $msg_num = $email->messagecount;

                              if ($msg_no == $max_msg_id) {             // the last message???
                                if ($msg_num !=1) {                     // there is only one message
                                  echo "<a href=\"readmessage.php?topmsg=$topmsg&msg_no=$prev_msg&mbox_id=$mbox_id\" class=\"style8\"><img src=\"images/previous.gif\" border=\"0\">$text30</a>\n";
                                }
                              } else {
                                if ($msg_no == $min_msg_id) {           // we are at first message
                                  echo "<a href=\"readmessage.php?topmsg=$topmsg&msg_no=$next_msg&mbox_id=$mbox_id\" class=\"style8\">$text31<img src=\"images/next.gif\" border=\"0\"></a>\n";
                                } else {
                                  echo "<a href=\"readmessage.php?topmsg=$topmsg&msg_no=$prev_msg&mbox_id=$mbox_id\" class=\"style8\"><img src=\"images/previous.gif\" border=\"0\">$text30</a> | <a href=\"readmessage.php?msg_no=$next_msg&mbox_id=$mbox_id\" class=\"style8\">$text31<img src=\"images/next.gif\" border=\"0\"></a>\n";
                                }
                              }
                            ?>
                          </td>
                          <td width=1%>
                            <img src="images/empty.gif" width="40" height="1">
                          </td>
                          <td width="89%" align="right" nowrap>
                            <?
                              echo "$text38 ";
                              echo "<select name=\"slcmbox\">\n";
                              for($i=0;$i<count($ids);$i++) {
                                $full_folder_name = rawurldecode($mbox_id);
                                if ($mbox_id != "INBOX" && $default_protocol == "pop3") {
                                  $dbq = $db->execute("select mboxname, mbox_type from tblMailBoxes where mbox_id = $mbox_id");
                                  $full_folder_name = $dbq->fields['mboxname'];
				  $carpeta = $dbq->fields['mbox_type'];
                                  $dbq->close();
                                }

                                if (trim($folder_names[$i]) != "INBOX" && $folder_names[$i] != $current_folder && $full_folder_name != trim($folder_names[$i])) {
                                  $folder_id = getFolderID($folder_names[$i],$user_id);
                                  echo "<option value=\"$folder_id\">$folder_names[$i]</option>\n";
                                }
                              }
                              echo "</select>\n";
                              echo $text17;
                              echo "<input type=\"submit\" name=\"Submit\" value=\"$text18\">\n";
                            ?>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                     <td width='18%' nowrap bgcolor='#D1D1D1'>
                       <table width='100%' border='0' cellspacing='0' cellpadding='1' bgcolor='#B6B6B6'>
                         <tr>
                           <td>
                             <table width='100%' border='0' cellspacing='0' cellpadding='1'>
                               <tr bgcolor='#DFDFDF'>
                                 <td width='13%' align='left' nowrap><b><font color='#4E4E4E'><? echo $text13; ?></font></b></td>
                                 <td width='1%' align='center' nowrap><b><font color='#4E4E4E'>:</font></b></td>
                                 <td width='86%' nowrap><? echo turkcelestir($postaci_subject); ?></td>
                               </tr>
                               <tr bgcolor='#DFDFDF'>
                                 <td width='13%' align='left' nowrap><b><font color='#4E4E4E'><? echo $text14; ?></font></b></td>
                                 <td width='1%' align='center' nowrap><b><font color='#4E4E4E'>:</font></b></td>
                                 <td width='86%' nowrap><? echo $postaci_date; ?></td>
                               </tr>
                               <tr bgcolor='#DFDFDF'>
                                 <td width='13%' align='left'><b><font color='#4E4E4E'><? echo $text12; ?></font></b></td>
                                 <td width='1%' align='center'><b><font color='#4E4E4E'>:</font></b></td>
                                 <td width='86%'>
                                   <? echo turkcelestir($postaci_from); ?>
                                   &nbsp;&nbsp;
                                   <a href="createcontact.php?mbox_id=<? echo $mbox_id; ?>&msg_no=<? echo $msg_no; ?>&rm=1&txtemail=<? echo urlencode($postaci_from); ?>"><img src="images/bt_addressbook2.gif" border="0" alt="<? echo $text86; ?>"></a>
                                   &nbsp;&nbsp;
                                   <?
                                     if ($ek == 1) {
                                       echo "&nbsp;&nbsp;&nbsp;<b><font color='#1F4687'>$text96</font></b>\n";
                                     }
                                   ?>
                                 </td>
                               </tr>
                               <tr bgcolor='#DFDFDF'>
                                 <td width='13%' align='left'><b><font color='#4E4E4E'><? echo $text32; ?></font></b></td>
                                 <td width='1%' align='center'><b><font color='#4E4E4E'>:</font></b></td>
                                 <td width='86%'><? echo turkcelestir($postaci_to); ?></td>
                               </tr>
                               <?
                                 if (trim($postaci_cc) != "") {
                                   echo "<tr bgcolor='#DFDFDF'>\n";
                                   echo "  <td width='13%' align='left'><b><font color='#4E4E4E'>CC</font></b></td>\n";
                                   echo "  <td width='1%' align='center'><b><font color='#4E4E4E'>:</font></b></td>\n";
                                   echo "  <td width='86%'>" . turkcelestir($postaci_cc) . "</td>\n";
                                   echo "</tr>\n";
                                 }

                                 if ($default_protocol == "imap" || $mbox_id == "INBOX") {

                                   $start=0;
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

                                   // Message is HTML or not. ýf it is we crop the unnecessary tags.

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
                                     if ($msg_enc == 4) {
                                       $qp = 1;
                                     } else {
                                       $qp = 0;
                                     }
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


                                   $structure=imap_fetchstructure($email->mbox,$msg_no);
                                   $c=count($structure->parts);
                                   if ($c<=1 ) {
                                     $no_attachments = 1;
                                   }

                                   if ($no_attachments == 0) {
                                     echo "<tr bgcolor='#DFDFDF'>\n";
                                     echo "  <td width='13%' align='left' valign='top'><b><font color='#4E4E4E'>" . $text33 . "</font></b></td>\n";
                                     echo "  <td width='1%' align='center' valign='top'><b><font color='#4E4E4E'>:</font></b></td>\n";
                                     echo "  <td width='86%'>\n";

                                     for ($i=$start; $i<$c; $i++) {
                                       if ($i != $start){
                                         echo "<br>\n";
                                       }

                                       $part0=$structure->parts[$i];
                                       $part=$i+1;
                                       $parameters=$part0->parameters;
                                       $attach_type=$part0->subtype;
                                       $mytype=$part0->type;
                                       $encoding=$part0->encoding;
                                       $text_encoding=$mime_encoding[$encoding];

                                       if (empty($text_encoding)) {
                                         $text_encoding="unknown";
                                       }
                                       if (eregi("RFC822",$attach_type)) {
                                         $att="RFC822 Message";
                                         $val="message.txt";
                                       } else {
                                         $att=$parameters[0]->attribute;
                                         $val=$parameters[0]->value;
                                       }
                                       $val=rawurlencode(eregi_replace(" ","_",$val));
                                       $size=sprintf("%0.2f",$part0->bytes / 1024);
                                       echo " <img src='images/attach.gif'> <a href='download_attachment.php?msg_no=$msg_no&part=$part&encoding=$encoding&real_name=$val&mbox_id=$mbox_id'>$val ($size K)</a>";
                                     } // end for
                                     echo "   </td>\n";
                                     echo "</tr>\n";
                                   } // end if
                                 } else {                                    // It is not POP3 nor IMAP. It is a database simulation....
                                   $dbq = $db->execute("select attach_id, file_name from tblAttachments where message_id = $msg_no and user_id = $user_id");
                                   $no_of_attachments = $dbq->getNumOfRows();
                                   if ($no_of_attachments != 0) {
                                     echo "<tr bgcolor='#DFDFDF'>\n";
                                     echo "  <td width='13%' align='left' valign='top'><b><font color='#4E4E4E'>" . $text33 . "</font></b></td>\n";
                                     echo "  <td width='1%' align='center' valign='top'><b><font color='#4E4E4E'>:</font></b></td>\n";
                                     echo "  <td width='86%'>\n";
                                     while (!$dbq->EOF) {
                                       $attach_id = $dbq->fields['attach_id'];
                                       $file_name = $dbq->fields['file_name'];
                                       $file_name = rawurlencode($file_name);
                                       echo " <img src='images/attach.gif'> <a href='download_attachment.php?msg_no=$msg_no&attach_id=$attach_id&real_name=$file_name&mbox_id=$mbox_id'>$file_name</a><br>";
                                       $dbq->nextRow();
                                     }
                                     $dbq->close();
                                     echo "   </td>\n";
                                     echo "</tr>\n";
                                   }

                                   $dbq = $db->execute("select * from tblMessages where mbox_id=$mbox_id and user_id = $user_id and message_id = $msg_no");
                                   $htmlbody = $dbq->fields['msg_body'];
                                   $dbq->close();
                                 }
                               ?>
                             </table>
                           </td>
                         </tr>
                       </table>
                     </td>
                   </tr>
                   <tr align='right'>
                     <td nowrap bgcolor='#E3E7E8' align="right">
                        <? $mbox_id = rawurlencode($mbox_id); ?>
                        <?
                          if ($mbox_id != "INBOX" && $default_protocol != "imap") {
                            $reply_act = 5;
                            $forward_act = 6;
                          }
                          if ($mbox_id == "INBOX" || $default_protocol == "imap") {
                            $reply_act = 1;
                            $forward_act = 2;
                          }
                          if ($mbox_id != "INBOX" && $default_protocol != "imap" && $carpeta != "1") {
                            $reply_act = 7;
                            $forward_act = 6;
                          }
                        ?>
                        <img src='images/reply.gif' width='16' height='17' border='0'> 
			<a href='sendmail.php?topmsg=<? echo $topmsg; ?>&post_act=<? echo $reply_act; ?>&msg_id=<? echo $msg_no; ?>&mbox_id=<? echo $mbox_id; ?>'><? echo $text34 ?></a>
			 | <img src='images/forward.gif' width='21' height='16' border='0'> 
			<a href='sendmail.php?topmsg=<? echo $topmsg; ?>&post_act=<? echo $forward_act; ?>&msg_id=<? echo $msg_no; ?>&mbox_id=<? echo $mbox_id; ?>'><? echo $text35 ?></a>
			 | <img src="images/delete.gif">&nbsp;<a href='deletemessage.php?chk[0]=<? echo $msg_no; ?>&mbox_id=<? echo $mbox_id ?>'><? echo $text85 ?></a></td>
                     </td>
                   </tr>
                   <tr>
                     <td>
                       <? echo "&nbsp;<br>" . turkcelestir(nl2br($htmlbody)) . "&nbsp;<p>"; ?>
                     </td>
                   </tr>
                   <tr align='right'>
                     <?
                       $real_name = rawurlencode(eregi_replace(' ','_',eregi_replace(':','_',$postaci_subject))) . ".txt";
                       if ($default_protocol == "imap" || $mbox_id == "INBOX") {
                         $structure=imap_fetchstructure($email->mbox,$msg_no);
                         $part0=$structure->parts[1];
                         $encoding=$part0->encoding;
                         echo "<td nowrap bgcolor='#DFDFDF'><img src='images/save.gif'><a href='download_attachment.php?msg_no=$msg_no&part=1&encoding=$encoding&real_name=$real_name&mbox_id=$mbox_id'>$text36</a> | <img src='images/print.gif'> <a href='javascript:printit()'>$text37</a></td>\n";
                       } else {
                         echo "<td nowrap bgcolor='#DFDFDF'><img src='images/save.gif'><a href='download_attachment.php?msg_no=$msg_no&real_name=$real_name&mbox_id=$mbox_id&attach_type=1'>$text36</a> | <img src='images/print.gif'> <a href='javascript:printit()'>$text37</a></td>\n";
                       }
                     ?>
                   </tr>
                 </table>
                 <div align='right'></div>
               </form>
             </td>
             <td valign='top' width='21'><img src='images/bos.gif' width='21' height='1'></td>
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
      </table>
  </body>
  </html>

<?
}
?>
