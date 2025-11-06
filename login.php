<?php
session_start();
// Giữ nguyên logic kiểm tra session và chuyển hướng
if (isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Hệ Thống - Quản lý phòng học</title>
    <style>
        /* ==== Biến CSS (Tùy chọn) ==== */
        :root {
            --primary-blue: #0047a0;
            --light-blue: #e8f0fe;
            --border-color: #d1d5db;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
        }

        /* ==== Toàn trang ==== */
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Inter', sans-serif;
            /* Thay đổi nền thành màu trắng/xanh nhạt tinh tế */
            background: #f7f9fc;
        }

        /* ==== Khung chính (Card) ==== */
        .login-box {
            background: white;
            padding: 50px 50px;
            /* Bo góc lớn, shadow tinh tế */
            border-radius: 20px;
            width: 380px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            animation: slideIn 0.5s ease-out;
            border: 1px solid #e5e7eb;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ==== Logo/Icon ==== */
        .logo-container {
            margin-bottom: 25px;
        }
        .logo-icon {
            /* Sử dụng SVG đơn giản cho logo geometric */
            display: inline-block;
            width: 48px;
            height: 48px;
            color: var(--primary-blue);
            background-color: var(--light-blue);
            border-radius: 12px;
            padding: 8px;
        }

        /* Icon SVG đơn giản (tượng trưng cho hệ thống/quản lý) */
        .logo-icon svg {
            width: 100%;
            height: 100%;
            fill: currentColor;
        }

        /* ==== Tiêu đề và Phụ đề ==== */
        h2 {
            color: var(--text-dark);
            font-size: 26px;
            font-weight: 700;
            margin: 0 0 5px 0;
        }
        .subtitle {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 35px;
            font-weight: 500;
        }

        /* ==== Nhóm input ==== */
        .input-group {
            text-align: left;
            margin-bottom: 25px;
        }

        label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 15px;
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
            background-color: #f9fafb;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(0, 71, 160, 0.2);
            outline: none;
            background-color: white;
        }

        /* ==== Nút Đăng nhập ==== */
        input[type="submit"] {
            background: var(--primary-blue);
            border: none;
            color: white;
            width: 100%;
            padding: 15px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: 0.3s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        input[type="submit"]:hover {
            background: #003880;
            box-shadow: 0 8px 20px rgba(0, 71, 160, 0.3);
            transform: translateY(-1px);
        }

        .submit-icon {
            margin-right: 8px;
            font-size: 18px;
            position: relative;
            top: 1px;
        }

        /* ==== Lỗi ==== */
        .error {
            color: #ef4444;
            background: #fee2e2;
            border: 1px solid #fecaca;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            margin-bottom: 25px;
        }

        /* ==== Link Đăng ký ==== */
        .register-link {
            margin-top: 30px;
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 500;
        }
        .register-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s;
        }
        .register-link a:hover {
            color: #003880;
            text-decoration: underline;
        }

        /* ==== Responsive ==== */
        @media (max-width: 480px) {
            .login-box {
                width: 90%;
                padding: 30px 20px;
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <!-- Logo Icon -->
        <div class="logo-container">
            <span class="logo-icon">
                <!-- Icon Tượng trưng: hình khối geometric (như trong ảnh) -->
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 1L2 6.5V17.5L12 23L22 17.5V6.5L12 1ZM12 3.32L19.26 7.42V16.58L12 20.68L4.74 16.58V7.42L12 3.32ZM12 12L17.2 9.1V14.9L12 17.8L6.8 14.9V9.1L12 12Z" fill="currentColor"/>
                </svg>
            </span>
        </div>

        <!-- Tiêu đề -->
        <h2>Đăng Nhập Hệ Thống</h2>
        <div class="subtitle">Quản Lý Phòng Học Đại Học</div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="handles/handle_login.php" method="POST">
            <div class="input-group">
                <label>Tên Đăng Nhập</label>
                <input type="text" name="email" required placeholder="Nhập tên đăng nhập">
            </div>

            <div class="input-group">
                <label>Mật Khẩu</label>
                <input type="password" name="password" required placeholder="Nhập mật khẩu">
            </div>

            <button type="submit" value="Đăng nhập" style="all: unset; width: 100%; display: block; margin-top: 10px;">
                <input type="submit" value="Đăng Nhập">
            </button>
        </form>

        <!-- Link Đăng ký -->
        <div class="register-link">
            Chưa có tài khoản? <a href="#">Đăng ký ngay</a>
        </div>
    </div>
</body>
</html>