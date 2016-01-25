<?php 
  require_once "../../absmaster_backend/absmaster.php";

  //TODO validate input on all fields before doing ANYTHING
  // if it's not good, then simply state all that and don't create a new user!
?>

<html>

<?php
  $new_user_error_free = true;
  $user_creation_errors = [];


  if ($userinventory->user_exists_by_email_address($_POST['email_address']) == false) {
    $userinventory->add_user($_POST['first_name'],$_POST['last_name'],$_POST['email_address']);
    $thenewuser = $userinventory->get_user_by_email_address($_POST['email_address']); // grabbing the new user object for use below
    $userinventory->write_user_data();
  } else {
    $new_user_error_free = false;
    $user_creation_errors[] = 'A user with that email address "' . $_POST['email_address'] . '" already exists.';
  }

  if ($new_user_error_free == true): //TODO also check for input validity here!
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

  You will login using your email address and PIN. Be sure to keep these.

<?php else: 

  foreach ($user_creation_errors as $e) {
    echo $e . "\n";
  }

?>

<?php endif; ?>

</html>
