<?php 
  session_start();
  require_once "../../absmaster_backend/absmaster.php";

  $admin_login_error_free = false;
  $admin_login_errors = [];

  // TODO do login validation and error message appending here!
  if (empty($_POST)) {
    $admin_login_errors[] = 'Error: You have not <a href="adminauth.html">logged in</a>.';
  } elseif ($projectstate->admin_email_validate($_POST['email_address']) && $projectstate->admin_pin_validate($_POST['pin'])) {
    $admin_login_error_free = true;
  } else {
    $admin_login_errors[] = 'Sorry, that is not a valid administrator email/PIN combination.';
  }

  if ($admin_login_error_free == true):

  // login is successful
  $_SESSION['admin_pin'] = $_POST['pin'];

  function generate_paper_download_link($user) {
    return '"<a href="' . 'downloadpaper.php?id=' . $user->get_uploaded_paper()['id'] . '">' . $user->get_uploaded_paper()['title'] . '</a>"';
  }

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
    "    <td>" . "Uploaded paper" . "</td>\n" .
    "    <td>" . "Number of submitted reviews" . "</td>\n" .
    "  </tr>\n";
    foreach ($users as $u) {
      if ($u->get_uploaded_paper()) {
        $has_uploaded = generate_paper_download_link($u);
      } else {
        $has_uploaded = '(none)';
      }
      $table_string .=
      "  <tr>\n" .
      "    <td>" . $u->get_first_name() . "</td>\n" .
      "    <td>" . $u->get_last_name() . "</td>\n" .
      "    <td>" . $u->get_email_address() . "</td>\n" .
      "    <td>" . $u->get_pin() . "</td>\n" .
      "    <td>" . $has_uploaded . "</td>\n" .
      "    <td>" . count($u->get_submitted_reviews()) . "</td>\n" .
      "  </tr>\n";
      
    }
    $table_string .= "</table>\n";
    return $table_string;
  }
?>

<html>

  <h1>Absmaster Project Administration</h1>


  <h2>Users</h2>
  The following users are currently in the system:

  <p>
  <?php echo generate_user_table($userinventory); ?>
  </p>


  <h2>Reviewer Assignment</h2>

  <form action="assignreviewers.php" method="get">
    <p>Papers to be reviewed per student:</p>
    <p><input type="number" name="num_reviews"></p>
    <input type="submit" value="Assign Reviewers">
  </form>

</html>



<?php // messages printed if login failed
  else:

  foreach ($admin_login_errors as $e) {
    echo $e . "<br>";
  }

  endif;
?>
