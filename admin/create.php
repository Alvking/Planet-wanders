<?php
$pageTitle = 'Add Vehicle';
require_once __DIR__ . '/auth.php';

$id = (int)($_GET['id'] ?? 0);
$editing = $id > 0;
$row = null;

// Fetch record if editing
if ($editing) {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        header('Location: index.php');
        exit;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name'] ?? '');
    $slug         = trim($_POST['slug'] ?? '');
    $vehicle_code = trim($_POST['vehicle_code'] ?? '');
    $rate         = (float)($_POST['rate'] ?? 0);
    $capacity     = (int)($_POST['capacity'] ?? 6);
    $image_url    = trim($_POST['image_url'] ?? '');
    $status       = $_POST['status'] ?? 'available';

    // Parse features comma-separated string into JSON array
    $rawFeatures  = explode(',', $_POST['features'] ?? '');
    $cleanArray   = array_values(array_filter(array_map('trim', $rawFeatures)));
    $featuresJson = json_encode($cleanArray);

    if ($editing) {
        $sql = "UPDATE vehicles 
                SET name = ?, slug = ?, vehicle_code = ?, daily_rate_usd = ?, passenger_capacity = ?, features = ?, image_url = ?, status = ? 
                WHERE id = ?";
        $params = [$name, $slug, $vehicle_code, $rate, $capacity, $featuresJson, $image_url, $status, $id];
    } else {
        $sql = "INSERT INTO vehicles (name, slug, vehicle_code, daily_rate_usd, passenger_capacity, features, image_url, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$name, $slug, $vehicle_code, $rate, $capacity, $featuresJson, $image_url, $status];
    }

    try {
        $pdo->prepare($sql)->execute($params);
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        $error = 'Failed to save vehicle. Unique code or slug might already exist.';
    }
}

// Parse existing features or set defaults
$featuresDefault = 'Pop-up Roof, Long-Range Radio, Cooler Box, In-Car USB';
$featuresVal = isset($row['features']) ? implode(', ', json_decode($row['features'], true) ?? []) : $featuresDefault;

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/sidebar.php';
require __DIR__ . '/../../includes/topbar.php';
?>

<main class="main">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><?= $editing ? 'Edit' : 'Add' ?> Vehicle</h1>
            <div class="page-subtitle">Configure fleet specifications, capacity, and rate settings.</div>
        </div>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Fleet
        </a>
    </div>

    <section class="card border-0 shadow-sm p-4 rounded-3">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <div><?= e($error) ?></div>
            </div>
        <?php endif; ?>

        <form method="post" id="vehicleForm">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Vehicle Name</label>
                    <input class="form-control" name="name" id="vehicleName" required value="<?= e($row['name'] ?? '') ?>" placeholder="e.g. Toyota Land Cruiser 4x4">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Slug</label>
                    <input class="form-control" name="slug" id="vehicleSlug" required value="<?= e($row['slug'] ?? '') ?>" placeholder="e.g. toyota-land-cruiser-4x4">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Vehicle Code</label>
                    <input class="form-control" name="vehicle_code" required value="<?= e($row['vehicle_code'] ?? '') ?>" placeholder="e.g. LCR-001">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Daily Rate (USD)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input class="form-control" type="number" step="0.01" name="rate" value="<?= e((string)($row['daily_rate_usd'] ?? '150.00')) ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Passenger Capacity</label>
                    <input class="form-control" type="number" name="capacity" min="1" max="50" value="<?= e((string)($row['passenger_capacity'] ?? 6)) ?>">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Vehicle Features</label>
                    <input class="form-control" name="features" value="<?= e($featuresVal) ?>">
                    <div class="form-text">Separate features with commas (e.g. Pop-up Roof, Fridge, Wi-Fi).</div>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Image URL</label>
                    <input class="form-control" name="image_url" value="<?= e($row['image_url'] ?? '') ?>" placeholder="https://example.com/vehicle-image.jpg">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select class="form-select" name="status">
                        <?php foreach (['available', 'maintenance', 'retired'] as $s): ?>
                            <option value="<?= $s ?>" <?= $s === ($row['status'] ?? 'available') ? 'selected' : '' ?>>
                                <?= ucfirst($s) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-warning text-dark fw-bold px-4 me-2">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Save Vehicle
                </button>
                <a href="index.php" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </section>
</main>

<script>
// Auto-generate slug from vehicle name if creating new record
const nameInput = document.getElementById('vehicleName');
const slugInput = document.getElementById('vehicleSlug');

if (nameInput && slugInput && !<?= $editing ? 'true' : 'false' ?>) {
    nameInput.addEventListener('input', () => {
        slugInput.value = nameInput.value
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    });
}
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>