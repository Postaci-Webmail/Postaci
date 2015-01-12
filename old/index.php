<?
/*
   File name         : index.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Purpose           : The welcome screeen for Postaci Webmail
   Last modified     : 28 Feb 2005 
*/

session_start();
session_unregister('ID');
session_unregister('txtusername');
session_unregister('password');
session_unregister('seperator');

include ("includes/global.inc");
include ("$postaci_directory" . "includes/commonhead.inc");
include ("$postaci_directory" . "includes/stylesheets.inc");
include ("$postaci_directory" . "includes/javascripts.inc");

//session_start();
session_register('lang_choice');
?>

</head>


<body bgcolor='#F3F3F3' leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" background="images/background.gif" text="#000000" link="#1F4687" vlink="#1F4687" alink="#1F4687" onLoad="MM_preloadImages('images/polski_version2.gif','images/espanol_version2.gif','images/norsk_version2.gif','images/francais_version2.gif','images/deutsch_version2.gif','images/italiano_version2.gif','images/turkce_version2.gif','images/english_version2.gif','images/portugues_version2.gif','images/dutch_version2.gif')">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="1%" bgcolor="<? echo $bgc; ?>"><img src="images/empty.gif" width="15" height="1"></td>
    <td width="1%" bgcolor="<? echo $bgc; ?>" background="images/line_vertical_blue_back.gif"><img src="images/empty.gif" width="152" height="1"></td>
    <td width="98%" bgcolor="<? echo $bgc; ?>" background="images/line_vertical_blue_back.gif" align="right"><a href="http://www.postaciwebmail.org" target="_blank"><img src="images/postaci_webmail.jpg" width="349" height="32" alt="ver :  <? echo $postaci_version; ?>"></a></td>
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
    <td width="1%" valign="top" align="left"><img src="images/company_logo.jpg" width="152" height="152"><br>
      <table width="111" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr valign="top" align="center">
          <td>
           <?
            if ($turkce_support) {
              echo "<a href=\"index.php?l=turkce\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image1','','images/turkce_version2.gif',1)\"><img src=\"images/turkce_version1.gif\" width=\"111\" height=\"15\" border=\"0\" name=\"Image1\"></a><br>\n";
            }
            if ($english_support) {
              echo "<a href=\"index.php?l=english\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image2','','images/english_version2.gif',1)\"><img src=\"images/english_version1.gif\" width=\"111\" height=\"15\" border=\"0\" name=\"Image2\"></a><br>\n";
            }
            if ($dutch_support) {
              echo "<a href=\"index.php?l=dutch\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image3','','images/dutch_version2.gif',1)\"><img src=\"images/dutch_version1.gif\" width=\"111\" height=\"15\" border=\"0\" name=\"Image3\"></a><br>\n";
            }
            if ($portugues_support) {
              echo "<a href=\"index.php?l=portugues\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image4','','images/portugues_version2.gif',1)\"><img src=\"images/portugues_version1.gif\" width=\"111\" height=\"15\" border=\"0\" name=\"Image4\"></a><br>\n";
            }
            if ($italiano_support) {
              echo "<a href=\"index.php?l=italiano\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image5','','images/italiano_version2.gif',1)\"><img src=\"images/italiano_version1.gif\" width=\"111\" height=\"15\" border=\"0\" name=\"Image5\"></a><br>\n";
            }
            if ($deutsch_support) {
              echo "<a href=\"index.php?l=deutsch\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image6','','images/deutsch_version2.gif',1)\"><img src=\"images/deutsch_version1.gif\" width=\"111\" height=\"15\" border=\"0\" name=\"Image6\"></a><br>\n";
            }
            if ($francais_support) {
              echo "<a href=\"index.php?l=francais\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image7','','images/francais_version2.gif',1)\"><img src=\"images/francais_version1.gif\" width=\"111\" height=\"15\" border=\"0\" name=\"Image7\"></a><br>\n";
            }
            if ($norsk_support) {
              echo "<a href=\"index.php?l=norsk\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image8','','images/norsk_version2.gif',1)\"><img src=\"images/norsk_version1.gif\" width=\"111\" height=\"15\" border=\"0\" name=\"Image8\"></a><br>\n";
            }
            if ($polski_support) {
              echo "<a href=\"index.php?l=polski\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image9','','images/polski_version2.gif',1)\"><img src=\"images/polski_version1.gif\" width=\"111\" height=\"15\" border=\"0\" name=\"Image9\"></a><br>\n";
            }
            if ($espanol_support) {
              echo "<a href=\"index.php?l=espanol\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image10','','images/espanol_version2.gif',1)\"><img src=\"images/espanol_version1.gif\" width=\"111\" height=\"15\" border=\"0\" name=\"Image10\"></a><br>\n";
            }
           ?>
          </td>
        </tr>
        <tr>
          <td nowrap>&nbsp;<br><img src="images/dot.gif" width="10" height="10"> <a href="http://www.postaciwebmail.org/" target="newpage"><? echo "$text76"; ?></a></td>
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
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="300" width="91%" align="center">
            <p>&nbsp;</p>
            <form method="post" action="validate_login.php">
              <br>
              <br>
              <img src="<? echo "images/welcome_" . $lang_choice . ".gif"; ?>" width="393" height="111" alt="<? echo $text82; ?>"><br>
              <?
                 switch ($error_id) {
                   case 1:
                     echo "<b class='styleerror'>$text5</b><br>";
                     break;
                   case 2:
                     echo "<b class='styleerror'>$text9</b><br>";
                     break;
                   case 3:
                     echo "<b class='styleerror'>$text89</b><br>";
                     break;
                   default:
                     echo "<b class='styleerror'>&nbsp;</b><br>";
                 }
              ?>

              <table width="290" border="0" cellspacing="0" cellpadding="3" bgcolor='#F3F3F3'>
                <tr>
                  <td colspan="3">
                    <hr size="1" noshade width="280" align="left">
                  </td>
                </tr>
                <tr>
                  <td nowrap><b><? echo "$text2"; ?></b></td>
                  <td><b>:</b></td>
                  <td>
                    <input type="text" name="txtusername" size="15" maxlength="70" class="styletextbox">
                  </td>
                </tr>
                <tr>
                  <td nowrap><b><? echo "$text3"; ?></b></td>
                  <td><b>:</b></td>
                  <td>
                    <input type="password" name="txtpassword" size="15" maxlength="70" class="styletextbox">
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                    <hr size="1" noshade width="280" align="left">
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>
                    <input type="submit" name="Submit" value="<? echo "$text4"; ?>">
                  </td>
                </tr>
              </table>
            </form>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
          </td>
          <td width="2%" valign="top"><img src="images/empty.gif" width="15" height="10">
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
