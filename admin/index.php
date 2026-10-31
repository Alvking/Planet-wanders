<?php
// admin/index.php
require_once __DIR__ . '/auth.php';
check_admin_auth();

// Helper function to safely escape HTML output if not already defined globally
if (!function_exists('esc')) {
    function esc(?string $string): string {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

$tab = $_GET['tab'] ?? 'inquiries';
$msg = '';

// -------------------------------------------------------------
// POST ACTIONS (INQUIRIES, PACKAGES, CATEGORIES, RATES, DESTINATIONS, VEHICLES)
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Update Inquiry Status
    if ($action === 'update_inquiry_status') {
        $inqId = (int)$_POST['inquiry_id'];
        $status = $_POST['status'];
        db_execute($db, "UPDATE inquiries SET status = ? WHERE id = ?", [$status, $inqId]);
        $msg = 'Inquiry status updated successfully.';
    }

    // Update Exchange Rate (KES/USD)
    if ($action === 'update_rates') {
        $kesRate = (float)$_POST['kes_rate'];
        if ($kesRate > 0) {
            db_execute($db, "UPDATE exchange_rates SET rate_to_usd = ? WHERE currency_code = 'KES'", [$kesRate]);
            $msg = 'Exchange rate updated to KSh ' . number_format($kesRate, 2);
        }
    }

    // Add or Edit Safari Package
    if ($action === 'save_package') {
        $title       = trim($_POST['title']);
        $catid = $_POST['category_id'] ?? null;
        $code        = trim($_POST['package_code']);
        $days        = (int)$_POST['days'];
        $nights      = (int)$_POST['nights'];
        $price       = (float)$_POST['base_price_usd'];
        $ribbon      = trim($_POST['ribbon_badge']);
        $img         = trim($_POST['featured_image']);
        $overview    = trim($_POST['overview']);
        $pkgId       = (int)($_POST['package_id'] ?? 0);
        $slug        = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));

        if ($pkgId > 0) {
            db_execute($db, "
                UPDATE packages SET 
                    category_id = ?, title = ?, slug = ?, package_code = ?, days = ?, 
                    nights = ?, base_price_usd = ?, ribbon_badge = ?, featured_image = ?, overview = ?
                WHERE id = ?
            ", [$catId, $title, $slug, $code, $days, $nights, $price, $ribbon, $img, $overview, $pkgId]);
            $msg = 'Package updated successfully.';
        } else {
            db_execute($db, "
                INSERT INTO packages (category_id, title, slug, package_code, days, nights, base_price_usd, ribbon_badge, featured_image, overview)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [$catId, $title, $slug, $code, $days, $nights, $price, $ribbon, $img, $overview]);
            $msg = 'New safari package created.';
        }
    }

    // Delete Package
    if ($action === 'delete_package') {
        $pId = (int)$_POST['package_id'];
        db_execute($db, "DELETE FROM packages WHERE id = ?", [$pId]);
        $msg = 'Package removed.';
    }

    // Add or Edit Vehicle
    if ($action === 'save_vehicle') {
        $vId         = (int)($_POST['vehicle_id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $type        = trim($_POST['type'] ?? '');
        $capacity    = (int)($_POST['capacity'] ?? 0);
        $dailyRate   = (float)($_POST['daily_rate_usd'] ?? 0);
        $img         = trim($_POST['image_url'] ?? '');

        if ($vId > 0) {
            db_execute($db, "
                UPDATE vehicles 
                SET name = ?, type = ?, passenger_capacity = ?, daily_rate_usd = ?, image_url = ? 
                WHERE id = ?
            ", [$name, $type, $capacity, $dailyRate, $img, $vId]);
            $msg = 'Vehicle updated successfully.';
        } else {
            db_execute($db, "
                INSERT INTO vehicles (name, type, passenger_capacity, daily_rate_usd, image_url) 
                VALUES (?, ?, ?, ?, ?)
            ", [$name, $type, $capacity, $dailyRate, $img]);
            $msg = 'New vehicle added successfully.';
        }
    }

    // Delete Vehicle
    if ($action === 'delete_vehicle') {
        $vId = (int)$_POST['vehicle_id'];
        db_execute($db, "DELETE FROM vehicles WHERE id = ?", [$vId]);
        $msg = 'Vehicle removed successfully.';
    }

    // Save or Update Category
    if ($action === 'save_category') {
        $catId     = (int)($_POST['category_id'] ?? 0);
        $name      = trim($_POST['name'] ?? '');
        $filterTag = strtolower(trim($_POST['filter_tag'] ?? ''));
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $slug      = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));

        if ($name !== '' && $filterTag !== '') {
            if ($catId > 0) {
                db_execute($db, "
                    UPDATE package_categories 
                    SET name = ?, slug = ?, filter_tag = ?, sort_order = ? 
                    WHERE id = ?
                ", [$name, $slug, $filterTag, $sortOrder, $catId]);
                $msg = 'Category updated successfully.';
            } else {
                db_execute($db, "
                    INSERT INTO package_categories (name, slug, filter_tag, sort_order) 
                    VALUES (?, ?, ?, ?)
                ", [$name, $slug, $filterTag, $sortOrder]);
                $msg = 'New category added.';
            }
        }
    }

    // Delete Category
    if ($action === 'delete_category') {
        $catId = (int)$_POST['category_id'];
        $inUse = db_query($db, "SELECT COUNT(*) as c FROM packages WHERE category_id = ?", [$catId]);
        if (($inUse[0]['c'] ?? 0) > 0) {
            $msg = 'Cannot delete category: packages are currently assigned to it.';
        } else {
            db_execute($db, "DELETE FROM package_categories WHERE id = ?", [$catId]);
            $msg = 'Category removed successfully.';
        }
    }

    // Update Destination Fee
    if ($action === 'update_destination_fee') {
        $dId = (int)$_POST['dest_id'];
        $fee = (float)$_POST['daily_conservation_fee_usd'];
        db_execute($db, "UPDATE destinations SET daily_conservation_fee_usd = ? WHERE id = ?", [$fee, $dId]);
        $msg = 'Conservation fee updated.';
    }
}

// -------------------------------------------------------------
// QUERIES FOR DASHBOARD METRICS & LISTS
// -------------------------------------------------------------
$totalInquiries = (int)(db_query($db, "SELECT COUNT(*) as c FROM inquiries")[0]['c'] ?? 0);
$newInquiries   = (int)(db_query($db, "SELECT COUNT(*) as c FROM inquiries WHERE status = 'new'")[0]['c'] ?? 0);
$totalPackages  = (int)(db_query($db, "SELECT COUNT(*) as c FROM packages")[0]['c'] ?? 0);

// Vehicle Metrics
$totalVehicles  = (int)(db_query($db, "SELECT COUNT(*) as c FROM vehicles")[0]['c'] ?? 0);

// Load Inquiries
$inquiries = db_query($db, "
    SELECT i.*, t.full_name, t.email, t.phone_whatsapp 
    FROM inquiries i
    LEFT JOIN travelers t ON i.traveler_id = t.id
    ORDER BY i.id DESC
");

// Load Packages & Categories
$packages = db_query($db, "
    SELECT p.*, c.name as category_name 
    FROM packages p 
    JOIN package_categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
");
$categories = db_query($db, "SELECT * FROM package_categories ORDER BY sort_order ASC");

// Load Vehicles
$vehicles = db_query($db, "SELECT * FROM vehicles ORDER BY id DESC");

// Load Destinations
$destinations = db_query($db, "SELECT * FROM destinations ORDER BY is_popular DESC, name ASC");

// Load Exchange Rates
$rates = db_query($db, "SELECT * FROM exchange_rates ORDER BY id ASC");
$kesRate = 130.0;
foreach ($rates as $r) {
    if ($r['currency_code'] === 'KES') $kesRate = (float)$r['rate_to_usd'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | Planet Wanders Tours</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Outfit', sans-serif; background-color: #f4f6f8; }
    .sidebar { background: #111712; min-height: 100vh; color: #eaeaea; }
    .sidebar .nav-link { color: #cfd4d0; padding: 0.8rem 1.2rem; border-radius: 8px; margin-bottom: 4px; font-weight: 500; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #d99a26; color: #111712; font-weight: 700; }
    .card-metric { border-radius: 16px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .table-card { border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
    .btn-gold { background: #d99a26; color: #fff; font-weight: 600; border: none; }
    .btn-gold:hover { background: #b87e1a; color: #fff; }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar p-3 d-flex flex-column">
      <div class="d-flex align-items-center gap-2 mb-4 px-2">
        <i class="fa-solid fa-compass text-warning fs-3"></i>
        <div>
          <span class="fw-bold d-block text-white">PLANET WANDERS</span>
          <small class="text-warning text-uppercase" style="font-size: 0.7rem;">Admin Console</small>
        </div>
      </div>

      <ul class="nav flex-column mb-auto">
        <li class="nav-item">
          <a class="nav-link <?= $tab === 'inquiries' ? 'active' : '' ?>" href="index.php?tab=inquiries">
            <i class="fa-solid fa-inbox me-2"></i> Inquiries 
            <?php if ($newInquiries > 0): ?>
              <span class="badge bg-danger ms-auto float-end"><?= $newInquiries ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $tab === 'packages' ? 'active' : '' ?>" href="index.php?tab=packages">
            <i class="fa-solid fa-map-location-dot me-2"></i> Safari Packages
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $tab === 'vehicles' ? 'active' : '' ?>" href="index.php?tab=vehicles">
            <i class="fa-solid fa-car me-2"></i> Vehicles
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $tab === 'categories' ? 'active' : '' ?>" href="index.php?tab=categories">
            <i class="fa-solid fa-tags me-2"></i> Categories & Filters
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $tab === 'destinations' ? 'active' : '' ?>" href="index.php?tab=destinations">
            <i class="fa-solid fa-tree me-2"></i> Parks & Fees
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $tab === 'rates' ? 'active' : '' ?>" href="index.php?tab=rates">
            <i class="fa-solid fa-coins me-2"></i> Exchange Rates
          </a>
        </li>
      </ul>

      <hr class="border-secondary opacity-50">
      <div class="d-flex align-items-center justify-content-between px-2">
        <small class="text-white-50"><i class="fa-solid fa-user me-1"></i> <?= esc($_SESSION['pw_admin_name'] ?? 'Admin') ?></small>
        <a href="logout.php" class="btn btn-sm btn-outline-danger" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-md-9 col-lg-10 p-4">
      
      <!-- Top header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold text-dark mb-0">Management Center</h3>
          <span class="text-muted small">Planet Wanders Tours & Safaris Base</span>
        </div>
        <a href="../index2.php" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3">
          <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Live Site
        </a>
      </div>

      <?php if ($msg): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
          <i class="fa-solid fa-circle-check me-2"></i> <?= esc($msg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Quick Metrics -->
      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <div class="card card-metric p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <small class="text-muted text-uppercase fw-bold">New Inquiries</small>
                <h3 class="fw-bold text-danger mb-0"><?= $newInquiries ?></h3>
              </div>
              <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                <i class="fa-solid fa-envelope-open-text fs-4"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card card-metric p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <small class="text-muted text-uppercase fw-bold">Total Inquiries</small>
                <h3 class="fw-bold text-dark mb-0"><?= $totalInquiries ?></h3>
              </div>
              <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                <i class="fa-solid fa-users fs-4"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card card-metric p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <small class="text-muted text-uppercase fw-bold">Active Packages</small>
                <h3 class="fw-bold text-warning mb-0"><?= $totalPackages ?></h3>
              </div>
              <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                <i class="fa-solid fa-paw fs-4"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card card-metric p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <small class="text-muted text-uppercase fw-bold">Total Vehicles</small>
                <h3 class="fw-bold text-success mb-0"><?= $totalVehicles ?></h3>
              </div>
              <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                <i class="fa-solid fa-car fs-4"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 1: INQUIRIES & LEADS -->
      <?php if ($tab === 'inquiries'): ?>
        <div class="card table-card bg-white p-4">
          <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-inbox text-warning me-2"></i> Client Booking Inquiries</h5>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light small text-uppercase">
                <tr>
                  <th>Ref</th>
                  <th>Client</th>
                  <th>Contact</th>
                  <th>Destination / Date</th>
                  <th>Message / Request</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($inquiries)): ?>
                  <tr><td colspan="7" class="text-center text-muted py-4">No inquiries received yet.</td></tr>
                <?php else: ?>
                  <?php foreach ($inquiries as $inq): ?>
                    <tr>
                      <td class="fw-bold text-dark"><?= esc($inq['inquiry_reference']) ?></td>
                      <td>
                        <span class="fw-bold d-block"><?= esc($inq['full_name'] ?: 'Guest') ?></span>
                        <small class="text-muted"><?= esc($inq['created_at']) ?></small>
                      </td>
                      <td>
                        <a href="mailto:<?= esc($inq['email']) ?>" class="d-block text-decoration-none small"><?= esc($inq['email']) ?></a>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $inq['phone_whatsapp']) ?>" target="_blank" class="text-success text-decoration-none small">
                          <i class="fa-brands fa-whatsapp"></i> <?= esc($inq['phone_whatsapp']) ?>
                        </a>
                      </td>
                      <td>
                        <span class="badge bg-light text-dark border"><?= esc($inq['destination_name'] ?: 'Custom Circuit') ?></span>
                      </td>
                      <td>
                        <div class="small text-secondary" style="max-width: 250px;"><?= nl2br(esc($inq['special_requests'])) ?></div>
                      </td>
                      <td>
                        <?php 
                          $badgeClass = match($inq['status']) {
                            'new' => 'bg-danger',
                            'contacted' => 'bg-warning text-dark',
                            'booked' => 'bg-success',
                            default => 'bg-secondary'
                          };
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= strtoupper(esc($inq['status'])) ?></span>
                      </td>
                      <td>
                        <form method="POST" class="d-flex gap-1">
                          <input type="hidden" name="action" value="update_inquiry_status">
                          <input type="hidden" name="inquiry_id" value="<?= $inq['id'] ?>">
                          <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="new" <?= $inq['status'] === 'new' ? 'selected' : '' ?>>New</option>
                            <option value="contacted" <?= $inq['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                            <option value="quoted" <?= $inq['status'] === 'quoted' ? 'selected' : '' ?>>Quoted</option>
                            <option value="booked" <?= $inq['status'] === 'booked' ? 'selected' : '' ?>>Booked</option>
                            <option value="closed" <?= $inq['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                          </select>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>

      <!-- TAB 2: SAFARI PACKAGES -->
      <?php if ($tab === 'packages'): ?>
        <div class="card table-card bg-white p-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-map-location-dot text-warning me-2"></i> Manage Safari Packages</h5>
            <button class="btn btn-gold btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#packageModal" onclick="resetPackageForm()">
              <i class="fa-solid fa-plus me-1"></i> Add New Package
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light small text-uppercase">
                <tr>
                  <th>Image</th>
                  <th>Title & Duration</th>
                  <th>Category</th>
                  <th>Base USD</th>
                  <th>Estimated KES</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($packages as $pkg): ?>
                  <tr>
                    <td>
                      <img src="<?= esc($pkg['featured_image']) ?>" class="rounded-3" style="width: 60px; height: 45px; object-fit: cover;">
                    </td>
                    <td>
                      <span class="fw-bold d-block text-dark"><?= esc($pkg['title']) ?></span>
                      <small class="text-muted"><?= esc($pkg['days']) ?> Days / <?= esc($pkg['nights']) ?> Nights • Code: <code><?= esc($pkg['package_code']) ?></code></small>
                    </td>
                    <td><span class="badge bg-light text-dark border"><?= esc($pkg['category_name']) ?></span></td>
                    <td class="fw-bold text-success">$<?= number_format($pkg['base_price_usd']) ?></td>
                    <td class="text-muted">KSh <?= number_format($pkg['base_price_usd'] * $kesRate) ?></td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary" onclick='editPackage(<?= json_encode($pkg) ?>)'>
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                      <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this package?');">
                        <input type="hidden" name="action" value="delete_package">
                        <input type="hidden" name="package_id" value="<?= $pkg['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Add/Edit Package Modal -->
        <div class="modal fade" id="packageModal" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4 border-0">
              <form method="POST">
                <input type="hidden" name="action" value="save_package">
                <input type="hidden" name="package_id" id="pkg_id" value="0">
                <div class="modal-header bg-dark text-white rounded-top-4">
                  <h5 class="modal-title" id="pkgModalTitle">Add Safari Package</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                  <div class="row g-3">
                    <div class="col-md-8">
                      <label class="form-label small fw-bold">Package Title</label>
                      <input type="text" name="title" id="pkg_title" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label small fw-bold">Package Code (e.g. mara3)</label>
                      <input type="text" name="package_code" id="pkg_code" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small fw-bold">Category</label>
                      <select name="category_id" id="pkg_category" class="form-select">
                        <?php foreach ($categories as $cat): ?>
                          <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label small fw-bold">Days</label>
                      <input type="number" name="days" id="pkg_days" class="form-control" value="3" required>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label small fw-bold">Nights</label>
                      <input type="number" name="nights" id="pkg_nights" class="form-control" value="2" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small fw-bold">Base Price (USD)</label>
                      <input type="number" step="0.01" name="base_price_usd" id="pkg_price" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small fw-bold">Ribbon Badge (e.g. Most Popular)</label>
                      <input type="text" name="ribbon_badge" id="pkg_badge" class="form-control">
                    </div>
                    <div class="col-12">
                      <label class="form-label small fw-bold">Featured Image URL</label>
                      <input type="url" name="featured_image" id="pkg_image" class="form-control" placeholder="https://..." required>
                    </div>
                    <div class="col-12">
                      <label class="form-label small fw-bold">Overview Description</label>
                      <textarea name="overview" id="pkg_overview" rows="3" class="form-control" required></textarea>
                    </div>
                  </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                  <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-gold rounded-pill px-4">Save Package</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- TAB: VEHICLES -->
      <?php if ($tab === 'vehicles'): ?>
        <div class="card table-card bg-white p-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-car text-warning me-2"></i> Manage Safari Vehicles</h5>
            <button class="btn btn-gold btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#vehicleModal" onclick="resetVehicleForm()">
              <i class="fa-solid fa-plus me-1"></i> Add New Vehicle
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light small text-uppercase">
                <tr>
                  <th>Image</th>
                  <th>Vehicle Name</th>
                  <th>Type</th>
                  <th>Capacity</th>
                  <th>Daily Rate (USD)</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($vehicles)): ?>
                  <tr><td colspan="6" class="text-center text-muted py-4">No vehicles added yet.</td></tr>
                <?php else: ?>
                  <?php foreach ($vehicles as $v): ?>
                    <tr>
                      <td>
                        <img src="<?= esc($v['image_url']) ?>" class="rounded-3" style="width: 60px; height: 45px; object-fit: cover;">
                      </td>
                      <td class="fw-bold text-dark"><?= esc($v['name']) ?></td>
                      <td><span class="badge bg-light text-dark border"><?= esc($v['type']) ?></span></td>
                      <td><?= esc($v['passenger_capacity']) ?> Passengers</td>
                      <td class="fw-bold text-success">$<?= number_format($v['daily_rate_usd'], 2) ?></td>
                      <td>
                        <button class="btn btn-sm btn-outline-primary" onclick='editVehicle(<?= json_encode($v) ?>)'>
                          <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this vehicle?');">
                          <input type="hidden" name="action" value="delete_vehicle">
                          <input type="hidden" name="vehicle_id" value="<?= $v['id'] ?>">
                          <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Add/Edit Vehicle Modal -->
        <div class="modal fade" id="vehicleModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0">
              <form method="POST">
                <input type="hidden" name="action" value="save_vehicle">
                <input type="hidden" name="vehicle_id" id="veh_id" value="0">
                <div class="modal-header bg-dark text-white rounded-top-4">
                  <h5 class="modal-title" id="vehModalTitle">Add Vehicle</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                  <div class="mb-3">
                    <label class="form-label small fw-bold">Vehicle Name</label>
                    <input type="text" name="name" id="veh_name" class="form-control" placeholder="e.g. 4x4 Land Cruiser Safari Spec" required>
                  </div>
                  <div class="row g-3 mb-3">
                    <div class="col-md-6">
                      <label class="form-label small fw-bold">Vehicle Type</label>
                      <input type="text" name="type" id="veh_type" class="form-control" placeholder="e.g. 4x4 Cruiser / Tour Van" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small fw-bold">Capacity (Passengers)</label>
                      <input type="number" name="capacity" id="veh_capacity" class="form-control" value="6" required>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small fw-bold">Daily Rate (USD)</label>
                    <input type="number" step="0.01" name="daily_rate_usd" id="veh_rate" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small fw-bold">Image URL</label>
                    <input type="url" name="image_url" id="veh_image" class="form-control" placeholder="https://..." required>
                  </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                  <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-gold rounded-pill px-4">Save Vehicle</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- TAB 3: DESTINATIONS & FEES -->
      <?php if ($tab === 'destinations'): ?>
        <div class="card table-card bg-white p-4">
          <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-tree text-success me-2"></i> Destinations & Calculator Conservation Fees</h5>
          <p class="text-muted small">These daily fees are directly used by the <strong>Live Safari Cost Calculator</strong> on your homepage.</p>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light small text-uppercase">
                <tr>
                  <th>Destination</th>
                  <th>Region / Country</th>
                  <th>Daily Conservation / Park Fee (USD)</th>
                  <th>Update</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($destinations as $d): ?>
                  <tr>
                    <td class="fw-bold text-dark"><?= esc($d['name']) ?></td>
                    <td><?= esc($d['region'] ?: $d['country']) ?></td>
                    <td>
                      <form method="POST" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="action" value="update_destination_fee">
                        <input type="hidden" name="dest_id" value="<?= $d['id'] ?>">
                        <div class="input-group input-group-sm" style="max-width: 160px;">
                          <span class="input-group-text">$</span>
                          <input type="number" step="0.01" name="daily_conservation_fee_usd" class="form-control" value="<?= esc($d['daily_conservation_fee_usd']) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-dark">Save</button>
                      </form>
                    </td>
                    <td><small class="text-muted">Affects Instant Estimator</small></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>

      <!-- TAB: CATEGORIES & FILTER TAGS -->
      <?php if ($tab === 'categories'): ?>
        <div class="card table-card bg-white p-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="fw-bold text-dark mb-1"><i class="fa-solid fa-tags text-warning me-2"></i> Package Categories & Filter Pills</h5>
              <p class="text-muted small mb-0">These categories generate the filter pill buttons on the homepage.</p>
            </div>
            <button class="btn btn-gold btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="resetCategoryForm()">
              <i class="fa-solid fa-plus me-1"></i> Add Category
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light small text-uppercase">
                <tr>
                  <th>Order</th>
                  <th>Category Name</th>
                  <th>Filter Tag (Slug)</th>
                  <th>Packages Linked</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  $categoriesWithCount = db_query($db, "
                    SELECT c.*, COUNT(p.id) as total_packages 
                    FROM package_categories c 
                    LEFT JOIN packages p ON c.id = p.category_id 
                    GROUP BY c.id 
                    ORDER BY c.sort_order ASC
                  ");
                ?>
                <?php foreach ($categoriesWithCount as $c): ?>
                  <tr>
                    <td class="fw-bold text-muted">#<?= esc($c['sort_order']) ?></td>
                    <td class="fw-bold text-dark"><?= esc($c['name']) ?></td>
                    <td><code><?= esc($c['filter_tag']) ?></code></td>
                    <td>
                      <span class="badge bg-secondary"><?= $c['total_packages'] ?> Packages</span>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary" onclick='editCategory(<?= json_encode($c) ?>)'>
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                      </button>
                      <?php if ($c['filter_tag'] !== 'all'): ?>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete category <?= esc($c['name']) ?>?');">
                          <input type="hidden" name="action" value="delete_category">
                          <input type="hidden" name="category_id" value="<?= $c['id'] ?>">
                          <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Add / Edit Category Modal -->
        <div class="modal fade" id="categoryModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0">
              <form method="POST">
                <input type="hidden" name="action" value="save_category">
                <input type="hidden" name="category_id" id="cat_id" value="0">
                <div class="modal-header bg-dark text-white rounded-top-4">
                  <h5 class="modal-title" id="catModalTitle">Add Category</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                  <div class="mb-3">
                    <label class="form-label small fw-bold">Category Display Name</label>
                    <input type="text" name="name" id="cat_name" class="form-control" placeholder="e.g. Samburu & North" required onkeyup="syncFilterTag(this.value)">
                  </div>
                  <div class="mb-3">
                    <label class="form-label small fw-bold">Filter Tag (lowercase identifier)</label>
                    <input type="text" name="filter_tag" id="cat_filter_tag" class="form-control" placeholder="e.g. samburu" required>
                    <small class="text-muted">Used by front-end JS filter buttons.</small>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small fw-bold">Sort / Display Order</label>
                    <input type="number" name="sort_order" id="cat_sort_order" class="form-control" value="1" required>
                  </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                  <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-gold rounded-pill px-4">Save Category</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- TAB 4: EXCHANGE RATES -->
      <?php if ($tab === 'rates'): ?>
        <div class="card table-card bg-white p-4" style="max-width: 600px;">
          <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-coins text-warning me-2"></i> Live Exchange Rates</h5>
          <p class="text-muted small">Update the conversion rate below. Front-end figures adapt automatically.</p>
          
          <form method="POST">
            <input type="hidden" name="action" value="update_rates">
            <div class="mb-3">
              <label class="form-label small fw-bold">1 USD in Kenyan Shillings (KES)</label>
              <div class="input-group">
                <span class="input-group-text fw-bold">1 USD = KSh</span>
                <input type="number" step="0.01" name="kes_rate" class="form-control form-control-lg fw-bold text-success" value="<?= esc($kesRate) ?>" required>
              </div>
            </div>
            <button type="submit" class="btn btn-gold rounded-pill px-4">Update Live Exchange Rate</button>
          </form>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function resetPackageForm() {
    document.getElementById('pkgModalTitle').textContent = 'Add Safari Package';
    document.getElementById('pkg_id').value = '0';
    document.getElementById('pkg_title').value = '';
    document.getElementById('pkg_code').value = '';
    document.getElementById('pkg_days').value = '3';
    document.getElementById('pkg_nights').value = '2';
    document.getElementById('pkg_price').value = '';
    document.getElementById('pkg_badge').value = '';
    document.getElementById('pkg_image').value = '';
    document.getElementById('pkg_overview').value = '';
  }

  function editPackage(pkg) {
    document.getElementById('pkgModalTitle').textContent = 'Edit Package: ' + pkg.title;
    document.getElementById('pkg_id').value = pkg.id;
    document.getElementById('pkg_title').value = pkg.title;
    document.getElementById('pkg_code').value = pkg.package_code;
    document.getElementById('pkg_category').value = pkg.category_id;
    document.getElementById('pkg_days').value = pkg.days;
    document.getElementById('pkg_nights').value = pkg.nights;
    document.getElementById('pkg_price').value = pkg.base_price_usd;
    document.getElementById('pkg_badge').value = pkg.ribbon_badge || '';
    document.getElementById('pkg_image').value = pkg.featured_image;
    document.getElementById('pkg_overview').value = pkg.overview;
    
    const myModal = new bootstrap.Modal(document.getElementById('packageModal'));
    myModal.show();
  }

  function resetVehicleForm() {
    document.getElementById('vehModalTitle').textContent = 'Add Vehicle';
    document.getElementById('veh_id').value = '0';
    document.getElementById('veh_name').value = '';
    document.getElementById('veh_type').value = '';
    document.getElementById('veh_capacity').value = '6';
    document.getElementById('veh_rate').value = '';
    document.getElementById('veh_image').value = '';
  }

  function editVehicle(veh) {
    document.getElementById('vehModalTitle').textContent = 'Edit Vehicle: ' + veh.name;
    document.getElementById('veh_id').value = veh.id;
    document.getElementById('veh_name').value = veh.name;
    document.getElementById('veh_type').value = veh.type;
    document.getElementById('veh_capacity').value = veh.capacity;
    document.getElementById('veh_rate').value = veh.daily_rate_usd;
    document.getElementById('veh_image').value = veh.image_url;
    
    const modal = new bootstrap.Modal(document.getElementById('vehicleModal'));
    modal.show();
  }

  function resetCategoryForm() {
    document.getElementById('catModalTitle').textContent = 'Add Category';
    document.getElementById('cat_id').value = '0';
    document.getElementById('cat_name').value = '';
    document.getElementById('cat_filter_tag').value = '';
    document.getElementById('cat_sort_order').value = '1';
  }

  function editCategory(c) {
    document.getElementById('catModalTitle').textContent = 'Edit Category: ' + c.name;
    document.getElementById('cat_id').value = c.id;
    document.getElementById('cat_name').value = c.name;
    document.getElementById('cat_filter_tag').value = c.filter_tag;
    document.getElementById('cat_sort_order').value = c.sort_order;
    const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
    modal.show();
  }

  function syncFilterTag(val) {
    const id = document.getElementById('cat_id').value;
    if (id === '0' || id === '') {
      document.getElementById('cat_filter_tag').value = val.toLowerCase().replace(/[^a-z0-9]/g, '');
    }
  }
</script>
</body>
</html>