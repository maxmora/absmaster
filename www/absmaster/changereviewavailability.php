<?php 
  session_start();
  require_once "../../absmaster_backend/absmaster.php";

  if ($PROJECTSTATE->admin_pin_validate($_SESSION['admin_pin']) == false) {
    die('Access denied. You must be logged in as administrator to do that.');
  }

  // make bool status into equivalent strings for newly POSTed state so they can be compared in next block
  $old_bool_rev_status = $PROJECTSTATE->get_reviews_available_status();
  if ($old_bool_rev_status) {
    $old_rev_status = 'enable';
  } else {
    $old_rev_status = 'disable';
  }

  $new_rev_status = $_POST['review_availability'];

  // do appropriate thing in project state, write project state, and tell the user
  if ($old_rev_status == $new_rev_status) {
    $report_string = 'You have not changed review availability; doing nothing.';
  } else {
    if ($new_rev_status == 'enable') {
      $PROJECTSTATE->enable_reviews_available();
      $report_string = 'Reviews are now available to authors.';
      $PROJECTSTATE->write_project_state_data();
    } elseif ($new_rev_status == 'disable') {
      $PROJECTSTATE->disable_reviews_available();
      $report_string = 'Reviews are no longer available to authors.';
      $PROJECTSTATE->write_project_state_data();
    }
  }

?>

<html>

  <p><?php echo $report_string;?></p>

  <a href="admin.php">Return to admin page</a>

</html>
