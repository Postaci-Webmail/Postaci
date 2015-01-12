<?

/*
   File name         : spellcheck.php
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Last modified     : 28 Feb 2005
*/

$text = $msgbody;

// if text+check is supplied, first open and create $temptext, then spell check
if (trim($text) != "") {
    
    // HERE'S WHERE YOU MIGHT NEED TO CHANGE FILE PATHS, etc.
    //
    // set up some vars and create a tempfile
    // tempnam() is a PHP function that creates a unique tempfile in the specified path,
    //    with the specified prefix
    $temptext= tempnam("/tmp", "spelltext");
    
    // if you spellcheck alot of HTML, add the -H flag to aspell to put it in SGML mode
    $aspellcommand= "cat $temptext | /usr/bin/aspell -a -d $spell_language";
    
    // these three determine how errors are flagged ($indicator is a description of what $opener and $closer do)
    $indicator= "bold";
    $opener= "<b>";
    $closer= "</b>";
    //
    // END OF CONFIGURATION


    if ($fd=fopen($temptext,"w")) {
        $textarray= explode("\n",$text);
        fwrite($fd,"!\n");
        foreach($textarray as $key=>$value) {
            // adding the carat to each line prevents the use of aspell commands within the text...
            fwrite($fd,"^$value\n");
            }
        fclose($fd);
        
        // next create tempdict and temprepl (skipping for now...)
        
        // next run aspell
        $return= shell_exec($aspellcommand);
    
    // now unlink that tempfile
    $ureturn= unlink($temptext);
        
        //next parse $return and $text line by line, eh?
        $returnarray= explode("\n",$return);
        $returnlines= count($returnarray);
        $textlines= count($textarray);
        
        //print "text has $textlines lines and return has $returnlines lines.";
        $lineindex= -1;
        $poscorrect= 0;
        $counter= 0;
        foreach($returnarray as $key=>$value) {
            // if there is a correction here, processes it, else move the $textarray pointer to the next line
            if (substr($value,0,1)=="&") {
                //print "Line $lineindex correction:".$value."<br>";
                $correction= explode(" ",$value);
                $word= $correction[1];
                $absposition= substr($correction[3],0,-1)-1;
                $position= $absposition+$poscorrect;
                $niceposition= $lineindex.",".$absposition;
                $suggstart= strpos($value,":")+2;
                $suggestions= substr($value,$suggstart);
                $suggestionarray= explode(", ",$suggestions);
                //print "I found <b>$word</b> at $position. Will suggest $suggestions.<br>";
                
                // highlight in text
                $beforeword= substr($textarray[$lineindex],0,$position);
                $afterword= substr($textarray[$lineindex],$position+strlen($word));
                $textarray[$lineindex]= $beforeword."$opener$word$closer".$afterword;
                
                // kludge for multiple words in one line ("<b></b>" adds 7 chars to subsequent positions, for instance)
                $poscorrect= $poscorrect+strlen("$opener$closer");
                
                // build the correction form
                $counter= $counter+1;
                $formbody.= "<tr>
                                <td align='right'>$word</td>
                                <td>
                                    <input type='hidden' name='position$counter' value='$niceposition'>
                                    <input type='hidden' name='incorrect$counter' value=\"$word\">
                                    <select name='suggest$counter' onChange=\"document.corrector.correct$counter.value=this.value;\">
                                    <option value=\"$word\" selected>$word (as-is)</option>
                                    ";
                foreach ($suggestionarray as $key=>$value) {
                    $formbody.= "<option value=\"$value\">$value</option>
                                ";
                    }
                $inputlen= strlen($word)+5;
                $formbody.= "<option value=''>custom:</option>
                                    </select>
                                    <input type='text' name='correct$counter' value=\"$word\" size='$inputlen'>
                                </td>
                              </tr>";
                }
                
            elseif (substr($value,0,1)=="#") {
                //print "Line $lineindex unknown:".$value."<br>";
                $correction= explode(" ",$value);
                $word= $correction[1];
                $absposition= $correction[2] - 1;
                $position= $absposition+$poscorrect;
                $niceposition= $lineindex.",".$absposition;
                $suggestions= "no suggestions";
                $suggestionarray= explode(", ",$suggestions);
                //print "I found <b>$word</b> at $position. Will suggest $suggestions.<br>";
                
                // highlight in text
                $beforeword= substr($textarray[$lineindex],0,$position);
                $afterword= substr($textarray[$lineindex],$position+strlen($word));
                $textarray[$lineindex]= $beforeword."$opener$word$closer".$afterword;
                
                // kludge for multiple words in one line ("<b></b>" adds 7 chars to subsequent positions)
                $poscorrect= $poscorrect+strlen("$opener$closer");
                
                // build the correction form
                $counter= $counter+1;
                $formbody.= "<tr>
                                <td align='right'>$word</td>
                                <td>
                                    <input type='hidden' name='position$counter' value='$niceposition'>
                                    <input type='hidden' name='incorrect$counter' value=\"$word\">
                                    <select name='suggest$counter' onChange=\"document.corrector.correct$counter.value=this.value;\">
                                    <option value=\"$word\" selected>$word (as-is)</option>
                                    ";
                $inputlen= strlen($word)+3;
                $formbody.= "<option value=''>custom:</option>
                                    </select>
                                    <input type='text' name='correct$counter' value=\"$word\" size='$inputlen'>
                                </td>
                              </tr>";
                }
                
            else {
                //print "Done with line $lineindex, next line...<br><br>";
                $poscorrect=0;
                $lineindex= $lineindex+1;
                }
            }
        }
    print "<hr size='1'>$text110<blockquote>";
    foreach ($textarray as $key=>$value) {
        print stripslashes($value)."<br>";
        }
    print "</b><!-- comment catcher --></blockquote>";
        
    $htmltext= htmlentities($text);
    if ($formbody=="") { 
	$formbody= "<tr><td>&nbsp;</td><td><br><b>$text115</b></td></tr>";
	$button = $text114;
    } else { $button = $text112; }
    print "<hr size='1'><h3>$text111</h3>
    <form name='corrector' action='sendmail.php' method='post'>
    <input type='hidden' name='spellto' value='".stripslashes($to)."'>
    <input type='hidden' name='spellcc' value='".stripslashes($cc)."'>
    <input type='hidden' name='spellbcc' value='".stripslashes($bcc)."'>
    <input type='hidden' name='spellsubject' value='".stripslashes($subject)."'>
    <input type='hidden' name='post_act' value='8'>
    <input type='hidden' name='attached' value='1'>
    <input type='hidden' name='text' value=\"".trim($htmltext)."\">
    <table>$formbody</table>
      <hr size='1'><input type='submit' name='submit' value='$button'>";
    if ($button == $text112) {
         print " <input type='reset' name='reset' value='$text113'>";
    }
    print "</form>";
    
    }
    
// or if text+correct is specified, make the indicated corrections
elseif (trim($text)!="" && $_POST['submit']=="correct") {
    $textarray= explode("\n",$text);
    
    $index= 1;
    $lastlineindex= 0;
    $poscorrect= 0;
    
    // look through list of positions and make corrections
    while (isset($_POST["position$index"])) {
        $positionarray= explode(",",$_POST["position$index"]);
        $lineindex= $positionarray[0];
        $absposition= $positionarray[1];
        
        if ($lastlineindex==$lineindex) {
            $position= $absposition+$poscorrect;
            }
        else {
            $poscorrect= 0;
            $position= $absposition;
            }
        $lastlineindex= $lineindex;   
        $correct= $_POST["correct$index"];
        $incorrect= $_POST["incorrect$index"];
        //print "Found correction at $lineindex,$absposition. Replacing ";
        
        $before= substr($textarray[$lineindex],0,$position);
        $after= substr($textarray[$lineindex],$position+strlen($incorrect));
        $textarray[$lineindex]= $before.$correct.$after;
        
        $poscorrect= (strlen($correct)-strlen($incorrect))+$poscorrect;
        //print "Position correction is now $poscorrect.<br>";
        $index= $index+1;
        }
    
    foreach ($textarray as $key=>$value) {
        $newtext.=$value;
        }
    print "
    <form action='spellcheck.php' method='post'>
        <h3>Your Corrected Text:</h3><br>
        <textarea name='text' cols='60' rows='10'>$newtext</textarea><br>
        <input type='submit' name='submit' value='re-check'> | <a href='spellcheck.php'>Clear/Restart</a> | <a href='spellcheck.php?showsource=1#source'>Show Source</a>
    </form>";
    }
?>
