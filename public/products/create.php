<?php
require_once __DIR__ . '/../../helpers/auth.php';
check_role(['admin']); // Khusus Admin

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/escape.php';
require_once __DIR__ . '/../../helpers/validation.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $category_id = validate_positive_int($_POST['category_id'] ?? 0);
    $price = validate_non_negative_number($_POST['price'] ?? -1);
    $stock = validate_non_negative_number($_POST['stock'] ?? -1);
    $min_stock = validate_non_negative_number($_POST['minimum_stock'] ?? -1);

    if (!$sku || !$name || !$category_id || $price === false || $stock === false || $min_stock === false) {
        $errors[] = 'Semua field wajib diisi dengan benar!';
    } else {
        // Cek Keunikan SKU
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE sku = :sku');
        $stmt->execute(['sku' => $sku]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'SKU sudah digunakan oleh produk lain.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO products (category_id, sku, name, price, stock, minimum_stock) VALUES (:cat, :sku, :name, :price, :stock, :min_stock)');
        $stmt->execute([
            'cat' => $category_id,
            'sku' => $sku,
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'min_stock' => $min_stock
        ]);
        header('Location: index.php');
        exit;
    }
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<?php include __DIR__ . '/../../views/header.php'; ?>

<h2>Tambah Produk Baru</h2>

<?php foreach ($errors as $err): ?>
    <div class="alert"><?= e($err) ?></div>
<?php endforeach; ?>

<form method="POST" style="max-width: 500px;">
    <div class="form-group">
        <label>SKU (Unik)</label>
        <input type="text" name="sku" required>
    </div>
    <div class="form-group">
        <label>Nama Produk</label>
        <input type="text" name="name" required>
    </div>
    <div class="form-group">
        <label>Kategori</label>
        <select name="category_id" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Harga (Rp)</label>
        <input type="number" step="0.01" name="price" required>
    </div>
    <div class="form-group">
        <label>Stok Awal</label>
        <input type="number" name="stock" value="0" required>
    </div>
    <div class="form-group">
        <label>Minimum Stok (Peringatan)</label>
        <input type="number" name="minimum_stock" value="5" required>
    </div>
    <button type="submit" class="btn">Simpan Produk</button>
    <a href="index.php" class="btn btn-warning">Batal</a>
</form>

<?php include __DIR__ . '/../../views/footer.php'; ?>