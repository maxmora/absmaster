<?php 
  require_once "../../absmaster_backend/absmaster.php";
?>

<html>
  <?php if($PROJECTSTATE->get_signup_enabled_status() == true): ?>

  <h1>Absmaster User Signup</h1>

  Welcome to Absmaster! Please complete the form to sign up.
  <p>

  <form action="newuser.php" method="post">
    First name:
    <p><input type="text" name="first_name"></p>
    <p>Last name:</p>
    <p><input type="text" name="last_name"></p>
    <p>Email:</p>
    <p><input type="text" name="email_address"></p>
    <input type="submit" value="Sign up">
  </form>

  <?php else: ?>

  Sorry, creation of new users for this project is disabled!

  <?php endif; ?>
</html>
