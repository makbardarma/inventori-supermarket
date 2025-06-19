<?php
session_start();
include '../../includes/database.php';
$error = '';


// Cek apakah user sudah login
if (isset($_POST['login'])) {
    $usernameOrEmail = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    // Validasi input
    $query = "SELECT * FROM users WHERE username = '$usernameOrEmail' OR email = '$usernameOrEmail'";
    $result = mysqli_query($conn, $query);
    // Cek apakah ada user dengan username atau email tersebut
    if (mysqli_num_rows($result) === 1) { // Jika ada, ambil data user
        // Ambil data user
        $user = mysqli_fetch_assoc($result); // Cek apakah password yang dimasukkan sesuai
        if (password_verify($password, $user['password'])) {
            if ($user['role'] === 'user') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Akun ini bukan akun user.";
            }
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Username atau email tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login User - Inventaris Supermarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #d0e7ff, #ffffff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background: #fff;
            padding: 3rem 3.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 119, 255, 0.15);
            width: 100%;
            max-width: 420px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 40px rgba(0, 119, 255, 0.3);
        }

        .logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
            color: #0066ff;
        }

        .logo i {
            font-size: 28px;
            margin-right: 10px;
        }

        .logo h2 {
            font-weight: 700;
            font-size: 24px;
            margin: 0;
            letter-spacing: 1.2px;
        }

        label {
            color: #0056b3;
            font-weight: 600;
            text-align: left;
            display: block;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid #a9c9ff;
            background-color: #f0f8ff;
            padding: 12px 15px;
            font-size: 1rem;
            margin-bottom: 1.5rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #0066ff;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 102, 255, 0.4);
            outline: none;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            font-weight: 700;
            border-radius: 14px;
            background-color: #0066ff;
            color: white;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0, 102, 255, 0.4);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-login:hover {
            background-color: #004db8;
            box-shadow: 0 8px 30px rgba(0, 77, 184, 0.6);
        }

        .alert {
            margin-bottom: 1.5rem;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border: 1px solid #cce0ff;
            background-color: #e6f0ff;
            color: #004a99;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="login-container" role="main" aria-label="Form login user">
        <div class="logo" aria-label="Logo aplikasi">
            <i class="fas fa-user-circle" aria-hidden="true"></i>
            <h2>LOGIN USER</h2>
        </div>

        <?php if ($error): ?>
            <div class="alert" role="alert"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <label for="username">Username atau Email</label>
            <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username/email" required autocomplete="username" autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required autocomplete="current-password">

            <button type="submit" name="login" class="btn-login" aria-label="Tombol login user">
                <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>Login
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>