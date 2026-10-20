<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';

checkAuth();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');

    if ($product_id > 0 && $quantity > 0) {
        $pdo->beginTransaction();
        try {
            $stmtIn = $pdo->prepare("INSERT INTO stock_movements (product_id, type, quantity, notes, user_id) VALUES (:product_id, 'IN', :quantity, :notes, :user_id)");
            $stmtIn->execute([
                'product_id' => $product_id,
                'quantity' => $quantity,
                'notes' => $notes,
                'user_id' => $_SESSION['user_id']
            ]);

            $stmtUpdate = $pdo->prepare("UPDATE products SET stock = stock + :quantity WHERE id = :id");
            $stmtUpdate->execute(['quantity' => $quantity, 'id' => $product_id]);

            $pdo->commit();
            $success = 'Transaksi barang masuk berhasil dicatat!';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Gagal memproses transaksi: ' . $e->getMessage();
        }
    } else {
        $error = 'Harap pilih produk dan masukkan jumlah barang yang valid!';
    }
}

$products = $pdo->query("SELECT id, sku, name, stock FROM products ORDER BY name ASC")->fetchAll();

include __DIR__ . '/../../views/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-arrow-down-right-circle me-2"></i>Catat Barang Masuk (Stock In)</h5>
            </div>
            <div class="card-body p-4">

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div><?= htmlspecialchars($success) ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/stock/in.php">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Produk</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    [<?= htmlspecialchars($p['sku']) ?>] <?= htmlspecialchars($p['name']) ?> (Stok Saat Ini: <?= $p['stock'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah Barang Masuk</label>
                        <input type="number" name="quantity" class="form-control" min="1" placeholder="Masukkan jumlah unit" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Catatan / Keterangan</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Restock dari supplier, Pembelian baru, dll."></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="/dashboard.php" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-success px-4 fw-semibold"><i class="bi bi-plus-circle me-1"></i> Proses Barang Masuk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../views/footer.php'; ?>