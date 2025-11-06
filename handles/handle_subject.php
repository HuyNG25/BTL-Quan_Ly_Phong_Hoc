<?php
session_start();
require_once __DIR__ . '/../functions/SubjectFunctions.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$subFn = new SubjectFunctions();

// Add
if (isset($_POST['add_subject'])) {
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);
    $credits = intval($_POST['credits']);
    $lecturer_id = !empty($_POST['lecturer_id']) ? intval($_POST['lecturer_id']) : null;

    if ($subFn->addSubject($subject_code, $subject_name, $credits, $lecturer_id)) {
        header("Location: ../views/subjects.php?msg=added");
    } else {
        header("Location: ../views/subjects.php?msg=error");
    }
    exit;
}

// Update
if (isset($_POST['update_subject'])) {
    $id = intval($_POST['subject_id']);
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);
    $credits = intval($_POST['credits']);
    $lecturer_id = !empty($_POST['lecturer_id']) ? intval($_POST['lecturer_id']) : null;

    if ($subFn->updateSubject($id, $subject_code, $subject_name, $credits, $lecturer_id)) {
        header("Location: ../views/subjects.php?msg=updated");
    } else {
        header("Location: ../views/subjects.php?msg=error");
    }
    exit;
}

// Delete
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if ($subFn->deleteSubject($id)) {
        header("Location: ../views/subjects.php?msg=deleted");
    } else {
        header("Location: ../views/subjects.php?msg=error");
    }
    exit;
}
?>
