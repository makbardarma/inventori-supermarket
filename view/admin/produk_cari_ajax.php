<?php
include '../../includes/database.php';

$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

$query = "SELECT p.*, c.name AS category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id";

if (!empty($search)) {
    $query .= " WHERE p.kode_barang LIKE '$search%' 
                OR p.name LIKE '$search%' 
                OR c.name LIKE '$search%'";
}

$query .= " ORDER BY p.id DESC LIMIT 50"; // Limit untuk efisiensi
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['kode_barang']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . nl2br(htmlspecialchars($row['description'])) . "</td>";
        echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
        echo "<td>" . number_format($row['price'], 2, ',', '.') . "</td>";
        echo "<td>" . (int)$row['stock'] . "</td>";
        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
        echo "<td>" . (!empty($row['date_in']) ? date('d-m-Y', strtotime($row['date_in'])) : '-') . "</td>";
        echo "<td>" . (!empty($row['date_out']) ? date('d-m-Y', strtotime($row['date_out'])) : '-') . "</td>";
        echo "<td>
                <a href='produk_edit.php?id={$row['id']}'>✏️ Edit</a> |
                <a href='produk_hapus.php?id={$row['id']}' onclick=\"return confirm('Yakin ingin menghapus produk ini?')\">🗑️ Hapus</a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10'>Tidak ada data ditemukan.</td></tr>";
}
