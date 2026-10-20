<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';

checkAuth();

$stmt = $pdo->query("
    SELECT sm.*, p.name AS product_name, p.sku, u.username 
    FROM stock_movements sm 
    JOIN products p ON sm.product_id = p.id 
    LEFT JOIN users u ON sm.user_id = u.id 
    ORDER BY sm.created_at DESC
");
$history = $stmt->fetchAll();

include __DIR__ . '/../../views/header.php';
?>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-clock-history me-2"></i>Histori Pergerakan Stok</h5>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light text-uppercase small fw-bold">
                    <tr>
                        <th>Tanggal & Waktu</th>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th class="text-center">Tipe Transaksi</th>
                        <th class="text-center">Jumlah</th>
                        <th>Catatan</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($history) > 0): ?>
                        <?php foreach ($history as $h): ?>
                            <tr>
                                <td class="small text-muted"><?= date('d M Y, H:i', strtotime($h['created_at'])) ?></td>
                                <td><span class="badge bg-light text-dark border fw-medium"><?= htmlspecialchars($h['sku']) ?></span></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($h['product_name']) ?></td>
                                <td class="text-center">
                                    <?php if ($h['type'] === 'IN'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-arrow-down-right me-1"></i> MASUK</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-arrow-up-right me-1"></i> KELUAR</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center fw-bold <?= $h['type'] === 'IN' ? 'text-success' : 'text-danger' ?>">
                                    <?= $h['type'] === 'IN' ? '+' : '-' ?><?= number_format($h['quantity']) ?>
                                </td>
                                <td><span class="small text-muted"><?= htmlspecialchars($h['notes'] ?? '-') ?></span></td>
                                <td><span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bi bi-person me-1"></i> <?= htmlspecialchars($h['username'] ?? 'System') ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada histori pergerakan stok.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../views/footer.php'; ?>