<?php 
  session_start();
  require_once "include_path.php";
  require_once "absmaster.php";
  require_once "batchdownload.php";

  function download_review_by_id($id) {
    if (is_numeric($id)) {
      $file = BACKEND_ROOT . '/reviews/' . $id . '.pdf';
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

  function download_reviews_batch($user_inv) {
    $reviews_dir = BACKEND_ROOT . '/reviews';
    $staged_dir = stage_batch_review_download($user_inv,$reviews_dir);
    $zipped_file = create_zip_from_dir_contents($staged_dir,'AllSubmittedReviews');

    if (file_exists($zipped_file)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename($zipped_file).'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($zipped_file));
      readfile($zipped_file);
    }
    cleanup_and_rm_temp_dir($staged_dir);
    exit;
  }


  if (isset($_GET['batchdownload']) && $_GET['batchdownload'] == 'true') {
    if (isset($_SESSION['admin_pin']) && $PROJECTSTATE->admin_pin_validate($_SESSION['admin_pin'])) {
      download_reviews_batch($USERINVENTORY);
    } else {
      die('Access denied');
    }
  } elseif (isset($_GET['id'])) {
    download_review_by_id($_GET['id']);
  }

?>
