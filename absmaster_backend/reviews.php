<?php
  /*
  This file is part of Absmaster.
  
  Absmaster is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  Absmaster is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Absmaster.  If not, see <http://www.gnu.org/licenses/>.
  */

  require_once "absmaster.php";

  // excluded review list should be a hash of emails => emails that shouldn't review it
  function assign_reviewers($user_inventory,$reviews_per_user,$excluded_review_list) {
    $users = [];
    foreach ($user_inventory->get_users() as $u) {
      $users[] = $u->get_email_address();
    }

    shuffle($users); // so assignments are random
    
    // make hash to keep track of how many reviews each user has been assigned
    $assigned_reviews = [];
    foreach ($users as $u) {
      $assigned_reviews[$u] = 0;
    }

    // loop through users and assign n reviewers to each
    // FIXME implement as an appropriate object type instead of just debug echoing!
    foreach ($users as $u) {
      $reviewer_pool = $users;
      // remove just the current user, so that $reviewer_pool is everyone but that user
      $index = array_search($u,$reviewer_pool);
      array_splice($reviewer_pool,$index,1);

      $revs_for_user = []; // TODO implement this as right kind of object for these pairs

      $i = $reviews_per_user;
      while ($i > 0) {
        $r = array_pop($reviewer_pool);
        if ($assigned_reviews[$r] < $reviews_per_user && true) { // FIXME this 'true' needs to instead check that that reviewer isn't excluded for that user
          $revs_for_user[] = $r; // TODO refactor along with correct implementation of author--reviewer pairs
          $assigned_reviews[$r]++;
        } else {
          // TODO here, save $r to a list of people with too many, iff they aren't in the excluded list; then go back and assign reviewers from them if there is no one else available to review the abstract that has fewer reviews assigned to them
          echo "skipped $r as rev for $u<br>";
          continue;
        }
        $i--;
      }

      // echo for debug
      echo "$u has reviewers: ";
      print_r($revs_for_user);
      echo '<br>';

    }

    // echo for dubug
    echo "assigned reviews:<br>";
    print_r($assigned_reviews);
    // TODO end up with concatenated object of author--reviewer pairs and return that
  }
