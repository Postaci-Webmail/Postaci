<?
/*
   File name         : mailbox.inc
   Version           : 1.1.0
   Author            : Umut Gokbayrak
   e-mail            : umut@trlinux.com
   Purpose           : Used for gathering information about and manipulating the mailboxes.
   Last modified     : 05 Sep 2000
*/

  class mailbox {
    var $mbox_id;

    function mailbox() {
    } // end function


    // Finds the user with max ID in tblUsers
    function findMaxMboxID() {
      global $db;

      $dbq = $db->execute("select mbox_id from tblMailBoxes");
      $dbq->lastrow();
      $this->mbox_id = $dbq->fields['mbox_id'];
      $dbq->close();

      return $this->mbox_id;
    } // end function


  } // end class

?>
