<?php 
  require_once "../../absmaster_backend/absmaster.php";

  //TODO validate input on all fields before doing ANYTHING
  //
  // if it's not good, then simply state all that and don't create a new user!
  // conditions include: improper email address, or existing email address that differs only by case sensitivity,
  // and THAT ALL FIELDS ARE FILLED! (no empty fields!)
?>

<html>

<?php
  $new_user_error_free = true;
  $user_creation_errors = [];

  //TODO also check for input validity in here! (setting value for $new_user_error_free and appending to $user_creation_errors

  // if someone navigates to this page manually
  if (empty($_POST)) {
    $new_user_error_free = false;
    $user_creation_errors[] = 'You cannot access this page.';
  } elseif ($userinventory->user_exists_by_email_address($_POST['email_address']) == false) {
    $userinventory->add_user($_POST['first_name'],$_POST['last_name'],$_POST['email_address']);
    $thenewuser = $userinventory->get_user_by_email_address($_POST['email_address']); // grabbing the new user object for use below
    $userinventory->write_user_data();
  } else {
    $new_user_error_free = false;
    $user_creation_errors[] = 'A user with that email address "' . $_POST['email_address'] . '" already exists.';
  }

  if ($new_user_error_free == true):
?> 

  You have been added as a new user!

  <p>
  <table>
    <tr>
      <td>First name:</td>
      <td><?php echo $thenewuser->get_first_name();?></td>
    </tr>
    <tr>
      <td>Last name:</td>
      <td><?php echo $thenewuser->get_last_name();?></td>
    </tr>
    <tr>
      <td>Email address:</td>
      <td><?php echo $thenewuser->get_email_address();?></td>
    </tr>
    <tr>
      <td>Login PIN:</td>
      <td><?php echo $thenewuser->get_pin();?></td>
    </tr>
  </table>
  </p>

  <p>You will login using your email address and PIN. Be sure to keep these.</p>
  <p>You can now log in <a href="login.html">here</a>.</p>

<?php else: 

  foreach ($user_creation_errors as $e) {
    echo $e . "\n";
  }

?>

<?php endif; ?>

</html>
