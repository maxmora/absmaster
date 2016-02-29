<?php 
  require_once "include_path.php";
  require_once "absmaster.php";

  if (empty($_POST)) {
    die('Access denied. Please log in and upload from your user page.');
  }

  if ($_POST['paper_title'] == '') {
    die('Please enter a title for your paper.');
  }

  if (file_exists(REVIEWER_ASSIGNMENTS_FILE)) {
    die('You cannot resubmit at this time.');
  }

  $SUBMISSION_DIR = BACKEND_ROOT . '/submissions/';
  $the_user = $USERINVENTORY->get_user_by_email_address($_POST['email_address']);

  $user_uploaded_paper = $the_user->get_uploaded_paper();
  if (isset($user_uploaded_paper)) {
    // keep paper id the same if this a re-upload so we just overwrite the file
    $new_id = $user_uploaded_paper['id'];
  } else {
    // if it's a new upload, generate unique id; should probably be encapsulated elsewhere
    $used_ids = $USERINVENTORY->get_used_paper_ids();
    $new_id = max($used_ids) + 1;
  }

  $new_basename = $new_id . '.pdf';
  $new_title = $_POST['paper_title'];

  if (file_is_pdf($_FILES['uploaded_file']['tmp_name'])) {
    move_uploaded_file($_FILES['uploaded_file']['tmp_name'],$SUBMISSION_DIR . $new_basename);
    $USERINVENTORY->set_user_uploaded_paper_by_email_address($the_user->get_email_address(),$new_id,$new_title);
    $USERINVENTORY->write_user_data();
  } else {
    unlink($_FILES['uploaded_file']['tmp_name']);
    die('File is not a PDF. Only PDFs can be uploaded.');
  }
?>

<html>
  <h1>File upload successful!</h1>

  <p>Your submission "<?php echo $_POST['paper_title'];?>" has successfully been uploaded.</p>
</html>
