<?php
require_once __DIR__ . '/../../helpers/auth.php';
check_role(['admin']);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/validation.php';

$id = validate_positive_int($_GET['id'] ?? 0);

if ($id) {
    try {
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
    } catch (\PDOException $e) {
        // Mencegah hapus jika ada Histori Transaksi (Aturan RESTRICT FK)
        echo "<script>alert('Gagal menghapus! Produk ini memiliki riwayat transaksi.'); window.location='index.php';</script>";
        exit;
    }
}

header('Location: index.php');
exit;