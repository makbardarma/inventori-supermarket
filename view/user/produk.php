<?php
session_start();
include '../../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: loginuser.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$searchTerm = "%$keyword%";

// Hitung total data produk user dengan atau tanpa pencarian
if ($keyword) {
    $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM products WHERE user_id = ? AND is_deleted = 0 AND (name LIKE ? OR kode_barang LIKE ?)");
    $stmtTotal->bind_param("iss", $user_id, $searchTerm, $searchTerm);
} else {
    $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM products WHERE user_id = ? AND is_deleted = 0");
    $stmtTotal->bind_param("i", $user_id);
}

$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalRow = $resultTotal->fetch_assoc();
$totalData = $totalRow['total'];
$totalPages = ceil($totalData / $limit);

if ($page > $totalPages && $totalPages > 0) {
    $page = $totalPages;
}

$offset = ($page - 1) * $limit;

// Query produk user dengan join kategori dan filter pencarian jika ada
if ($keyword) {
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.user_id = ? AND p.is_deleted = 0 AND (p.name LIKE ? OR p.kode_barang LIKE ?) ORDER BY p.date_in DESC LIMIT ?, ?");
    $stmt->bind_param("issii", $user_id, $searchTerm, $searchTerm, $offset, $limit);
} else {
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.user_id = ? AND p.is_deleted = 0 ORDER BY p.date_in DESC LIMIT ?, ?");
    $stmt->bind_param("iii", $user_id, $offset, $limit);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Produk User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
        }

        .container-box {
            max-width: 1400px;
            margin: 2rem auto;
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .table th {
            background-color: #e9f1ff;
            color: #0d6efd;
        }

        .badge-status {
            font-size: 0.9rem;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .pagination a {
            margin: 0 3px;
        }

        .btn-action {
            margin: 0 2px;
            padding: 0.3rem 0.6rem;
            font-size: 0.875rem;
        }

        h2 {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container container-box">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div>
                <h2>Halo, <?= htmlspecialchars($_SESSION['username']); ?></h2>
                <p class="text-muted">Kelola produk yang kamu kirim</p>
            </div>
            <div>
                <a href="dashboard.php" class="btn btn-outline-secondary me-2 mb-2 mb-md-0">Dashboard</a>
                <a href="produk_tambah.php" class="btn btn-primary">Tambah Produk</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="GET" class="mb-3 d-flex" role="search" aria-label="Form pencarian produk">
            <input type="text" name="keyword" class="form-control me-2" placeholder="Cari produk..." value="<?= htmlspecialchars($keyword); ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th> <!-- Kolom kategori -->
                        <th>Deskripsi</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = $offset + 1;
                    if (mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)) :
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                                <td><?= htmlspecialchars($row['name']); ?></td>
                                <td><?= htmlspecialchars($row['category_name'] ?? '-'); ?></td> <!-- Tampilkan kategori -->
                                <td><?= htmlspecialchars($row['description']); ?></td>
                                <td><span class="badge bg-primary"><?= $row['stock']; ?></span></td>
                                <td>Rp <?= number_format($row['price'], 0, ',', '.'); ?></td>
                                <td><?= date('d M Y', strtotime($row['date_in'])); ?></td>
                                <td>
                                    <?php if ($row['status'] === 'submitted'): ?>
                                        <span class="badge bg-success badge-status">Sudah Disubmit</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark badge-status">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'draft'): ?>
                                        <a href="produk_edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-info btn-action">Edit</a>
                                        <a href="produk_hapus.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger btn-action" onclick="return confirm('Yakin hapus produk?')">Hapus</a>
                                        <a href="produk_submit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-success btn-action" onclick="return confirm('Yakin ingin submit produk ini? Setelah submit tidak bisa diubah.')">Submit</a>
                                    <?php else: ?>
                                        <span class="text-success">Terkirim</span>
                                        <a href="produk_hapus.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger btn-action" onclick="return confirm('Produk ini sudah disubmit. Yakin ingin hapus?')">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">Belum ada produk yang ditambahkan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="mt-4" aria-label="Navigasi halaman produk">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i; ?>&keyword=<?= urlencode($keyword); ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</body>

</html>