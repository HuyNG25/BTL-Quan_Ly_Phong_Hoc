<?php
require_once __DIR__ . '/db_connect.php';

class ReportFunctions {
    private $conn;
    public function __construct() {
        $this->conn = connectDB();
    }

    public function getStats() {
        $stats = [];
        $tables = ['users', 'rooms', 'subjects', 'schedules'];
        foreach ($tables as $t) {
            $res = $this->conn->query("SELECT COUNT(*) AS total FROM $t");
            $row = $res->fetch_assoc();
            $stats[$t] = $row['total'];
        }
        return $stats;
    }
}
?>
