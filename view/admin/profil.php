<?php
session_start();
include '../../includes/database.php';

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = $error = '';

// Ambil data admin dari database
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id LIMIT 1");
$user = mysqli_fetch_assoc($result);

// Handle Update Profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);

    // Update username dan email
    $updateQuery = "UPDATE users SET username = '$username', email = '$email' WHERE id = $user_id";
    mysqli_query($conn, $updateQuery);

    // Update password jika ada input
    if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword     = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        // Cek apakah password lama cocok
        if (password_verify($currentPassword, $user['password'])) {
            mysqli_query($conn, "UPDATE users SET password = '$newPassword' WHERE id = $user_id");
            $success = "Profil berhasil diperbarui, termasuk password.";
        } else {
            $error = "Password lama salah.";
        }
    } else if (empty($error)) {
        // Jika tidak ganti password dan belum ada error
        $success = "Profil berhasil diperbarui.";
    }

    // Upload foto profil jika ada
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = $_FILES['photo'];
        $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
        $newFilename = 'admin_' . $user_id . '_' . time() . '.' . $ext;

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($ext), $allowedTypes)) {
            $uploadDir = '../../uploads/profile/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            if (move_uploaded_file($photo['tmp_name'], $uploadDir . $newFilename)) {
                // Hapus foto lama jika ada
                if (!empty($user['photo']) && file_exists($uploadDir . $user['photo'])) {
                    unlink($uploadDir . $user['photo']);
                }

                // Simpan ke DB
                mysqli_query($conn, "UPDATE users SET photo = '$newFilename' WHERE id = $user_id");
                $success .= " Foto profil diperbarui.";
            } else {
                $error = "Gagal upload foto.";
            }
        } else {
            $error = "Format foto tidak valid. Gunakan JPG, PNG, atau GIF.";
        }
    }

    // Refresh data user setelah update
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id LIMIT 1");
    $user = mysqli_fetch_assoc($result);
}

$photoPath = '../../uploads/profile/' . ($user['photo'] ?: 'default-profile.png');
if (!file_exists($photoPath)) {
    $photoPath = '../../assets/img/default-profile.png';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profil Admin - Inventaris</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        body {
            background-color: #e3f2fd;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 30px 20px;
        }

        h2 {
            color: #0288d1;
            font-weight: 700;
            user-select: none;
            margin-bottom: 30px;
        }

        .profile-photo {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #4dabf7;
            box-shadow: 0 0 20px rgba(77, 171, 247, 0.6);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .profile-photo:hover {
            transform: scale(1.05);
        }

        form {
            max-width: 480px;
            background: white;
            padding: 28px 32px;
            border-radius: 18px;
            box-shadow: 0 8px 22px rgb(77 171 247 / 0.25);
            margin: 0 auto;
        }

        label {
            font-weight: 600;
            color: #01579b;
        }

        input[type="file"] {
            padding: 6px 12px;
        }

        hr {
            border-color: #4dabf7;
            margin: 32px 0 24px 0;
            opacity: 0.6;
        }

        h5 {
            color: #1976d2;
            font-weight: 700;
            margin-bottom: 20px;
            user-select: none;
        }

        .btn-primary {
            background-color: #4dabf7;
            border-color: #4dabf7;
            font-weight: 600;
            border-radius: 12px;
            padding: 10px 22px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0288d1;
            border-color: #0288d1;
        }

        .btn-secondary {
            font-weight: 600;
            border-radius: 12px;
            padding: 10px 22px;
        }

        .alert {
            max-width: 480px;
            margin: 0 auto 20px auto;
            border-radius: 12px;
            font-weight: 600;
        }

        @media (max-width: 576px) {
            form {
                padding: 20px 20px;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <h2>👤 Profil Admin</h2>

    <?php if ($success): ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($success); ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="text-center">
        <img src="<?= htmlspecialchars($photoPath); ?>" alt="Foto Profil Admin" class="profile-photo" />
    </div>

    <form method="post" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input id="username" type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" class="form-control" required />
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" class="form-control" required />
        </div>

        <div class="mb-3">
            <label for="photo" class="form-label">Foto Profil (opsional)</label>
            <input id="photo" type="file" name="photo" accept="image/*" class="form-control" />
        </div>

        <hr />

        <h5>Ganti Password (Opsional)</h5>

        <div class="mb-3">
            <label for="current_password" class="form-label">Password Saat Ini</label>
            <input id="current_password" type="password" name="current_password" class="form-control" />
        </div>

        <div class="mb-3">
            <label for="new_password" class="form-label">Password Baru</label>
            <input id="new_password" type="password" name="new_password" class="form-control" />
        </div>

        <div class="d-flex gap-3 justify-content-center mt-4">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>