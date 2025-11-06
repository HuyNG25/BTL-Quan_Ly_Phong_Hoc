<?php
session_start();
require_once __DIR__ . '/../functions/ReportFunctions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$reportFn = new ReportFunctions();
$stats = $reportFn->getStats();
?>
