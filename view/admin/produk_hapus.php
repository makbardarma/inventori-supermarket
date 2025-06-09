<?php
session_start();
include '../../includes/database.php';

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

// Cek apakah ada parameter id produk
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: produk.php');
    exit;
}

$id = (int)$_GET['id'];

// Hapus produk berdasarkan id
$query = "DELETE FROM products WHERE id = $id";
if (mysqli_query($conn, $query)) {
    // Redirect ke halaman produk dengan pesan sukses (bisa pakai session jika mau)
    header('Location: produk.php?msg=hapus_berhasil');
    exit;
} else {
    // Jika gagal hapus, bisa tampilkan error atau redirect
    echo "Gagal menghapus produk: " . mysqli_error($conn);
}
