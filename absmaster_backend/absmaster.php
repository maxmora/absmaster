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

  require_once "local_env.php";
  require_once "UserInventory.php";
  require_once "User.php";
  require_once "ProjectState.php";
  require_once "reviews.php";

  function log_msg($file,$msg) {
    $t = DateTime::ISO8601; // format to use for time
    $line = date($t) . ' ' . $msg . "\n";
    file_put_contents($file,$line,FILE_APPEND);
  }


  //UserInventory setup
  $USERINVENTORY = new UserInventory(USERS_FILE);
  $USERINVENTORY->read_user_data();

  //ProjectState setup
  $PROJECTSTATE = new ProjectState(PROJECT_STATE_FILE);
  $PROJECTSTATE->read_project_state_data();
  

