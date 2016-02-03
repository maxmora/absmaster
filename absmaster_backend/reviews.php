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


  define("REVIEWER_ASSIGNMENTS_FILE", BACKEND_ROOT . '/data/assigned_reviewers.json');
  define("REVIEWER_EXCLUSIONS_FILE", BACKEND_ROOT . '/data/excluded_reviewers.json');

  function read_reviewer_assignments() {
    return json_decode(file_get_contents(REVIEWER_ASSIGNMENTS_FILE),true);
  }

  function write_reviewer_assignments($rev_assignments_json) {
    file_put_contents(REVIEWER_ASSIGNMENTS_FILE,$rev_assignments_json);
    log_msg(PROJECT_LOG,'Reviewer assignments written to file "' . REVIEWER_ASSIGNMENTS_FILE .'"');
  }

  function read_reviewer_exclusions() {
    // TODO implement
  }

  function write_reviewer_exclusions() {
    // TODO implement
  }

  function reviewer_is_allowed_for_author($author,$reviewer,$exclusions) {
    if (isset($exclusions[$author])) {
      if (in_array($reviewer,$exclusions[$author])) {
        return false;
      }
    }
    return true;
  }


  function compile_valid_reviewers($author,$list_of_users,$excluded_reviewer_list) {
    $non_author_users = $list_of_users;
    // exclude the current author; you can't review yourself
    $index = array_search($author,$non_author_users);
    array_splice($non_author_users,$index,1);

    $valid_reviewers = [];
    foreach ($non_author_users as $u) {
      if (reviewer_is_allowed_for_author($author,$u,$excluded_reviewer_list)) {
        $valid_reviewers[] = $u;
      }
    }
    return $valid_reviewers;
  }

  // used in assign_reviewers_alt(), not currently implemented
  function compile_valid_reviewees($reviewer,$list_of_users,$excluded_reviewer_list) {
    $non_reviewer_users = $list_of_users;
    // remove this reviwer; you can't review yourself
    $index = array_search($reviewer,$non_reviewer_users);
    array_splice($non_reviewer_users,$index,1);

    $valid_reviewees = [];
    foreach ($non_reviewer_users as $u) {
      if (reviewer_is_allowed_for_author($u,$reviewer,$excluded_reviewer_list)) {
        $valid_reviewees[] = $u;
      }
    }
    return $valid_reviewees;
  }


  // excluded review list should be a hash of emails => emails that shouldn't review it
  function assign_reviewers($users,$reviews_per_user,$excluded_reviewer_list) {
    // make hash table to keep track of how many reviews each user has been assigned
    $assigned_reviews = [];
    foreach ($users as $u) {
      $assigned_reviews[$u] = 0;
    }

    
    $authors_and_their_reviewers = [];

    // loop through users and assign n reviewers to each
    foreach ($users as $u) {
      $reviewer_pool = compile_valid_reviewers($u,$users,$excluded_reviewer_list);
      $reviewers_for_user = []; // TODO implement this as right kind of object for these pairs

      shuffle($reviewer_pool);

      $i = $reviews_per_user;
      while ($i > 0) {
        $r = array_pop($reviewer_pool);
        // has $reviewer_pool become empty?
        if (isset($r)) {
          if ($assigned_reviews[$r] < $reviews_per_user) {
            $reviewers_for_user[] = $r;
            $assigned_reviews[$r]++;
          } else {
            continue;
          }
          $i--;
        } else {
          // $reviewer_pool is empty, so we can't find a match; recurse until we don't run into this problem
          return assign_reviewers($users,$reviews_per_user,$excluded_reviewer_list);
        }
      }

      $authors_and_their_reviewers[$u] = $reviewers_for_user;
    }
    
    return $authors_and_their_reviewers;
  }

  
  function distribute_papers_to_reviewers($rev_list,$user_inv) {
    // TODO implement
  } 

