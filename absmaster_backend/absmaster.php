<?php
  // This shall be the main file that all the pages include. Put all includes that are needed in here.

  // define the backend root directory and the frontend root directories as constants here
  define("BACKEND_ROOT", "/home/chris/Documents/jobs/work_study/abstract_reviewer/absmaster/absmaster_backend");
  define("FRONTEND_ROOT", "/home/chris/Documents/jobs/work_study/abstract_reviewer/absmaster/www/absmaster");

  // some constants I'll use in here, but should refactor later to have passed down the line on creation of various objects
  // (they should probably be stored as project state properties)
  define("PROJECT_STATE_FILE", BACKEND_ROOT . "/data/project_state.json");
  define("USERS_FILE", BACKEND_ROOT . "/data/users.json");


  // Class for the project state (interface to info stored across sessions in project_state.json)
  class ProjectState {
    private $_project_state_file;
    //private $_users_file = ''; //maybe this doesn't make sense as something to store here

    // trying for sensible defaults
    private $_project_name = '';
    private $_signup_enabled = true;
    private $_max_users = 50;

    public function __construct($projstatefile) {
      $this->_project_state_file = $projstatefile;
    }

    public function read_project_state_data() {
      $pdata = json_decode(file_get_contents($this->_project_state_file),true);
      $this->_project_name = $pdata['project_name'];
      $this->_signup_enabled = $pdata['signup_enabled'];
      $this->_max_users = $pdata['max_users'];
    }

    public function write_project_state_data() {
      file_put_contents($this->_project_state_file,$this->_jsonify_project_data());
      //TODO log writing of data! state what changed?
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


    public function set_project_name($name) {
      $this->_project_name = $name;
    }

    public function enable_signup() {
      $this->_signup_enabled = true;
    }

    public function disable_signup() {
      $this->_signup_enabled = false;
    }

    public function set_max_users($n) {
      $this->_max_users = $n;
    }

    private function _jsonify_project_data() {
      $arr = [];
      $arr['project_name'] = $this->_project_name;
      $arr['signup_enabled'] = $this->_signup_enabled; //FIXME may need special handling for boolean!, though apparently not...
      $arr['max_users'] = $this->_max_users;
      return json_encode($arr);
    }
  }

  // Class for list of users; needs a method to add to that list
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
      // TODO log addition of user
    }

    public function remove_user($email_address) {
      foreach ($this->_users as $k=>$v) {
        if ($v->get_email_address() == $email_address) {
          array_splice($this->_users,$k,1); // remove just this item from $this->_users without leaving a gap in indices
          // TODO log removal of user
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

    //FIXME make private and refactor once tested
    public function read_user_data() {
      $foo = json_decode(file_get_contents($this->_users_file),true);
      foreach ($foo as $user) {
        $this->_users[] = new User ($user['first_name'],$user['last_name'],$user['email_address'],$user['pin']);
      }
    }
    
    // TODO figure out where this will actually get called from, and think about if the file and the data it writes should be added as parameters!
    //FIXME make private and refactor once tested
    public function write_user_data() {
      file_put_contents($this->_users_file,$this->_jsonify_users());
    }
    
    // TODO have this be a sensibly pretty-printed version so that the resulting file can be easily-ish human read?
    private function _jsonify_users() {
      return json_encode($this->_arrayify_user_list($this->_users));
    }

    private function _generate_user_pin($length=6) {
      $new_pin = '';
      for ($i = 0; $i < $length; $i++) {
        $new_pin .= rand(0,9);
      }
      return $new_pin;
    }
    
    private function _arrayify_user_list($user_list) {
      $arr = [];
      foreach ($user_list as $u) {
        $arr[] = $u->arrayify_user();
      }
      return $arr;
    }
  }

  // Class for user
  class User {
    private $_first_name;
    private $_last_name;
    private $_email_address;
    private $_pin;

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

    public function __construct($first_name,$last_name,$email_address,$pin) {
      $this->_first_name = $first_name;
      $this->_last_name = $last_name;
      $this->_email_address = $email_address;
      $this->_pin = $pin;
    }
    
    public function arrayify_user() {
      $arr = [];
      $arr['first_name'] = $this->_first_name;
      $arr['last_name'] = $this->_last_name;
      $arr['email_address'] = $this->_email_address;
      $arr['pin'] = $this->_pin;
      return $arr;
    }
  }

  //UserInventory setup
  $userinventory = new UserInventory(USERS_FILE);
  $userinventory->read_user_data();

  //ProjectState setup
  $projectstate = new ProjectState(PROJECT_STATE_FILE);
  $projectstate->read_project_state_data();
  


  #$fooinv->remove_user('john@smith.com');

  //$fooinv->add_user('Jane','Doe','jane@doe.com',10634);
  //$fooinv->add_user('John','Smith','john@smith.com',105832);
  //$fooinv->add_user('Rob','Johnson','rob@johnson.com',205827);

  #$fooinv->write_user_data();
  #$fooinv->remove_user('john@smith.com');

  //$fooinv->read_user_data();
  //$fooinv->write_user_data();
  

