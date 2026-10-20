<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';

checkAuth();

// Query untuk statistik dashboard
$totalProduk = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$nilaiStok = $pdo->query("SELECT SUM(stock * price) FROM products")->fetchColumn() ?? 0;
$stokRendah = $pdo->query("SELECT COUNT(*) FROM products WHERE stock <= minimum_stock")->fetchColumn();
$totalMasuk = $pdo->query("SELECT SUM(quantity) FROM stock_movements WHERE type = 'IN'")->fetchColumn() ?? 0;
$totalKeluar = $pdo->query("SELECT SUM(quantity) FROM stock_movements WHERE type = 'OUT'")->fetchColumn() ?? 0;

include __DIR__ . '/../views/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Dashboard Ringkasan</h3>
        <p class="text-muted small mb-0">Pantau performa dan pergerakan stok barang kamu secara real-time.</p>
    </div>
    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
        <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y') ?>
    </span>
</div>

<div class="row g-4">
    <!-- Total Produk -->
    <div class="col-md-4">
        <div class="card stat-card shadow-sm h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Produk</span>
                    <h2 class="fw-bold text-dark mt-2 mb-0"><?= number_format($totalProduk) ?></h2>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Nilai Total Stok -->
    <div class="col-md-4">
        <div class="card stat-card shadow-sm h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Nilai Total Stok</span>
                    <h2 class="fw-bold text-dark mt-2 mb-0">Rp <?= number_format($nilaiStok, 0, ',', '.') ?></h2>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-currency-dollar"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stok Rendah -->
    <div class="col-md-4">
        <div class="card stat-card shadow-sm h-100 p-3 border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Stok Perlu Restock</span>
                    <h2 class="fw-bold text-danger mt-2 mb-0"><?= number_format($stokRendah) ?> <span class="fs-6 text-muted font-normal">Produk</span></h2>
                </div>
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Barang Masuk -->
    <div class="col-md-6">
        <div class="card stat-card shadow-sm h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Barang Masuk</span>
                    <h2 class="fw-bold text-dark mt-2 mb-0"><?= number_format($totalMasuk) ?> <span class="fs-6 text-muted">Item</span></h2>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-arrow-down-left-square"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Barang Keluar -->
    <div class="col-md-6">
        <div class="card stat-card shadow-sm h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Barang Keluar</span>
                    <h2 class="fw-bold text-dark mt-2 mb-0"><?= number_format($totalKeluar) ?> <span class="fs-6 text-muted">Item</span></h2>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-arrow-up-right-square"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../views/footer.php'; ?>