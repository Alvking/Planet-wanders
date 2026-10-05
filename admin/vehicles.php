<?php
$pageTitle = 'Vehicles';
require_once __DIR__ . '/auth.php';

// Handle vehicle deletion
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: index.php');
    exit;
}

// Fetch all vehicles from database
$stmt = $pdo->query("SELECT * FROM vehicles ORDER BY id DESC");
$vehicles = $stmt->fetchAll();

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/sidebar.php';
require __DIR__ . '/../../includes/topbar.php';
?>

<main class="main">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Safari Fleet</h1>
            <div class="page-subtitle">Manage vehicle details, daily rates, capacities, and availability.</div>
        </div>
        <a class="btn btn-warning text-dark fw-bold" href="create.php">
            <i class="fa-solid fa-plus me-2"></i>Add Vehicle
        </a>
    </div>

    <section class="card border-0 shadow-sm rounded-3">
        <div class="p-3 border-bottom">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input class="form-control border-start-0 ps-0" id="tableSearch" placeholder="Search fleet by name, code, or features...">
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="dataTable">
                <thead class="table-light">
                    <tr>
                        <th>Vehicle Name</th>
                        <th>Code</th>
                        <th>Capacity</th>
                        <th>Daily Rate</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vehicles)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No vehicles found in the fleet. Click "Add Vehicle" to register one.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($vehicles as $v): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($v['image_url'])): ?>
                                            <img src="<?= e($v['image_url']) ?>" alt="" class="rounded me-2" style="width: 48px; height: 36px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded me-2 bg-light d-flex align-items-center justify-content-center text-muted" style="width: 48px; height: 36px;">
                                                <i class="fa-solid fa-van-shuttle"></i>
                                            </div>
                                        <?php endif; ?>
                                        <strong><?= e($v['name']) ?></strong>
                                    </div>
                                </td>
                                <td><code><?= e($v['vehicle_code']) ?></code></td>
                                <td><i class="fa-solid fa-users me-1 text-muted"></i> <?= (int)$v['passenger_capacity'] ?> Passengers</td>
                                <td><strong>$<?= number_format((float)$v['daily_rate_usd'], 2) ?></strong> / day</td>
                                <td>
                                    <?php 
                                        $statusClass = match($v['status']) {
                                            'available' => 'bg-success-subtle text-success border border-success-subtle',
                                            'maintenance' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                            'retired' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                            default => 'bg-light text-dark'
                                        };
                                    ?>
                                    <span class="badge <?= $statusClass ?> px-2 py-1 rounded-pill">
                                        <?= ucfirst(e($v['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary me-1" href="create.php?id=<?= $v['id'] ?>">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                                    </a>
                                    <a class="btn btn-sm btn-outline-danger" href="?delete=<?= $v['id'] ?>" onclick="return confirm('Are you sure you want to remove this vehicle from the database?');">
                                        <i class="fa-solid fa-trash me-1"></i>Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
document.getElementById('tableSearch')?.addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    document.querySelectorAll('#dataTable tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
    });
});
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>