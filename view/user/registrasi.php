<?php
include '../../includes/database.php';

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user';

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username atau email sudah terdaftar.";
    } else {
        $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            $success = "Registrasi berhasil. Silakan login.";
        } else {
            $error = "Terjadi kesalahan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registrasi User - Inventaris Supermarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #d0e7ff, #ffffff);
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card-register {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 119, 255, 0.15);
            width: 100%;
            max-width: 420px;
            padding: 3rem 3.5rem;
            text-align: center;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .card-register:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 40px rgba(0, 119, 255, 0.3);
        }

        h2 {
            color: #0056b3;
            font-weight: 700;
            margin-bottom: 2rem;
            letter-spacing: 1.2px;
        }

        label {
            font-weight: 600;
            color: #003d80;
            text-align: left;
            display: block;
            margin-bottom: 0.4rem;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid #a9c9ff;
            box-shadow: none !important;
            transition: border-color 0.3s ease;
            padding: 10px 15px;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: #0066ff;
            box-shadow: 0 0 10px rgba(0, 102, 255, 0.4);
            outline: none;
        }

        .btn-register {
            background: #0066ff;
            color: white;
            font-weight: 700;
            border-radius: 14px;
            padding: 0.7rem;
            width: 100%;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 6px 20px rgba(0, 102, 255, 0.4);
        }

        .btn-register:hover {
            background: #004db8;
            box-shadow: 0 8px 30px rgba(0, 77, 184, 0.6);
        }

        .message-error {
            margin-bottom: 1.3rem;
            font-weight: 600;
            color: #b32121;
            background-color: #ffe1e1;
            border-radius: 10px;
            padding: 0.8rem 1.2rem;
            border: 1px solid #f5a9a9;
        }

        .message-success {
            margin-bottom: 1.3rem;
            font-weight: 600;
            color: #198754;
            background-color: #d4edda;
            border-radius: 10px;
            padding: 0.8rem 1.2rem;
            border: 1px solid #c3e6cb;
        }

        p.text-center {
            margin-top: 1.5rem;
            font-weight: 500;
            color: #0056b3;
        }

        p.text-center a {
            color: #0066ff;
            text-decoration: none;
            font-weight: 600;
        }

        p.text-center a:hover {
            text-decoration: underline;
        }

        @media (max-width: 450px) {
            .card-register {
                padding: 2rem 2rem;
            }
        }
    </style>
</head>

<body>
    <main class="card-register" role="main" aria-label="Form registrasi user">
        <h2>Registrasi User</h2>

        <?php if ($error): ?>
            <div class="message-error" role="alert"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="message-success" role="alert"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-4 text-start">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required autocomplete="username" autofocus />
            </div>

            <div class="mb-4 text-start">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required autocomplete="email" />
            </div>

            <div class="mb-4 text-start">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required autocomplete="new-password" />
            </div>

            <button type="submit" name="register" class="btn-register" aria-label="Tombol daftar">Daftar</button>
        </form>

        <p class="text-center">
            Sudah punya akun? <a href="loginuser.php" aria-label="Link ke halaman login">Masuk di sini</a>
        </p>
    </main>
</body>

</html>