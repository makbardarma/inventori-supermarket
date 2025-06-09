<?php
session_start();
include '../../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM users WHERE id = $id AND role = 'user'");
header('Location: kelola_user.php');
exit;
