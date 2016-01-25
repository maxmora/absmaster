<?php 
  require_once "../../absmaster_backend/absmaster.php";

  // TODO begin by checking if they've even posted who they are; if not, then just display an error
  // do this by validating here!

  $login_error_free = true;
  $login_errors = [];

  $the_user = $userinventory->get_user_by_email_address($_POST['email_address']);
  if ($the_user->get_pin() != $_POST['pin']) {
    $login_errors[] = 'Sorry, that email/PIN combination does not exist!';
    $login_error_free = false;
  }

?>

<html>
  <h1>Absmaster User Page</h1>

  <?php if ($login_error_free == true): ?>

  Welcome <?php echo $the_user->get_first_name() . ' ' . $the_user->get_last_name(); ?>!


  (here is where the user content will be)

  <?php else:
    foreach ($login_errors as $e) {
      echo $e . "\n";
    }

    endif;
  ?>

</html>
