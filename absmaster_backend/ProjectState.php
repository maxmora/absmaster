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

    public function set_admin_email($email) {
      $this->_admin_email = $email;
    }

    public function set_admin_pin($pin) {
      $this->_admin_pin = $pin;
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
