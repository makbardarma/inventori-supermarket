<?php
$host = "localhost";
$user = "root"; // sesuaikan dengan phpMyAdmin kamu
$pass = "";     // biasanya kosong di localhost
$dbname = "inventory_supermarket";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
