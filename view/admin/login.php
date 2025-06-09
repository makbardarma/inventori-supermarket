<?php
session_start();
include __DIR__ . '/../../includes/database.php';

$error = '';

if (isset($_POST['login'])) {
    $usernameOrEmail = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$usernameOrEmail' OR email = '$usernameOrEmail'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: dashboard.php");
                exit;
            } else {
                header("Location: view/user/dashboard.php");
                exit;
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
    <title>Login - Inventaris Supermarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            /* Background degradasi biru ke putih */
            background: linear-gradient(135deg, #d0e7ff, #ffffff);
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card-login {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 119, 255, 0.15);
            width: 100%;
            max-width: 420px;
            padding: 3rem 3.5rem;
            text-align: center;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .card-login:hover {
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

        .btn-login {
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

        .btn-login:hover {
            background: #004db8;
            box-shadow: 0 8px 30px rgba(0, 77, 184, 0.6);
        }

        .alert-error {
            margin-bottom: 1.3rem;
            font-weight: 600;
            color: #b32121;
            background-color: #ffe1e1;
            border-radius: 10px;
            padding: 0.8rem 1.2rem;
            border: 1px solid #f5a9a9;
        }

        .btn-back {
            margin-top: 1.8rem;
            display: inline-block;
            font-weight: 600;
            color: #0066ff;
            text-decoration: none;
            border: 2px solid #0066ff;
            padding: 9px 24px;
            border-radius: 14px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .btn-back:hover {
            background-color: #0066ff;
            color: white;
            box-shadow: 0 10px 25px rgba(0, 102, 255, 0.6);
        }

        @media (max-width: 450px) {
            .card-login {
                padding: 2.5rem 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="card-login" role="main" aria-label="Form login user">
        <h2>Login Admin Inventaris </h2>

        <?php if ($error): ?>
            <div class="alert-error" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" novalidate>
            <div class="mb-4 text-start">
                <label for="username">Username atau Email</label>
                <input type="text" id="username" name="username" class="form-control" required autofocus autocomplete="username" />
            </div>

            <div class="mb-4 text-start">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password" />
            </div>

            <button type="submit" name="login" class="btn-login" aria-label="Tombol login">Masuk</button>
        </form>

        <a href="../../index.php" class="btn-back" aria-label="Kembali ke landing page">Beranda</a>
    </div>
</body>

</html>