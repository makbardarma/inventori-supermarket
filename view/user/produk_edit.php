<?php
session_start();
include '../../includes/database.php';

// Cek apakah user sudah login dan role user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: loginuser.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Validasi ID produk dari parameter GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ID produk tidak valid.";
    header('Location: produk.php');
    exit;
}

$id = (int) $_GET['id'];

// Ambil data produk berdasarkan ID, milik user, status draft, dan belum dihapus
$query = "SELECT * FROM products WHERE id = $id AND user_id = $user_id AND status = 'draft' AND is_deleted = 0 LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['message'] = "Produk tidak ditemukan atau tidak dapat diedit.";
    header('Location: produk.php');
    exit;
}

$product = mysqli_fetch_assoc($result);
$error = '';

// Proses update saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $stock = (int) $_POST['stock'];
    $price = (float) $_POST['price'];

    if (empty($name) || $stock < 0 || $price < 0) {
        $error = "Nama produk, stok, dan harga wajib diisi dengan benar (tidak boleh negatif).";
    } else {
        $updateQuery = "UPDATE products SET
            name = '$name',
            description = '$description',
            stock = $stock,
            price = $price
            WHERE id = $id AND user_id = $user_id AND status = 'draft' AND is_deleted = 0";

        if (mysqli_query($conn, $updateQuery)) {
            $_SESSION['message'] = "Produk berhasil diperbarui.";
            header('Location: produk.php');
            exit;
        } else {
            $error = "Gagal memperbarui produk: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Edit Produk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-edit {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px 35px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-weight: 600;
            margin-bottom: 25px;
            color: #343a40;
        }

        label {
            font-weight: 500;
        }

        textarea {
            resize: vertical;
        }

        .btn-primary {
            width: 100%;
            font-weight: 600;
        }

        .links {
            margin-bottom: 20px;
        }

        .links a {
            margin-right: 15px;
            color: #0d6efd;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container container-edit">
        <h2>✏️ Edit Produk</h2>

        <div class="links">
            <a href="produk.php">⬅️ Kembali ke Daftar Produk</a>
            <a href="dashboard.php">🏠 Kembali ke Dashboard</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="name" class="form-label">Nama Produk:</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($product['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi:</label>
                <textarea id="description" name="description" class="form-control" rows="4" required><?= htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="stock" class="form-label">Stok:</label>
                <input type="number" id="stock" name="stock" class="form-control" min="0" value="<?= (int)$product['stock']; ?>" required>
            </div>

            <div class="mb-4">
                <label for="price" class="form-label">Harga (Rp):</label>
                <input type="number" id="price" name="price" class="form-control" min="0" step="0.01" value="<?= (float)$product['price']; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN (optional, for components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>