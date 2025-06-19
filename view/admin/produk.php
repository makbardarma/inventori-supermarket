<?php
session_start();
include '../../includes/database.php';

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

// === LOGIKA UPDATE STATUS (Accept/Reject Product) ===
if (isset($_GET['status_update']) && isset($_GET['id']) && isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action']; // 'accept' atau 'reject'

    // Validasi input
    if ($id > 0 && in_array($action, ['accept', 'reject'])) {
        // Gunakan status yang konsisten dengan sistem
        $newStatus = ($action === 'accept') ? 'berhasil' : 'ditolak';

        $updateQuery = "UPDATE products SET status = '$newStatus' WHERE id = $id";
        if (mysqli_query($conn, $updateQuery)) {
            $_SESSION['message'] = "✅ Status produk berhasil diubah menjadi: <strong>" . ucfirst($newStatus) . "</strong>.";
        } else {
            $_SESSION['error'] = "❌ Gagal mengubah status produk. Error: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "❌ Permintaan tidak valid.";
    }
    header("Location: produk.php");
    exit;
}

// === PENCARIAN PRODUK (AJAX Response) ===
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    $search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

    $query = "SELECT p.*, c.name AS category_name
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id";

    if (!empty($search)) {
        $query .= " WHERE p.kode_barang LIKE '%$search%'
                     OR p.name LIKE '%$search%'
                     OR c.name LIKE '%$search%'";
    }

    $query .= " ORDER BY p.id DESC LIMIT 50";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $statusClass = '';
            if ($row['status'] === 'pending') $statusClass = 'status-badge pending';
            elseif ($row['status'] === 'accepted') $statusClass = 'status-badge accepted';
            elseif ($row['status'] === 'rejected') $statusClass = 'status-badge rejected';
            else $statusClass = 'status-badge default';

            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['kode_barang']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td class='description'>" . nl2br(htmlspecialchars($row['description'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
            echo "<td>Rp " . number_format($row['price'], 0, ',', '.') . "</td>";
            echo "<td>" . (int)$row['stock'] . "</td>";
            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
            echo "<td>" . (!empty($row['date_in']) ? date('d-m-Y', strtotime($row['date_in'])) : '-') . "</td>";
            echo "<td>" . (!empty($row['date_out']) ? date('d-m-Y', strtotime($row['date_out'])) : '-') . "</td>";
            echo "<td><span class='$statusClass'>" . ucfirst($row['status']) . "</span></td>";
            echo "<td class='text-center text-nowrap'>";
            echo "<a href='produk_edit.php?id={$row['id']}' class='btn btn-sm btn-info action-btn' title='Edit produk'><i class='bi bi-pencil'></i></a>";
            echo "<a href='produk.php?status_update=1&id={$row['id']}&action=accept' class='btn btn-sm btn-success action-btn' onclick=\"return confirm('Terima produk ini?')\" title='Terima produk'><i class='bi bi-check-lg'></i></a>";
            echo "<a href='produk.php?status_update=1&id={$row['id']}&action=reject' class='btn btn-sm btn-danger action-btn' onclick=\"return confirm('Tolak produk ini?')\" title='Tolak produk'><i class='bi bi-x-lg'></i></a>";
            echo "<a href='produk_hapus.php?id={$row['id']}' class='btn btn-sm btn-secondary action-btn' onclick=\"return confirm('Yakin ingin menghapus produk ini?')\" title='Hapus produk'><i class='bi bi-trash3'></i></a>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='11' class='text-center py-4'><i class='bi bi-info-circle me-2'></i>Tidak ada data ditemukan untuk pencarian ini.</td></tr>";
    }
    exit;
}

// === NORMAL VIEW (Initial Load & Pagination) ===
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

// Query untuk menghitung total data
$totalQuery = "SELECT COUNT(*) as total FROM products p LEFT JOIN categories c ON p.category_id = c.id";
if ($search !== '') {
    $totalQuery .= " WHERE p.kode_barang LIKE '%$search%' OR p.name LIKE '%$search%' OR c.name LIKE '%$search%'";
}
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalData = $totalRow['total'];

// Query untuk mendapatkan data produk
$query = "SELECT p.*, c.name AS category_name
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id";
if ($search !== '') {
    $query .= " WHERE p.kode_barang LIKE '%$search%'
                 OR p.name LIKE '%$search%'
                 OR c.name LIKE '%$search%'";
}
$query .= " ORDER BY p.id DESC
            LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

$totalPages = ceil($totalData / $limit);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin Dashboard</title>

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
            box-shadow: 0 2px 6px rgba(255, 255, 255, 0.3);
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
            max-width: 1200px;
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
            box-shadow: 0 8px 22px rgba(77, 171, 247, 0.2);
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

        .section-title {
            font-weight: 700;
            color: #0288d1;
            font-size: 1.9rem;
            margin-bottom: 26px;
            user-select: none;
        }

        .search-add-section {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            align-items: center;
        }

        .search-input {
            flex: 1;
            border-radius: 12px;
            padding: 12px 18px;
            border: 2px solid #4dabf7;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #0288d1;
            box-shadow: 0 0 0 0.25rem rgba(77, 171, 247, 0.25);
            outline: none;
        }

        .btn-add-product {
            background: #4dabf7;
            border: 2px solid #4dabf7;
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(77, 171, 247, 0.3);
        }

        .btn-add-product:hover {
            background: #82c7ff;
            border-color: #82c7ff;
            color: #003366;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(130, 199, 255, 0.6);
        }

        /* Alert Messages */
        .alert {
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
            margin-top: 20px;
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
            transition: background-color 0.3s ease;
        }

        tbody tr:hover {
            background-color: #bbdefb;
        }

        tbody td {
            padding: 14px 18px;
            vertical-align: middle;
            color: #26418f;
            border: none;
        }

        tbody td:first-child {
            border-radius: 14px 0 0 14px;
        }

        tbody td:last-child {
            border-radius: 0 14px 14px 0;
        }

        /* Action Buttons */
        .action-btn {
            padding: 8px 12px;
            margin: 0 3px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .btn-info {
            background: #4dabf7;
        }

        .btn-success {
            background: #28a745;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-secondary {
            background: #6c757d;
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.accepted {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        /* Pagination */
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

        /* Loading Message */
        #loadingMsg {
            padding: 20px;
            text-align: center;
            color: #4dabf7;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 900px) {
            header {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }

            .search-add-section {
                flex-direction: column;
                align-items: stretch;
            }

            .search-input {
                margin-bottom: 10px;
            }
        }

        @media (max-width: 576px) {
            main {
                padding: 25px 18px;
            }

            nav {
                flex-wrap: wrap;
                gap: 12px;
                padding: 12px 0;
            }

            nav a {
                padding: 8px 12px;
                font-size: 14px;
            }

            thead th,
            tbody td {
                padding: 12px 10px;
                font-size: 14px;
            }

            .action-btn {
                padding: 6px 8px;
                margin: 2px;
            }

            .pagination a {
                padding: 8px 12px;
                font-size: 14px;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <nav aria-label="Navigasi Menu Admin">
        <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="produk.php" class="active"><i class="bi bi-box-seam"></i> Kelola Produk</a>
        <a href="kategori.php"><i class="bi bi-tags-fill"></i> Kategori</a>
        <a href="kelola_user.php"><i class="bi bi-people-fill"></i> Kelola User</a>
        <a href="profil.php"><i class="bi bi-person-circle"></i> Profil Admin</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>

    <main>
        <header>
            <img src="<?= htmlspecialchars($photoPath); ?>" alt="Foto Profil Admin" />
            <h2>Kelola Produk Inventaris</h2>
        </header>

        <section class="content-section">
            <h3 class="section-title">Manajemen Produk</h3>

            <!-- Pesan Sukses/Error -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <div><?= $_SESSION['message'];
                            unset($_SESSION['message']); ?></div>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-x-circle me-2"></i>
                    <div><?= $_SESSION['error'];
                            unset($_SESSION['error']); ?></div>
                </div>
            <?php endif; ?>

            <!-- Search and Add Product Section -->
            <div class="search-add-section">
                <input type="text" id="searchInput" class="search-input" placeholder="Cari produk berdasarkan kode, nama, kategori...">
                <a href="produk_tambah.php" class="btn-add-product">
                    <i class="bi bi-plus-circle"></i>Tambah Produk
                </a>
            </div>

            <div id="loadingMsg" style="display: none;">
                <i class="bi bi-arrow-clockwise"></i> Memuat data...
            </div>

            <!-- Product Table -->
            <div class="table-responsive">
                <table role="table" aria-describedby="product-table-desc" aria-live="polite" aria-relevant="all">
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Stok</th>
                            <th scope="col">Lokasi</th>
                            <th scope="col">Tanggal Masuk</th>
                            <th scope="col">Tanggal Keluar</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="produkTableBody">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)):
                                $statusClass = '';
                                if ($row['status'] === 'pending') $statusClass = 'status-badge pending';
                                elseif ($row['status'] === 'accepted') $statusClass = 'status-badge accepted';
                                elseif ($row['status'] === 'rejected') $statusClass = 'status-badge rejected';
                                else $statusClass = 'status-badge default';
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['kode_barang']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td class="description"><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                                    <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                                    <td><?= (int)$row['stock'] ?></td>
                                    <td><?= htmlspecialchars($row['location']) ?></td>
                                    <td><?= !empty($row['date_in']) ? date('d-m-Y', strtotime($row['date_in'])) : '-' ?></td>
                                    <td><?= !empty($row['date_out']) ? date('d-m-Y', strtotime($row['date_out'])) : '-' ?></td>
                                    <td><span class="<?= $statusClass ?>"><?= ucfirst($row['status']) ?></span></td>
                                    <td class="text-center text-nowrap">
                                        <a href="produk_edit.php?id=<?= $row['id'] ?>" class="action-btn btn-info" title="Edit produk"><i class="bi bi-pencil"></i></a>
                                        <a href="produk.php?status_update=1&id=<?= $row['id'] ?>&action=accept" class="action-btn btn-success" onclick="return confirm('Terima produk ini?')" title="Terima produk"><i class="bi bi-check-lg"></i></a>
                                        <a href="produk.php?status_update=1&id=<?= $row['id'] ?>&action=reject" class="action-btn btn-danger" onclick="return confirm('Tolak produk ini?')" title="Tolak produk"><i class="bi bi-x-lg"></i></a>
                                        <a href="produk_hapus.php?id=<?= $row['id'] ?>" class="action-btn btn-secondary" onclick="return confirm('Yakin ingin menghapus produk ini?')" title="Hapus produk"><i class="bi bi-trash3"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center py-4"><i class="bi bi-info-circle me-2"></i>Tidak ada data produk.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Navigasi halaman produk" class="mt-4">
                <ul class="pagination">
                    <!-- Previous -->
                    <li class="<?= ($page <= 1) ? 'disabled' : ''; ?>">
                        <a href="produk.php?page=<?= max(1, $page - 1) ?><?= $search ? '&q=' . urlencode($search) : '' ?>" aria-label="Previous">
                            <i class="bi bi-chevron-left"></i> Prev
                        </a>
                    </li>

                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);

                    if ($start > 1) {
                        echo '<li><a href="produk.php?page=1' . ($search ? '&q=' . urlencode($search) : '') . '">1</a></li>';
                        if ($start > 2) echo '<li><span>...</span></li>';
                    }

                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <li class="<?= ($i == $page) ? 'active' : ''; ?>">
                            <a href="produk.php?page=<?= $i ?><?= $search ? '&q=' . urlencode($search) : '' ?>"><?= $i ?></a>
                        </li>
                    <?php endfor;

                    if ($end < $totalPages) {
                        if ($end < $totalPages - 1) echo '<li><span>...</span></li>';
                        echo '<li><a href="produk.php?page=' . $totalPages . ($search ? '&q=' . urlencode($search) : '') . '">' . $totalPages . '</a></li>';
                    }
                    ?>

                    <!-- Next -->
                    <li class="<?= ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a href="produk.php?page=<?= min($totalPages, $page + 1) ?><?= $search ? '&q=' . urlencode($search) : '' ?>" aria-label="Next">
                            Next <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </section>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // === SCRIPT AJAX PENCARIAN ===
        const searchInput = document.getElementById('searchInput');
        const produkTableBody = document.getElementById('produkTableBody');
        const loadingMsg = document.getElementById('loadingMsg');

        let typingTimer;
        const typingInterval = 600;

        function fetchProduk(query) {
            loadingMsg.style.display = 'block';
            fetch(`produk.php?ajax=1&q=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    produkTableBody.innerHTML = html;
                    loadingMsg.style.display = 'none';
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    produkTableBody.innerHTML = `<tr><td colspan="11" class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle me-2"></i>Gagal memuat data. Mohon coba lagi.</td></tr>`;
                    loadingMsg.style.display = 'none';
                });
        }

        searchInput.addEventListener('input', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                const query = searchInput.value.trim();
                if (query.length > 0) {
                    fetchProduk(query);
                } else {
                    window.location.href = 'produk.php';
                }
            }, typingInterval);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const initialQuery = urlParams.get('q');
            if (initialQuery) {
                searchInput.value = initialQuery;
            }
        });

        // === KONFIRMASI AKSI ===
        function confirmAction(action, productName) {
            const messages = {
                'accept': `Apakah Anda yakin ingin menerima produk "${productName}"?`,
                'reject': `Apakah Anda yakin ingin menolak produk "${productName}"?`,
                'delete': `Apakah Anda yakin ingin menghapus produk "${productName}"? Tindakan ini tidak dapat dibatalkan.`
            };
            return confirm(messages[action] || 'Apakah Anda yakin?');
        }

        // === HIGHLIGHT SEARCH RESULTS ===
        function highlightSearchTerm(text, term) {
            if (!term) return text;
            const regex = new RegExp(`(${term})`, 'gi');
            return text.replace(regex, '<mark style="background-color: #fff3cd; padding: 2px 4px; border-radius: 3px;">$1</mark>');
        }

        // === AUTO REFRESH STATUS ===
        function autoRefreshStatus() {
            const currentUrl = window.location.href;
            if (currentUrl.includes('status_update=1')) {
                setTimeout(() => {
                    window.location.href = 'produk.php';
                }, 2000);
            }
        }

        // === KEYBOARD SHORTCUTS ===
        document.addEventListener('keydown', function(e) {
            // Ctrl + F untuk fokus ke search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }

            // Escape untuk clear search
            if (e.key === 'Escape' && document.activeElement === searchInput) {
                searchInput.value = '';
                window.location.href = 'produk.php';
            }
        });

        // === TOOLTIP INITIALIZATION ===
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Jalankan auto refresh jika ada status update
        autoRefreshStatus();
    </script>
</body>

</html>