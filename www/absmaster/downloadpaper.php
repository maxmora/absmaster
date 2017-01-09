<?php 
  session_start();
  require_once "include_path.php";
  require_once "absmaster.php";
  require_once "batchdownload.php";

  function download_paper_by_id($id, $user_inventory) {
    if (is_numeric($id)) {
      $file = BACKEND_ROOT . '/submissions/' . $id . '.pdf';
      // TODO set filename as the paper title

      $ids_to_emails = $user_inventory->get_paper_ids_and_author_emails();
      $paper_author = $user_inventory->get_user_by_email_address($ids_to_emails[$id]);
      $paper_title = $paper_author->get_uploaded_paper()['title'];

      // for file name, replace non-alphanumeric characters with underscore
      $alphanumeric_title = preg_replace('([^a-zA-Z0-9])', '_', $paper_title);

      if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$alphanumeric_title.'.pdf'.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
      }
    } else {
      die('Requested file does not exist.');
    }
  }

  function download_papers_batch($user_inv) {
    $submission_dir = BACKEND_ROOT . '/submissions';
    $staged_dir = stage_batch_paper_download($user_inv,$submission_dir);
    $zipped_file = create_zip_from_dir_contents($staged_dir,'AllSubmittedPapers');
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
      download_papers_batch($USERINVENTORY);
    } else {
      die('Access denied');
    }
  } elseif (isset($_GET['id'])) {
    // admin can do anything they want
    if (isset($_SESSION['admin_pin']) && $PROJECTSTATE->admin_pin_validate($_SESSION['admin_pin'])) {
            download_paper_by_id($_GET['id'], $USERINVENTORY);
    }

    // make sure normal user is allowed to download this paper
    $user_email = $_SESSION['user_email'];
    $reviewer_assignments = read_reviewer_assignments();
    $assigned_author_emails = compile_papers_for_reviewer_by_email_address($reviewer_assignments, $user_email);
    foreach ($assigned_author_emails as $author_email) {
      $author = $USERINVENTORY->get_user_by_email_address($author_email);
      $paper_id = $author->get_uploaded_paper()['id'];

      if ($paper_id == $_GET['id']) {
        download_paper_by_id($_GET['id'], $USERINVENTORY);
      }
    }

    // we fell through, so the user wasn't allowed to download this paper.
    die('You are not allowed to access this paper.');
  }

  die('Access denied');
  

?>
