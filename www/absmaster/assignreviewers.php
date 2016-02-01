<?php 
  session_start();
  require_once "../../absmaster_backend/absmaster.php";

  if ($projectstate->admin_pin_validate($_SESSION['admin_pin']) == false) {
    die('Access denied. You must be logged in as administrator to do that.');
  }

  require_once "../../absmaster_backend/reviews.php";

  // TODO get these from user input
  // TODO also, this whole thing should only be operating on people who _have_ submitted reviews; check for this when having admin assign reviewers
  $reviews_per_user = 2;
  $excluded_reviewers = ''; // TODO figure out how to implement this
?>

<html>

  <?php assign_reviewers($userinventory,$reviews_per_user,$excluded_reviewers); ?>

</html>
