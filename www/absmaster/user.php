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

  <p>
    Welcome <?php echo $the_user->get_first_name() . ' ' . $the_user->get_last_name(); ?>!
  </p>

  <h2>Paper/abstract upload</h2>

  <form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value=5000000>
    <input type="hidden" name="email_address" value="<?php echo $the_user->get_email_address();?>">

    Title of paper:
    <input name="paper_title" type="text">
    <br>
    File to upload:
    <input name="uploaded_file" type="file">
    <br>
    <input type="submit" value="Upload File">
  </form>



  <?php // USER LOGIN ERRORS
    else:
    foreach ($login_errors as $e) {
      echo $e . "<br>";
    }
    endif;
  ?>

</html>
