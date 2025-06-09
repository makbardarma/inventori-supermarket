<?php
session_start();
include '../../includes/database.php';

// Cek login admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

if (!isset($_GET['id'], $_GET['action'])) {
    header('Location: produk.php');
    exit;
}

$id = (int)$_GET['id'];
$action = $_GET['action'];

if (!in_array($action, ['accept', 'reject'])) {
    header('Location: produk.php');
    exit;
}

$status = ($action === 'accept') ? 'accepted' : 'rejected';

$sql = "UPDATE products SET status = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $status, $id);
mysqli_stmt_execute($stmt);

header('Location: produk.php');
exit;
