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

// Default values for form fields to repopulate on error
$kode_barang = isset($_POST['kode_barang']) ? htmlspecialchars($_POST['kode_barang']) : '';
$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
$description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';
$price = isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '';
$stock = isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : '';
$category_id = isset($_POST['category_id']) ? htmlspecialchars($_POST['category_id']) : '';
$location = isset($_POST['location']) ? htmlspecialchars($_POST['location']) : '';
$date_in = isset($_POST['date_in']) ? htmlspecialchars($_POST['date_in']) : date('Y-m-d');

// proses permintaan tambah produk
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
    if (empty($kode_barang) || empty($name) || $price <= 0 || $stock < 0 || empty($date_in) || empty($category_id)) {
        $error = "Pastikan semua data wajib diisi dengan benar (termasuk Kategori).";
    } else {
        // Cek kode_barang unik
        $checkQuery = "SELECT id FROM products WHERE kode_barang = '$kode_barang' LIMIT 1";
        $checkResult = mysqli_query($conn, $checkQuery);
        if (mysqli_num_rows($checkResult) > 0) {
            $error = "Kode barang sudah digunakan, silakan gunakan kode lain.";
        } else { // Jika semua validasi lolos, simpan produk baru
            $query = "INSERT INTO products (kode_barang, name, description, price, stock, category_id, location, date_in) 
                      VALUES ('$kode_barang', '$name', '$description', $price, $stock, $category_id, '$location', '$date_in')";
            if (mysqli_query($conn, $query)) {
                $success = "Produk berhasil ditambahkan.";
                // Clear form fields after successful submission
                $kode_barang = $name = $description = $price = $stock = $category_id = $location = '';
                $date_in = date('Y-m-d');
            } else {
                $error = "Gagal menambahkan produk: " . mysqli_error($conn);
            }
        }
    }
}

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
    <title>Tambah Produk Baru - Inventaris</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #01579b;
            font-size: 1.1rem;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #b3e5fc;
            border-radius: 10px;
            font-size: 1rem;
            color: #01579b;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box;
            /* Ensures padding doesn't add to the width */
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #4dabf7;
            box-shadow: 0 0 0 0.25rem rgba(77, 171, 247, 0.25);
            outline: none;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 90px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #4dabf7, #0288d1);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(77, 171, 247, 0.3);
            cursor: pointer;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #0288d1, #01579b);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(77, 171, 247, 0.4);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 25px;
            color: #0288d1;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 15px;
            border: 2px solid #b3e5fc;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #e0f2f7;
            border-color: #4dabf7;
            color: #01579b;
            transform: translateX(-3px);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert.alert-error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }

        .alert.alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }

        .alert i {
            font-size: 1.3rem;
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

            .content-section {
                padding: 20px 16px;
            }

            .form-group label {
                font-size: 1rem;
            }

            .form-group input,
            .form-group textarea,
            .form-group select {
                font-size: 0.9rem;
                padding: 10px 12px;
            }

            .btn-submit {
                font-size: 1rem;
                padding: 12px 20px;
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
            <h2>Tambah Produk Baru</h2>
        </header>

        <section class="content-section">
            <h3><i class="bi bi-box-seam"></i> Formulir Tambah Produk</h3>

            <a href="produk.php" class="btn-back">
                <i class="bi bi-arrow-left-circle"></i> Kembali ke Daftar Produk
            </a>

            <?php if ($error): ?>
                <div class="alert alert-error" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i> <?= $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle-fill"></i> <?= $success; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="kode_barang">Kode Barang*</label>
                    <input type="text" id="kode_barang" name="kode_barang" required
                        value="<?= $kode_barang; ?>">
                </div>

                <div class="form-group">
                    <label for="name">Nama Produk*</label>
                    <input type="text" id="name" name="name" required
                        value="<?= $name; ?>">
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="4"><?= $description; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="price">Harga (Rp)*</label>
                    <input type="number" id="price" name="price" min="0" step="0.01" required
                        value="<?= $price; ?>">
                </div>

                <div class="form-group">
                    <label for="stock">Stok*</label>
                    <input type="number" id="stock" name="stock" min="0" required
                        value="<?= $stock; ?>">
                </div>

                <div class="form-group">
                    <label for="category_id">Kategori*</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php
                        // Reset pointer to fetch kategori again in case of repopulate form
                        mysqli_data_seek($kategoriResult, 0);
                        while ($cat = mysqli_fetch_assoc($kategoriResult)):
                        ?>
                            <option value="<?= $cat['id']; ?>" <?= ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="location">Lokasi Rak Penyimpanan</label>
                    <input type="text" id="location" name="location"
                        value="<?= $location; ?>">
                </div>

                <div class="form-group">
                    <label for="date_in">Tanggal Masuk*</label>
                    <input type="date" id="date_in" name="date_in"
                        value="<?= $date_in; ?>" required>
                </div>

                <button type="submit" name="submit" class="btn-submit">
                    <i class="bi bi-save"></i> Simpan Produk
                </button>
            </form>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>