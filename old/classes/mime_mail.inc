<?
/*
   File name         : mime_mail.inc
   Version           : 2.0.0 
   Author            : Umut Gokbayrak, Serdar Soydemir, Pete Larsen 
   Purpose           : Generating MIME based e-mail
   Last modified     : 29 Sept 2006 
*/

class mime_mail
{
   var $parts;
   var $to;
   var $from;
   var $cc;
   var $bcc;
   var $headers;
   var $subject;
   var $body;
   var $message_all;
   var $smtp_user;
   var $smtp_pass;

   function mime_mail() {
      $this->parts = array();
      $this->to =  "";
      $this->from =  "";
      $this->cc =  "";
      $this->bcc =  "";
      $this->subject =  "";
      $this->body =  "";
      $this->headers =  "";
      $this->message_all = "";
      $this->smtp_user = "";
      $this->smtp_pass = "";
   }

   function add_attachment($message, $name, $ctype) {
      $this->parts[] = array ("ctype"   => $ctype,
                              "message" => $message,
                              "encode"  => $encode,
                              "name"    => $name);
   }

   function build_message($part) {
      $message = $part[ "message"];
      $message = chunk_split(base64_encode($message));
      $encoding =  "base64";
      return  "Content-Type: ".$part[ "ctype"].
         ($part[ "name"]? "; name = \"".$part[ "name"].
         "\"" :  "").

         "\nContent-Transfer-Encoding: $encoding\n\n$message\n";
   }

   function build_multipart() {
      $boundary =  "b".md5(uniqid(time()));
      $multipart =
         "Content-Type: multipart/mixed; boundary = $boundary\n\nThis is a MIME encoded message.\n\n--$boundary";

         for($i = sizeof($this->parts)-1; $i >= 0; $i--)
      {
         $multipart .=  "\n".$this->build_message($this->parts[$i]).
            "--$boundary";
      }
      return $multipart.=  "--\n";
   }

   function get_mail($complete = true) {
      global $remote_ip;

      $mime =  "";
      $this->message_all = $mime;

      $this->message_all = $this->message_all . "Subject: $this->subject\n";
      $this->message_all = $this->message_all . "To: ".$this->to. "\n";
      if (!empty($this->from))
         $mime .= "From: ".$this->from. "\n";
      if (!empty($this->cc))
         $mime .= "Cc: ".$this->cc. "\n";
      if (!empty($this->bcc))
         $mime .= "Bcc: ".$this->bcc. "\n";

      $mime .=  "X-Mailer:Postaci 1.1.0 (ftp://ftp.onar.com.tr/pub/linux/postaci)\n";
      $mime .=  "X-Comment: This message was sent from " . $remote_ip . "\n";

      if (!empty($this->body))
         $this->add_attachment($this->body,  "",  "text/plain");
      $mime .=  "MIME-Version: 1.0\n".$this->build_multipart();

      $this->message_all = $this->message_all . $mime;
      return $mime;
   }

   function send() {
      global $smtp_host, $smtp_port, $text91;
      global $smtp_auth;

      $mime = $this->get_mail(false);

      $allrecip = $this->to.",".$this->cc.",".$this->bcc;
      str_replace(",,", ",", $allrecip);
      $recipients = explode(",", $allrecip);

      if ($smtp_host != "localhost") {
        $fp = fsockopen($smtp_host,$smtp_port);
        $data = substr(fgets($fp,1024),0,3);
        if ($data != "220") {
          echo "<div align='center'>&nbsp;<p>&nbsp;<p>" . $text91 . "</div>";
          die;
        }
        fputs($fp,"HELO localhost\r\n");

        if ($smtp_auth == "yes") {
	  $user = base64_encode($this->smtp_user);
          $pass = base64_encode($this->smtp_pass);
          fputs($fp, "AUTH LOGIN\r\n");
          fputs($fp, "$user\r\n");
          fputs($fp, "$pass\r\n");
        }

        $data = fgets($fp,1024);
        fputs($fp, "mail from:<" . $this->from . ">\r\n");
        for($i=0; $i<=sizeof($recipients); $i++) {
	   if ($recipients[$i] != null) {
	      $data = fgets($fp,1024);
	      fputs($fp,"rcpt to: <" . $recipients[$i] . ">\r\n");
	   }
	}
        $data = fgets($fp,1024);
        fputs($fp,"data" . "\r\n");
        $data = fgets($fp,1024);
        fputs($fp, $this->message_all . "\r\n");
        fputs($fp,"\n.\r\n");
        $data = fgets($fp,1024);
        fputs($fp,"quit\r\n");
        $data = fgets($fp,1024);
        sleep(1);
        fclose($fp);
      } else {
        mail($this->to, $this->subject,  "", $mime);
      }
   }
};

?>
