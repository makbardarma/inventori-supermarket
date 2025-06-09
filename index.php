<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Inventory Supermarket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Aplikasi manajemen inventori supermarket modern">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AOS Animation CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("year").textContent = new Date().getFullYear();
        });
    </script>
</head>

<body>

    <!-- NAVIGATION -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#" style="color: var(--primary);">
                <i class="fas fa-shopping-cart me-2"></i>InventorySupermarket
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex flex-column flex-lg-row gap-2">
                    <a href="view/user/loginuser.php" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="fas fa-user me-2"></i>Login User
                    </a>
                    <a href="view/admin/login.php" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="fas fa-lock me-2"></i>Login Admin
                    </a>
                    <a href="view/user/registrasi.php" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-user-plus me-2"></i>Daftar User
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center text-white" data-aos="fade-up" data-aos-duration="1000">
                    <h1 class="display-4 fw-bold mb-4">Kelola Inventori Supermarket dengan Mudah</h1>
                    <p class="lead mb-5">Solusi digital modern untuk manajemen stok, transaksi, dan laporan real-time</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-light btn-lg px-4 rounded-pill">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                        <a href="#" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                            <i class="fas fa-user-plus me-2"></i>Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Animated elements -->
        <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden">
            <div class="position-absolute top-20 start-20 rounded-circle bg-primary opacity-10" style="width: 300px; height: 300px; animation: float 6s infinite ease-in-out;"></div>
            <div class="position-absolute bottom-10 end-10 rounded-circle bg-accent opacity-10" style="width: 400px; height: 400px; animation: float 8s infinite ease-in-out 2s;"></div>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="py-5 bg-light">
        <div class="container py-5">
            <h2 class="text-center mb-5 section-title" data-aos="fade-down">Fitur Unggulan</h2>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Manajemen Produk</h4>
                            <p class="card-text text-muted">Kelola produk dengan mudah, termasuk tambah, edit, dan hapus dengan antarmuka intuitif.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Analisis Real-time</h4>
                            <p class="card-text text-muted">Dapatkan laporan penjualan dan stok secara real-time dengan visualisasi yang mudah dipahami.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Multi-level Akses</h4>
                            <p class="card-text text-muted">Sistem role-based untuk admin, manajer, dan staff dengan hak akses yang berbeda.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-barcode"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Scan Barcode</h4>
                            <p class="card-text text-muted">Fitur scan barcode untuk input data produk yang cepat dan akurat.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Notifikasi Stok</h4>
                            <p class="card-text text-muted">Dapatkan pemberitahuan ketika stok produk hampir habis atau kadaluarsa.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Responsive Design</h4>
                            <p class="card-text text-muted">Akses aplikasi dari berbagai perangkat dengan tampilan yang optimal.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CAROUSEL -->
    <section class="py-5">
        <div class="container py-5">
            <h2 class="text-center mb-5 section-title" data-aos="fade-up">Galeri Supermarket</h2>
            <div class="row justify-content-center">
                <div class="col-lg-10" data-aos="zoom-in">
                    <div id="galleryCarousel" class="carousel slide shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        </div>
                        <div class="carousel-inner rounded-4">
                            <div class="carousel-item active">
                                <img src="assets/images/pica.jpg" class="d-block w-100" alt="Supermarket">
                                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded-3 p-3">
                                    <h5>Manajemen Produk Modern</h5>
                                    <p>Sistem pengelolaan produk yang terintegrasi</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img src="assets/images/super.jpg" class="d-block w-100" alt="Supermarket">
                                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded-3 p-3">
                                    <h5>Antarmuka Pengguna Intuitif</h5>
                                    <p>Mudah digunakan oleh semua staff</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img src="assets/images/lotim.jpg" class="d-block w-100" alt="Supermarket">
                                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded-3 p-3">
                                    <h5>Laporan Real-time</h5>
                                    <p>Pantau perkembangan bisnis kapan saja</p>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon bg-primary rounded-circle p-3" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon bg-primary rounded-circle p-3" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="py-5 bg-primary bg-opacity-10">
        <div class="container py-5">
            <h2 class="text-center mb-5 section-title" data-aos="fade-up">Apa Kata Mereka?</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-aos="zoom-in">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4 text-center">
                                        <img src="https://randomuser.me/api/portraits/women/32.jpg" class="rounded-circle mb-3" width="80" height="80" alt="Testimonial">
                                        <h5 class="mb-3">Sarah Johnson</h5>
                                        <p class="text-muted mb-4">"Aplikasi ini sangat membantu tim kami dalam mengelola inventori. Antarmukanya sederhana namun powerful."</p>
                                        <div class="text-warning mb-3">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <small class="text-muted">Manager, Supermarket Sejahtera</small>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4 text-center">
                                        <img src="https://randomuser.me/api/portraits/men/75.jpg" class="rounded-circle mb-3" width="80" height="80" alt="Testimonial">
                                        <h5 class="mb-3">David Wilson</h5>
                                        <p class="text-muted mb-4">"Fitur laporan real-time sangat membantu dalam pengambilan keputusan bisnis kami. Sangat direkomendasikan!"</p>
                                        <div class="text-warning mb-3">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <small class="text-muted">Owner, Toko Makmur</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon bg-primary rounded-circle p-2" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon bg-primary rounded-circle p-2" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5 bg-primary text-white">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center" data-aos="fade-up">
                    <h2 class="display-5 fw-bold mb-4">Siap Mengubah Cara Anda Mengelola Inventori?</h2>
                    <p class="lead mb-5">Daftar sekarang dan dapatkan 14 hari gratis untuk mencoba semua fitur kami</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-light btn-lg px-4 rounded-pill">
                            <i class="fas fa-rocket me-2"></i>Mulai Sekarang
                        </a>
                        <a href="#" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                            <i class="fas fa-play-circle me-2"></i>Lihat Demo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="py-5">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="text-white mb-4">
                        <i class="fas fa-shopping-cart me-2"></i>InventorySupermarket
                    </h5>
                    <p class="text-white-50">Solusi modern untuk manajemen inventori supermarket dengan fitur lengkap dan antarmuka yang user-friendly.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="text-white mb-4">Tautan Cepat</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Beranda</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Fitur</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Harga</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Tentang Kami</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="text-white mb-4">Dukungan</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">FAQ</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Pusat Bantuan</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Kebijakan Privasi</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h6 class="text-white mb-4">Hubungi Kami</h6>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-white"></i> Jakarta, Indonesia</li>
                        <li class="mb-2"><i class="fas fa-phone me-2 text-white"></i> +62 123 4567 890</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2 text-white"></i> info@inventorypro.com</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-white-50">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-white-50">&copy; <span id="year"></span> InventorySupermarket. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-white-50">KELOMPOK 11</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Action Button -->
    <a href="#" class="floating-btn" data-aos="fade-up" data-aos-delay="500">
        <i class="fas fa-comment-dots"></i>
    </a>

    <!-- JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="assets/js/script.js"></script>
</body>

</html>