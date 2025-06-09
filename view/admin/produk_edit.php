<?php
session_start();
include '../../includes/database.php';

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

// Ambil id produk dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: produk.php');
    exit;
}

$id = (int)$_GET['id'];

// Ambil data produk dari database
$query = "SELECT * FROM products WHERE id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) !== 1) {
    header('Location: produk.php');
    exit;
}

$produk = mysqli_fetch_assoc($result);

// Ambil semua kategori untuk dropdown
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

    if (empty($kode_barang) || empty($name) || $price <= 0 || $stock < 0 || empty($date_in)) {
        $error = "Pastikan semua data wajib diisi dengan benar.";
    } else {
        // Cek kode_barang unik (kecuali untuk produk ini sendiri)
        $checkQuery = "SELECT id FROM products WHERE kode_barang = '$kode_barang' AND id != $id LIMIT 1";
        $checkResult = mysqli_query($conn, $checkQuery);
        if (mysqli_num_rows($checkResult) > 0) {
            $error = "Kode barang sudah digunakan oleh produk lain, gunakan kode lain.";
        } else {
            $updateQuery = "UPDATE products SET
                            kode_barang = '$kode_barang',
                            name = '$name',
                            description = '$description',
                            price = $price,
                            stock = $stock,
                            category_id = $category_id,
                            location = '$location',
                            date_in = '$date_in',
                            updated_at = NOW()
                            WHERE id = $id";

            if (mysqli_query($conn, $updateQuery)) {
                $success = "Produk berhasil diperbarui.";
                // Update data produk setelah perubahan
                $produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = $id"));
            } else {
                $error = "Gagal memperbarui produk: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="../../assets/css/style.css"> <!-- opsional -->
</head>

<body>

    <h2>✏️ Edit Produk</h2>
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
        <input type="text" name="kode_barang" required value="<?= htmlspecialchars($produk['kode_barang']); ?>"><br><br>

        <label>Nama Produk*</label><br>
        <input type="text" name="name" required value="<?= htmlspecialchars($produk['name']); ?>"><br><br>

        <label>Deskripsi</label><br>
        <textarea name="description" rows="4"><?= htmlspecialchars($produk['description']); ?></textarea><br><br>

        <label>Harga (Rp)*</label><br>
        <input type="number" name="price" min="0" step="0.01" required value="<?= $produk['price']; ?>"><br><br>

        <label>Stok*</label><br>
        <input type="number" name="stock" min="0" required value="<?= $produk['stock']; ?>"><br><br>

        <label>Kategori</label><br>
        <select name="category_id" required>
            <option value="">-- Pilih Kategori --</option>
            <?php while ($cat = mysqli_fetch_assoc($kategoriResult)): ?>
                <option value="<?= $cat['id']; ?>" <?= $cat['id'] == $produk['category_id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($cat['name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Lokasi Rak Penyimpanan</label><br>
        <input type="text" name="location" value="<?= htmlspecialchars($produk['location']); ?>"><br><br>

        <label>Tanggal Masuk*</label><br>
        <input type="date" name="date_in" required value="<?= $produk['date_in']; ?>"><br><br>

        <button type="submit" name="submit">Perbarui Produk</button>
    </form>

</body>

</html>