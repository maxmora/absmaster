<?php 
  require_once "include_path.php";
  require_once "absmaster.php";

  function download_paper_by_id($id) {
    if (is_numeric($id)) {
      $file = BACKEND_ROOT . '/submissions/' . $id . '.pdf';
      if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
      }
    } else {
      die('Access denied');
    }
  }

  download_paper_by_id($_GET['id']);
?>
