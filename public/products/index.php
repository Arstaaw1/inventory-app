<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';

checkAuth();

$search = trim($_GET['search'] ?? '');
$category_id = (int)($_GET['category_id'] ?? 0);

// Pengaturan Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 5; // Jumlah data per halaman
$offset = ($page - 1) * $limit;

// Query Hitung Total Data
$countQuery = "SELECT COUNT(*) FROM products p WHERE 1=1";
$params = [];

if ($search !== '') {
    $countQuery .= " AND (p.name LIKE :search OR p.sku LIKE :search)";
    $params['search'] = "%{$search}%";
}

if ($category_id > 0) {
    $countQuery .= " AND p.category_id = :category_id";
    $params['category_id'] = $category_id;
}

$stmtCount = $pdo->prepare($countQuery);
$stmtCount->execute($params);
$totalData = $stmtCount->fetchColumn();
$totalPages = ceil($totalData / $limit);

// Query Data Utama
$query = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";

if ($search !== '') {
    $query .= " AND (p.name LIKE :search OR p.sku LIKE :search)";
}

if ($category_id > 0) {
    $query .= " AND p.category_id = :category_id";
}

$query .= " ORDER BY p.id DESC LIMIT {$limit} OFFSET {$offset}";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

include __DIR__ . '/../../views/header.php';
?>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-boxes me-2"></i>Daftar Produk Inventory</h5>
        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <a href="/products/add.php" class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Tambah Produk
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body p-4">
        <!-- Form Filter & Cari -->
        <form method="GET" action="/products/index.php" class="row g-3 mb-4">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Cari nama atau SKU produk..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-4">
                <select name="category_id" class="form-select bg-light">
                    <option value="">-- Semua Kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $category_id === (int)$cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold"><i class="bi bi-filter me-1"></i> Filter</button>
                <a href="/products/index.php" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>

        <!-- Tabel Produk -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light text-uppercase small fw-bold">
                    <tr>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><span class="badge bg-light text-dark border fw-medium"><?= htmlspecialchars($p['sku']) ?></span></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($p['name']) ?></td>
                                <td><span class="badge bg-secondary-subtle text-secondary"><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></span></td>
                                <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                                <td class="text-center fw-bold"><?= number_format($p['stock']) ?></td>
                                <td class="text-center">
                                    <?php if ($p['stock'] <= $p['minimum_stock']): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-exclamation-triangle me-1"></i> Stok Rendah</span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i> Aman</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Data produk tidak ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tombol Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav class="d-flex justify-content-between align-items-center mt-4">
                <span class="small text-muted">Menampilkan halaman <?= $page ?> dari <?= $totalPages ?> (Total <?= $totalData ?> data)</span>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&category_id=<?= $category_id ?>">Sebelumnya</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category_id=<?= $category_id ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&category_id=<?= $category_id ?>">Selanjutnya</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../views/footer.php'; ?>