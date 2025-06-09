<?php
session_start();
include '../../includes/database.php';

// Cek apakah user sudah login dan memiliki role user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: loginuser.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Validasi ID produk dari parameter GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ID produk tidak valid.";
    header('Location: produk.php');
    exit;
}

$id = (int) $_GET['id'];

// Cek apakah produk tersebut milik user dan belum dihapus
$cek = mysqli_query($conn, "SELECT * FROM products WHERE id = $id AND user_id = $user_id AND is_deleted = 0");

if (mysqli_num_rows($cek) === 0) {
    $_SESSION['message'] = "Produk tidak ditemukan atau sudah dihapus sebelumnya.";
    header('Location: produk.php');
    exit;
}

// Jalankan soft delete dengan mengubah is_deleted menjadi 1
$hapus = mysqli_query($conn, "UPDATE products SET is_deleted = 1 WHERE id = $id AND user_id = $user_id");

if ($hapus) {
    $_SESSION['message'] = "Produk berhasil dihapus (nonaktif).";
} else {
    $_SESSION['message'] = "Gagal menghapus produk.";
}

// Redirect kembali ke halaman produk user
header('Location: produk.php');
exit;
