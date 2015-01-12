<script language="JavaScript">
<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v3.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function ToggleAll() {
//checkbox adý allbox olacak ve onClick="CheckAll();" ile çaðrýlacak
  for (var i=0;i<document.mesajlar.elements.length;i++) {
    var e = document.mesajlar.elements[i];
    if (e.name != 'allbox')
      e.checked = document.mesajlar.allbox.checked;
  }
}

function submitonce(theform){
  if (document.all||document.getElementById){
    for (i=0;i<theform.length;i++){
      var tempobj=theform.elements[i]
        if(tempobj.type.toLowerCase()=="submit")
        tempobj.disabled=true
    }
  }
}

function formURL(){
  if (formURL.arguments[0] == 1)
  document.myForm.action="attachments.php";
  if (formURL.arguments[0] == 2)
  document.myForm.action="postmail.php";
}

function formMailBox(){
  if (formMailBox.arguments[0] == 1)
  document.mesajlar.action="deletemessage.php";
  if (formMailBox.arguments[0] == 2)
  document.mesajlar.action="addspammer.php";
}

function printit(){
  var NS = (navigator.appName == "Netscape");
  if (NS) {
    window.print() ;
  } else {
    var WebBrowser = '<object id="WebBrowser1" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></OBJECT>';
    document.body.insertAdjacentHTML('beforeEnd', WebBrowser);
    WebBrowser1.ExecWB(6, 2);//Use a 1 vs. a 2 for a prompting dialog box
    WebBrowser1.outerHTML = "";
  }
}
//-->
</script>
