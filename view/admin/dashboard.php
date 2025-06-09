<?php
session_start();
include '../../includes/database.php';

// Cek login admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

// Pagination config
$limit = 3;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total produk
$total_query = "SELECT COUNT(*) AS total FROM products";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_data = $total_row['total'];

// Total halaman
$total_pages = ceil($total_data / $limit);

// Ambil produk terbaru
$query = "SELECT p.kode_barang, p.name, p.price, p.description, p.stock, p.date_in, c.name AS category 
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          ORDER BY p.date_in DESC
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

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
    <title>Dashboard Admin - Inventaris</title>

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

        .summary-cards {
            display: flex;
            gap: 24px;
            margin-bottom: 48px;
            flex-wrap: wrap;
        }

        .card-summary {
            flex: 1 1 260px;
            background: white;
            padding: 28px 24px;
            border-radius: 20px;
            box-shadow: 0 14px 30px rgba(77, 171, 247, 0.15);
            text-align: center;
            cursor: default;
            transition: box-shadow 0.3s ease;
        }

        .card-summary:hover {
            box-shadow: 0 18px 38px rgba(77, 171, 247, 0.3);
        }

        .card-summary h3 {
            font-weight: 700;
            color: #0288d1;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .card-summary p {
            font-size: 1.35rem;
            color: #4dabf7;
            margin: 0;
        }

        section[aria-label="Produk Terbaru"] {
            background: white;
            border-radius: 18px;
            padding: 28px 32px;
            box-shadow: 0 12px 32px rgba(77, 171, 247, 0.12);
        }

        section h3 {
            font-weight: 700;
            color: #0288d1;
            font-size: 1.9rem;
            margin-bottom: 26px;
            user-select: none;
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
            text-align: left;
        }

        tbody tr {
            background: #e3f2fd;
            box-shadow: 0 5px 15px rgba(77, 171, 247, 0.14);
            border-radius: 14px;
            transition: background-color 0.3s ease;
        }

        tbody tr:hover {
            background-color: #bbdefb;
        }

        tbody td {
            padding: 14px 18px;
            vertical-align: middle;
            color: #26418f;
        }

        tbody td:nth-child(4) {
            font-weight: 700;
            color: #01579b;
            white-space: nowrap;
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

            .summary-cards {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            main {
                padding: 25px 18px;
            }

            thead th,
            tbody td {
                padding: 12px 10px;
                font-size: 14px;
            }

            .pagination a {
                padding: 8px 14px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <nav aria-label="Navigasi Menu Admin">
        <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="produk.php"><i class="bi bi-box-seam"></i> Kelola Produk</a>
        <a href="kategori.php"><i class="bi bi-tags-fill"></i> Kategori</a>
        <a href="kelola_user.php"><i class="bi bi-people-fill"></i> Kelola User</a>
        <a href="profil.php"><i class="bi bi-person-circle"></i> Profil Admin</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>

    <main>
        <header>
            <img src="<?= htmlspecialchars($photoPath); ?>" alt="Foto Profil Admin" />
            <h2>Selamat datang, <?= htmlspecialchars($_SESSION['username']); ?></h2>
        </header>

        <section aria-label="Ringkasan Admin" class="summary-cards">
            <div class="card-summary" title="Total Produk">
                <h3><?= $total_data ?></h3>
                <p>Total Produk</p>
            </div>
            <div class="card-summary" title="Total Kategori">
                <?php
                $cat_result = mysqli_query($conn, "SELECT COUNT(*) AS total_cat FROM categories");
                $cat_row = mysqli_fetch_assoc($cat_result);
                ?>
                <h3><?= $cat_row['total_cat'] ?></h3>
                <p>Total Kategori</p>
            </div>
            <div class="card-summary" title="Total Stok Barang">
                <?php
                $stok_result = mysqli_query($conn, "SELECT SUM(stock) AS total_stock FROM products");
                $stok_row = mysqli_fetch_assoc($stok_result);
                ?>
                <h3><?= $stok_row['total_stock'] ?></h3>
                <p>Total Stok Barang</p>
            </div>
        </section>

        <section aria-label="Produk Terbaru">
            <h3>Barang Terbaru Masuk</h3>
            <table role="table" aria-describedby="produk-terbaru-desc" aria-live="polite" aria-relevant="all">
                <thead>
                    <tr>
                        <th scope="col">Kode Produk</th>
                        <th scope="col">Nama Produk</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Deskripsi</th>
                        <th scope="col">Stok</th>
                        <th scope="col">Tanggal Masuk</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['category']); ?></td>
                            <td>Rp <?= number_format($row['price'], 0, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($row['description']); ?></td>
                            <td><?= $row['stock']; ?></td>
                            <td><?= date('d-m-Y', strtotime($row['date_in'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav aria-label="Navigasi halaman produk terbaru" class="mt-4">
                <ul class="pagination">
                    <!-- Previous -->
                    <li class="<?= ($page <= 1) ? 'disabled' : ''; ?>">
                        <a href="?page=<?= max(1, $page - 1); ?>" aria-label="Previous">
                            <i class="bi bi-chevron-left"></i> Prev
                        </a>
                    </li>

                    <?php
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);

                    if ($start > 1) {
                        echo '<li><a href="?page=1">1</a></li>';
                        if ($start > 2) echo '<li><span>...</span></li>';
                    }

                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <li class="<?= ($i == $page) ? 'active' : ''; ?>">
                            <a href="?page=<?= $i; ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor;

                    if ($end < $total_pages) {
                        if ($end < $total_pages - 1) echo '<li><span>...</span></li>';
                        echo '<li><a href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                    }
                    ?>

                    <!-- Next -->
                    <li class="<?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a href="?page=<?= min($total_pages, $page + 1); ?>" aria-label="Next">
                            Next <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </section>
    </main>

    <!-- Bootstrap JS Bundle (optional for some components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>