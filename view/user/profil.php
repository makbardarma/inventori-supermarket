<?php
session_start();
include '../../includes/database.php';

// Cek login dan role user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: loginuser.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Ambil data user
$query = "SELECT username, email, fullname, photo FROM users WHERE id = $user_id LIMIT 1";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "User tidak ditemukan.";
    exit;
}

$upload_dir = '../../uploads/profile/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $old_photo = $user['photo'];
    $new_photo_filename = $old_photo;

    if (empty($email) || empty($fullname)) {
        $error = "Email dan Nama lengkap wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (!empty($password) && $password !== $password_confirm) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        // Upload foto
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['photo'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowed_types)) {
                $error = "Format foto tidak didukung. Gunakan JPG, PNG, atau GIF.";
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $error = "Ukuran foto maksimal 2MB.";
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $new_photo_filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
                $destination = $upload_dir . $new_photo_filename;

                if (!move_uploaded_file($file['tmp_name'], $destination)) {
                    $error = "Gagal mengunggah foto profil.";
                } else {
                    if ($old_photo && file_exists($upload_dir . $old_photo)) {
                        unlink($upload_dir . $old_photo);
                    }
                }
            }
        }

        if (!$error) {
            $update_sql = "UPDATE users SET email = '$email', fullname = '$fullname', photo = '$new_photo_filename'";
            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $update_sql .= ", password = '$password_hash'";
            }
            $update_sql .= " WHERE id = $user_id";

            if (mysqli_query($conn, $update_sql)) {
                $success = "Profil berhasil diperbarui.";
                $user['email'] = $email;
                $user['fullname'] = $fullname;
                $user['photo'] = $new_photo_filename;
            } else {
                $error = "Gagal memperbarui profil: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
        }

        .container-profile {
            max-width: 600px;
            margin: 3rem auto;
            background-color: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .profile-photo {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #ddd;
            margin-bottom: 1rem;
        }

        h2 {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container container-profile">
        <div class="text-center mb-4">
            <h2>Profil Saya</h2>
            <p class="text-muted mb-1"><?= htmlspecialchars($user['username']); ?></p>
            <div class="d-flex justify-content-center gap-2">
                <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Kembali ke Dashboard</a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin logout?')">Logout</a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="text-center">
            <?php if ($user['photo'] && file_exists($upload_dir . $user['photo'])): ?>
                <img src="../../uploads/profile/<?= htmlspecialchars($user['photo']); ?>" alt="Foto Profil" class="profile-photo">
            <?php else: ?>
                <img src="../../assets/img/default-profile.png" alt="Foto Profil Default" class="profile-photo">
            <?php endif; ?>
        </div>

        <form method="POST" enctype="multipart/form-data" class="mt-3">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']); ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Foto Profil (jpg/png/gif, max 2MB)</label>
                <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/gif">
            </div>

            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin ganti">
            </div>

            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirm" class="form-control" placeholder="Ulangi password baru">
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</body>

</html>