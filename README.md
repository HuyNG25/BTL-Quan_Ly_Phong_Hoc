<h2 align="center">
    <a href="https://dainam.edu.vn/vi/khoa-cong-nghe-thong-tin">
    🎓 Faculty of Information Technology (DaiNam University)
    </a>
</h2>
<h2 align="center">
    Youth Union Member Management
</h2>
<div align="center">
    <p align="center">
        <img src="/aiotlab_logo.png" alt="AIoTLab Logo" width="170"/>
        <img src="/fitdnu_logo.png" alt="AIoTLab Logo" width="180"/>
        <img src="/dnu_logo.png" alt="DaiNam University Logo" width="200"/>
    </p>

[![AIoTLab](https://img.shields.io/badge/AIoTLab-green?style=for-the-badge)](https://www.facebook.com/DNUAIoTLab)
[![Faculty of Information Technology](https://img.shields.io/badge/Faculty%20of%20Information%20Technology-blue?style=for-the-badge)](https://dainam.edu.vn/vi/khoa-cong-nghe-thong-tin)
[![DaiNam University](https://img.shields.io/badge/DaiNam%20University-orange?style=for-the-badge)](https://dainam.edu.vn)

</div>
 
## 📖 1. Giới thiệu
Hệ thống Quản lý Phòng Học giúp tối ưu hóa việc sử dụng cơ sở vật chất bằng cách tự động hóa sắp xếp và loại bỏ xung đột phòng học. Mang lại sự minh bạch và tiện lợi khi cho phép tra cứu và đặt phòng trực tuyến theo thời gian thực. Điều này giúp toàn bộ trường tiết kiệm thời gian quản lý và nâng cao đáng kể chất lượng dịch vụ cho giảng viên và sinh viên.

## 🔧 2. Các công nghệ được sử dụng
<div align="center">

### Hệ điều hành
![macOS](https://img.shields.io/badge/macOS-000000?style=for-the-badge&logo=macos&logoColor=F0F0F0)
[![Windows](https://img.shields.io/badge/Windows-0078D6?style=for-the-badge&logo=windows&logoColor=white)](https://www.microsoft.com/en-us/windows/)
[![Ubuntu](https://img.shields.io/badge/Ubuntu-E95420?style=for-the-badge&logo=ubuntu&logoColor=white)](https://ubuntu.com/)

### Công nghệ chính
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](#)
[![CSS](https://img.shields.io/badge/CSS-1572B6?style=for-the-badge&logo=css3&logoColor=white)](#)
[![SCSS](https://img.shields.io/badge/SCSS-CC6699?style=for-the-badge&logo=sass&logoColor=white)](#)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](#)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)

### Web Server & Database
[![Apache](https://img.shields.io/badge/Apache-D22128?style=for-the-badge&logo=apache&logoColor=white)](https://httpd.apache.org/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/) 
[![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)](https://www.apachefriends.org/)

### Database Management Tools
[![MySQL Workbench](https://img.shields.io/badge/MySQL_Workbench-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://dev.mysql.com/downloads/workbench/)
</div>

## 🚀 3. Hình ảnh các chức năng
### Trang đăng nhập
<img width="1902" height="931" alt="image" src="./login.png" />
### Trang index
<img width="1885" height="467" alt="image" src="" />
<img width="1885" height="467" alt="image" src="" />
### Trang dashboard admin
<img width="1886" height="940" alt="image" src="./dashboard_admin.png" />
### Trang thông tin tài khoản    
<img width="1890" height="932" alt="image" src="./user.png" />
### Trang người dùng 
<img width="1901" height="939" alt="image" src="./nguoidung.png" />
### Trang quản lý phòng học
<img width="1892" height="938" alt="image" src="./phonghoc.png" />
### Trang quản lý môn học
<img width="1882" height="937" alt="image" src="" />
### Trang quản lý lịch học
<img width="1882" height="937" alt="image" src="" />
### Trang quản lý thời khoá biểu cá nhân
<img width="1882" height="937" alt="image" src="" />
### Trang báo cáo & thống kê
<img width="1882" height="937" alt="image" src="" />
### Thông báo hệ thống
<img width="1882" height="937" alt="image" src="./thongbao.png" />
## ⚙️ 4. Cài đặt

### 4.1. Cài đặt công cụ, môi trường và các thư viện cần thiết

- Tải và cài đặt **XAMPP**  
  👉 https://www.apachefriends.org/download.html  
  (Khuyến nghị bản XAMPP với PHP 8.x)

- Cài đặt **Visual Studio Code** và các extension:
  - PHP Intelephense  
  - MySQL  
  - Prettier – Code Formatter  
### 4.2. Tải project
Clone project về thư mục `htdocs` của XAMPP (ví dụ ổ C):

```bash
cd C:\xampp\htdocs
https://github.com/HuyNG25/BTL-Quan_Ly_Phong_Hoc.git
Truy cập project qua đường dẫn:
👉 http://localhost/QuanLyPhongHoc
```
### 4.3. Setup database
Mở XAMPP Control Panel, Start Apache và MySQL

Truy cập MySQL WorkBench
Tạo database:
```bash
CREATE DATABASE IF NOT EXISTS quanlyphonghoc
   CHARACTER SET utf8mb4
   COLLATE utf8mb4_unicode_ci;
```

### 4.4. Setup tham số kết nối
Mở file db_connect.php (hoặc .env) trong project, chỉnh thông tin DB:
```bash

<?php
    function getDbConnection() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "quanlyphonghoc";
        $port = 3306;
        $conn = mysqli_connect($servername, $username, $password, $dbname, $port);
        if (!$conn) {
            die("Kết nối database thất bại: " . mysqli_connect_error());
        }
        mysqli_set_charset($conn, "utf8");
        return $conn;
    }
?>
```
### 4.5. Chạy hệ thống
Mở XAMPP Control Panel → Start Apache và MySQL

Truy cập hệ thống:
👉 http://localhost/index.php

### 4.6. Đăng nhập lần đầu
Hệ thống có thể cấp tài khoản admin 

Sau khi đăng nhập Admin có thể:

Tạo thông tin tổ chức đoàn (Đoàn trường, Liên chi, Chi đoàn)

Thêm đoàn viên và cấp tài khoản

Quản lý phân quyền theo cấp
    
