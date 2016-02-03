<?php 
  session_start();
  require_once "../../absmaster_backend/absmaster.php";

  if ($projectstate->admin_pin_validate($_SESSION['admin_pin']) == false) {
    die('Access denied. You must be logged in as administrator to do that.');
  }

  require_once "../../absmaster_backend/reviews.php";

  // TODO get these from user input
  $reviews_per_user = 2;

  // TODO implement gathering of these
  $excluded_reviewers = []; // when implemented, this will be a hash table of reviewer => [exclusions]

  // make list of users' email addresses
  $users = [];
  foreach ($userinventory->get_users() as $u) {
    $users[] = $u->get_email_address();
  }

  // TODO this whole thing should only be operating on people who have submitted reviews; check for this when having admin assign reviewers?
  $review_pairings = assign_reviewers($users,$reviews_per_user,$excluded_reviewers);


  function generate_reviewer_assignment_table($review_list,$user_inv) {

    function string_of_reviewers($user,$review_list,$user_inv) {
      $reviewer_string = '';
      foreach ($review_list[$user->get_email_address()] as $r) {
        $rev_user = $user_inv->get_user_by_email_address($r);
        $reviewer_string = $reviewer_string . $rev_user->get_first_name() . ' ' . $rev_user->get_last_name() . ' (' . $rev_user->get_email_address() . '), ';
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

  <h2>Assigned Reviews</h2>

  <p>
    <?php echo generate_reviewer_assignment_table($review_pairings,$userinventory); ?>
  </p>

  <p>
    You can refresh the page to re-assign. Click the button below to distribute papers to reviewers when you are satisfied.
  </p>

</html>
