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
  // FIXME define the paths in some kind of environment file instead of hard coding them
  define("BACKEND_ROOT", "/home/chris/Documents/jobs/work_study/abstract_reviewer/absmaster/absmaster_backend");
  define("FRONTEND_ROOT", "/home/chris/Documents/jobs/work_study/abstract_reviewer/absmaster/www/absmaster");

  // some constants I'll use in here, but should refactor later to have passed down the line on creation of various objects
  // (they should probably be stored as project state properties)
  define("PROJECT_STATE_FILE", BACKEND_ROOT . "/data/project_state.json");
  define("USERS_FILE", BACKEND_ROOT . "/data/users.json");
  define("USER_LOG", BACKEND_ROOT . '/data/user.log');
  define("PROJECT_LOG", BACKEND_ROOT . '/data/project.log');

