<?php
require_once __DIR__ . '/db_connect.php';

class RoomFunctions {
    private $conn;

    public function __construct() {
        $this->conn = connectDB();
    }

    public function getAllRooms() {
        $sql = "SELECT room_id, room_name, type, capacity, equipment, status FROM rooms ORDER BY room_id DESC";
        $res = $this->conn->query($sql);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function addRoom($room_name, $type, $capacity, $equipment, $status = 'trong') {
        $stmt = $this->conn->prepare("INSERT INTO rooms (room_name, type, capacity, equipment, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $room_name, $type, $capacity, $equipment, $status);
        return $stmt->execute();
    }

    public function getRoomById($id) {
        $stmt = $this->conn->prepare("SELECT room_id, room_name, type, capacity, equipment, status FROM rooms WHERE room_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateRoom($id, $room_name, $type, $capacity, $equipment, $status) {
        $stmt = $this->conn->prepare("UPDATE rooms SET room_name=?, type=?, capacity=?, equipment=?, status=? WHERE room_id=?");
        $stmt->bind_param("ssissi", $room_name, $type, $capacity, $equipment, $status, $id);
        return $stmt->execute();
    }

    public function deleteRoom($id) {
        $stmt = $this->conn->prepare("DELETE FROM rooms WHERE room_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
