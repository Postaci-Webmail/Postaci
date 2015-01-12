<?
/*
   File name         : userclass.inc
   Version           : 1.1.0
   Author            : Umut Gokbayrak
   e-mail            : umut@trlinux.com
   Purpose           : Used for gathering information about the users.
   Last modified     : 05 Sep 2000
*/

  class userclass {
    var $user_id;

    function userclass() {
    } // end function


    // Finds the user with max ID in tblUsers
    function findMaxUserID() {
      global $db;

      $dbq = $db->execute("select user_id from tblUsers order by user_id desc");
      //$dbq->lastrow();
      $this->user_id = $dbq->fields['user_id'];
      $dbq->close();

      return $this->user_id;
    } // end function

    // Finds the domain_id for any username in tblUserDomains
    function findUserDomainID($username) {
      global $db;

      $domain_id = 1;
      $dbq = $db->execute("select domain_id from tblUserDomains where username='$username'");
      $domain_id = $dbq->fields['domain_id'];
      if (!$domain_id) {
        $domain_id = 1;
      }
      return $domain_id;
    } // end function


  } // end class

?>
