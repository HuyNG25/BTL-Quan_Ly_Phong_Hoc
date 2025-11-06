<?php
session_start();
require_once __DIR__ . '/../functions/TimetableFunctions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$timetableFn = new TimetableFunctions();
$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;
$room_id = $_GET['room_id'] ?? null;
$lecturer_id = $_GET['lecturer_id'] ?? null;

$data = $timetableFn->getTimetable($from, $to, $room_id, $lecturer_id);
?>
