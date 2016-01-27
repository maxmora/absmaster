<?php 
  require_once "../../absmaster_backend/absmaster.php";

  $admin_login_error_free = false;
  $admin_login_errors = [];

  // TODO do login validation and error message appending here!
  if ($projectstate->admin_email_validate($_POST['email_address']) && $projectstate->admin_pin_validate($_POST['pin'])) {
    $admin_login_error_free = true;
  } else {
    $admin_login_errors[] = 'Sorry, that is not a valid administrator email/PIN combination.';
  }

  if ($admin_login_error_free == true):

  function generate_user_table($user_inv) {
    $users = $user_inv->get_users();
    $table_string = '';
    $table_string .=
    "<table>\n" .
    "  <tr>\n" .
    "    <td>" . "First name" . "</td>\n" .
    "    <td>" . "Last name" . "</td>\n" .
    "    <td>" . "Email address" . "</td>\n" .
    "    <td>" . "PIN" . "</td>\n" .
    "  </tr>\n";
    foreach ($users as $u) {
      $table_string .=
      "  <tr>\n" .
      "    <td>" . $u->get_first_name() . "</td>\n" .
      "    <td>" . $u->get_last_name() . "</td>\n" .
      "    <td>" . $u->get_email_address() . "</td>\n" .
      "    <td>" . $u->get_pin() . "</td>\n" .
      "  </tr>\n";
      
    }
    $table_string .= "</table>\n";
    return $table_string;
  }
?>

<html>

  <h1>Absmaster Project Administration</h1>


  The following users are currently in the system:

  <p>
  <?php echo generate_user_table($userinventory); ?>
  </p>
</html>

<?php
  else:

  foreach ($admin_login_errors as $e) {
    echo $e . "\n";
  }

  endif;
?>
