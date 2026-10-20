<?php
require_once __DIR__ . '/../../helpers/auth.php';
check_role(['admin']);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/escape.php';
require_once __DIR__ . '/../../helpers/validation.php';

$id = validate_positive_int($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    exit('Produk tidak ditemukan!');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $category_id = validate_positive_int($_POST['category_id'] ?? 0);
    $price = validate_non_negative_number($_POST['price'] ?? -1);
    $min_stock = validate_non_negative_number($_POST['minimum_stock'] ?? -1);

    if (!$sku || !$name || !$category_id || $price === false || $min_stock === false) {
        $errors[] = 'Semua field wajib diisi dengan benar!';
    } else {
        // Cek Keunikan SKU jika diubah
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE sku = :sku AND id != :id');
        $stmt->execute(['sku' => $sku, 'id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'SKU sudah dipakai produk lain.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE products SET category_id = :cat, sku = :sku, name = :name, price = :price, minimum_stock = :min_stock WHERE id = :id');
        $stmt->execute([
            'cat' => $category_id,
            'sku' => $sku,
            'name' => $name,
            'price' => $price,
            'min_stock' => $min_stock,
            'id' => $id
        ]);
        header('Location: index.php');
        exit;
    }
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<?php include __DIR__ . '/../../views/header.php'; ?>

<h2>Edit Produk</h2>

<?php foreach ($errors as $err): ?>
    <div class="alert"><?= e($err) ?></div>
<?php endforeach; ?>

<form method="POST" style="max-width: 500px;">
    <div class="form-group">
        <label>SKU</label>
        <input type="text" name="sku" value="<?= e($product['sku']) ?>" required>
    </div>
    <div class="form-group">
        <label>Nama Produk</label>
        <input type="text" name="name" value="<?= e($product['name']) ?>" required>
    </div>
    <div class="form-group">
        <label>Kategori</label>
        <select name="category_id" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Harga (Rp)</label>
        <input type="number" step="0.01" name="price" value="<?= e($product['price']) ?>" required>
    </div>
    <div class="form-group">
        <label>Minimum Stok</label>
        <input type="number" name="minimum_stock" value="<?= e($product['minimum_stock']) ?>" required>
    </div>
    <button type="submit" class="btn">Update Produk</button>
    <a href="index.php" class="btn btn-warning">Batal</a>
</form>

<?php include __DIR__ . '/../../views/footer.php'; ?>