<?php 
  session_start();
  require_once "include_path.php";
  require_once "absmaster.php";

  if ($PROJECTSTATE->admin_pin_validate($_SESSION['admin_pin']) == false) {
    die('Access denied. You must be logged in as administrator to do that.');
  }

  if (empty($_POST)) {
    die('Error. You cannot access this page directly.');
  }

  $review_pairings_json = html_entity_decode($_POST['reviewer_assignments']);
  write_reviewer_assignments($review_pairings_json);

  // TODO email users to tell them their reviews are ready
?>

<html>

  <p>
    Papers have been distributed for review; reviewers can now log in and download their papers.
  </p>


</html>
