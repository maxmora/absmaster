<?php 
  require_once "../../absmaster_backend/absmaster.php";
  require_once "../../absmaster_backend/reviews.php";


  if (empty($_POST)) {
    die('Access denied. Please log in and upload from your user page.');
  }
  if (empty($_FILES['uploaded_file']['tmp_name'])) {
    die('You must select a file to upload. Please try again.');
  }

  // TODO: 
  //   -validate that it's a real PDF (do same way as for paper upload)
  $SUBMISSION_DIR = BACKEND_ROOT . '/reviews/';
  $the_reviewer = $userinventory->get_user_by_email_address($_POST['email_address']);

  // TODO do this right; base it on reading off from a JSON file of what's been submitted
  $used_review_ids = $userinventory->get_used_review_ids();
  $new_review_id = max($used_review_ids) + 1;

  $author_email = $userinventory->get_paper_ids_and_author_emails()[$_POST['paper_id']];
  $the_author = $userinventory->get_user_by_email_address($author_email);

  if (isset($the_reviewer->get_submitted_reviews()[$author_email])){
    die('You have already uploaded a review for this paper!');
  }

  $new_basename = $new_review_id . '.pdf';
  move_uploaded_file($_FILES['uploaded_file']['tmp_name'],$SUBMISSION_DIR . $new_basename);

  $userinventory->add_user_submitted_review_by_email_address($the_reviewer->get_email_address(),$author_email,$new_review_id);
  $userinventory->write_user_data();
?>

<html>
  <h1>File upload successful!</h1>

  <p>Your review for the paper "<?php echo $the_author->get_uploaded_paper()['title'];?>" has been successfully submitted.</p>
</html>
