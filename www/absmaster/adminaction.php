<?php 
  session_start();
  require_once "include_path.php";
  require_once "absmaster.php";

  if ($PROJECTSTATE->admin_pin_validate($_SESSION['admin_pin']) == false) {
    die('Access denied. You must be logged in as administrator to do that.');
  }

  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
      case 'clearreviews':
        $USERINVENTORY->clear_all_reviews();
        echo '<p>All reviews and review data have been cleared</p>';
        break;
      case 'clearpapers':
        $USERINVENTORY->clear_all_papers();
        echo '<p>All papers, reviews, and review data have been cleared</p>';
        break;
      case 'clearusers':
        $USERINVENTORY->clear_all_users();
        echo '<p>All users, papers, reviews, and review data have been cleared</p>';
        break;
    }
  }
?>

<html>


  <a href="admin.php">Return to admin page</a>

</html>
