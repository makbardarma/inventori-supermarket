<?php
session_start();
include '../../includes/database.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

// Ambil data user dari database (kecuali admin)
$query = "SELECT id, username, email, role FROM users WHERE role = 'user'";
$result = mysqli_query($conn, $query);

// Ambil data admin untuk foto profil
$user_id = $_SESSION['user_id'];
$admin_query = mysqli_query($conn, "SELECT photo FROM users WHERE id = $user_id LIMIT 1");
$admin = mysqli_fetch_assoc($admin_query);
$photoPath = '../../uploads/profile/' . ($admin['photo'] ?: 'default-profile.png');
if (!file_exists($photoPath)) {
    $photoPath = '../../assets/img/default-profile.png';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola User - Inventaris</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e3f2fd;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        nav {
            background: #4dabf7;
            padding: 14px 0;
            display: flex;
            justify-content: center;
            gap: 28px;
            font-weight: 600;
            font-size: 16px;
            position: sticky;
            top: 0;
            box-shadow: 0 4px 8px rgba(77, 171, 247, 0.3);
            z-index: 1000;
            border-radius: 0 0 16px 16px;
        }

        nav a {
            color: #f9f9f9;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease, color 0.3s ease;
            box-shadow: 0 2px 6px rgba(255 255 255 / 0.3);
        }

        nav a i {
            font-size: 18px;
        }

        nav a:hover,
        nav a.active {
            background-color: #82c7ff;
            color: #003366;
            box-shadow: 0 6px 16px rgba(130, 199, 255, 0.6);
        }

        main {
            flex-grow: 1;
            padding: 40px 50px;
            max-width: 1100px;
            margin: 0 auto 40px auto;
            width: 100%;
        }

        header {
            display: flex;
            align-items: center;
            gap: 22px;
            margin-bottom: 38px;
            background: white;
            padding: 22px 30px;
            border-radius: 18px;
            box-shadow: 0 8px 22px rgb(77 171 247 / 0.2);
        }

        header img {
            width: 88px;
            height: 88px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #4dabf7;
            box-shadow: 0 0 15px rgba(77, 171, 247, 0.6);
            transition: transform 0.3s ease;
        }

        header img:hover {
            transform: scale(1.1);
        }

        header h2 {
            font-weight: 700;
            color: #01579b;
            margin: 0;
            font-size: 2rem;
            user-select: none;
        }

        h2.page-title {
            color: #0288d1;
            margin-bottom: 24px;
            font-weight: 700;
            user-select: none;
        }

        a.btn-primary {
            background-color: #4dabf7;
            border-color: #4dabf7;
            font-weight: 600;
            border-radius: 12px;
            padding: 8px 18px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease;
        }

        a.btn-primary:hover {
            background-color: #0288d1;
            border-color: #0288d1;
            color: white;
            text-decoration: none;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 22px rgb(77 171 247 / 0.15);
            overflow: hidden;
        }

        thead tr {
            background: #4dabf7;
            color: white;
            box-shadow: 0 5px 14px rgba(25, 118, 210, 0.7);
            user-select: none;
        }

        thead th {
            padding: 14px 16px;
            font-weight: 700;
            text-align: left;
            font-size: 1rem;
        }

        tbody tr {
            background: #e3f2fd;
            color: #26418f;
            transition: background-color 0.3s ease;
        }

        tbody tr:hover {
            background-color: #bbdefb;
        }

        tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            font-size: 0.95rem;
        }

        tbody td.actions a {
            color: #01579b;
            font-weight: 600;
            text-decoration: none;
            margin-right: 12px;
            transition: color 0.3s ease;
        }

        tbody td.actions a:hover {
            color: #0288d1;
            text-decoration: underline;
        }

        footer-link {
            margin-top: 30px;
            display: inline-block;
            color: #4dabf7;
            font-weight: 600;
            text-decoration: none;
            user-select: none;
            transition: color 0.3s ease;
        }

        footer-link:hover {
            color: #0288d1;
            text-decoration: underline;
        }

        @media (max-width: 900px) {
            main {
                padding: 25px 30px;
            }

            header {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            main {
                padding: 20px 16px;
            }

            thead th,
            tbody td {
                padding: 10px 8px;
                font-size: 14px;
            }

            a.btn-primary {
                padding: 6px 14px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <nav aria-label="Navigasi Menu Admin">
        <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="produk.php"><i class="bi bi-box-seam"></i> Kelola Produk</a>
        <a href="kategori.php"><i class="bi bi-tags-fill"></i> Kategori</a>
        <a href="kelola_user.php" class="active"><i class="bi bi-people-fill"></i> Kelola User</a>
        <a href="profil.php"><i class="bi bi-person-circle"></i> Profil Admin</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>

    <main>
        <header>
            <img src="<?= htmlspecialchars($photoPath); ?>" alt="Foto Profil Admin" />
            <h2>Selamat datang, <?= htmlspecialchars($_SESSION['username']); ?></h2>
        </header>

        <h2 class="page-title">👥 Kelola User</h2>

        <a href="tambah_user.php" class="btn-primary">➕ Tambah User</a>

        <div class="table-responsive mt-4">
            <table role="table" aria-label="Daftar user" aria-live="polite" aria-relevant="all">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['username']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td><?= $row['role']; ?></td>
                                <td class="actions">
                                    <a href="edit_user.php?id=<?= $row['id']; ?>">✏️ Edit</a>
                                    <a href="hapus_user.php?id=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus user ini?');">🗑️ Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 20px; color: #555;">
                                Tidak ada user yang ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="dashboard.php" class="mt-4 d-inline-block" style="color:#4dabf7; font-weight:600; user-select:none; text-decoration:none; transition:color 0.3s ease;">
            ⬅️ Kembali ke Dashboard
        </a>
    </main>

    <!-- Bootstrap JS Bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>