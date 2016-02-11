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


