<?php 
  require_once "../../absmaster_backend/absmaster.php";

  $login_error_free = true;
  $login_errors = [];

  $the_user = $userinventory->get_user_by_email_address($_POST['email_address']);

  if (empty($_POST)) {
    $login_error_free = false;
    $login_errors[] = 'You cannot access this page without <a href="login.html">logging in</a>.';
  } elseif ($the_user->get_pin() != $_POST['pin']) {
    $login_errors[] = 'Sorry, that email/PIN combination does not exist!';
    $login_error_free = false;
  }

?>

<html>
  <?php if ($login_error_free == true): ?>

  <h1>Absmaster User Page</h1>

  Welcome <?php echo $the_user->get_first_name() . ' ' . $the_user->get_last_name(); ?>!


  (here is where the user content will be)

  <?php
    else:
    foreach ($login_errors as $e) {
      echo $e . "<br>";
    }
    endif;
  ?>

</html>
