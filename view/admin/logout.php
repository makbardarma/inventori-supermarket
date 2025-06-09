<?php
session_start();
session_unset(); // Menghapus semua data sesi
session_destroy(); // Mengakhiri sesi

header("Location: login.php"); // Arahkan ke halaman login (ubah jika lokasinya berbeda)
exit;
