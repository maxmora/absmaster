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

  require_once "User.php";

  class UserInventory {
    private $_users;
    private $_users_file;

    public function __construct($usersfile) {
      $this->_users_file = $usersfile;
    }

    // this is to be used only to ADD users as called upon by end user interface, NOT for populating the list (just use new User for that)
    public function add_user($first_name,$last_name,$email_address,$pin='') {
      if ($pin == '') {
        $pin = $this->_generate_user_pin();
      }
      $this->_users[] = new User($first_name,$last_name,$email_address,$pin);
      log_msg(USER_LOG,"Added user \"$email_address\"");
    }

    public function remove_user($email_address) {
      foreach ($this->_users as $k=>$v) {
        if ($v->get_email_address() == $email_address) {
          array_splice($this->_users,$k,1); // remove just this item from $this->_users without leaving a gap in indices
          log_msg(USER_LOG,"Removed user \"$email_address\"");
        }
      }
    }

    public function set_user_uploaded_paper_by_email_address($email_address,$id,$title) {
      foreach ($this->_users as $k=>$v) {
        if ($v->get_email_address() == $email_address) {
          $v->set_uploaded_paper($id,$title);
          break;
        }
      }
    }

    public function add_user_submitted_review_by_email_address($email_address,$author_email,$review_id) {
      foreach ($this->_users as $k=>$v) {
        if ($v->get_email_address() == $email_address) {
          $v->add_submitted_review($author_email,$review_id);
          break;
        }
      }
    }

    public function get_user_by_email_address($email_address) {
      foreach ($this->_users as $k=>$v) {
        if ($v->get_email_address() == $email_address) {
          return $v;
        }
      }
    }

    public function user_exists_by_email_address($email_address) {
      foreach ($this->_users as $k=>$v) {
        if ($v->get_email_address() == $email_address) {
          return true;
        }
      }
      return false;
    }

    public function get_users() {
      return $this->_users;
    }

    public function get_used_paper_ids() {
      $ids = [];
      foreach ($this->_users as $u) {
        $p = $u->get_uploaded_paper();
        if ($p) {
          $ids[] = $p['id'];
        }
      }
      return $ids;
    }

    public function get_used_review_ids() {
      $ids = [];
      foreach ($this->_users as $u) {
        $reviews = $u->get_submitted_reviews();
        if ($reviews) {
          foreach ($reviews as $a_email=>$revid) {
            $ids[] = $revid;
          }
        }
      }
      return $ids;
    }

    // allows lookup of author emails by paper ID
    public function get_paper_ids_and_author_emails() {
      $ids_and_emails = [];
      foreach ($this->_users as $u) {
        $p = $u->get_uploaded_paper();
        if ($p) {
          $ids_and_emails[$p['id']] = $u->get_email_address();
        }
      }
      return $ids_and_emails;
    }

    public function read_user_data() {
      $user_data = json_decode(file_get_contents($this->_users_file),true);
      foreach ($user_data as $u) {
        $this->_users[] = new User ($u['first_name'],$u['last_name'],$u['email_address'],$u['pin'],$u['uploaded_paper'],$u['submitted_reviews']);
      }
    }
    
    // TODO figure out where this will actually get called from, and think about if the file and the data it writes should be added as parameters!
    //FIXME make private and refactor once tested
    public function write_user_data() {
      file_put_contents($this->_users_file,$this->_jsonify_users());
      log_msg(USER_LOG,"User inventory data written to file \"$this->_users_file\"");
    }

    public function clear_all_reviews() {
      foreach ($this->_users as $u) {
        $submitted_reviews = $u->get_submitted_reviews();
        if (isset($submitted_reviews)) {
          foreach ($submitted_reviews as $reviewer => $rev_id) {
            unlink(BACKEND_ROOT . '/reviews/' . $rev_id . '.pdf');
          }
        }
        $u->clear_submitted_reviews();
      }
      if (file_exists(REVIEWER_ASSIGNMENTS_FILE)) {
        unlink(REVIEWER_ASSIGNMENTS_FILE);
      }
      $this->write_user_data();
    }

    public function clear_all_papers() {
      $this->clear_all_reviews();
      foreach ($this->_users as $u) {
        $paper = $u->get_uploaded_paper();
        if (isset($paper)) {
          $paper_id = $paper['id'];
          unlink(BACKEND_ROOT . '/submissions/' . $paper_id . '.pdf');
        }
        $u->clear_uploaded_paper();
      }
      $this->write_user_data();
    }

    public function clear_all_users() {
      $this->clear_all_papers();
      // keep each email address first, to avoid deleting from an array as we walk through it
      $emails = [];
      foreach ($this->_users as $u) {
        $emails[] = $u->get_email_address();
      }
      // now walk through those email addresses and remove users by them
      foreach ($emails as $user_email) {
        $this->remove_user($user_email);
      }
      $this->write_user_data();
    }
    
    // TODO have this be a sensibly pretty-printed version so that the resulting file can be easily-ish human read?
    private function _jsonify_users() {
      return json_encode($this->_arrayify_user_list($this->_users));
    }
    
    private function _arrayify_user_list($user_list) {
      $arr = [];
      foreach ($user_list as $u) {
        $arr[] = $u->arrayify_user();
      }
      return $arr;
    }

    private function _generate_user_pin($length=6) {
      $new_pin = '';
      for ($i = 0; $i < $length; $i++) {
        $new_pin .= rand(0,9);
      }
      return $new_pin;
    }
  }
