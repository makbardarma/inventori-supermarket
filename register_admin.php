<?php
include 'includes/database.php';

$success = '';
$error = '';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash aman

    // Cek apakah username/email sudah dipakai
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' OR email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username atau email sudah digunakan.";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO users (username, email, password, role) VALUES 
            ('$username', '$email', '$password', 'admin')");

        if ($insert) {
            $success = "Admin berhasil didaftarkan. Silakan login.";
        } else {
            $error = "Gagal menyimpan data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Register Admin</title>
</head>

<body>
    <h2>Form Registrasi Admin</h2>

    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
        <a href="login.php">➡️ Login Sekarang</a>
    <?php elseif ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit" name="register">Daftarkan Admin</button>
    </form>
</body>

</html>