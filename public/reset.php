<?php
require_once __DIR__ . '/../config/database.php';

// Buat hash password 'password123' baru sesuai versi PHP aktif
$passwordBaru = 'password123';
$hashBaru = password_hash($passwordBaru, PASSWORD_DEFAULT);

try {
    // Reset password untuk akun admin dan staff
    $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash WHERE username IN ('admin', 'staff')");
    $stmt->execute(['hash' => $hashBaru]);
    
    echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
    echo "<h2 style='color:green;'>RESET PASSWORD BERHASIL!</h2>";
    echo "<p>Password untuk akun <b>admin</b> & <b>staff</b> sekarang diset ke: <b>password123</b></p>";
    echo "<a href='login.php' style='display:inline-block; padding:10px 20px; background:#007bff; color:white; text-decoration:none; border-radius:4px;'>Ke Halaman Login</a>";
    echo "</div>";
} catch (\PDOException $e) {
    echo "<h2 style='color:red;'>Gagal reset: " . $e->getMessage() . "</h2>";
}