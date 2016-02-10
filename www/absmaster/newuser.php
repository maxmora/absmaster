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
  // if someone navigates to this page manually, terminate immediately.
  if (empty($_POST)) {
    echo "You cannot access this page.\n</html>";
    exit();
    //$new_user_error_free = false;
    //$user_creation_errors[] = 'You cannot access this page.';
  }


  // validate fields for new user
  $user_fields_valid = true;
  $user_creation_errors = [];

  if ($_POST['first_name'] == '') {
    $user_fields_valid = false;
    $user_creation_errors[] = 'Empty first name.';
  }
  if ($_POST['last_name'] == '') {
    $user_fields_valid = false;
    $user_creation_errors[] = 'Empty last name.';
  }
  if (filter_var($_POST['email_address'],FILTER_VALIDATE_EMAIL) == false) {
    $user_fields_valid = false;
    $user_creation_errors[] = 'Invalid email address.';
  }
  if ($USERINVENTORY->user_exists_by_email_address($_POST['email_address'])) {
    $user_fields_valid = false;
    $user_creation_errors[] = 'A user with that email address already exists.';
  }

  // create user and save data
  if ($user_fields_valid) {
    $USERINVENTORY->add_user($_POST['first_name'],$_POST['last_name'],$_POST['email_address']);
    $thenewuser = $USERINVENTORY->get_user_by_email_address($_POST['email_address']); // grabbing the new user object for use below
    $USERINVENTORY->write_user_data();
  }

  if ($user_fields_valid == true):
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
  echo '<h1> Error(s)!</h1>';

  foreach ($user_creation_errors as $e) {
    echo $e . "<br>";
  }

?>

<?php endif; ?>

</html>
