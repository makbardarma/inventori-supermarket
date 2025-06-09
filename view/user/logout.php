<?php

echo "Logout file executed!";


session_start();

// Hapus semua data session
session_unset();
session_destroy();

// Hapus cookie session agar benar-benar logout
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirect ke halaman login user
header('Location: loginuser.php');
exit;
