<?php
  function log_msg($file,$msg) {
    $t = DateTime::ISO8601; // format to use for time
    $line = date($t) . ' ' . $msg . "\n";
    file_put_contents($file,$line,FILE_APPEND);
  }

  function file_is_pdf($file) {
    if (mime_content_type($file) == 'application/pdf') {
      return true;
    } else {
      return false;
    }
  }

