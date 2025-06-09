<?php
session_start();
include '../../includes/database.php';

// Cek login admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

// Cek koneksi database
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Konfigurasi paginasi
$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Total data kategori
$totalSql = "SELECT COUNT(*) as total FROM categories";
$totalResult = $conn->query($totalSql);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Query kategori + jumlah produk dengan LIMIT
$sql = "SELECT c.id, c.name AS nama_kategori, COUNT(p.id) AS total_produk
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.id
        GROUP BY c.id, c.name
        ORDER BY c.name ASC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

// Ambil data admin
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
    <title>Kelola Kategori - Inventaris</title>

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

        .content-section {
            background: white;
            border-radius: 18px;
            padding: 28px 32px;
            box-shadow: 0 12px 32px rgba(77, 171, 247, 0.12);
        }

        .content-section h3 {
            font-weight: 700;
            color: #0288d1;
            font-size: 1.9rem;
            margin-bottom: 26px;
            user-select: none;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .content-section h3 i {
            font-size: 2rem;
            color: #4dabf7;
        }

        .btn-add {
            background: linear-gradient(135deg, #4dabf7, #0288d1);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(77, 171, 247, 0.3);
            margin-bottom: 24px;
        }

        .btn-add:hover {
            background: linear-gradient(135deg, #0288d1, #01579b);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(77, 171, 247, 0.4);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        thead tr {
            background: #4dabf7;
            color: white;
            border-radius: 14px;
            box-shadow: 0 5px 14px rgba(25, 118, 210, 0.7);
        }

        thead th {
            padding: 15px 18px;
            font-weight: 700;
            text-align: center;
            border: none;
        }

        thead th:first-child {
            border-radius: 14px 0 0 14px;
        }

        thead th:last-child {
            border-radius: 0 14px 14px 0;
        }

        tbody tr {
            background: #e3f2fd;
            box-shadow: 0 5px 15px rgba(77, 171, 247, 0.14);
            border-radius: 14px;
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background-color: #bbdefb;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(77, 171, 247, 0.25);
        }

        tbody td {
            padding: 16px 18px;
            vertical-align: middle;
            color: #26418f;
            border: none;
            text-align: center;
        }

        tbody td:first-child {
            border-radius: 14px 0 0 14px;
            font-weight: 700;
            color: #01579b;
        }

        tbody td:last-child {
            border-radius: 0 14px 14px 0;
        }

        .badge-count {
            background: linear-gradient(135deg, #4dabf7, #0288d1);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            color: #4dabf7;
            margin-bottom: 16px;
        }

        .empty-state h4 {
            color: #0288d1;
            margin-bottom: 8px;
        }

        /* Pagination modern */
        .pagination {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 12px;
            list-style: none;
            padding-left: 0;
            user-select: none;
        }

        .pagination li {
            display: inline-block;
        }

        .pagination a {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4dabf7;
            padding: 10px 16px;
            text-decoration: none;
            border: 2px solid #4dabf7;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            gap: 6px;
        }

        .pagination a i {
            font-size: 18px;
        }

        .pagination a:hover {
            background-color: #4dabf7;
            color: white;
            box-shadow: 0 7px 20px rgba(77, 171, 247, 0.5);
            transform: translateY(-2px);
        }

        .pagination .active a,
        .pagination .disabled a {
            background-color: #0288d1;
            border-color: #0288d1;
            color: white;
            pointer-events: none;
            cursor: default;
            box-shadow: none;
        }

        /* Responsive */
        @media (max-width: 900px) {
            header {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }

            nav {
                flex-wrap: wrap;
                gap: 12px;
                padding: 14px 10px;
            }

            nav a {
                font-size: 14px;
                padding: 8px 12px;
            }
        }

        @media (max-width: 576px) {
            main {
                padding: 25px 18px;
            }

            thead th,
            tbody td {
                padding: 12px 8px;
                font-size: 14px;
            }

            .pagination a {
                padding: 8px 12px;
                font-size: 14px;
            }

            .content-section {
                padding: 20px 16px;
            }
        }
    </style>
</head>

<body>
    <nav aria-label="Navigasi Menu Admin">
        <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="produk.php"><i class="bi bi-box-seam"></i> Kelola Produk</a>
        <a href="kategori.php" class="active"><i class="bi bi-tags-fill"></i> Kategori</a>
        <a href="kelola_user.php"><i class="bi bi-people-fill"></i> Kelola User</a>
        <a href="profil.php"><i class="bi bi-person-circle"></i> Profil Admin</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>

    <main>
        <header>
            <img src="<?= htmlspecialchars($photoPath); ?>" alt="Foto Profil Admin" />
            <h2>Kelola Kategori Produk</h2>
        </header>

        <section class="content-section">
            <h3><i class="bi bi-tags-fill"></i> Daftar Kategori</h3>

            <a href="tambah_kategori.php" class="btn-add">
                <i class="bi bi-plus-circle"></i> Tambah Kategori Baru
            </a>

            <div class="table-responsive">
                <table role="table" aria-describedby="kategori-desc" aria-live="polite" aria-relevant="all">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama Kategori</th>
                            <th scope="col">Jumlah Produk</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php $no = $offset + 1; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td style="text-align: left; font-weight: 600; color: #01579b;">
                                        <?= htmlspecialchars($row['nama_kategori']) ?>
                                    </td>
                                    <td>
                                        <span class="badge-count"><?= $row['total_produk'] ?> Produk</span>
                                    </td>
                                    <td>
                                        <a href="edit_kategori.php?id=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-outline-primary me-2"
                                            title="Edit Kategori">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="hapus_kategori.php?id=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Hapus Kategori"
                                            onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="bi bi-tags"></i>
                                    <h4>Belum Ada Kategori</h4>
                                    <p>Silakan tambahkan kategori produk terlebih dahulu.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Navigasi halaman kategori" class="mt-4">
                    <ul class="pagination">
                        <!-- Previous -->
                        <li class="<?= ($page <= 1) ? 'disabled' : ''; ?>">
                            <a href="?page=<?= max(1, $page - 1); ?>" aria-label="Previous">
                                <i class="bi bi-chevron-left"></i> Prev
                            </a>
                        </li>

                        <?php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);

                        if ($start > 1) {
                            echo '<li><a href="?page=1">1</a></li>';
                            if ($start > 2) echo '<li><span style="padding: 10px 16px; color: #6c757d;">...</span></li>';
                        }

                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <li class="<?= ($i == $page) ? 'active' : ''; ?>">
                                <a href="?page=<?= $i; ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor;

                        if ($end < $totalPages) {
                            if ($end < $totalPages - 1) echo '<li><span style="padding: 10px 16px; color: #6c757d;">...</span></li>';
                            echo '<li><a href="?page=' . $totalPages . '">' . $totalPages . '</a></li>';
                        }
                        ?>

                        <!-- Next -->
                        <li class="<?= ($page >= $totalPages) ? 'disabled' : ''; ?>">
                            <a href="?page=<?= min($totalPages, $page + 1); ?>" aria-label="Next">
                                Next <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </section>
    </main>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>