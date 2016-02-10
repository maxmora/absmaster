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

  // define the backend root directory and the frontend root directories as constants here
  define("BACKEND_ROOT", "/home/chris/Documents/jobs/work_study/abstract_reviewer/absmaster/absmaster_backend");
  define("FRONTEND_ROOT", "/home/chris/Documents/jobs/work_study/abstract_reviewer/absmaster/www/absmaster");

  // some constants I'll use in here, but should refactor later to have passed down the line on creation of various objects
  // (they should probably be stored as project state properties)
  define("PROJECT_STATE_FILE", BACKEND_ROOT . "/data/project_state.json");
  define("USERS_FILE", BACKEND_ROOT . "/data/users.json");
  define("USER_LOG", BACKEND_ROOT . '/data/user.log');
  define("PROJECT_LOG", BACKEND_ROOT . '/data/project.log');

  function log_msg($file,$msg) {
    $t = DateTime::ISO8601; // format to use for time
    $line = date($t) . ' ' . $msg . "\n";
    file_put_contents($file,$line,FILE_APPEND);
  }

  // Class for the project state (interface to info stored across sessions in project_state.json)
  class ProjectState {
    private $_project_state_file;
    //private $_users_file = ''; //maybe this doesn't make sense as something to store here

    // trying for sensible defaults
    private $_project_name = '';
    private $_signup_enabled = true;
    private $_reviews_available = false;
    private $_max_users = 50;

    // TODO default these to empty and have them be assigned in the initial project state on new project creation 
    private $_admin_email = '';
    private $_admin_pin = '';

    public function __construct($projstatefile) {
      $this->_project_state_file = $projstatefile;
    }

    public function read_project_state_data() {
      $pdata = json_decode(file_get_contents($this->_project_state_file),true);
      $this->_project_name = $pdata['project_name'];
      $this->_signup_enabled = $pdata['signup_enabled'];
      $this->_reviews_available = $pdata['reviews_available'];
      $this->_max_users = $pdata['max_users'];
      $this->_admin_email = $pdata['admin_email'];
      $this->_admin_pin = $pdata['admin_pin'];
    }

    public function write_project_state_data() {
      file_put_contents($this->_project_state_file,$this->_jsonify_project_data());
      log_msg(PROJECT_LOG,"Project state data written to file \"$this->_project_state_file\"");
    }

    public function admin_email_validate($e) {
      return $this->_admin_email == $e;
    }

    public function admin_pin_validate($p) {
      return $this->_admin_pin == $p;
    }

    public function get_project_name() {
      return $this->_project_name;
    }

    public function get_max_users() {
      return $this->_max_users;
    }

    public function get_signup_enabled_status() {
      return $this->_signup_enabled;
    }

    public function get_reviews_available_status() {
      return $this->_reviews_available;
    }

    public function get_admin_email() {
      return $this->_admin_email;
    }

    public function set_project_name($name) {
      $this->_project_name = $name;
    }

    public function enable_signup() {
      $this->_signup_enabled = true;
    }

    public function disable_signup() {
      $this->_signup_enabled = false;
    }

    public function enable_reviews_available() {
      $this->_reviews_available = true;
    }

    public function disable_reviews_available() {
      $this->_reviews_available = false;
    }

    public function set_max_users($n) {
      $this->_max_users = $n;
    }

    private function _jsonify_project_data() {
      $arr = [];
      $arr['project_name'] = $this->_project_name;
      $arr['signup_enabled'] = $this->_signup_enabled;
      $arr['reviews_available'] = $this->_reviews_available;
      $arr['max_users'] = $this->_max_users;
      $arr['admin_email'] = $this->_admin_email;
      $arr['admin_pin'] = $this->_admin_pin;
      return json_encode($arr);
    }
  }

  // Class for list of users
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

  // Class for user
  class User {
    private $_first_name;
    private $_last_name;
    private $_email_address;
    private $_pin;

    private $_uploaded_paper = null;
    private $_submitted_reviews = [];

    public function __construct($first_name,$last_name,$email_address,$pin,$uploaded_paper=null,$submitted_reviews=[]) {
      $this->_first_name = $first_name;
      $this->_last_name = $last_name;
      $this->_email_address = $email_address;
      $this->_pin = $pin;
      $this->_uploaded_paper = $uploaded_paper;
      $this->_submitted_reviews= $submitted_reviews;
    }

    public function get_first_name() {
      return $this->_first_name;
    }

    public function get_last_name() {
      return $this->_last_name;
    }

    public function get_email_address() {
      return $this->_email_address;
    }

    public function get_pin() {
      return $this->_pin;
    }

    public function get_uploaded_paper() {
      return $this->_uploaded_paper;
    }

    public function get_submitted_reviews() {
      return $this->_submitted_reviews;
    }

    public function add_submitted_review($author_email,$review_id) {
      $this->_submitted_reviews[$author_email] = $review_id;
    }
    
    public function arrayify_user() {
      $arr = [];
      $arr['first_name'] = $this->_first_name;
      $arr['last_name'] = $this->_last_name;
      $arr['email_address'] = $this->_email_address;
      $arr['pin'] = $this->_pin;
      $arr['uploaded_paper'] = $this->_uploaded_paper;
      $arr['submitted_reviews'] = $this->_submitted_reviews;
      return $arr;
    }

    public function set_uploaded_paper($id,$title) {
      $this->_uploaded_paper = ['id'=>$id, 'title'=>$title];
    }
  }

  //UserInventory setup
  $USERINVENTORY = new UserInventory(USERS_FILE);
  $USERINVENTORY->read_user_data();


  //ProjectState setup
  $PROJECTSTATE = new ProjectState(PROJECT_STATE_FILE);
  $PROJECTSTATE->read_project_state_data();
  

