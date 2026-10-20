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
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
        $stmt->execute(['id' => $product_id]);
        $product = $stmt->fetch();

        if ($product) {
            if ($product['stock'] >= $quantity) {
                $pdo->beginTransaction();
                try {
                    $stmtOut = $pdo->prepare("INSERT INTO stock_movements (product_id, type, quantity, notes, user_id) VALUES (:product_id, 'OUT', :quantity, :notes, :user_id)");
                    $stmtOut->execute([
                        'product_id' => $product_id,
                        'quantity' => $quantity,
                        'notes' => $notes,
                        'user_id' => $_SESSION['user_id']
                    ]);

                    $stmtUpdate = $pdo->prepare("UPDATE products SET stock = stock - :quantity WHERE id = :id");
                    $stmtUpdate->execute(['quantity' => $quantity, 'id' => $product_id]);

                    $pdo->commit();
                    $success = 'Transaksi barang keluar berhasil dicatat!';
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = 'Gagal memproses transaksi: ' . $e->getMessage();
                }
            } else {
                $error = "Stok tidak mencukupi! Stok saat ini: {$product['stock']}, diminta: {$quantity}";
            }
        } else {
            $error = 'Produk tidak ditemukan!';
        }
    } else {
        $error = 'Harap pilih produk dan masukkan jumlah yang valid!';
    }
}

$products = $pdo->query("SELECT id, sku, name, stock FROM products ORDER BY name ASC")->fetchAll();

include __DIR__ . '/../../views/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-arrow-up-right-circle me-2"></i>Catat Barang Keluar (Stock Out)</h5>
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

                <form method="POST" action="/stock/out.php">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Produk</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    [<?= htmlspecialchars($p['sku']) ?>] <?= htmlspecialchars($p['name']) ?> (Sisa Stok: <?= $p['stock'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah Barang Keluar</label>
                        <input type="number" name="quantity" class="form-control" min="1" placeholder="Masukkan jumlah unit" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Catatan / Keterangan</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Penjualan ke PT ABC, Barang Rusak, dll."></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="/dashboard.php" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-danger px-4 fw-semibold"><i class="bi bi-dash-circle me-1"></i> Proses Barang Keluar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../views/footer.php'; ?>