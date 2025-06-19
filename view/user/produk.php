<?php
// Memulai session PHP untuk mengelola data user yang login
session_start();

// Mengimpor file konfigurasi database dari direktori parent (2 level ke atas)
include '../../includes/database.php';

// === SISTEM KEAMANAN DAN AUTENTIKASI ===
// Mengecek apakah user sudah login dan memiliki role 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Jika belum login atau bukan user, redirect ke halaman login
    header('Location: loginuser.php');
    exit; // Menghentikan eksekusi script setelah redirect
}

// Mengambil ID user dari session untuk filter data sesuai user yang login
$user_id = $_SESSION['user_id'];

// === SISTEM PESAN NOTIFIKASI (FLASH MESSAGE) ===
// Inisialisasi variabel pesan kosong
$message = '';

// Mengecek apakah ada pesan dari session (biasanya setelah operasi CRUD)
if (isset($_SESSION['message'])) {
    // Mengambil pesan dari session
    $message = $_SESSION['message'];
    // Menghapus pesan dari session agar tidak muncul lagi di refresh berikutnya
    unset($_SESSION['message']);
}

// === PENGATURAN PAGINATION (PEMBAGIAN HALAMAN) ===
// Menentukan jumlah data yang ditampilkan per halaman
$limit = 5;

// Mengambil nomor halaman dari parameter URL, default halaman 1
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Validasi: jika halaman kurang dari 1, set ke halaman 1
if ($page < 1) $page = 1;

// === FITUR PENCARIAN ===
// Mengambil kata kunci pencarian dari parameter URL, hapus spasi di awal/akhir
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Membuat pattern untuk LIKE query dengan wildcard (%) di awal dan akhir
$searchTerm = "%$keyword%";

// === MENGHITUNG TOTAL DATA UNTUK PAGINATION ===
// Jika ada kata kunci pencarian
if ($keyword) {
    // Query untuk menghitung total data yang sesuai dengan pencarian
    // Mencari di kolom 'name' dan 'kode_barang'
    $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM products WHERE user_id = ? AND is_deleted = 0 AND (name LIKE ? OR kode_barang LIKE ?)");
    $stmtTotal->bind_param("iss", $user_id, $searchTerm, $searchTerm);
} else {
    // Jika tidak ada pencarian, hitung semua data user tersebut
    // Query ini lebih efisien daripada menghitung semua lalu filter di PHP
    $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM products WHERE user_id = ? AND is_deleted = 0");
    $stmtTotal->bind_param("i", $user_id);
}

// Eksekusi query penghitungan total
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalRow = $resultTotal->fetch_assoc();

// Mengambil jumlah total data
$totalData = $totalRow['total'];

// Menghitung total halaman yang dibutuhkan (pembulatan ke atas)
$totalPages = ceil($totalData / $limit);

// Validasi: jika halaman yang diminta melebihi total halaman dan ada data
if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

// Menghitung offset (posisi data mulai diambil) untuk LIMIT query
// Rumus: (halaman_sekarang - 1) × jumlah_data_per_halaman
$offset = ($page - 1) * $limit;

// === MENGAMBIL DATA PRODUK DENGAN PAGINATION DAN PENCARIAN ===
// Jika ada kata kunci pencarian
if ($keyword) {
    // Query dengan JOIN ke tabel categories untuk mendapatkan nama kategori
    // Filter berdasarkan user_id, is_deleted=0, dan pencarian di name/kode_barang
    // Urutkan berdasarkan tanggal masuk terbaru, batasi sesuai pagination
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.user_id = ? AND p.is_deleted = 0 AND (p.name LIKE ? OR p.kode_barang LIKE ?) ORDER BY p.date_in DESC LIMIT ?, ?");
    // Bind parameter: user_id (integer), searchTerm (string), searchTerm (string), offset (integer), limit (integer)
    $stmt->bind_param("issii", $user_id, $searchTerm, $searchTerm, $offset, $limit);
} else {
    // Query tanpa pencarian - hanya filter user_id dan is_deleted
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.user_id = ? AND p.is_deleted = 0 ORDER BY p.date_in DESC LIMIT ?, ?");
    // Bind parameter: user_id (integer), offset (integer), limit (integer)
    $stmt->bind_param("iii", $user_id, $offset, $limit);
}

// Eksekusi query untuk mengambil data produk
$stmt->execute();

// Mendapatkan hasil query dalam bentuk result set
$result = $stmt->get_result();

// === PENJELASAN KEAMANAN DAN BEST PRACTICES ===
/*
1. PREPARED STATEMENTS: Semua query menggunakan prepared statements untuk mencegah SQL Injection
2. PARAMETER BINDING: Semua input user di-bind dengan tipe data yang sesuai (i=integer, s=string)
3. SOFT DELETE: Menggunakan flag 'is_deleted' instead of menghapus data permanen
4. USER ISOLATION: Setiap user hanya bisa melihat data miliknya sendiri (filter user_id)
5. INPUT VALIDATION: Validasi halaman, trim keyword, type casting untuk keamanan
6. SESSION MANAGEMENT: Proper session handling untuk autentikasi dan flash messages
*/

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
            font-size: 0.85rem;
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
                        <th>Kategori</th>
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
                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()): // Mengambil setiap baris data produk
                            $status = strtolower(trim($row['status'])); // Normalisasi status menjadi huruf kecil dan trim spasi
                            $badgeClass = 'badge bg-secondary';
                            $statusText = 'Status Tidak Dikenali';
                            // Menentukan kelas badge dan teks status berdasarkan nilai status
                            if ($status === 'draft') {
                                $badgeClass = 'badge bg-warning text-dark';
                                $statusText = 'Draft';
                            } elseif ($status === 'submitted') {
                                $badgeClass = 'badge bg-primary';
                                $statusText = 'Sudah Disubmit';
                            } elseif (in_array($status, ['pending', 'menunggu konfirmasi'])) {
                                $badgeClass = 'badge bg-warning text-dark';
                                $statusText = 'Menunggu Konfirmasi';
                            } elseif (in_array($status, ['approved', 'berhasil'])) {
                                $badgeClass = 'badge bg-success';
                                $statusText = 'Berhasil';
                            } elseif (in_array($status, ['rejected', 'ditolak'])) {
                                $badgeClass = 'badge bg-danger';
                                $statusText = 'Ditolak';
                            }
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                                <td><?= htmlspecialchars($row['name']); ?></td>
                                <td><?= htmlspecialchars($row['category_name'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['description']); ?></td>
                                <td><span class="badge bg-primary"><?= $row['stock']; ?></span></td>
                                <td>Rp <?= number_format($row['price'], 0, ',', '.'); ?></td>
                                <td><?= date('d M Y', strtotime($row['date_in'])); ?></td>
                                <td><span class="<?= $badgeClass ?> badge-status"><?= $statusText ?></span></td>
                                <td>
                                    <?php if ($status === 'draft'): ?>
                                        <a href="produk_edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-info btn-action">Edit</a>
                                        <a href="produk_hapus.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger btn-action" onclick="return confirm('Yakin hapus produk?')">Hapus</a>
                                        <a href="produk_submit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-success btn-action" onclick="return confirm('Yakin ingin submit produk ini? Setelah submit tidak bisa diubah.')">Submit</a>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak dapat diedit</span><br>
                                        <a href="produk_hapus.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger btn-action mt-1" onclick="return confirm('Produk sudah disubmit. Yakin ingin hapus?')">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
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