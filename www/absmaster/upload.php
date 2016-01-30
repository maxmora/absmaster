<?php 
  require_once "../../absmaster_backend/absmaster.php";

  if (empty($_POST)) {
    die('Access denied. Please log in and upload from your user page.');
  }

  // TODO: 
  //   -do some validation: they entered a title, must be .pdf (preferably a real check, not just extension), not over max size
  //   -name as integer ID and store index of all those IDs by user (need model for this)
  $SUBMISSION_DIR = BACKEND_ROOT . '/submissions/';
  $the_user = $userinventory->get_user_by_email_address($_POST['email_address']);

  // generate uniq id for paper, assign id and title to user, and write user data

  // generate unique id; should probably be encapsulated elsewhere
  $used_ids = $userinventory->get_used_paper_ids();
  $new_id = max($used_ids) + 1;

  $new_basename = $new_id . '.pdf';
  $new_title = $_POST['paper_title'];

  move_uploaded_file($_FILES['uploaded_file']['tmp_name'],$SUBMISSION_DIR . $new_basename);
  $userinventory->set_user_uploaded_paper_by_email_address($the_user->get_email_address(),$new_id,$new_title);
  $userinventory->write_user_data();
?>

<html>
  <h1>File upload successful!</h1>

  your email is <?php echo $the_user->get_email_address();?>

  <p>Your file "<?php echo $_POST['paper_title'];?>" has successfully been uploaded.</p>
</html>