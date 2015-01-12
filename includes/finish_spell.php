<?

/*
   File name         : finish_spell.php 
   Version           : 2.0.0
   Last Modified By  : Pete Larsen
   e-mail            : pete@postaciwebmail.org
   Last modified     : 28 Feb 2005
*/

if (trim($text) != "") {
    ltrim($text);
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
   
    //print "Original text:<br>";
    //print nl2br($text);
    //print "<hr>";
   
    foreach ($textarray as $key=>$value) {
        $newtext.=$value;
        }
}
?>
