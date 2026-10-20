<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>
    <!-- Google Font & Bootstrap CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .stat-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4 py-3">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/dashboard.php">
      <i class="bi bi-box-seam-fill fs-4"></i> Inventory System
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
        <li class="nav-item">
          <a class="nav-link text-white opacity-75" href="/dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white opacity-75" href="/products/index.php"><i class="bi bi-boxes me-1"></i> Produk</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white opacity-75" href="/stock/in.php"><i class="bi bi-arrow-down-right-circle me-1"></i> Barang Masuk</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white opacity-75" href="/stock/out.php"><i class="bi bi-arrow-up-right-circle me-1"></i> Barang Keluar</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white opacity-75" href="/stock/history.php"><i class="bi bi-clock-history me-1"></i> Histori Stok</a>
        </li>
      </ul>
      <div class="d-flex align-items-center gap-3">
        <span class="navbar-text text-white small">
          <i class="bi bi-person-circle me-1"></i> <strong><?= htmlspecialchars($_SESSION['username'] ?? '') ?></strong> (<?= htmlspecialchars($_SESSION['role'] ?? '') ?>)
        </span>
        <a href="/logout.php" class="btn btn-outline-light btn-sm rounded-pill px-3"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
      </div>
    </div>
  </div>
</nav>
<div class="container pb-5"></div>