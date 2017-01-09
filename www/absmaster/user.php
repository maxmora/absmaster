<?php 
  require_once "include_path.php";
  require_once "absmaster.php";

  $login_error_free = true;
  $login_errors = [];

  $the_user = $USERINVENTORY->get_user_by_email_address($_POST['email_address']);

  if (empty($_POST)) {
    $login_error_free = false;
    $login_errors[] = 'You cannot access this page without <a href="login.html">logging in</a>.';
  } elseif ($the_user->get_pin() != $_POST['pin']) {
    $login_errors[] = 'Sorry, that email/PIN combination does not exist!';
    $login_error_free = false;
  }

  session_start();
  $_SESSION['user_email'] = $_POST['email_address'];

  function generate_paper_download_link($user) {
    return '<a href="' . 'downloadpaper.php?id=' . $user->get_uploaded_paper()['id'] . '">' . $user->get_uploaded_paper()['title'] . '</a>';
  }

  function generate_review_upload_table($author_emails,$user_inv,$this_reviewer) {
    $table_string = "<table>\n";
    $list_num = 1;
    foreach ($author_emails as $a) {
      // if the user has already submitted a review for this author, disable submission of another
      $review_upload_disabled_string = '';
      if (isset($this_reviewer->get_submitted_reviews()[$a])) {
        $review_upload_disabled_string = ' disabled';
      }
      $the_author = $user_inv->get_user_by_email_address($a);
      $table_string .=
      "  <tr>\n" .
      "    <td>" . $list_num . "." . "</td>\n" .
      "    <td>\"" . generate_paper_download_link($the_author) . "\"</td>\n" .
      "    <td>\n" . 
      '      <form action="submitreview.php" method="post" enctype="multipart/form-data">' . "\n" .
      '        <input type="hidden" name="MAX_FILE_SIZE" value=5000000>' . "\n" .
      '        <input type="hidden" name="email_address" value="' . $this_reviewer->get_email_address() . '">' . "\n" .
      '        <input type="hidden" name="paper_id" value="' . $the_author->get_uploaded_paper()['id'] . '">' . "\n" .
      "        Review file:\n" .
      '        <input name="uploaded_file" type="file"' . $review_upload_disabled_string . ">\n" .
      '        <input type="submit" value="Upload Review"' . $review_upload_disabled_string . ">\n" .
      "      </form>\n" .
      "    </td>\n" .
      "  </tr>\n";
      $list_num++;
    }
    $table_string .= "<table>\n";
    return $table_string;
  }
 
  function generate_review_retrieval_table($rev_assignments,$user_email,$user_inv) {

    function generate_review_download_link($rev_id,$link_text) {
      return '<a href="' . 'downloadreview.php?id=' . $rev_id . '">' . $link_text . '</a>';
    }

    $links_string = '';
    $i = 1;
    foreach ($rev_assignments[$user_email] as $r) {
      $authors_for_that_rev = $user_inv->get_user_by_email_address($r)->get_submitted_reviews();
      if (isset($authors_for_that_rev[$user_email])) {
        $links_string = $links_string . generate_review_download_link($authors_for_that_rev[$user_email],"Review $i") . "<br>\n";
      } else {
        $links_string = $links_string . "Review $i (not yet available)" . "<br>\n";
      }
      $i++;
    }
    return $links_string;
  }
?>

<html>
  <?php if ($login_error_free == true): ?>

  <h1>Absmaster User Page</h1>

  <p>
    Welcome <?php echo $the_user->get_first_name() . ' ' . $the_user->get_last_name(); ?>!
  </p>

  <h2>Uploaded paper</h2>

  <p>
    <?php
      $upload = $the_user->get_uploaded_paper();
      if ($upload) {
        echo 'You have uploaded a paper titled "' . generate_paper_download_link($the_user) . '".';
        if (file_exists(REVIEWER_ASSIGNMENTS_FILE)) {
          $paper_submission_disabled_string = ' disabled'; // users can't submit again if reviews have been assigned
        }
      } else {
        echo 'You have not uploaded a paper yet.';
        $paper_submission_disabled_string = '';
      }
    ?>
  </p>

  <h2>Paper/abstract upload</h2>

  <p>Upload your paper below. Uploaded papers must be in PDF format.</p>

  <form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value=5000000>
    <input type="hidden" name="email_address" value="<?php echo $the_user->get_email_address();?>">

    Title of paper:
    <input name="paper_title" type="text">
    <br>
    File to upload:
    <input name="uploaded_file" type="file"<?php echo $paper_submission_disabled_string;?>>
    <br>
    <input type="submit" value="Upload File"<?php echo $paper_submission_disabled_string;?>>
  </form>

  <h2>Papers to review</h2>

    <p>
    To download paper, click on the title. To submit your review for that paper, use the box to the right of the title. Submit only one review at a time.
    Reviews must be in PDF format.
    </p>

    <?php
      if (file_exists(REVIEWER_ASSIGNMENTS_FILE)) {
        echo "<p>\nYou have been assigned the following papers to review. Click on a title to download a paper. Use the box next to the title to upload your review for that paper.\n</p>\n";
        $revs = read_reviewer_assignments();
        $papers_for_the_user = compile_papers_for_reviewer_by_email_address($revs,$the_user->get_email_address());
        echo "<p>\n" . generate_review_upload_table($papers_for_the_user,$USERINVENTORY,$the_user) . "\n</p>\n";
      } else {
        echo "<p>\nNo papers are available to review yet. Please check back later.\n</p>\n";
      }
    ?>

  <h2>Reviews of your paper</h2>

  <?php
    if ($PROJECTSTATE->get_reviews_available_status()) {
      $revs = read_reviewer_assignments();
      echo generate_review_retrieval_table($revs,$the_user->get_email_address(),$USERINVENTORY);
    } else {
      echo '<p>No reviews are currently available.</p>';
    }
  ?>

  <?php // USER LOGIN ERRORS
    else:
    foreach ($login_errors as $e) {
      echo $e . "<br>";
    }
    endif;
  ?>

</html>
