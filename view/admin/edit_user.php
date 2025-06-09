<?php
session_start();
include '../../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

$id = $_GET['id'];
$query = "SELECT * FROM users WHERE id = $id AND role = 'user' LIMIT 1";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("User tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $query = "UPDATE users SET username = '$username', email = '$email' WHERE id = $id";
    mysqli_query($conn, $query);
    header('Location: kelola_user.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">
    <h2>✏️ Edit User</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-warning">Update</button>
        <a href="kelola_user.php" class="btn btn-secondary">Batal</a>
    </form>
</body>

</html>