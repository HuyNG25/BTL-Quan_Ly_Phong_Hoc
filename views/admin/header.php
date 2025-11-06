<?php
// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phòng học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            /* **SỬA LỖI:** Thêm khoảng đệm cho body để không bị navbar cố định che mất. */
            padding-top: 65px; 
        }
        .navbar {
            background: #0d6efd;
        }
        .navbar-brand, .nav-link, .text-white {
            color: white !important;
        }
        /* Đã loại bỏ .container-fluid margin-top: 70px; */
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="dashboard_admin.php">Hệ thống quản lý phòng học</a>
    <div class="d-flex">
      <span class="text-white me-3">
        Xin chào, <?= $_SESSION['user']['fullname'] ?? 'Khách' ?>
      </span>
      <a href="../login.php" class="btn btn-sm btn-light">Đăng xuất</a>
    </div>
  </div>
</nav>

<div class="d-flex" id="wrapper">