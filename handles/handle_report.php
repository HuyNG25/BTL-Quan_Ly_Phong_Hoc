<?php
// Bắt buộc phải có file chứa hàm kết nối DB và ReportFunctions
require_once __DIR__ . '/db_connect.php'; 
require_once __DIR__ . '/ReportFunctions.php'; // Giả định file class ReportFunctions nằm cùng cấp

// 1. Kiểm tra phương thức yêu cầu (Chỉ cho phép POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
    exit;
}

// 2. Kiểm tra các tham số cần thiết
if (!isset($_POST['report_id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin báo cáo hoặc hành động.']);
    exit;
}

$reportId = (int)$_POST['report_id'];
$action = $_POST['action'];

// 3. Khởi tạo đối tượng chức năng
$reportFunc = new ReportFunctions();

try {
    $result = false;
    $message = '';

    // 4. Xử lý các hành động
    switch ($action) {
        case 'resolve':
            // Chuyển trạng thái báo cáo sang 'resolved'
            $result = $reportFunc->updateReportStatus($reportId, 'resolved');
            $message = 'Báo cáo đã được đánh dấu là **Đã giải quyết**.';
            break;
            
        case 'dismiss':
            // Chuyển trạng thái báo cáo sang 'dismissed' (bỏ qua)
            $result = $reportFunc->updateReportStatus($reportId, 'dismissed');
            $message = 'Báo cáo đã được **Bỏ qua**.';
            break;
            
        case 'pending':
             // Chuyển trạng thái báo cáo sang 'pending' (đang chờ)
            $result = $reportFunc->updateReportStatus($reportId, 'pending');
            $message = 'Báo cáo đã được chuyển lại trạng thái **Đang chờ**.';
            break;

        // Có thể thêm các case khác như 'view_detail' nếu cần AJAX
            
        default:
            $message = 'Hành động không xác định.';
            $result = false;
    }

    // 5. Trả về kết quả
    if ($result) {
        // Thông báo thành công và chuyển hướng về trang reports_admin.php
        $_SESSION['success_message'] = $message;
        header('Location: reports_admin.php');
        exit;
    } else {
        // Xử lý thất bại (ví dụ: không tìm thấy ID, lỗi DB)
        $_SESSION['error_message'] = 'Lỗi khi xử lý báo cáo. Vui lòng kiểm tra lại ID hoặc kết nối CSDL.';
        header('Location: reports_admin.php');
        exit;
    }

} catch (Exception $e) {
    // Xử lý lỗi ngoại lệ
    $_SESSION['error_message'] = 'Lỗi hệ thống: ' . $e->getMessage();
    header('Location: reports_admin.php');
    exit;
}
?>
