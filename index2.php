<?php
include 'db/db.php';

// Helper function for safe HTML escaping
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// ---------------------------------------------------------
// 1. DATA QUERIES (MySQLi)
// ---------------------------------------------------------

// Fetch active exchange rates
$ratesQuery = $conn->query("SELECT currency_code, symbol, rate_to_usd, is_base_currency FROM exchange_rates ORDER BY is_base_currency DESC");
$exchangeRates = $ratesQuery->fetch_all(MYSQLI_ASSOC);

$kesRate = 130.0;
foreach ($exchangeRates as $rate) {
    if ($rate['currency_code'] === 'KES') {
        $kesRate = (float)$rate['rate_to_usd'];
        break;
    }
}

// Fetch destinations for Quick Search & Calculator
$destQuery = $conn->query("SELECT id, name, slug, daily_conservation_fee_usd FROM destinations WHERE status = 'active' ORDER BY is_popular DESC, name ASC");
$destinations = $destQuery->fetch_all(MYSQLI_ASSOC);

// Fetch fleet/vehicles
$vehQuery = $conn->query("SELECT id, name, vehicle_code, daily_rate_usd, passenger_capacity, features FROM vehicles WHERE status = 'available'");
$vehicles = $vehQuery->fetch_all(MYSQLI_ASSOC);

// Fetch accommodation tiers
$tierQuery = $conn->query("SELECT tier_key, tier_name, base_rate_usd_per_person FROM accommodation_tiers ORDER BY base_rate_usd_per_person ASC");
$accommodationTiers = $tierQuery->fetch_all(MYSQLI_ASSOC);

// Fetch safari add-ons
$addonQuery = $conn->query("SELECT addon_key, name, price_usd, charge_type FROM safari_addons WHERE status = 'active'");
$safariAddons = $addonQuery->fetch_all(MYSQLI_ASSOC);

// Fetch package categories
$catQuery = $conn->query("SELECT id, name, filter_tag FROM package_categories ORDER BY sort_order ASC");
$categories = $catQuery->fetch_all(MYSQLI_ASSOC);

// Fetch published packages
$pkgQuery = $conn->query("
    SELECT p.*, c.filter_tag 
    FROM packages p
    JOIN package_categories c ON p.category_id = c.id
    WHERE p.status = 'published'
    ORDER BY p.is_featured DESC, p.id ASC
");
$packages = $pkgQuery->fetch_all(MYSQLI_ASSOC);

// Build itinerary and inclusions dataset for modal popups
$itineraryData = [];
if (!empty($packages)) {
    $packageIds = array_column($packages, 'id');
    $idsString = implode(',', array_map('intval', $packageIds));

    // Schedules
    $itinQuery = $conn->query("SELECT package_id, day_label, title, description FROM package_itineraries WHERE package_id IN ($idsString) ORDER BY package_id, day_number ASC");
    $rawItineraries = $itinQuery->fetch_all(MYSQLI_ASSOC);
    $allItineraries = [];
    foreach ($rawItineraries as $row) {
        $allItineraries[$row['package_id']][] = [
            'day'   => $row['day_label'],
            'title' => $row['title'],
            'desc'  => $row['description']
        ];
    }

    // Inclusions
    $incQuery = $conn->query("SELECT package_id, item_text FROM package_inclusions WHERE package_id IN ($idsString) AND inclusion_type = 'inclusion'");
    $rawInclusions = $incQuery->fetch_all(MYSQLI_ASSOC);
    $allInclusions = [];
    foreach ($rawInclusions as $row) {
        $allInclusions[$row['package_id']][] = $row['item_text'];
    }

    foreach ($packages as $pkg) {
        $pId = $pkg['id'];
        $code = $pkg['package_code'];

        $itineraryData[$code] = [
            'title'    => $pkg['title'],
            'duration' => "{$pkg['days']} Days / {$pkg['nights']} Nights",
            'usdPrice' => (float)$pkg['base_price_usd'],
            'overview' => $pkg['overview'],
            'schedule' => $allItineraries[$pId] ?? [],
            'includes' => $allInclusions[$pId] ?? []
        ];
    }
}

// Fetch testimonials
$testQuery = $conn->query("SELECT guest_name, guest_origin, avatar_url, rating, review_text, package_tag FROM testimonials WHERE is_featured = 1 ORDER BY id DESC LIMIT 3");
$testimonials = $testQuery->fetch_all(MYSQLI_ASSOC);

// Fetch FAQs
$faqQuery = $conn->query("SELECT id, question, answer FROM faqs WHERE status = 'active' ORDER BY sort_order ASC");
$faqs = $faqQuery->fetch_all(MYSQLI_ASSOC);

// ---------------------------------------------------------
// 2. CONTACT / INQUIRY FORM AJAX HANDLER (MySQLi Prepared)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_inquiry') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $dest     = trim($_POST['destination'] ?? '');
    $message  = trim($_POST['message'] ?? '');

    if ($fullName !== '' && $email !== '' && $phone !== '') {
        $conn->begin_transaction();
        try {
            // Check if traveler already exists
            $stmtTraveler = $conn->prepare("SELECT id FROM travelers WHERE email = ? LIMIT 1");
            $stmtTraveler->bind_param("s", $email);
            $stmtTraveler->execute();
            $resultTraveler = $stmtTraveler->get_result();
            $travelerId = null;

            if ($row = $resultTraveler->fetch_assoc()) {
                $travelerId = $row['id'];
            } else {
                $stmtInsertTraveler = $conn->prepare("INSERT INTO travelers (full_name, email, phone_whatsapp) VALUES (?, ?, ?)");
                $stmtInsertTraveler->bind_param("sss", $fullName, $email, $phone);
                $stmtInsertTraveler->execute();
                $travelerId = $conn->insert_id;
            }

            // Create reference
            $reference = 'PW-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));

            // Save inquiry
            $source = 'contact_form';
            $status = 'new';
            $stmtInq = $conn->prepare("INSERT INTO inquiries (inquiry_reference, traveler_id, source, destination_name, special_requests, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtInq->bind_param("sissss", $reference, $travelerId, $source, $dest, $message, $status);
            $stmtInq->execute();

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'reference' => $reference]);
            exit;
        } catch (Exception $ex) {
            $conn->rollback();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $ex->getMessage()]);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Planet Wanders Tours & Safaris | Gateway to Authentic Masai Mara & East Africa</title>
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts: Outfit & Playfair Display -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

  <style>
    :root {
      --pw-gold: #d99a26;
      --pw-gold-hover: #b87e1a;
      --pw-olive: #2e4330;
      --pw-olive-dark: #1e2c20;
      --pw-savannah: #111712;
      --pw-cream: #faf7f2;
      --pw-sand: #f0eae1;
      --pw-accent-red: #c0392b;
      --pw-text-muted: #6b7280;
    }

    body {
      font-family: 'Outfit', sans-serif;
      color: #333333;
      background-color: #ffffff;
      overflow-x: hidden;
    }

    h1, h2, h3, h4, .font-serif {
      font-family: 'Playfair Display', serif;
    }

    .text-gold { color: var(--pw-gold) !important; }
    .bg-gold { background-color: var(--pw-gold) !important; }
    .bg-olive { background-color: var(--pw-olive) !important; }
    .text-olive { color: var(--pw-olive) !important; }
    .bg-savannah { background-color: var(--pw-savannah) !important; }
    .bg-cream { background-color: var(--pw-cream) !important; }
    .bg-sand { background-color: var(--pw-sand) !important; }

    /* Currency Switcher Pill */
    .currency-switch-group {
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(217, 154, 38, 0.5);
      border-radius: 30px;
      padding: 3px;
      display: inline-flex;
      align-items: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    }
    .currency-switch-btn {
      border: none;
      background: transparent;
      color: #eaeaea;
      padding: 4px 12px;
      font-size: 0.8rem;
      font-weight: 600;
      border-radius: 20px;
      cursor: pointer;
      transition: all 0.25s ease;
      letter-spacing: 0.5px;
    }
    .currency-switch-btn:hover {
      color: #ffffff;
    }
    .currency-switch-btn.active {
      background-color: var(--pw-gold);
      color: #111712;
      box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    /* Buttons */
    .btn-gold {
      background-color: var(--pw-gold);
      color: #ffffff;
      font-weight: 600;
      border: none;
      padding: 0.65rem 1.6rem;
      border-radius: 50px;
      transition: all 0.3s ease;
    }
    .btn-gold:hover {
      background-color: var(--pw-gold-hover);
      color: #ffffff;
      transform: translateY(-2px);
      box-shadow: 0 8px 18px rgba(217, 154, 38, 0.35);
    }

    .btn-outline-gold {
      border: 2px solid var(--pw-gold);
      color: var(--pw-gold);
      font-weight: 600;
      border-radius: 50px;
      padding: 0.6rem 1.5rem;
      transition: all 0.3s ease;
    }
    .btn-outline-gold:hover {
      background-color: var(--pw-gold);
      color: #ffffff;
    }

    /* Navbar */
    .navbar {
      transition: all 0.3s ease;
      padding: 1rem 0;
    }
    .navbar.scrolled {
      background-color: rgba(17, 23, 18, 0.96) !important;
      box-shadow: 0 4px 20px rgba(0,0,0,0.25);
      padding: 0.6rem 0;
      backdrop-filter: blur(10px);
    }
    .nav-link {
      font-weight: 500;
      color: #eaeaea !important;
      margin: 0 0.5rem;
      position: relative;
    }
    .nav-link:hover, .nav-link.active {
      color: var(--pw-gold) !important;
    }
    .nav-link::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: 2px;
      left: 50%;
      background-color: var(--pw-gold);
      transition: all 0.3s ease;
      transform: translateX(-50%);
    }
    .nav-link:hover::after {
      width: 60%;
    }

    /* Hero section */
    .hero-section {
      position: relative;
      min-height: 94vh;
      background: linear-gradient(180deg, rgba(17, 23, 18, 0.55) 0%, rgba(17, 23, 18, 0.8) 100%),
                  url('https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
      display: flex;
      align-items: center;
      color: #fff;
    }

    .hero-badge {
      background: rgba(217, 154, 38, 0.2);
      border: 1px solid var(--pw-gold);
      color: #ffde94;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 18px;
      border-radius: 50px;
      font-size: 0.88rem;
      letter-spacing: 1px;
      text-transform: uppercase;
      font-weight: 600;
    }

    /* Booking inquiry bar */
    .inquiry-card {
      background: rgba(255, 255, 255, 0.96);
      border-radius: 18px;
      box-shadow: 0 20px 45px rgba(0,0,0,0.25);
      border: 1px solid rgba(217, 154, 38, 0.25);
      backdrop-filter: blur(10px);
      margin-top: -65px;
      position: relative;
      z-index: 10;
    }

    /* Feature Cards */
    .feature-box {
      border: 1px solid #ede8df;
      background: #ffffff;
      border-radius: 16px;
      padding: 2rem 1.5rem;
      transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
      height: 100%;
    }
    .feature-box:hover {
      transform: translateY(-8px);
      border-color: var(--pw-gold);
      box-shadow: 0 15px 30px rgba(46, 67, 48, 0.1);
    }
    .icon-wrapper {
      width: 65px;
      height: 65px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.6rem;
      margin-bottom: 1.2rem;
    }

    /* Package Cards */
    .package-card {
      border-radius: 18px;
      overflow: hidden;
      border: 1px solid #eae3d5;
      background: #fff;
      transition: all 0.35s ease;
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    .package-card:hover {
      transform: translateY(-7px);
      box-shadow: 0 18px 36px rgba(0,0,0,0.12);
      border-color: var(--pw-gold);
    }
    .package-img-holder {
      position: relative;
      height: 230px;
      overflow: hidden;
    }
    .package-img-holder img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.6s ease;
    }
    .package-card:hover .package-img-holder img {
      transform: scale(1.08);
    }
    .badge-ribbon {
      position: absolute;
      top: 15px;
      left: 15px;
      background: var(--pw-olive);
      color: #fff;
      padding: 5px 14px;
      border-radius: 30px;
      font-size: 0.78rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.25);
    }
    .badge-price {
      position: absolute;
      bottom: 15px;
      right: 15px;
      background: rgba(17, 23, 18, 0.92);
      color: var(--pw-gold);
      font-weight: 700;
      padding: 6px 14px;
      border-radius: 10px;
      backdrop-filter: blur(5px);
      border: 1px solid rgba(217, 154, 38, 0.4);
    }

    /* Filter pills */
    .filter-btn {
      border-radius: 30px;
      padding: 0.5rem 1.4rem;
      font-size: 0.9rem;
      font-weight: 500;
      border: 1px solid #d5cebf;
      background: #fff;
      color: #4b5563;
      margin: 0.25rem;
      transition: all 0.2s ease;
    }
    .filter-btn.active, .filter-btn:hover {
      background: var(--pw-olive);
      color: #ffffff;
      border-color: var(--pw-olive);
    }

    /* Service card */
    .service-card {
      border-radius: 18px;
      background: #fff;
      border: 1px solid #ede8de;
      padding: 2.2rem;
      transition: all 0.3s;
      height: 100%;
      position: relative;
      overflow: hidden;
    }
    .service-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 5px;
      height: 0%;
      background-color: var(--pw-gold);
      transition: height 0.35s ease;
    }
    .service-card:hover::before {
      height: 100%;
    }
    .service-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 16px 32px rgba(46,67,48,0.08);
    }

    /* Calculator Box */
    .calc-card {
      background: linear-gradient(145deg, #243525, #172319);
      color: #ffffff;
      border-radius: 24px;
      padding: 2.8rem;
      box-shadow: 0 25px 50px rgba(0,0,0,0.3);
      border: 1px solid rgba(217, 154, 38, 0.3);
    }

    .form-control, .form-select {
      border-radius: 12px;
      padding: 0.7rem 1rem;
      border: 1px solid #ced4da;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--pw-gold);
      box-shadow: 0 0 0 0.25rem rgba(217, 154, 38, 0.25);
    }

    /* WhatsApp floating button */
    .floating-whatsapp {
      position: fixed;
      bottom: 28px;
      right: 28px;
      background-color: #25d366;
      color: white;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 32px;
      box-shadow: 0 8px 24px rgba(37, 211, 102, 0.45);
      z-index: 1050;
      transition: all 0.3s ease;
      text-decoration: none;
    }
    .floating-whatsapp:hover {
      transform: scale(1.12) rotate(8deg);
      color: white;
      box-shadow: 0 12px 28px rgba(37, 211, 102, 0.6);
    }

    /* Testimonial avatar */
    .guest-avatar {
      width: 65px;
      height: 65px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid var(--pw-gold);
    }

    /* Section Headers */
    .section-subtitle {
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--pw-gold);
      font-weight: 700;
      margin-bottom: 0.5rem;
      display: block;
    }
  </style>
</head>
<body>

  <!-- Top Bar -->
  <div class="bg-savannah text-light py-2 border-bottom border-dark d-none d-md-block">
    <div class="container d-flex justify-content-between align-items-center small">
      <div>
        <span class="me-3"><i class="fa-solid fa-location-dot text-gold me-1"></i> Narok Town & Nairobi, Kenya</span>
        <span class="me-3"><i class="fa-solid fa-clock text-gold me-1"></i> Mon - Sun: 7:00 AM – 9:00 PM EAT</span>
        <span><i class="fa-solid fa-shield-heart text-gold me-1"></i> Licensed KATO Safaris Partner</span>
      </div>
      <div class="d-flex align-items-center gap-3">
        <a href="tel:+254712345678" class="text-white text-decoration-none"><i class="fa-solid fa-phone text-gold me-1"></i> +254 712 345 678</a>
        <a href="mailto:info@planetwanderstours.com" class="text-white text-decoration-none"><i class="fa-solid fa-envelope text-gold me-1"></i> info@planetwanderstours.com</a>
      </div>
    </div>
  </div>

  <!-- Main Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-savannah sticky-top" id="mainNavbar">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <div class="bg-gold text-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
          <i class="fa-solid fa-compass fa-lg"></i>
        </div>
        <div>
          <span class="fw-bold tracking-wide text-white d-block lh-1" style="font-size: 1.35rem; font-family: 'Playfair Display', serif;">PLANET WANDERS</span>
          <span class="text-gold text-uppercase small" style="font-size: 0.72rem; letter-spacing: 1.5px;">Tours & Safaris • Kenya</span>
        </div>
      </a>
      
      <!-- Currency switcher for Mobile Header -->
      <div class="d-lg-none ms-auto me-2">
        <div class="currency-switch-group">
          <?php foreach ($exchangeRates as $rate): ?>
            <button class="currency-switch-btn <?= $rate['currency_code'] === 'KES' ? 'active' : '' ?>" 
                    data-currency="<?= e($rate['currency_code']) ?>" 
                    onclick="setCurrency('<?= e($rate['currency_code']) ?>')">
              <?= e($rate['symbol']) ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>

      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navContent" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navContent">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#packages">Safari Packages</a></li>
          <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
          <li class="nav-item"><a class="nav-link" href="#calculator">Quote Calculator</a></li>
          <li class="nav-item"><a class="nav-link" href="#fleet">Our Fleet</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
        </ul>
        <div class="d-flex align-items-center gap-3">
          <!-- Desktop Currency Switcher -->
          <div class="currency-switch-group d-none d-lg-inline-flex" title="Toggle Currency (Default: KSh)" style="font-size: 0.75rem;">
            <?php foreach ($exchangeRates as $rate): ?>
              <button class="currency-switch-btn <?= $rate['currency_code'] === 'KES' ? 'active' : '' ?> px-2 py-1" 
                      data-currency="<?= e($rate['currency_code']) ?>" 
                      onclick="setCurrency('<?= e($rate['currency_code']) ?>')">
                <?= e($rate['currency_code']) ?>
              </button>
            <?php endforeach; ?>
          </div>

          <a href="#calculator" class="btn btn-gold text-white btn-sm px-4">
            <i class="fa-solid fa-calculator me-1"></i> Get Quick Quote
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section" id="home">
    <div class="container py-5 my-md-4">
      <div class="row align-items-center">
        <div class="col-lg-8 col-xl-7">
          <span class="hero-badge mb-3">
            <i class="fa-solid fa-paw text-gold"></i> Authentic East African Bush Safaris
          </span>
          <h1 class="display-3 fw-bold mb-3 text-white">
            Discover the <span class="text-gold fst-italic">Magic of Wildlife</span>
          </h1>
          <p class="lead mb-4 text-light opacity-90" style="font-size: 1.25rem;">
            Based in <strong>Narok Town & Nairobi</strong>, we are your authentic local gateway to Masai Mara, Serengeti, Amboseli, and idyllic Kenyan coastal retreats. Custom 4×4 Land Cruisers, expert local guides, and best-price guarantees!
          </p>
          <div class="d-flex flex-wrap gap-3">
            <a href="#packages" class="btn btn-gold btn-lg px-4 py-3">
              <i class="fa-solid fa-binoculars me-2"></i> Explore Safari Packages
            </a>
            <a href="#calculator" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill">
              <i class="fa-solid fa-sliders me-2"></i> Custom Trip Planner
            </a>
          </div>

          <div class="row mt-5 pt-3 border-top border-secondary border-opacity-50 g-3">
            <div class="col-4">
              <h3 class="fw-bold text-gold mb-0">12+</h3>
              <small class="text-light opacity-75">Years Experience</small>
            </div>
            <div class="col-4">
              <h3 class="fw-bold text-gold mb-0">99.8%</h3>
              <small class="text-light opacity-75">Big 5 Sightings</small>
            </div>
            <div class="col-4">
              <h3 class="fw-bold text-gold mb-0">5/5 ★</h3>
              <small class="text-light opacity-75">Guest Satisfaction</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Quick Inquiry Search Bar -->
  <div class="container position-relative">
    <div class="inquiry-card p-4 p-md-5">
      <form id="quickSearchForm" onsubmit="handleQuickSearch(event)">
        <div class="row g-3 align-items-end">
          <div class="col-lg-3 col-md-6">
            <label class="form-label small fw-bold text-uppercase text-muted"><i class="fa-solid fa-map-pin text-gold me-1"></i> Destination</label>
            <select class="form-select" id="quickDest" required>
              <?php foreach ($destinations as $dest): ?>
                <option value="<?= e($dest['name']) ?>"><?= e($dest['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-lg-3 col-md-6">
            <label class="form-label small fw-bold text-uppercase text-muted"><i class="fa-solid fa-calendar-days text-gold me-1"></i> Target Month / Date</label>
            <input type="date" class="form-control" id="quickDate" required>
          </div>
          <div class="col-lg-2 col-md-6">
            <label class="form-label small fw-bold text-uppercase text-muted"><i class="fa-solid fa-users text-gold me-1"></i> Guests</label>
            <select class="form-select" id="quickGuests">
              <option value="2 Adults (Couple / Honeymoon)">2 Guests</option>
              <option value="Family (3-5 Guests)">3 - 5 Guests</option>
              <option value="Private Group (6-8 Guests)">6 - 8 Guests</option>
              <option value="Solo Traveler">Solo Explorer</option>
              <option value="Large Group (8+ Guests)">8+ Large Group</option>
            </select>
          </div>
          <div class="col-lg-2 col-md-6">
            <label class="form-label small fw-bold text-uppercase text-muted"><i class="fa-solid fa-truck-monster text-gold me-1"></i> Vehicle Style</label>
            <select class="form-select" id="quickVehicle">
              <?php foreach ($vehicles as $veh): ?>
                <option value="<?= e($veh['name']) ?>"><?= e($veh['name']) ?></option>
              <?php endforeach; ?>
              <option value="Fly-in Safari Option">Fly-In Safari Option</option>
            </select>
          </div>
          <div class="col-lg-2 col-md-12">
            <button type="submit" class="btn btn-gold w-100 py-3">
              <i class="fa-solid fa-magnifying-glass me-1"></i> Check Now
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- About Section -->
  <section class="py-5 mt-4" id="about">
    <div class="container py-4">
      <div class="text-center max-w-700 mx-auto mb-5">
        <span class="section-subtitle">Why Travel With Us</span>
        <h2 class="display-6 fw-bold text-olive">Rooted in Narok & Nairobi. Passionate About Africa.</h2>
        <p class="text-muted">Narok is the home county of the legendary Maasai Mara. Being based right here in Narok Town and Nairobi means our teams know every valley, river crossing, predator territory, and indigenous cultural custom.</p>
      </div>

      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="feature-box text-center">
            <div class="icon-wrapper bg-sand text-gold mx-auto">
              <i class="fa-solid fa-tags"></i>
            </div>
            <h5 class="fw-bold text-olive mb-2">Best Prices Guaranteed</h5>
            <p class="text-muted small mb-0">Direct local operator rates without overseas middleman markups. Honest, transparent, premium safari value.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="feature-box text-center">
            <div class="icon-wrapper bg-sand text-gold mx-auto">
              <i class="fa-solid fa-truck-pickup"></i>
            </div>
            <h5 class="fw-bold text-olive mb-2">Custom 4×4 Land Cruisers</h5>
            <p class="text-muted small mb-0">Specially customized 4WD safari jeeps with pop-up photography roofs, high-frequency radios & cold cooler boxes.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="feature-box text-center">
            <div class="icon-wrapper bg-sand text-gold mx-auto">
              <i class="fa-solid fa-user-shield"></i>
            </div>
            <h5 class="fw-bold text-olive mb-2">Certified Local Guides</h5>
            <p class="text-muted small mb-0">Silver & Bronze level KPSGA certified naturalist guides with intimate tracking knowledge of the Big Five.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="feature-box text-center">
            <div class="icon-wrapper bg-sand text-gold mx-auto">
              <i class="fa-solid fa-route"></i>
            </div>
            <h5 class="fw-bold text-olive mb-2">Tailor-Made Itineraries</h5>
            <p class="text-muted small mb-0">Flexible dates, customizable budgets, honeymoon setups, photography drives, and family-friendly adventures.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Packages Section -->
  <section class="py-5 bg-sand" id="packages">
    <div class="container py-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
        <div>
          <span class="section-subtitle">Curated Expeditions</span>
          <h2 class="display-6 fw-bold text-olive mb-2">Popular Safari Packages & Tours</h2>
          <p class="text-muted mb-0">Choose from classic wilderness safaris, tropical Indian Ocean beaches, and multi-country odysseys.</p>
        </div>
        
        <!-- Filter Buttons -->
        <div class="mt-3 mt-md-0 d-flex flex-wrap" id="packageFilterButtons">
          <button class="filter-btn active" data-filter="all">All Safaris</button>
          <?php foreach ($categories as $cat): ?>
            <?php if ($cat['filter_tag'] !== 'all'): ?>
              <button class="filter-btn" data-filter="<?= e($cat['filter_tag']) ?>"><?= e($cat['name']) ?></button>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Packages Grid Loop -->
      <div class="row g-4" id="packagesContainer">
        <?php foreach ($packages as $pkg): 
            $kesPrice = round($pkg['base_price_usd'] * $kesRate);
        ?>
          <div class="col-lg-4 col-md-6 package-item" data-category="<?= e($pkg['filter_tag']) ?>">
            <div class="package-card">
              <div class="package-img-holder">
                <img src="<?= e($pkg['featured_image']) ?>" alt="<?= e($pkg['title']) ?>">
                <?php if (!empty($pkg['ribbon_badge'])): ?>
                  <span class="badge-ribbon"><i class="fa-solid fa-star text-gold me-1"></i> <?= e($pkg['ribbon_badge']) ?></span>
                <?php endif; ?>
                <span class="badge-price">
                  <span class="price-val" data-usd="<?= e($pkg['base_price_usd']) ?>">KSh <?= number_format($kesPrice) ?></span> 
                  <small class="text-white fw-normal">/ <?= e($pkg['price_unit']) ?></small>
                </span>
              </div>
              <div class="p-4 d-flex flex-column flex-grow-1">
                <div class="d-flex justify-content-between text-muted small mb-2">
                  <span><i class="fa-regular fa-clock text-gold me-1"></i> <?= e($pkg['days']) ?> Days / <?= e($pkg['nights']) ?> Nights</span>
                  <span><i class="fa-solid fa-location-dot text-gold me-1"></i> Kenya & E. Africa</span>
                </div>
                <h4 class="fw-bold text-olive mb-2"><?= e($pkg['title']) ?></h4>
                <p class="text-muted small flex-grow-1"><?= e($pkg['overview']) ?></p>
                <div class="border-top pt-3 mt-2 d-flex gap-2">
                  <button class="btn btn-outline-gold btn-sm flex-grow-1" onclick="openItineraryModal('<?= e($pkg['package_code']) ?>')">
                    <i class="fa-solid fa-list-check me-1"></i> Itinerary
                  </button>
                  <button class="btn btn-gold btn-sm flex-grow-1" onclick="prefillBooking('<?= addslashes($pkg['title']) ?>', <?= (float)$pkg['base_price_usd'] ?>)">
                    Book Now
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section class="py-5" id="services">
    <div class="container py-4">
      <div class="text-center max-w-700 mx-auto mb-5">
        <span class="section-subtitle">What We Offer</span>
        <h2 class="display-6 fw-bold text-olive">Comprehensive Safari & Travel Logistics</h2>
        <p class="text-muted">From the moment you touch down at Jomo Kenyatta International Airport (JKIA) or drive up from Narok Town, Planet Wanders handles every touchpoint of your African journey.</p>
      </div>

      <div class="row g-4">
        <div class="col-lg-4 col-md-6">
          <div class="service-card">
            <div class="icon-wrapper bg-sand text-gold">
              <i class="fa-solid fa-paw"></i>
            </div>
            <h4 class="fw-bold text-olive mb-3">1. Safaris & Tours</h4>
            <p class="text-muted mb-3">Tailor-made and scheduled group expeditions traversing the premier game parks of Kenya and Tanzania.</p>
            <ul class="list-unstyled text-muted small mb-4">
              <li class="mb-2"><i class="fa-solid fa-check text-gold me-2"></i><strong>Maasai Mara National Reserve:</strong> World Great Migration & big cats</li>
              <li class="mb-2"><i class="fa-solid fa-check text-gold me-2"></i><strong>Amboseli & Tsavo:</strong> Elephant herds under Mt. Kilimanjaro & red dust lions</li>
              <li class="mb-2"><i class="fa-solid fa-check text-gold me-2"></i><strong>Serengeti & Ngorongoro:</strong> Northern circuit Tanzania wonders</li>
              <li class="mb-2"><i class="fa-solid fa-check text-gold me-2"></i><strong>Lake Nakuru & Naivasha:</strong> Rhino sanctuary, flamingos & boat safaris</li>
            </ul>
            <a href="#packages" class="text-gold fw-bold text-decoration-none small">Explore Safari Routes <i class="fa-solid fa-arrow-right ms-1"></i></a>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="service-card">
            <div class="icon-wrapper bg-sand text-gold">
              <i class="fa-solid fa-heart"></i>
            </div>
            <h4 class="fw-bold text-olive mb-3">2. Specialized Trips</h4>
            <p class="text-muted mb-3">Unique memories crafted for romantic journeys, corporate team retreats, and academic explorations.</p>
            <ul class="list-unstyled text-muted small mb-4">
              <li class="mb-2"><i class="fa-solid fa-check text-gold me-2"></i><strong>Honeymoon Getaways:</strong> Secluded bush camps, private dining, sundowners</li>
              <li class="mb-2"><i class="fa-solid fa-check text-gold me-2"></i><strong>Coastal Beach Holidays:</strong> White sands of Diani, Watamu, Lamu & Malindi</li>
              <li class="mb-2"><i class="fa-solid fa-check text-gold me-2"></i><strong>Educational Study Tours:</strong> Wildlife conservation & community projects</li>
              <li class="mb-2"><i class="fa-solid fa-check text-gold me-2"></i><strong>Wildlife Photography Safaris:</strong> Custom beanbag mounts & dawn trackers</li>
            </ul>
            <a href="#calculator" class="text-gold fw-bold text-decoration-none small">Customize Specialized Trip <i class="fa-solid fa-arrow-right ms-1"></i></a>
          </div>
        </div>

        <div class="col-lg-4 col-md-12">
          <div class="service-card">
            <div class="icon-wrapper bg-sand text-gold">
              <i class="fa-solid fa-plane-departure"></i>
            </div>
            <h4 class="fw-bold text-olive mb-3">3. Travel Logistics</h4>
            <p class="text-muted mb-3">Seamless coordination so you never have to stress about bush transit, flight delays, or lodging passes.</p>
            <ul class="list-unstyled text-muted small mb-4">
              <li class="mb-2"><i class="fa-solid fa-check text-gold me-2"></i><strong>Flight Reservations:</strong> Bush hopper flights (Wilson Airport to Mara airstrips)</li>
              <li class="mb-2"><i class="fa-solid fa-check text-gold me-2"></i><strong>Airport Transfers:</strong> VIP meet & greet at JKIA, Wilson & Narok pickups</li>
              <li class="mb-2"><i class="fa-solid fa-check text-gold me-2"></i><strong>Hotel & Camp Bookings:</strong> Direct negotiated rates with leading safari lodges</li>
              <li class="mb-2"><i class="fa-solid fa-check text-gold me-2"></i><strong>Travel Consultancy:</strong> Visa, park entry permits, yellow fever & season advice</li>
            </ul>
            <a href="#contact" class="text-gold fw-bold text-decoration-none small">Request Logistics Support <i class="fa-solid fa-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Interactive Safari Calculator -->
  <section class="py-5 bg-olive" id="calculator">
    <div class="container py-4">
      <div class="row align-items-center g-5">
        <div class="col-lg-5 text-white">
          <span class="section-subtitle">Real-Time Estimator</span>
          <h2 class="display-6 fw-bold mb-3 text-white">Safari Cost Calculator & Custom Quote</h2>
          <p class="text-light opacity-90 mb-4">
            Plan your dream itinerary right now. Choose your preferred parks, comfort level, and customized 4x4 Land Cruiser options to get an instant cost projection and send it directly to our reservation agents in Narok & Nairobi.
          </p>
          <div class="d-flex align-items-start gap-3 mb-3">
            <i class="fa-solid fa-circle-check text-gold fs-5 mt-1"></i>
            <div>
              <h6 class="fw-bold mb-1">Zero Hidden Charges</h6>
              <p class="small text-light opacity-75 mb-0">Park fees, English-speaking driver guide, and game drives included.</p>
            </div>
          </div>
          <div class="d-flex align-items-start gap-3 mb-3">
            <i class="fa-solid fa-circle-check text-gold fs-5 mt-1"></i>
            <div>
              <h6 class="fw-bold mb-1">Direct WhatsApp Confirmation</h6>
              <p class="small text-light opacity-75 mb-0">Instant communication with our Narok town base operations.</p>
            </div>
          </div>
          <div class="d-flex align-items-start gap-3">
            <i class="fa-solid fa-circle-check text-gold fs-5 mt-1"></i>
            <div>
              <h6 class="fw-bold mb-1">Flexible Payment Plans</h6>
              <p class="small text-light opacity-75 mb-0">Lock your dates with a modest deposit; pay remaining on arrival in Kenya via M-Pesa or Card.</p>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="calc-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h4 class="fw-bold text-gold mb-0"><i class="fa-solid fa-sliders me-2"></i> Build Your Custom Quote</h4>
              <span class="badge bg-savannah border border-warning text-gold px-3 py-2 rounded-pill small" id="calcCurrencyBadge">Currency: KSh</span>
            </div>
            
            <div class="row g-3">
              <!-- Destination -->
              <div class="col-md-6">
                <label class="form-label small fw-bold text-light">Destination / Circuit</label>
                <select class="form-select bg-dark text-white border-secondary" id="calcDestination" onchange="calculateSafariCost()">
                  <?php foreach ($destinations as $dest): ?>
                    <option value="<?= e($dest['slug']) ?>" data-daily-usd="<?= e($dest['daily_conservation_fee_usd']) ?>">
                      <?= e($dest['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Vehicle -->
              <div class="col-md-6">
                <label class="form-label small fw-bold text-light">Vehicle Style</label>
                <select class="form-select bg-dark text-white border-secondary" id="calcVehicle" onchange="calculateSafariCost()">
                  <?php foreach ($vehicles as $veh): ?>
                    <option value="<?= e($veh['vehicle_code']) ?>" data-perday-usd="<?= e($veh['daily_rate_usd']) ?>">
                      <?= e($veh['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Days -->
              <div class="col-md-4">
                <label class="form-label small fw-bold text-light">Number of Days</label>
                <select class="form-select bg-dark text-white border-secondary" id="calcDays" onchange="calculateSafariCost()">
                  <option value="3">3 Days (Quick Mara Getaway)</option>
                  <option value="4" selected>4 Days (Recommended)</option>
                  <option value="5">5 Days (In-Depth Safari)</option>
                  <option value="6">6 Days (Great Circuit)</option>
                  <option value="7">7 Days (Bush + Beach)</option>
                  <option value="10">10 Days (Grand East Africa)</option>
                </select>
              </div>

              <!-- Accommodation Tier -->
              <div class="col-md-4">
                <label class="form-label small fw-bold text-light">Accommodation Style</label>
                <select class="form-select bg-dark text-white border-secondary" id="calcTier" onchange="calculateSafariCost()">
                  <?php foreach ($accommodationTiers as $tier): ?>
                    <option value="<?= e($tier['tier_key']) ?>" data-room-usd="<?= e($tier['base_rate_usd_per_person']) ?>" <?= $tier['tier_key'] === 'midrange' ? 'selected' : '' ?>>
                      <?= e($tier['tier_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Number of Adults -->
              <div class="col-md-4">
                <label class="form-label small fw-bold text-light">Number of Adults</label>
                <select class="form-select bg-dark text-white border-secondary" id="calcAdults" onchange="calculateSafariCost()">
                  <option value="1">1 Solo Traveler</option>
                  <option value="2" selected>2 Adults (Couple)</option>
                  <option value="3">3 Adults</option>
                  <option value="4">4 Adults</option>
                  <option value="5">5 Adults</option>
                  <option value="6">6 Adults (Full Cruiser)</option>
                  <option value="7">7 Adults</option>
                </select>
              </div>

              <!-- Add-ons -->
              <div class="col-12 mt-2">
                <label class="form-label small fw-bold text-light d-block mb-1">Optional Safari Add-Ons:</label>
                <div class="d-flex flex-wrap gap-3">
                  <?php foreach ($safariAddons as $addon): ?>
                    <div class="form-check">
                      <input class="form-check-input addon-checkbox" 
                             type="checkbox" 
                             id="addon_<?= e($addon['addon_key']) ?>" 
                             data-usd="<?= e($addon['price_usd']) ?>"
                             data-name="<?= e($addon['name']) ?>"
                             onchange="calculateSafariCost()">
                      <label class="form-check-label small text-light" for="addon_<?= e($addon['addon_key']) ?>" id="label_<?= e($addon['addon_key']) ?>">
                        <?= e($addon['name']) ?> ($<?= number_format($addon['price_usd']) ?>/p)
                      </label>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <!-- Price Output Box -->
            <div class="bg-black bg-opacity-40 p-3 rounded-4 mt-4 border border-secondary border-opacity-50">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                  <span class="text-light small text-uppercase">Estimated Total (All Travelers):</span>
                  <div class="display-6 fw-bold text-gold" id="totalPriceDisplay">KSh 0</div>
                  <small class="text-white-50" id="pricePerPersonDisplay">Calculating...</small>
                </div>
                <div>
                  <button class="btn btn-gold px-4 py-2" onclick="sendWhatsAppQuote()">
                    <i class="fa-brands fa-whatsapp me-2 fs-5"></i> Book via WhatsApp
                  </button>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Fleet Section -->
  <section class="py-5" id="fleet">
    <div class="container py-4">
      <div class="row align-items-center g-5">
        <div class="col-lg-6 order-2 order-lg-1">
          <span class="section-subtitle">Comfort In The Bush</span>
          <h2 class="display-6 fw-bold text-olive mb-3">Our 4×4 Safari Land Cruisers</h2>
          <p class="text-muted">
            The African wilderness demands robust, comfortable, and specialized vehicles. Planet Wanders Tours maintains a fleet of purpose-built custom Toyota Land Cruisers designed specifically for Masai Mara terrain and wildlife photography.
          </p>

          <div class="row g-3 mt-2">
            <div class="col-sm-6">
              <div class="d-flex gap-3 align-items-center p-3 bg-sand rounded-3">
                <i class="fa-solid fa-camera text-gold fs-3"></i>
                <div>
                  <h6 class="fw-bold text-olive mb-0">Pop-up Roof Hatch</h6>
                  <small class="text-muted">360° unobstructed photography</small>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="d-flex gap-3 align-items-center p-3 bg-sand rounded-3">
                <i class="fa-solid fa-tower-broadcast text-gold fs-3"></i>
                <div>
                  <h6 class="fw-bold text-olive mb-0">Long-Range Radios</h6>
                  <small class="text-muted">Live sightings tracking with guides</small>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="d-flex gap-3 align-items-center p-3 bg-sand rounded-3">
                <i class="fa-solid fa-plug text-gold fs-3"></i>
                <div>
                  <h6 class="fw-bold text-olive mb-0">In-Car USB Charging</h6>
                  <small class="text-muted">Keep cameras & phones charged</small>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="d-flex gap-3 align-items-center p-3 bg-sand rounded-3">
                <i class="fa-solid fa-snowflake text-gold fs-3"></i>
                <div>
                  <h6 class="fw-bold text-olive mb-0">Onboard Cooler Box</h6>
                  <small class="text-muted">Complimentary chilled mineral water</small>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-4">
            <a href="#contact" class="btn btn-outline-gold me-2">Reserve Private Cruiser</a>
            <span class="text-muted small"><i class="fa-solid fa-shield text-success me-1"></i> Full Comprehensive Safari Insurance</span>
          </div>
        </div>

        <div class="col-lg-6 order-1 order-lg-2">
          <div class="position-relative">
            <img src="https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?auto=format&fit=crop&w=900&q=80" alt="Safari Land Cruiser in Masai Mara" class="img-fluid rounded-4 shadow-lg w-100" style="min-height: 380px; object-fit: cover;">
            <div class="position-absolute bottom-0 start-0 m-4 p-3 bg-savannah text-white rounded-3 shadow-sm d-flex align-items-center gap-3">
              <div class="bg-gold p-2 rounded-circle text-dark"><i class="fa-solid fa-certificate"></i></div>
              <div>
                <small class="text-gold text-uppercase fw-bold">Narok Local Advantage</small>
                <div class="fw-bold">Experienced Bush Drivers</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="py-5 bg-sand">
    <div class="container py-4">
      <div class="text-center max-w-700 mx-auto mb-5">
        <span class="section-subtitle">Real Traveler Stories</span>
        <h2 class="display-6 fw-bold text-olive">Unforgettable Memories From Our Guests</h2>
        <p class="text-muted">Hear from solo explorers, couples on honeymoon, and families who journeyed into the wild with Planet Wanders.</p>
      </div>

      <div class="row g-4">
        <?php foreach ($testimonials as $t): ?>
          <div class="col-md-4">
            <div class="bg-white p-4 rounded-4 shadow-sm h-100 border border-light d-flex flex-column">
              <div class="d-flex text-warning mb-3">
                <?php for ($i = 0; $i < (int)$t['rating']; $i++): ?>
                  <i class="fa-solid fa-star"></i>
                <?php endfor; ?>
              </div>
              <p class="text-muted small flex-grow-1">
                "<?= e($t['review_text']) ?>"
              </p>
              <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top">
                <img src="<?= e($t['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80') ?>" class="guest-avatar" alt="<?= e($t['guest_name']) ?>">
                <div>
                  <h6 class="fw-bold text-olive mb-0"><?= e($t['guest_name']) ?></h6>
                  <small class="text-muted"><?= e($t['guest_origin']) ?></small>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- FAQs Section -->
  <section class="py-5">
    <div class="container py-4">
      <div class="text-center max-w-700 mx-auto mb-5">
        <span class="section-subtitle">Got Questions?</span>
        <h2 class="display-6 fw-bold text-olive">Frequently Asked Questions</h2>
        <p class="text-muted">Everything you need to know about preparing for your Kenyan & Tanzanian wildlife adventure.</p>
      </div>

      <div class="row justify-content-center">
        <div class="col-lg-9">
          <div class="accordion" id="safariFaq">
            <?php foreach ($faqs as $index => $faq): ?>
              <div class="accordion-item border-0 mb-3 rounded-3 overflow-hidden shadow-sm">
                <h2 class="accordion-header">
                  <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?> fw-bold text-olive" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= e($faq['id']) ?>">
                    <?= e($faq['question']) ?>
                  </button>
                </h2>
                <div id="faq<?= e($faq['id']) ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#safariFaq">
                  <div class="accordion-body text-muted">
                    <?= nl2br(e($faq['answer'])) ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="py-5 bg-savannah text-white" id="contact">
    <div class="container py-4">
      <div class="row g-5">
        <div class="col-lg-5">
          <span class="section-subtitle">Get In Touch</span>
          <h2 class="display-6 fw-bold mb-3 text-white">Let’s Plan Your Dream Safari</h2>
          <p class="text-light opacity-75 mb-4">
            Speak directly with our local tour consultants in Narok Town and Nairobi. We tailor every itinerary to your budget, time, and wildlife wishlist.
          </p>

          <div class="d-flex align-items-start gap-3 mb-4">
            <div class="bg-gold p-3 rounded-3 text-dark"><i class="fa-solid fa-map-location-dot fs-5"></i></div>
            <div>
              <h6 class="fw-bold text-gold mb-1">Narok Town Head Office</h6>
              <p class="small text-light opacity-75 mb-0">Narok Business Plaza, Along Masai Mara Highway, Narok Town, Kenya</p>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3 mb-4">
            <div class="bg-gold p-3 rounded-3 text-dark"><i class="fa-solid fa-building text-dark fs-5"></i></div>
            <div>
              <h6 class="fw-bold text-gold mb-1">Nairobi Booking Hub</h6>
              <p class="small text-light opacity-75 mb-0">Westlands Commercial Centre, Nairobi, Kenya</p>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3 mb-4">
            <div class="bg-gold p-3 rounded-3 text-dark"><i class="fa-solid fa-phone fs-5"></i></div>
            <div>
              <h6 class="fw-bold text-gold mb-1">Direct Lines & WhatsApp</h6>
              <p class="small text-light opacity-75 mb-0">+254 712 345 678 / +254 798 765 432</p>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3">
            <div class="bg-gold p-3 rounded-3 text-dark"><i class="fa-solid fa-envelope fs-5"></i></div>
            <div>
              <h6 class="fw-bold text-gold mb-1">Email Reservations</h6>
              <p class="small text-light opacity-75 mb-0">info@planetwanderstours.com | bookings@planetwanderstours.com</p>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="bg-white p-4 p-md-5 rounded-4 text-dark shadow-lg">
            <h4 class="fw-bold text-olive mb-3">Send Us a Direct Safari Inquiry</h4>
            <p class="text-muted small mb-4">Fill out your travel details and we will reply with a detailed quote & custom itinerary within 2 hours.</p>

            <form id="contactForm" onsubmit="handleContactSubmit(event)">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small fw-bold">Full Name *</label>
                  <input type="text" class="form-control" id="contactName" placeholder="e.g. John Doe" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold">Email Address *</label>
                  <input type="email" class="form-control" id="contactEmail" placeholder="e.g. john@example.com" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold">Phone / WhatsApp *</label>
                  <input type="tel" class="form-control" id="contactPhone" placeholder="+254 700 000 000" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold">Preferred Destination</label>
                  <select class="form-select" id="contactDestination">
                    <?php foreach ($destinations as $dest): ?>
                      <option value="<?= e($dest['name']) ?>"><?= e($dest['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label small fw-bold">Trip Details / Special Requests</label>
                  <textarea class="form-control" id="contactMessage" rows="4" placeholder="Tell us your desired travel dates, number of guests, budget range, or any specific wildlife expectations..."></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-gold w-100 py-3" id="contactSubmitBtn">
                    <i class="fa-solid fa-paper-plane me-2"></i> Submit Inquiry
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-black text-light py-5 border-top border-secondary border-opacity-25">
    <div class="container">
      <div class="row g-4 justify-content-between">
        <div class="col-lg-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <div class="bg-gold text-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
              <i class="fa-solid fa-compass"></i>
            </div>
            <span class="fw-bold tracking-wide text-white fs-5 font-serif">PLANET WANDERS TOURS</span>
          </div>
          <p class="text-white-50 small mb-3">
            Authentic wildlife journeys based in Narok Town & Nairobi. Gateway to Masai Mara, Amboseli, Serengeti, and pristine Swahili coastal getaways.
          </p>
          <div class="d-flex gap-2">
            <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle text-gold"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle text-gold"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle text-gold"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle text-gold"><i class="fa-brands fa-tripadvisor"></i></a>
          </div>
        </div>

        <div class="col-6 col-lg-2">
          <h6 class="text-gold fw-bold mb-3">Safaris</h6>
          <ul class="list-unstyled small text-white-50">
            <li class="mb-2"><a href="#packages" class="text-white-50 text-decoration-none">Masai Mara Safaris</a></li>
            <li class="mb-2"><a href="#packages" class="text-white-50 text-decoration-none">Amboseli Elephants</a></li>
            <li class="mb-2"><a href="#packages" class="text-white-50 text-decoration-none">Serengeti Tanzania</a></li>
            <li class="mb-2"><a href="#packages" class="text-white-50 text-decoration-none">Lake Nakuru & Naivasha</a></li>
            <li class="mb-2"><a href="#packages" class="text-white-50 text-decoration-none">Tsavo East & West</a></li>
          </ul>
        </div>

        <div class="col-6 col-lg-2">
          <h6 class="text-gold fw-bold mb-3">Special Trips</h6>
          <ul class="list-unstyled small text-white-50">
            <li class="mb-2"><a href="#services" class="text-white-50 text-decoration-none">Honeymoon Getaways</a></li>
            <li class="mb-2"><a href="#services" class="text-white-50 text-decoration-none">Diani Beach Holidays</a></li>
            <li class="mb-2"><a href="#services" class="text-white-50 text-decoration-none">Watamu Marine Tours</a></li>
            <li class="mb-2"><a href="#services" class="text-white-50 text-decoration-none">Educational Study Tours</a></li>
            <li class="mb-2"><a href="#fleet" class="text-white-50 text-decoration-none">Private 4x4 Jeep Hire</a></li>
          </ul>
        </div>

        <div class="col-lg-3">
          <h6 class="text-gold fw-bold mb-3">Accreditations & Trust</h6>
          <p class="small text-white-50 mb-2">Proud member of regional tour operator safety guidelines and community conservancy funds.</p>
          <div class="p-2 bg-dark rounded border border-secondary border-opacity-50 small text-gold">
            <i class="fa-solid fa-medal me-1"></i> Verified Local Safari Operator
          </div>
        </div>
      </div>

      <div class="border-top border-secondary border-opacity-25 mt-4 pt-4 text-center small text-white-50">
        © <span id="currentYear"><?= date('Y') ?></span> Planet Wanders Tours & Safaris. Narok & Nairobi, Kenya. All rights reserved.
      </div>
    </div>
  </footer>

  <!-- WhatsApp Floating Trigger -->
  <a href="https://wa.me/254712345678?text=Hello%20Planet%20Wanders%20Tours!%20I%20would%20like%20to%20inquire%20about%20a%20Masai%20Mara%20safari." 
     class="floating-whatsapp" target="_blank" rel="noopener noreferrer" title="Chat with Narok Office on WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
  </a>

  <!-- Itinerary Modal -->
  <div class="modal fade" id="itineraryModal" tabindex="-1" aria-labelledby="itineraryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rounded-4 border-0">
        <div class="modal-header bg-olive text-white rounded-top-4">
          <h5 class="modal-title font-serif" id="itineraryModalLabel">Tour Details</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4" id="itineraryModalContent"></div>
        <div class="modal-footer bg-sand rounded-bottom-4">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-gold rounded-pill px-4" id="modalBookBtn">Inquire For This Trip</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Status Notification Modal -->
  <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-body text-center p-4">
          <div class="text-gold display-4 mb-3" id="statusModalIcon">
            <i class="fa-solid fa-circle-check"></i>
          </div>
          <h4 class="fw-bold text-olive mb-2" id="statusModalTitle">Inquiry Received</h4>
          <p class="text-muted" id="statusModalBody">Thank you! Our Narok & Nairobi team will reach back out promptly.</p>
          <button type="button" class="btn btn-gold rounded-pill px-4 mt-2" data-bs-dismiss="modal">Got it</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // ------------------------------------------------------------------------
    // Dynamic Configuration from Database
    // ------------------------------------------------------------------------
    const exchangeRatesMap = <?= json_encode(array_column($exchangeRates, 'rate_to_usd', 'currency_code')) ?>;
    const currencySymbols = <?= json_encode(array_column($exchangeRates, 'symbol', 'currency_code')) ?>;
    const itineraryData = <?= json_encode($itineraryData) ?>;

    let currentCurrency = 'KES';

    // Currency Formatting Utility
    function formatMoney(amountInUSD, currency) {
      const rate = parseFloat(exchangeRatesMap[currency]) || 1;
      const symbol = currencySymbols[currency] || currency;
      const converted = Math.round(amountInUSD * rate);
      return `${symbol} ${converted.toLocaleString()}`;
    }

    // Set & Persist Currency Switcher
    function setCurrency(curr) {
      currentCurrency = curr;
      localStorage.setItem('planet_wanders_currency', curr);

      // Update button active states
      document.querySelectorAll('.currency-switch-btn').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-currency') === curr);
      });

      // Update calculator badge
      const badge = document.getElementById('calcCurrencyBadge');
      if (badge) {
        badge.textContent = `Currency: ${curr}`;
      }

      // Update add-ons labels in calculator
      document.querySelectorAll('.addon-checkbox').forEach(cb => {
        const usdPrice = parseFloat(cb.getAttribute('data-usd'));
        const name = cb.getAttribute('data-name');
        const label = document.getElementById(`label_${cb.id.replace('addon_', '')}`);
        if (label && !isNaN(usdPrice)) {
          label.textContent = `${name} (${formatMoney(usdPrice, curr)}/p)`;
        }
      });

      // Re-render package cards prices
      document.querySelectorAll('.package-item .price-val').forEach(el => {
        const usdPrice = parseFloat(el.getAttribute('data-usd'));
        if (!isNaN(usdPrice)) {
          el.textContent = formatMoney(usdPrice, curr);
        }
      });

      // Recalculate calculator display
      calculateSafariCost();
    }

    document.addEventListener('DOMContentLoaded', function() {
      // Navbar scroll effect
      window.addEventListener('scroll', function() {
        const navbar = document.getElementById('mainNavbar');
        navbar.classList.toggle('scrolled', window.scrollY > 40);
      });

      // Read stored currency or default to KES
      const savedCurrency = localStorage.getItem('planet_wanders_currency') || 'KES';
      setCurrency(savedCurrency);

      // Filter Packages Category Tabs
      const filterButtons = document.querySelectorAll('#packageFilterButtons .filter-btn');
      const packageItems = document.querySelectorAll('.package-item');

      filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
          filterButtons.forEach(b => b.classList.remove('active'));
          this.classList.add('active');

          const filterValue = this.getAttribute('data-filter');
          packageItems.forEach(item => {
            const category = item.getAttribute('data-category');
            item.style.display = (filterValue === 'all' || category === filterValue) ? 'block' : 'none';
          });
        });
      });
    });

    // Safari Cost Calculator Calculation
    function calculateSafariCost() {
      const destSelect = document.getElementById('calcDestination');
      const vehicleSelect = document.getElementById('calcVehicle');
      const tierSelect = document.getElementById('calcTier');
      const days = parseInt(document.getElementById('calcDays').value) || 1;
      const adults = parseInt(document.getElementById('calcAdults').value) || 1;

      const destDailyParkFeeUSD = parseFloat(destSelect.options[destSelect.selectedIndex]?.getAttribute('data-daily-usd') || 0);
      const vehicleCostPerDayUSD = parseFloat(vehicleSelect.options[vehicleSelect.selectedIndex]?.getAttribute('data-perday-usd') || 0);
      const roomCostPerPersonPerDayUSD = parseFloat(tierSelect.options[tierSelect.selectedIndex]?.getAttribute('data-room-usd') || 0);

      let addonCostUSD = 0;
      document.querySelectorAll('.addon-checkbox:checked').forEach(cb => {
        const cost = parseFloat(cb.getAttribute('data-usd') || 0);
        addonCostUSD += (cost * adults);
      });

      // Formula: (Vehicle daily * days) + (Parks per adult * days) + (Rooms per adult * max(1, days-1)) + Addons
      const totalVehicleCost = vehicleCostPerDayUSD * days;
      const totalParkAndGuide = destDailyParkFeeUSD * adults * days;
      const totalRooms = roomCostPerPersonPerDayUSD * adults * Math.max(1, days - 1);

      const grandTotalUSD = Math.round(totalVehicleCost + totalParkAndGuide + totalRooms + addonCostUSD);
      const perPersonUSD = Math.round(grandTotalUSD / adults);

      document.getElementById('totalPriceDisplay').textContent = formatMoney(grandTotalUSD, currentCurrency);
      document.getElementById('pricePerPersonDisplay').textContent = `Approx. ${formatMoney(perPersonUSD, currentCurrency)} per person (for ${adults} guests)`;
    }

    // Direct WhatsApp quote
    function sendWhatsAppQuote() {
      const dest = document.getElementById('calcDestination').selectedOptions[0].text;
      const vehicle = document.getElementById('calcVehicle').selectedOptions[0].text;
      const days = document.getElementById('calcDays').value;
      const adults = document.getElementById('calcAdults').value;
      const total = document.getElementById('totalPriceDisplay').textContent;

      const message = `Hello Planet Wanders Tours! I would like to book a safari based on your website calculator:%0A- Destination: ${encodeURIComponent(dest)}%0A- Vehicle: ${encodeURIComponent(vehicle)}%0A- Duration: ${days} Days%0A- Adults: ${adults}%0A- Quoted Total: ${encodeURIComponent(total)}%0APlease confirm availability for my dates!`;
      window.open(`https://wa.me/254712345678?text=${message}`, '_blank');
    }

    // Quick Search submission
    function handleQuickSearch(e) {
      e.preventDefault();
      const dest = document.getElementById('quickDest').value;
      const date = document.getElementById('quickDate').value;
      const guests = document.getElementById('quickGuests').value;
      const vehicle = document.getElementById('quickVehicle').value;

      showStatusModal(
        'Checking Availability',
        `Checking availability for ${dest} around ${date || 'upcoming dates'} for ${guests} using ${vehicle}. Connecting you with our Narok reservation team!`
      );
      
      setTimeout(() => {
        const text = `Hi Planet Wanders! I am inquiring for ${dest}, Target Date: ${date}, Group: ${guests}, Vehicle: ${vehicle}. Are there available slots?`;
        window.open(`https://wa.me/254712345678?text=${encodeURIComponent(text)}`, '_blank');
      }, 1400);
    }

    // Open Package Modal
    function openItineraryModal(key) {
      const data = itineraryData[key];
      if (!data) return;

      const modalTitle = document.getElementById('itineraryModalLabel');
      const modalBody = document.getElementById('itineraryModalContent');
      const bookBtn = document.getElementById('modalBookBtn');

      modalTitle.textContent = data.title;

      let scheduleHtml = '';
      data.schedule.forEach(item => {
        scheduleHtml += `
          <div class="mb-3 ps-3 border-start border-3 border-warning">
            <h6 class="fw-bold text-olive mb-1">${item.day}: ${item.title}</h6>
            <p class="small text-muted mb-0">${item.desc}</p>
          </div>
        `;
      });

      let includesHtml = '';
      data.includes.forEach(inc => {
        includesHtml += `<li class="small text-muted mb-1"><i class="fa-solid fa-check text-gold me-2"></i>${inc}</li>`;
      });

      modalBody.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="badge bg-olive px-3 py-2 fs-6"><i class="fa-regular fa-clock me-1"></i> ${data.duration}</span>
          <span class="text-gold fw-bold fs-5">${formatMoney(data.usdPrice, currentCurrency)} per person</span>
        </div>
        <p class="text-secondary">${data.overview}</p>
        <h5 class="fw-bold text-olive mt-4 mb-3 font-serif">Daily Itinerary Schedule</h5>
        ${scheduleHtml || '<p class="small text-muted">Detailed day-by-day itinerary will be provided upon booking inquiry.</p>'}
        <h5 class="fw-bold text-olive mt-4 mb-2 font-serif">Package Inclusions</h5>
        <ul class="list-unstyled mb-0">
          ${includesHtml || '<li class="small text-muted">All-inclusive transport, park entry, and accommodation.</li>'}
        </ul>
      `;

      bookBtn.onclick = function() {
        const myModalEl = document.getElementById('itineraryModal');
        const modal = bootstrap.Modal.getInstance(myModalEl);
        if (modal) modal.hide();
        prefillBooking(data.title, data.usdPrice);
      };

      const myModal = new bootstrap.Modal(document.getElementById('itineraryModal'));
      myModal.show();
    }

    // Prefill form
    function prefillBooking(packageName, priceUSD) {
      document.getElementById('contact').scrollIntoView({ behavior: 'smooth' });
      const contactMsg = document.getElementById('contactMessage');
      contactMsg.value = `Hello, I am interested in booking the "${packageName}". Please provide available departure dates and the confirmation procedure.`;
      contactMsg.focus();
    }

    // Contact Form submission via Fetch (AJAX)
    async function handleContactSubmit(e) {
      e.preventDefault();
      const submitBtn = document.getElementById('contactSubmitBtn');
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Submitting...';

      const formData = new FormData();
      formData.append('action', 'submit_inquiry');
      formData.append('full_name', document.getElementById('contactName').value);
      formData.append('email', document.getElementById('contactEmail').value);
      formData.append('phone', document.getElementById('contactPhone').value);
      formData.append('destination', document.getElementById('contactDestination').value);
      formData.append('message', document.getElementById('contactMessage').value);

      try {
        const response = await fetch(window.location.href, {
          method: 'POST',
          body: formData
        });
        const res = await response.json();

        if (res.status === 'success') {
          showStatusModal(
            'Inquiry Received!',
            `Thank you! Your reference code is ${res.reference}. Our Narok & Nairobi team has received your request and will reach out via WhatsApp / Email within 2 hours.`
          );
          document.getElementById('contactForm').reset();
        } else {
          showStatusModal('Notice', 'We could not submit your inquiry. Please reach us directly via WhatsApp.');
        }
      } catch (err) {
        showStatusModal('Inquiry Sent', 'Thank you! Your request has been queued for our reservation desk.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i> Submit Inquiry';
      }
    }

    function showStatusModal(title, body) {
      document.getElementById('statusModalTitle').textContent = title;
      document.getElementById('statusModalBody').textContent = body;
      const modal = new bootstrap.Modal(document.getElementById('statusModal'));
      modal.show();
    }
  </script>
</body>
</html>