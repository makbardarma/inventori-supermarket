<?php
session_start();
include '../../includes/database.php';

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

// Ambil kategori untuk dropdown
$kategoriResult = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

$error = '';
$success = '';

if (isset($_POST['submit'])) {
    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $category_id = (int) $_POST['category_id'];
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $date_in = $_POST['date_in'];

    // Validasi sederhana
    if (empty($kode_barang) || empty($name) || $price <= 0 || $stock < 0 || empty($date_in)) {
        $error = "Pastikan semua data wajib diisi dengan benar.";
    } else {
        // Cek kode_barang unik
        $checkQuery = "SELECT id FROM products WHERE kode_barang = '$kode_barang' LIMIT 1";
        $checkResult = mysqli_query($conn, $checkQuery);
        if (mysqli_num_rows($checkResult) > 0) {
            $error = "Kode barang sudah digunakan, silakan gunakan kode lain.";
        } else {
            $query = "INSERT INTO products (kode_barang, name, description, price, stock, category_id, location, date_in) 
                      VALUES ('$kode_barang', '$name', '$description', $price, $stock, $category_id, '$location', '$date_in')";
            if (mysqli_query($conn, $query)) {
                $success = "Produk berhasil ditambahkan.";
            } else {
                $error = "Gagal menambahkan produk: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Produk Baru</title>
    <link rel="stylesheet" href="../../assets/css/style.css"> <!-- opsional -->
</head>

<body>

    <h2>➕ Tambah Produk Baru</h2>
    <a href="produk.php">⬅️ Kembali ke Daftar Produk</a>
    <br><br>

    <?php if ($error): ?>
        <p style="color: red;"><?= $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color: green;"><?= $success; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label>Kode Barang*</label><br>
        <input type="text" name="kode_barang" required value="<?= isset($_POST['kode_barang']) ? htmlspecialchars($_POST['kode_barang']) : ''; ?>"><br><br>

        <label>Nama Produk*</label><br>
        <input type="text" name="name" required value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"><br><br>

        <label>Deskripsi</label><br>
        <textarea name="description" rows="4"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea><br><br>

        <label>Harga (Rp)*</label><br>
        <input type="number" name="price" min="0" step="0.01" required value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>"><br><br>

        <label>Stok*</label><br>
        <input type="number" name="stock" min="0" required value="<?= isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : ''; ?>"><br><br>

        <label>Kategori</label><br>
        <select name="category_id" required>
            <option value="">-- Pilih Kategori --</option>
            <?php
            // reset pointer to fetch kategori again in case of repopulate form
            mysqli_data_seek($kategoriResult, 0);
            while ($cat = mysqli_fetch_assoc($kategoriResult)):
            ?>
                <option value="<?= $cat['id']; ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($cat['name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Lokasi Rak Penyimpanan</label><br>
        <input type="text" name="location" value="<?= isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>"><br><br>

        <label>Tanggal Masuk*</label><br>
        <input type="date" name="date_in" value="<?= isset($_POST['date_in']) ? htmlspecialchars($_POST['date_in']) : date('Y-m-d'); ?>" required><br><br>

        <button type="submit" name="submit">Simpan Produk</button>
    </form>

</body>

</html>