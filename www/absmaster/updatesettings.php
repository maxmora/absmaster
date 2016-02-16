<?php 
  session_start();
  require_once "include_path.php";
  require_once "absmaster.php";

  if ($PROJECTSTATE->admin_pin_validate($_SESSION['admin_pin']) == false) {
    die('Access denied. You must be logged in as administrator to do that.');
  }

  $report_string = '';

  // only make set/write settings if they're actually changes
  if ($_POST['signup_enablement'] == 'enable' && !$PROJECTSTATE->get_signup_enabled_status()) {
   $PROJECTSTATE->enable_signup();
   $PROJECTSTATE->write_project_state_data();
   $report_string .= 'Signup is now enabled<br>';
  } elseif ($_POST['signup_enablement'] == 'disable' && $PROJECTSTATE->get_signup_enabled_status()) {
   $PROJECTSTATE->disable_signup();
   $PROJECTSTATE->write_project_state_data();
   $report_string .= 'Signup is now disabled<br>';
  }

  if ($_POST['max_users'] != $PROJECTSTATE->get_max_users()) {
    $PROJECTSTATE->set_max_users($_POST['max_users']);
    $PROJECTSTATE->write_project_state_data();
    $report_string .= 'Maximum number of users has been changed to ' . $_POST['max_users'] . '.<br>';
  }

  if ($report_string == '') {
    $report_string = 'No settings have been changed.';
  }

?>

<html>
  <p>
    <?php echo $report_string;?>
  </p>

  <a href="admin.php">Return to admin page</a>

</html>
