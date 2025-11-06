<?php
session_start();
require_once __DIR__ . '/../functions/RoomFunctions.php';

// Optionally check permission: admin only or admin+staff
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$roomFn = new RoomFunctions();

// Add
if (isset($_POST['add_room'])) {
    $room_name = trim($_POST['room_name']);
    $type = $_POST['type'] ?? 'LT';
    $capacity = intval($_POST['capacity']);
    $equipment = trim($_POST['equipment']);
    $status = $_POST['status'] ?? 'trong';

    if ($roomFn->addRoom($room_name, $type, $capacity, $equipment, $status)) {
        header("Location: ../views/rooms.php?msg=added");
    } else {
        header("Location: ../views/rooms.php?msg=error");
    }
    exit;
}

// Update
if (isset($_POST['update_room'])) {
    $id = intval($_POST['room_id']);
    $room_name = trim($_POST['room_name']);
    $type = $_POST['type'] ?? 'LT';
    $capacity = intval($_POST['capacity']);
    $equipment = trim($_POST['equipment']);
    $status = $_POST['status'] ?? 'trong';

    if ($roomFn->updateRoom($id, $room_name, $type, $capacity, $equipment, $status)) {
        header("Location: ../views/rooms.php?msg=updated");
    } else {
        header("Location: ../views/rooms.php?msg=error");
    }
    exit;
}

// Delete
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if ($roomFn->deleteRoom($id)) {
        header("Location: ../views/rooms.php?msg=deleted");
    } else {
        header("Location: ../views/rooms.php?msg=error");
    }
    exit;
}
?>
