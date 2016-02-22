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
  require_once "global_functions.php";
  require_once "ProjectState.php";
  require_once "UserInventory.php";

  // Set these project variables and run this file as `php init_new_project.php` to initialize Absmaster
  $PROJECT_NAME = 'Fall Seminar Abstracts';
  $ADMIN_EMAIL = 'admin@admin.net';
  $ADMIN_PIN = '123456';

  if (file_exists(PROJECT_STATE_FILE) || file_exists(USERS_FILE)) {
    die("This project already seems to be initiated; data files exist.\n");
  }

  if (filter_var($ADMIN_EMAIL,FILTER_VALIDATE_EMAIL) == false) {
    die("Please use a valid email address for the admin email.\n");
  }
  if (is_numeric($ADMIN_PIN) == false) {
    die("Please choose a numerical admin pin.\n");
  }

  $initiated_project = new ProjectState(PROJECT_STATE_FILE);
  $initiated_project->set_project_name($PROJECT_NAME);
  $initiated_project->set_admin_email($ADMIN_EMAIL);
  $initiated_project->set_admin_pin($ADMIN_PIN);
  $initiated_project->write_project_state_data();

  $initiated_user_inventory = new UserInventory(USERS_FILE);
  $initiated_user_inventory->write_user_data();

  echo "Project \"$PROJECT_NAME\" has now been initialized with admin email \"$ADMIN_EMAIL\" and admin PIN \"$ADMIN_PIN\".\n";

