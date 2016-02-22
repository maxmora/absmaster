<?php 
  session_start();
  require_once "include_path.php";
  require_once "absmaster.php";

  $admin_login_error_free = false;
  $admin_login_errors = [];

  // session and/or login validation
  if (isset($_SESSION['admin_pin']) || $PROJECTSTATE->admin_pin_validate($_SESSION['admin_pin'])) {
    $admin_login_error_free = true;
  } elseif (empty($_POST)) {
    $admin_login_errors[] = 'Error: You have not <a href="adminauth.html">logged in</a>.';
  } elseif ($PROJECTSTATE->admin_email_validate($_POST['email_address']) && $PROJECTSTATE->admin_pin_validate($_POST['pin'])) {
    $admin_login_error_free = true;
  } else {
    $admin_login_errors[] = 'Sorry, that is not a valid administrator email/PIN combination.';
  }

  if ($admin_login_error_free == true):

  // login is successful and we've just gotten a new pin from logging in, save this to $_SESSION
  if (isset($_POST['pin'])) {
    $_SESSION['admin_pin'] = $_POST['pin'];
  }

  function generate_paper_download_link($user) {
    return '"<a href="' . 'downloadpaper.php?id=' . $user->get_uploaded_paper()['id'] . '">' . $user->get_uploaded_paper()['title'] . '</a>"';
  }

  function generate_review_download_link($rev_id,$link_text) {
    return '<a href="' . 'downloadreview.php?id=' . $rev_id . '">' . $link_text . '</a>';
  }

  function generate_user_table($user_inv) {
    $users = $user_inv->get_users();
    $table_string = '';
    $table_string .=
    "<table>\n" .
    "  <tr>\n" .
    "    <td>" . "First name" . "</td>\n" .
    "    <td>" . "Last name" . "</td>\n" .
    "    <td>" . "Email address" . "</td>\n" .
    "    <td>" . "PIN" . "</td>\n" .
    "    <td>" . "Uploaded paper" . "</td>\n" .
    "    <td>" . "Number of submitted reviews" . "</td>\n" .
    "  </tr>\n";
    foreach ($users as $u) {
      if ($u->get_uploaded_paper()) {
        $has_uploaded = generate_paper_download_link($u);
      } else {
        $has_uploaded = '(none)';
      }
      $table_string .=
      "  <tr>\n" .
      "    <td>" . $u->get_first_name() . "</td>\n" .
      "    <td>" . $u->get_last_name() . "</td>\n" .
      "    <td>" . $u->get_email_address() . "</td>\n" .
      "    <td>" . $u->get_pin() . "</td>\n" .
      "    <td>" . $has_uploaded . "</td>\n" .
      "    <td>" . count($u->get_submitted_reviews()) . "</td>\n" .
      "  </tr>\n";
      
    }
    $table_string .= "</table>\n";
    return $table_string;
  }

  function generate_reviewer_assignment_table($review_list,$user_inv) {

    function string_of_reviewers($user,$review_list,$user_inv) {
      $reviewer_string = '';
      $user_email = $user->get_email_address();
      if (isset($review_list[$user_email])) {
        foreach ($review_list[$user->get_email_address()] as $r) {
          $rev_user = $user_inv->get_user_by_email_address($r);
          $rev_user_string = $rev_user->get_first_name() . ' ' . $rev_user->get_last_name() . ' (' . $rev_user->get_email_address() . ')';
          // show name as download link to review if the review has been submitted
          if (isset($rev_user->get_submitted_reviews()[$user->get_email_address()])) {
            $rev_user_string = generate_review_download_link($rev_user->get_submitted_reviews()[$user->get_email_address()],$rev_user_string);
          }
          $reviewer_string = $reviewer_string . $rev_user_string . ', ';
        }
      }
      return $reviewer_string;
    }
    $users = $user_inv->get_users();
    $table_string = '';
    $table_string .=
    "<table>\n" .
    "  <tr>\n" .
    "    <td>" . "First name" . "</td>\n" .
    "    <td>" . "Last name" . "</td>\n" .
    "    <td>" . "Email address" . "</td>\n" .
    "    <td>" . "Uploaded paper" . "</td>\n" .
    "    <td>" . "Reviewers" . "</td>\n" .
    "  </tr>\n";
    foreach ($users as $u) {
      $table_string .=
      "  <tr>\n" .
      "    <td>" . $u->get_first_name() . "</td>\n" .
      "    <td>" . $u->get_last_name() . "</td>\n" .
      "    <td>" . $u->get_email_address() . "</td>\n" .
      "    <td>" . '"' . $u->get_uploaded_paper()['title'] . '"' . "</td>\n" .
      "    <td>" . string_of_reviewers($u,$review_list,$user_inv) . "</td>\n" .
      "  </tr>\n";
    }
    $table_string .= "</table>\n";
    return $table_string;
  }

?>

<html>

  <h1>Absmaster Project Administration</h1>


  <h2>Users</h2>

  The following users are currently in the system:

  <p>
  <?php echo generate_user_table($USERINVENTORY); ?>
  </p>


  <h2>Reviewer Assignment</h2>

  <p>
  <?php
    // don't allow assignment of reviewers if everyone hasn't uploaded a paper
    $disable_review_assignments_string = '';
    $papers_not_all_uploaded_yet = false;
    :foreach ($USERINVENTORY->get_users() as $u) {
      if ($u->get_uploaded_paper() == false) {
        $disable_review_assignments_string = ' disabled';
        $papers_not_all_uploaded_yet = true;
      }
    }

    if ($papers_not_all_uploaded_yet) {
      echo "Papers have not yet been uploaded by all authors. Distribution of papers to reviewers disabled.";
    }

    // also don't allow distribution if papers have already been distributed
    if (file_exists(REVIEWER_ASSIGNMENTS_FILE)) {
      $disable_review_assignments_string = ' disabled';
    }
  ?>
  </p>

  <p>
  <form action="assignreviewers.php" method="get">
    <p>Number of papers to be reviewed per author:</p>
    <p><input type="number" name="num_reviews"<?php echo $disable_review_assignments_string;?>></p>
    <input type="submit" value="Assign Reviewers"<?php echo $disable_review_assignments_string;?>>
  </form>
  </p>

  <?php
    if (file_exists(REVIEWER_ASSIGNMENTS_FILE)) {
      echo "<p>\nThe current reviewer assignments are as follows:\n</p>";
      echo "<p>\n" . generate_reviewer_assignment_table(read_reviewer_assignments(),$USERINVENTORY) . "\n</p>";
    }
  ?>


  <h2>Review Distribution</h2>

  <?php
    if (file_exists(REVIEWER_ASSIGNMENTS_FILE)) {
      // not elegant: how many reviews are we supposed to have? check how many the first user is supposed to have * how many users we have
      $total_expected_reviews = count(read_reviewer_assignments()[$USERINVENTORY->get_users()[0]->get_email_address()]) * count($USERINVENTORY->get_users());
      $total_received_reviews = 0;
      foreach ($USERINVENTORY->get_users() as $u) {
        $total_received_reviews += count($u->get_submitted_reviews());
      }
     
      echo '<p>' . $total_received_reviews . ' out of ' . $total_expected_reviews . 'have been submitted so far.</p>';
     
      $reviews_awaited = $total_expected_reviews - $total_received_reviews;
      if ($reviews_awaited > 0) {
        echo '<b>Note: Reviews submitted after distribution is enabled (still waiting on ' . $reviews_awaited . ' more) will immediately be viewable by authors without your approval.</b>';
      }
      echo '</p>';

      $revs_on_string = '';
      $revs_off_string = '';
      if ($PROJECTSTATE->get_reviews_available_status()) {
        $revs_on_string = ' checked';
      } else {
        $revs_off_string = ' checked';
      }

      echo '<form action="changereviewavailability.php" method="post">
          <input type="radio" name="review_availability" value="disable"' . $revs_off_string . '> Reviews NOT available to authors<br>
          <input type="radio" name="review_availability" value="enable"' . $revs_on_string . '> Reviews available to authors<br>
          <input type="submit" value="Change review availability">
        </form>';
    } else {
      echo '<p>Reviews have not yet been assigned.</p>';
    }
  ?>

  <h2>Project Settings</h2>

  <?php
    $signup_on_string = '';
    $signup_off_string = '';
    if ($PROJECTSTATE->get_signup_enabled_status()) {
      $signup_on_string = ' checked';
    } else {
      $signup_off_string = ' checked';
    }
  ?>

  <form action="updatesettings.php" method="post">
    <p>Enable/disable signing up by new users (automatically disabled upon distribution of reviews):</p>
    <input type="radio" name="signup_enablement" value="enable"<?php echo $signup_on_string;?>> Enabled
    <input type="radio" name="signup_enablement" value="disable"<?php echo $signup_off_string;?>> Disabled

    <p>Maximum number of users:</p>
    <input type="number" name="max_users" value="<?php echo $PROJECTSTATE->get_max_users();?>">


    <p><input type="submit" value="Update settings"></p>
  </form>

</html>



<?php // messages printed if login failed
  else:

  foreach ($admin_login_errors as $e) {
    echo $e . "<br>";
  }

  endif;
?>
