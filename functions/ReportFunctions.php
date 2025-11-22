<?php
// Giả định file này nằm trong một thư mục có thể truy cập db_connect.php
require_once __DIR__ . '/db_connect.php'; 

class ReportFunctions {
    private $conn;

    public function __construct() {
        // Đảm bảo kết nối database
        $this->conn = connectDB(); 
    }

    /**
     * Lấy thống kê số lượng bản ghi từ các bảng quan trọng
     * @return array Mảng thống kê
     */
    public function getStats() {
        $stats = [];
        // Thêm 'notifications' và 'reports' vào danh sách các bảng cần thống kê
        $tables = ['users', 'rooms', 'subjects', 'schedules', 'notifications', 'reports']; 
        
        foreach ($tables as $t) {
            // Sử dụng truy vấn an toàn (dù tên bảng cố định nên không lo SQL Injection)
            $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM {$t}");
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stats[$t] = $row['total'];
            $stmt->close();
        }

        // Lấy thêm thống kê đặc biệt cho reports (Pending và Resolved)
        $stats['reports_pending'] = $this->countReportsByStatus('pending');
        $stats['reports_resolved'] = $this->countReportsByStatus('resolved');
        
        return $stats;
    }

    /**
     * Đếm số lượng báo cáo theo trạng thái
     * @param string $status Trạng thái báo cáo ('pending', 'resolved', 'dismissed')
     * @return int Số lượng báo cáo
     */
    public function countReportsByStatus($status) {
        $count = 0;
        // status là ENUM('pending', 'resolved', 'dismissed')
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM reports WHERE status = ?");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $count = $row['total'];
        }
        $stmt->close();
        return $count;
    }
}
?>