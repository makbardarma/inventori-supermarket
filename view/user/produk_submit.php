<?php
session_start();
include '../../includes/database.php';

// Cek login dan role user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: loginuser.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: produk.php');
    exit;
}

$id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

// Cek apakah produk milik user dan statusnya masih draft
$query = "SELECT * FROM products WHERE id = $id AND user_id = $user_id AND status = 'draft'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    // Produk tidak ditemukan atau sudah disubmit
    header('Location: produk.php');
    exit;
}

// Update status menjadi submitted
$update = "UPDATE products SET status = 'submitted' WHERE id = $id";
if (mysqli_query($conn, $update)) {
    $_SESSION['message'] = 'Produk berhasil disubmit dan tidak bisa diedit lagi.';
} else {
    $_SESSION['message'] = 'Gagal submit produk.';
}

header('Location: produk.php');
exit;
