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

  // function to create zip archive from a folder; return the file location or null on error
  function create_zip_from_dir_contents($dir,$zipfile_basename) {
    $zip_archive_path = $dir . '/' . $zipfile_basename . '.zip';

    $zipfile = new ZipArchive();
    $zipfile->open($zip_archive_path,ZipArchive::CREATE);
    foreach (scandir($dir) as $f) {
      $f_path = $dir . '/' . $f;
      if (file_is_pdf($f_path)) {
        $zipfile->addFile($f_path,$zipfile_basename.'/'.$f);
      }
    }
    $zipfile->close();

    if (file_exists($zip_archive_path)) {
      return $zip_archive_path;
    } else {
      return false;
    }
  }


  // make a tmp directory with all of the papers first_name . last_name . paper_id
  function stage_batch_paper_download($user_inv,$submission_dir) {
    // make unique name for temp staging directory
    $staged_dir = sys_get_temp_dir() . '/' . uniqid('php_');
    mkdir($staged_dir);

    // copy all files there with renaming
    foreach ($user_inv->get_users() as $u) {
      $id = $u->get_uploaded_paper()['id'];
      $uploaded_file_name = $submission_dir . '/' . $id . '.pdf';
      $new_file_name = $staged_dir . '/' . $u->get_first_name() . $u->get_last_name() . $id . '.pdf';
      copy($uploaded_file_name,$new_file_name);
    }

    return $staged_dir;
  }

  function stage_batch_review_download() {
    // TODO implement
  }

  function cleanup_and_rm_temp_dir($dir) {
    if (file_exists($dir)) {
      $contents_files = scandir($dir);
      foreach ($contents_files as $f) {
        if ($f != '.' && $f != '..') {
          unlink($dir . '/' . $f);
        }
      }
      return rmdir($dir);
    } else {
      return false;
    }
  }


