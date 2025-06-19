<?php
session_start();
include '../../includes/database.php';

// Cek login dan role user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: loginuser.php');
    exit;
}

// Ambil kategori dari tabel categories
$kategoriResult = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

$error = '';


// === PEMROSESAN FORM KETIKA DISUBMIT ===
// Mengecek apakah request yang masuk adalah POST (form disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // === SANITASI DAN VALIDASI INPUT DARI FORM ===
    // Membersihkan input nama produk dari karakter berbahaya untuk mencegah XSS
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    // Membersihkan input deskripsi produk dari karakter berbahaya
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Konversi input stok ke integer untuk memastikan tipe data yang benar
    // Jika input bukan angka, akan menjadi 0
    $stock = (int) $_POST['stock'];

    $price = (float) $_POST['price'];
    $category_id = (int) $_POST['category_id'];
    $user_id = $_SESSION['user_id'];

    $kode_barang = 'USR-' . time() . '-' . rand(1000, 9999);

    if (empty($name) || $stock < 0 || $price < 0 || empty($category_id)) {
        $error = "Nama produk, kategori, stok, dan harga wajib diisi dengan benar.";
    } else {
        $query = "INSERT INTO products (kode_barang, name, description, stock, price, date_in, user_id, category_id, status)
                  VALUES ('$kode_barang', '$name', '$description', $stock, $price, NOW(), $user_id, $category_id, 'draft')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = "Produk berhasil ditambahkan.";
            header("Location: produk.php");
            exit;
        } else {
            $error = "Gagal menambahkan produk: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-box {
            max-width: 700px;
            margin: 3rem auto;
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        h2 {
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .form-label {
            font-weight: 500;
        }

        .btn-custom-primary {
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
            border: none;
            color: white;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-custom-primary:hover {
            background: linear-gradient(135deg, #4338ca, #2563eb);
            transform: translateY(-2px);
        }

        .btn-custom-secondary {
            background: #f1f5f9;
            color: #374151;
            border: 1px solid #cbd5e1;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            transition: background 0.2s ease;
        }

        .btn-custom-secondary:hover {
            background: #e2e8f0;
        }

        .btn-custom-outline {
            background: transparent;
            border: 2px solid #0d6efd;
            color: #0d6efd;
            font-weight: 500;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .btn-custom-outline:hover {
            background: #0d6efd;
            color: white;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }
    </style>
</head>

<body>

    <div class="container container-box">
        <h2>Tambah Produk</h2>
        <p class="text-muted mb-4">Silakan isi formulir di bawah untuk menambahkan produk baru.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label">Nama Produk</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Stok</label>
                <input type="number" name="stock" class="form-control" min="0" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="price" class="form-control" min="0" step="0.01" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php while ($cat = mysqli_fetch_assoc($kategoriResult)) : ?>
                        <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <div>
                    <a href="produk.php" class="btn btn-custom-secondary">Kembali</a>
                    <a href="dashboard.php" class="btn btn-custom-outline">Dashboard</a>
                </div>
                <button type="submit" class="btn btn-custom-primary">Simpan Produk</button>
            </div>
        </form>
    </div>

</body>

</html>