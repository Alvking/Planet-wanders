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
      background: rgba(17, 23, 18, 0.88);
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
        <a href="tel:+254700000000" class="text-white text-decoration-none"><i class="fa-solid fa-phone text-gold me-1"></i> +254 712 345 678</a>
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
        <div class="d-flex align-items-center gap-2">
          <a href="#calculator" class="btn btn-gold text-white btn-sm px-4">
            <i class="fa-solid fa-calculator me-1"></i> Get Quick Quote
          </a>
        </div>
      </div>
    </div>
  </nav>

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

  <div class="container position-relative">
    <div class="inquiry-card p-4 p-md-5">
      <form id="quickSearchForm" onsubmit="handleQuickSearch(event)">
        <div class="row g-3 align-items-end">
          <div class="col-lg-3 col-md-6">
            <label class="form-label small fw-bold text-uppercase text-muted"><i class="fa-solid fa-map-pin text-gold me-1"></i> Destination</label>
            <select class="form-select" id="quickDest" required>
              <option value="Masai Mara Game Reserve">Masai Mara Reserve</option>
              <option value="Amboseli National Park">Amboseli (Mt Kilimanjaro)</option>
              <option value="Serengeti & Ngorongoro (Tanzania)">Serengeti & Ngorongoro</option>
              <option value="Lake Nakuru & Naivasha">Lake Nakuru & Naivasha</option>
              <option value="Diani & Watamu Beach Escapes">Diani & Watamu Beach</option>
              <option value="Full Kenya Wildlife & Coast Circuit">Kenya Wildlife & Coast</option>
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
              <option value="Custom 4x4 Safari Land Cruiser">4x4 Land Cruiser</option>
              <option value="Safari Tour Minivan">Tour Minivan</option>
              <option value="Fly-in Safari Option">Fly-In Safari</option>
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
          <button class="filter-btn" data-filter="mara">Maasai Mara</button>
          <button class="filter-btn" data-filter="amboseli">Amboseli & Rift</button>
          <button class="filter-btn" data-filter="tanzania">Serengeti (TZ)</button>
          <button class="filter-btn" data-filter="beach">Beach & Coastal</button>
          <button class="filter-btn" data-filter="honeymoon">Honeymoon & Special</button>
        </div>
      </div>

      <!-- Packages Grid -->
      <div class="row g-4" id="packagesContainer">
        
        <!-- Package 1: 3-Day Masai Mara Classic -->
        <div class="col-lg-4 col-md-6 package-item" data-category="mara">
          <div class="package-card">
            <div class="package-img-holder">
              <img src="https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?auto=format&fit=crop&w=700&q=80" alt="Masai Mara Safari">
              <span class="badge-ribbon"><i class="fa-solid fa-star text-gold me-1"></i> Most Popular</span>
              <span class="badge-price">From $450 <small class="text-white fw-normal">/ person</small></span>
            </div>
            <div class="p-4 d-flex flex-column flex-grow-1">
              <div class="d-flex justify-content-between text-muted small mb-2">
                <span><i class="fa-regular fa-clock text-gold me-1"></i> 3 Days / 2 Nights</span>
                <span><i class="fa-solid fa-location-dot text-gold me-1"></i> Masai Mara</span>
              </div>
              <h4 class="fw-bold text-olive mb-2">3-Day Authentic Masai Mara Wildebeest Safari</h4>
              <p class="text-muted small flex-grow-1">Embark on the quintessential African safari. Private 4x4 game drives through Mara plains, lion pride sightings, and optional Maasai village visit.</p>
              <div class="border-top pt-3 mt-2 d-flex gap-2">
                <button class="btn btn-outline-gold btn-sm flex-grow-1" onclick="openItineraryModal('mara3')">
                  <i class="fa-solid fa-list-check me-1"></i> Itinerary
                </button>
                <button class="btn btn-gold btn-sm flex-grow-1" onclick="prefillBooking('3-Day Authentic Masai Mara Wildebeest Safari', 450)">
                  Book Now
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Package 2: 4-Day Amboseli & Lake Naivasha -->
        <div class="col-lg-4 col-md-6 package-item" data-category="amboseli">
          <div class="package-card">
            <div class="package-img-holder">
              <img src="https://images.unsplash.com/photo-1575550959106-5a7defe28b56?auto=format&fit=crop&w=700&q=80" alt="Amboseli Elephants">
              <span class="badge-ribbon"><i class="fa-solid fa-mountain me-1"></i> Mt. Kilimanjaro Views</span>
              <span class="badge-price">From $620 <small class="text-white fw-normal">/ person</small></span>
            </div>
            <div class="p-4 d-flex flex-column flex-grow-1">
              <div class="d-flex justify-content-between text-muted small mb-2">
                <span><i class="fa-regular fa-clock text-gold me-1"></i> 4 Days / 3 Nights</span>
                <span><i class="fa-solid fa-location-dot text-gold me-1"></i> Amboseli & Naivasha</span>
              </div>
              <h4 class="fw-bold text-olive mb-2">4-Day Amboseli Giant Tuskers & Crescent Island</h4>
              <p class="text-muted small flex-grow-1">Magnificent elephant herds against the snowcapped backdrop of Mount Kilimanjaro, followed by a boat safari & walking safari in Naivasha.</p>
              <div class="border-top pt-3 mt-2 d-flex gap-2">
                <button class="btn btn-outline-gold btn-sm flex-grow-1" onclick="openItineraryModal('amboseli4')">
                  <i class="fa-solid fa-list-check me-1"></i> Itinerary
                </button>
                <button class="btn btn-gold btn-sm flex-grow-1" onclick="prefillBooking('4-Day Amboseli Giant Tuskers & Naivasha', 620)">
                  Book Now
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Package 3: 5-Day Serengeti & Ngorongoro Crater -->
        <div class="col-lg-4 col-md-6 package-item" data-category="tanzania">
          <div class="package-card">
            <div class="package-img-holder">
              <img src="https://images.unsplash.com/photo-1534177616072-ef7dc120449d?auto=format&fit=crop&w=700&q=80" alt="Serengeti Tanzania">
              <span class="badge-ribbon"><i class="fa-solid fa-globe-africa me-1"></i> Tanzania Premier</span>
              <span class="badge-price">From $980 <small class="text-white fw-normal">/ person</small></span>
            </div>
            <div class="p-4 d-flex flex-column flex-grow-1">
              <div class="d-flex justify-content-between text-muted small mb-2">
                <span><i class="fa-regular fa-clock text-gold me-1"></i> 5 Days / 4 Nights</span>
                <span><i class="fa-solid fa-location-dot text-gold me-1"></i> Serengeti & Ngorongoro</span>
              </div>
              <h4 class="fw-bold text-olive mb-2">5-Day Endless Serengeti & Ngorongoro Crater</h4>
              <p class="text-muted small flex-grow-1">Descend into the UNESCO World Heritage Ngorongoro Caldera and traverse the endless plains of the legendary Serengeti for big cats.</p>
              <div class="border-top pt-3 mt-2 d-flex gap-2">
                <button class="btn btn-outline-gold btn-sm flex-grow-1" onclick="openItineraryModal('serengeti5')">
                  <i class="fa-solid fa-list-check me-1"></i> Itinerary
                </button>
                <button class="btn btn-gold btn-sm flex-grow-1" onclick="prefillBooking('5-Day Serengeti & Ngorongoro Crater', 980)">
                  Book Now
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Package 4: 7-Day Bush to Beach (Mara + Diani Beach) -->
        <div class="col-lg-4 col-md-6 package-item" data-category="beach">
          <div class="package-card">
            <div class="package-img-holder">
              <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=700&q=80" alt="Diani Beach">
              <span class="badge-ribbon"><i class="fa-solid fa-umbrella-beach me-1"></i> Bush & Beach Combo</span>
              <span class="badge-price">From $1,150 <small class="text-white fw-normal">/ person</small></span>
            </div>
            <div class="p-4 d-flex flex-column flex-grow-1">
              <div class="d-flex justify-content-between text-muted small mb-2">
                <span><i class="fa-regular fa-clock text-gold me-1"></i> 7 Days / 6 Nights</span>
                <span><i class="fa-solid fa-location-dot text-gold me-1"></i> Mara & Diani Coast</span>
              </div>
              <h4 class="fw-bold text-olive mb-2">7-Day Bush-to-Beach: Masai Mara & Diani Sands</h4>
              <p class="text-muted small flex-grow-1">Experience raw savanna game tracking followed by scenic coastal flight to Diani Beach. White sands, turquoise waters, and fresh seafood.</p>
              <div class="border-top pt-3 mt-2 d-flex gap-2">
                <button class="btn btn-outline-gold btn-sm flex-grow-1" onclick="openItineraryModal('bushbeach7')">
                  <i class="fa-solid fa-list-check me-1"></i> Itinerary
                </button>
                <button class="btn btn-gold btn-sm flex-grow-1" onclick="prefillBooking('7-Day Bush-to-Beach: Masai Mara & Diani', 1150)">
                  Book Now
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Package 5: 6-Day Luxury Honeymoon Safari Escape -->
        <div class="col-lg-4 col-md-6 package-item" data-category="honeymoon">
          <div class="package-card">
            <div class="package-img-holder">
              <img src="https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=700&q=80" alt="Luxury Safari Camp">
              <span class="badge-ribbon"><i class="fa-solid fa-champagne-glasses me-1"></i> Honeymoon Special</span>
              <span class="badge-price">From $1,420 <small class="text-white fw-normal">/ couple</small></span>
            </div>
            <div class="p-4 d-flex flex-column flex-grow-1">
              <div class="d-flex justify-content-between text-muted small mb-2">
                <span><i class="fa-regular fa-clock text-gold me-1"></i> 6 Days / 5 Nights</span>
                <span><i class="fa-solid fa-location-dot text-gold me-1"></i> Mara & Watamu</span>
              </div>
              <h4 class="fw-bold text-olive mb-2">Romantic Bush & Coral Reef Honeymoon Escape</h4>
              <p class="text-muted small flex-grow-1">Candlelight bush dinners under African stars, private hot air balloon over Mara river, luxury tented suites, and serene coastal marine park relaxing.</p>
              <div class="border-top pt-3 mt-2 d-flex gap-2">
                <button class="btn btn-outline-gold btn-sm flex-grow-1" onclick="openItineraryModal('honeymoon6')">
                  <i class="fa-solid fa-list-check me-1"></i> Itinerary
                </button>
                <button class="btn btn-gold btn-sm flex-grow-1" onclick="prefillBooking('Romantic Bush & Coral Reef Honeymoon Escape', 1420)">
                  Book Now
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Package 6: 4-Day Educational Wildlife & Conservation Study Tour -->
        <div class="col-lg-4 col-md-6 package-item" data-category="honeymoon">
          <div class="package-card">
            <div class="package-img-holder">
              <img src="https://images.unsplash.com/photo-1534567153574-2b12153a87f0?auto=format&fit=crop&w=700&q=80" alt="Ranger & Rhinos">
              <span class="badge-ribbon"><i class="fa-solid fa-graduation-cap me-1"></i> Educational & Group</span>
              <span class="badge-price">From $390 <small class="text-white fw-normal">/ student</small></span>
            </div>
            <div class="p-4 d-flex flex-column flex-grow-1">
              <div class="d-flex justify-content-between text-muted small mb-2">
                <span><i class="fa-regular fa-clock text-gold me-1"></i> 4 Days / 3 Nights</span>
                <span><i class="fa-solid fa-location-dot text-gold me-1"></i> Ol Pejeta & Mara</span>
              </div>
              <h4 class="fw-bold text-olive mb-2">Educational Ecology & Rhino Sanctuary Study Tour</h4>
              <p class="text-muted small flex-grow-1">Curated for universities, researchers, and school groups. Includes talks with Kenya Wildlife Service rangers, rhino tracking, and community conservancies.</p>
              <div class="border-top pt-3 mt-2 d-flex gap-2">
                <button class="btn btn-outline-gold btn-sm flex-grow-1" onclick="openItineraryModal('study4')">
                  <i class="fa-solid fa-list-check me-1"></i> Itinerary
                </button>
                <button class="btn btn-gold btn-sm flex-grow-1" onclick="prefillBooking('Educational Ecology & Rhino Sanctuary Tour', 390)">
                  Book Now
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <section class="py-5" id="services">
    <div class="container py-4">
      <div class="text-center max-w-700 mx-auto mb-5">
        <span class="section-subtitle">What We Offer</span>
        <h2 class="display-6 fw-bold text-olive">Comprehensive Safari & Travel Logistics</h2>
        <p class="text-muted">From the moment you touch down at Jomo Kenyatta International Airport (JKIA) or drive up from Narok Town, Planet Wanders handles every touchpoint of your African journey.</p>
      </div>

      <div class="row g-4">
        <!-- Service 1 -->
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

        <!-- Service 2 -->
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

        <!-- Service 3 -->
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
              <p class="small text-light opacity-75 mb-0">Lock your dates with a modest deposit; pay remaining on arrival in Kenya.</p>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="calc-card">
            <h4 class="fw-bold text-gold mb-4"><i class="fa-solid fa-sliders me-2"></i> Build Your Custom Quote</h4>
            
            <div class="row g-3">
              <!-- Destination -->
              <div class="col-md-6">
                <label class="form-label small fw-bold text-light">Destination / Circuit</label>
                <select class="form-select bg-dark text-white border-secondary" id="calcDestination" onchange="calculateSafariCost()">
                  <option value="mara" data-daily="180">Maasai Mara Game Reserve</option>
                  <option value="mara-nakuru" data-daily="210">Masai Mara + Lake Nakuru</option>
                  <option value="amboseli" data-daily="195">Amboseli (Kilimanjaro View)</option>
                  <option value="serengeti" data-daily="260">Serengeti & Ngorongoro (TZ)</option>
                  <option value="bush-beach" data-daily="230">Mara Wildlife + Diani Beach</option>
                </select>
              </div>

              <!-- Vehicle Type -->
              <div class="col-md-6">
                <label class="form-label small fw-bold text-light">Vehicle Style</label>
                <select class="form-select bg-dark text-white border-secondary" id="calcVehicle" onchange="calculateSafariCost()">
                  <option value="cruiser" data-perday="220">Custom 4x4 Safari Land Cruiser (Pop-up)</option>
                  <option value="van" data-perday="130">Safari Tour Minivan (Pop-up Roof)</option>
                </select>
              </div>

              <!-- Days -->
              <div class="col-md-4">
                <label class="form-label small fw-bold text-light">Number of Days</label>
                <select class="form-select bg-dark text-white border-secondary" id="calcDays" onchange="calculateSafariCost()">
                  <option value="3">3 Days (Quick Mara Getaway)</option>
                  <option value="4">4 Days (Recommended)</option>
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
                  <option value="budget" data-room="90">Budget / Adventure Camp</option>
                  <option value="midrange" data-room="180" selected>Mid-Range Tented Lodge</option>
                  <option value="luxury" data-room="360">Luxury Safari Resort / Camp</option>
                </select>
              </div>

              <!-- Number of Travelers -->
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

              <!-- Optional Add-ons -->
              <div class="col-12 mt-2">
                <label class="form-label small fw-bold text-light d-block mb-1">Optional Safari Add-Ons:</label>
                <div class="d-flex flex-wrap gap-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="addonBalloon" onchange="calculateSafariCost()">
                    <label class="form-check-label small text-light" for="addonBalloon">Hot Air Balloon Safari ($450/p)</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="addonMaasai" onchange="calculateSafariCost()">
                    <label class="form-check-label small text-light" for="addonMaasai">Maasai Cultural Village Visit ($30/p)</label>
                  </div>
                </div>
              </div>

            </div>

            <!-- Price Output Box -->
            <div class="bg-black bg-opacity-40 p-3 rounded-4 mt-4 border border-secondary border-opacity-50">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                  <span class="text-light small text-uppercase">Estimated Total (All Travelers):</span>
                  <div class="display-6 fw-bold text-gold" id="totalPriceDisplay">$1,480</div>
                  <small class="text-white-50" id="pricePerPersonDisplay">Approx. $740 per person</small>
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

  <section class="py-5 bg-sand">
    <div class="container py-4">
      <div class="text-center max-w-700 mx-auto mb-5">
        <span class="section-subtitle">Real Traveler Stories</span>
        <h2 class="display-6 fw-bold text-olive">Unforgettable Memories From Our Guests</h2>
        <p class="text-muted">Hear from solo explorers, couples on honeymoon, and families who journeyed into the wild with Planet Wanders.</p>
      </div>

      <div class="row g-4">
        <!-- Testimonial 1 -->
        <div class="col-md-4">
          <div class="bg-white p-4 rounded-4 shadow-sm h-100 border border-light d-flex flex-column">
            <div class="d-flex text-warning mb-3">
              <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <p class="text-muted small flex-grow-1">
              "Planet Wanders made our Masai Mara safari extraordinary! Because their team is rooted in Narok, our guide Peter knew every shortcut and secret spot. We witnessed a leopard hunting and the Great Migration crossing on our second day!"
            </p>
            <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top">
              <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80" class="guest-avatar" alt="Guest Sarah">
              <div>
                <h6 class="fw-bold text-olive mb-0">Sarah & David M.</h6>
                <small class="text-muted">London, United Kingdom</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 2 -->
        <div class="col-md-4">
          <div class="bg-white p-4 rounded-4 shadow-sm h-100 border border-light d-flex flex-column">
            <div class="d-flex text-warning mb-3">
              <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <p class="text-muted small flex-grow-1">
              "We booked the 7-day Bush to Beach package. The Land Cruiser was super clean, pop-up roof gave us stunning shots of cheetahs, and the flight transfer to Diani beach was seamlessly organized. Top notch value!"
            </p>
            <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top">
              <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80" class="guest-avatar" alt="Guest Marcus">
              <div>
                <h6 class="fw-bold text-olive mb-0">Marcus Weber</h6>
                <small class="text-muted">Munich, Germany</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial 3 -->
        <div class="col-md-4">
          <div class="bg-white p-4 rounded-4 shadow-sm h-100 border border-light d-flex flex-column">
            <div class="d-flex text-warning mb-3">
              <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <p class="text-muted small flex-grow-1">
              "Our honeymoon in Kenya was a dream come true. The private candlelit dinner in the Mara bush and the surprise champagne breakfast after our hot air balloon ride made us cry happy tears. Asanteni sana Planet Wanders!"
            </p>
            <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top">
              <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" class="guest-avatar" alt="Guest Elena">
              <div>
                <h6 class="fw-bold text-olive mb-0">Elena & Carlos G.</h6>
                <small class="text-muted">Madrid, Spain</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

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
            
            <div class="accordion-item border-0 mb-3 rounded-3 overflow-hidden shadow-sm">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold text-olive" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                  When is the best time to see the Great Wildebeest Migration in Masai Mara?
                </button>
              </h2>
              <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#safariFaq">
                <div class="accordion-body text-muted">
                  The Great Migration typically arrives in the Maasai Mara from late June/July and stays until October or November. However, the Maasai Mara is a world-class, year-round wildlife paradise with big cats (lions, leopards, cheetahs) and wildlife present in large numbers all 12 months.
                </div>
              </div>
            </div>

            <div class="accordion-item border-0 mb-3 rounded-3 overflow-hidden shadow-sm">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold text-olive" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                  What is the difference between a 4x4 Land Cruiser and a Safari Tour Van?
                </button>
              </h2>
              <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#safariFaq">
                <div class="accordion-body text-muted">
                  Both vehicles feature pop-up roofs for 360-degree game viewing. The <strong>4x4 Toyota Land Cruiser</strong> has superior off-road clearance, handles deep mud during rainy seasons effortlessly, and offers a smoother, elevated ride. The <strong>Safari Minivan</strong> is a more budget-conscious alternative suited for smooth dry trails.
                </div>
              </div>
            </div>

            <div class="accordion-item border-0 mb-3 rounded-3 overflow-hidden shadow-sm">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold text-olive" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                  Are park entrance fees and meals included in your packages?
                </button>
              </h2>
              <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#safariFaq">
                <div class="accordion-body text-muted">
                  Yes! All our standard safari packages are all-inclusive: government park conservation fees, full-board accommodation (breakfast, lunch, dinner), unlimited game drives, licensed guide services, and mineral drinking water in the safari vehicle.
                </div>
              </div>
            </div>

            <div class="accordion-item border-0 mb-3 rounded-3 overflow-hidden shadow-sm">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold text-olive" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                  Where do safaris depart from? Can we get picked up in Narok or Nairobi?
                </button>
              </h2>
              <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#safariFaq">
                <div class="accordion-body text-muted">
                  We provide door-to-door pickups from any hotel or airport in Nairobi (JKIA / Wilson Airport), as well as direct departures from our Narok Town offices if you are already in the Great Rift Valley or western Kenya region.
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>

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
                  <input type="tel" class="form-control" id="contactPhone" placeholder="+1 234 567 8900" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold">Preferred Destination</label>
                  <select class="form-select" id="contactDestination">
                    <option value="Masai Mara Classic Safari">Masai Mara Classic</option>
                    <option value="Amboseli & Naivasha">Amboseli & Kilimanjaro</option>
                    <option value="Serengeti & Tanzania">Serengeti & Ngorongoro</option>
                    <option value="Diani or Watamu Beach">Diani / Watamu Beach Vacation</option>
                    <option value="Custom Multi-Day Circuit">Custom Circuit</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label small fw-bold">Trip Details / Special Requests</label>
                  <textarea class="form-control" id="contactMessage" rows="4" placeholder="Tell us your desired travel dates, number of guests, budget range, or any specific wildlife expectations..."></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-gold w-100 py-3">
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
        © <span id="currentYear"></span> Planet Wanders Tours & Safaris. Narok & Nairobi, Kenya. All rights reserved.
      </div>
    </div>
  </footer>

  <a href="https://wa.me/254712345678?text=Hello%20Planet%20Wanders%20Tours!%20I%20would%20like%20to%20inquire%20about%20a%20Masai%20Mara%20safari." 
     class="floating-whatsapp" target="_blank" rel="noopener noreferrer" title="Chat with Narok Office on WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
  </a>

  <div class="modal fade" id="itineraryModal" tabindex="-1" aria-labelledby="itineraryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rounded-4 border-0">
        <div class="modal-header bg-olive text-white rounded-top-4">
          <h5 class="modal-title font-serif" id="itineraryModalLabel">Tour Details</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4" id="itineraryModalContent">
          <!-- Dynamically populated via JS -->
        </div>
        <div class="modal-footer bg-sand rounded-bottom-4">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-gold rounded-pill px-4" id="modalBookBtn">Inquire For This Trip</button>
        </div>
      </div>
    </div>
  </div>

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
    // Data store for itinerary previews
    const itineraryData = {
      mara3: {
        title: "3-Day Authentic Masai Mara Wildebeest Safari",
        duration: "3 Days / 2 Nights",
        price: "$450 per person",
        overview: "The quintessential African bush getaway starting from Nairobi or Narok Town. Features morning and late afternoon game drives across the Maasai Mara National Reserve in search of the Big Five (Lion, Leopard, Elephant, Rhino, Buffalo) and millions of migratory ungulates.",
        schedule: [
          { day: "Day 1", title: "Nairobi / Narok to Masai Mara", desc: "Scenic departure descending the Great Rift Valley escarpment. Arrive in Masai Mara for lunch, followed by an introductory sunset game drive." },
          { day: "Day 2", title: "Full Day Big 5 & Mara River Safari", desc: "Full day in the reserve with packed picnic lunch under an acacia tree. Visit the Mara River to view hippos, Nile crocodiles, and crossing trails." },
          { day: "Day 3", title: "Dawn Game Drive & Return", desc: "Catch early sunrise predators on the hunt. Enjoy bush breakfast, optional Maasai village cultural tour, and scenic return journey." }
        ],
        includes: ["All park entry and conservation fees", "Full-board accommodation at safari tented camp", "Transport in 4x4 Land Cruiser with pop-up roof", "Certified professional naturalist driver guide"]
      },
      amboseli4: {
        title: "4-Day Amboseli Giant Tuskers & Crescent Island",
        duration: "4 Days / 3 Nights",
        price: "$620 per person",
        overview: "Witness huge free-ranging elephant herds with the breathtaking snow peaks of Mount Kilimanjaro in Amboseli National Park, combined with the freshwater bird sanctuary of Lake Naivasha.",
        schedule: [
          { day: "Day 1", title: "Transfer to Amboseli National Park", desc: "Morning pickup and drive to Amboseli. Afternoon game drive across the lake bed with views of Mt. Kilimanjaro." },
          { day: "Day 2", title: "Full Day Amboseli Wildlife", desc: "Observation hill excursion, swamp birding, and tracking the legendary big tuskers." },
          { day: "Day 3", title: "Amboseli to Lake Naivasha", desc: "Drive to the Great Rift Valley lake. Afternoon boat ride to Crescent Island Game Sanctuary for a walking safari among giraffes and zebras." },
          { day: "Day 4", title: "Naivasha to Nairobi", desc: "Leisurely breakfast, optional Hell’s Gate cycling tour, and transfer back to Nairobi or airport." }
        ],
        includes: ["Park fees for Amboseli & Naivasha boat rides", "Accommodations and all meals", "4x4 Land Cruiser transportation", "Drinking water throughout"]
      },
      serengeti5: {
        title: "5-Day Endless Serengeti & Ngorongoro Crater",
        duration: "5 Days / 4 Nights",
        price: "$980 per person",
        overview: "Cross borders into Tanzania to explore the world's most renowned wildlife ecosystem: the Serengeti's endless golden savannas and the dramatic volcanic caldera of Ngorongoro.",
        schedule: [
          { day: "Day 1", title: "Arusha to Ngorongoro Highlands", desc: "Drive through coffee plantations to the crater rim with breathtaking vistas." },
          { day: "Day 2", title: "Ngorongoro Crater Floor Safari", desc: "600-meter descent to the caldera floor for black rhino sightings and dense lion prides." },
          { day: "Day 3", title: "Serengeti Central Plains (Seronera)", desc: "Head into the Serengeti. Game viewing around the Seronera river valley famous for leopards." },
          { day: "Day 4", title: "Serengeti Big Cats & Migration", desc: "Full day tracking predator-prey dynamics and migration herds." },
          { day: "Day 5", title: "Serengeti to Arusha / Kilimanjaro Airport", desc: "Morning game drive and transfer for onward flight." }
        ],
        includes: ["Tanzania National Park entry & crater vehicle permits", "Comfortable safari lodge stays", "4x4 Land Cruiser with pop-up roof", "English/Swahili speaking expert tracker"]
      },
      bushbeach7: {
        title: "7-Day Bush-to-Beach: Masai Mara & Diani Sands",
        duration: "7 Days / 6 Nights",
        price: "$1,150 per person",
        overview: "The ultimate Kenya experience: 3 nights of adrenaline-pumping wildlife in the Maasai Mara followed by 3 nights of tropical serenity on the award-winning white sands of Diani Beach.",
        schedule: [
          { day: "Day 1-3", title: "Masai Mara Wilderness", desc: "Three unforgettable days of 4x4 game drives in Mara. Big five encounters and sundowners." },
          { day: "Day 4", title: "Bush Flight to Diani Beach", desc: "Fly from Mara bush airstrip directly to Ukunda / Diani Coast. Check into oceanfront beach resort." },
          { day: "Day 5-6", title: "Diani Beach Relaxation & Snorkeling", desc: "Unwind on powdery sand, optional Kisite Mpunguti Marine Park dolphin dhow safari or kite surfing." },
          { day: "Day 7", title: "Departure", desc: "Transfer to Mombasa airport or SGR train terminal for your flight back home." }
        ],
        includes: ["Mara safari all-inclusive", "Domestic flight connection to coastal strip", "Beach resort accommodation on Half Board", "All ground airport transfers"]
      },
      honeymoon6: {
        title: "Romantic Bush & Coral Reef Honeymoon Escape",
        duration: "6 Days / 5 Nights",
        price: "$1,420 per couple",
        overview: "Specially curated for newly married couples looking for intimacy, luxury, and thrill. Features private romantic bush dinners, champagne breakfasts, and boutique suites.",
        schedule: [
          { day: "Day 1", title: "VIP Arrival & Tented Suite Welcome", desc: "Welcome bottle of wine and private evening game drive with sunset appetizers." },
          { day: "Day 2", title: "Sunrise Hot Air Balloon & Bush Breakfast", desc: "Glide above the savanna at sunrise followed by champagne breakfast on the Mara plains." },
          { day: "Day 3", title: "Couples Massage & Stargazing", desc: "Relaxation in camp followed by intimate candlelit dinner under the Southern Cross stars." },
          { day: "Day 4-6", title: "Watamu Marine Bliss", desc: "Escape to Watamu turtle sanctuary and romantic sandbank dhow cruise." }
        ],
        includes: ["Luxury honeymoon safari suites", "Hot air balloon flight for two", "Special celebratory surprises and private vehicle", "Romantic dinners"]
      },
      study4: {
        title: "Educational Ecology & Rhino Sanctuary Study Tour",
        duration: "4 Days / 3 Nights",
        price: "$390 per student",
        overview: "Designed for school and college groups interested in biodiversity, wildlife veterinary science, anti-poaching canines, and community conservancies.",
        schedule: [
          { day: "Day 1", title: "Ol Pejeta Conservancy & Chimpanzee Sanctuary", desc: "Behind the scenes look at the world’s last remaining Northern White Rhinos." },
          { day: "Day 2", title: "Anti-Poaching Canine Unit Demonstration", desc: "Interactive session with ranger handlers and conservation technology." },
          { day: "Day 3", title: "Community Conservancy & Grazing Management", desc: "Visit Maasai group ranches to understand coexistence with lions." },
          { day: "Day 4", title: "Ecological Workshop & Return", desc: "Interactive debrief and student certifications before return." }
        ],
        includes: ["Conservation specialist lectures", "Group lodge/dorm accommodations", "Safari coach transport", "Park study permits"]
      }
    };

    document.addEventListener('DOMContentLoaded', function() {
      // Set year
      document.getElementById('currentYear').textContent = new Date().getFullYear();

      // Navbar scroll effect
      window.addEventListener('scroll', function() {
        const navbar = document.getElementById('mainNavbar');
        if (window.scrollY > 40) {
          navbar.classList.add('scrolled');
        } else {
          navbar.classList.remove('scrolled');
        }
      });

      // Filter packages
      const filterButtons = document.querySelectorAll('#packageFilterButtons .filter-btn');
      const packageItems = document.querySelectorAll('.package-item');

      filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
          filterButtons.forEach(b => b.classList.remove('active'));
          this.classList.add('active');

          const filterValue = this.getAttribute('data-filter');

          packageItems.forEach(item => {
            const category = item.getAttribute('data-category');
            if (filterValue === 'all' || category === filterValue) {
              item.style.display = 'block';
            } else {
              item.style.display = 'none';
            }
          });
        });
      });

      // Initial calculation
      calculateSafariCost();
    });

    function calculateSafariCost() {
      const destSelect = document.getElementById('calcDestination');
      const vehicleSelect = document.getElementById('calcVehicle');
      const tierSelect = document.getElementById('calcTier');
      const days = parseInt(document.getElementById('calcDays').value);
      const adults = parseInt(document.getElementById('calcAdults').value);

      const destDailyParkFee = parseFloat(destSelect.options[destSelect.selectedIndex].getAttribute('data-daily'));
      const vehicleCostPerDay = parseFloat(vehicleSelect.options[vehicleSelect.selectedIndex].getAttribute('data-perday'));
      const roomCostPerPersonPerDay = parseFloat(tierSelect.options[tierSelect.selectedIndex].getAttribute('data-room'));

      const hasBalloon = document.getElementById('addonBalloon').checked;
      const hasMaasai = document.getElementById('addonMaasai').checked;

      // Calculation formula:
      // Total = (Vehicle daily * days) + (Parks & Guides per adult * days) + (Room per adult * (days - 1)) + Add-ons
      const totalVehicleCost = vehicleCostPerDay * days;
      const totalParkAndGuide = destDailyParkFee * adults * days;
      const totalRooms = roomCostPerPersonPerDay * adults * Math.max(1, days - 1);
      
      let addonCost = 0;
      if (hasBalloon) addonCost += (450 * adults);
      if (hasMaasai) addonCost += (30 * adults);

      const grandTotal = Math.round(totalVehicleCost + totalParkAndGuide + totalRooms + addonCost);
      const perPerson = Math.round(grandTotal / adults);

      document.getElementById('totalPriceDisplay').textContent = '$' + grandTotal.toLocaleString();
      document.getElementById('pricePerPersonDisplay').textContent = `Approx. $${perPerson.toLocaleString()} per person (for ${adults} guests)`;
    }

    // Direct WhatsApp send with calculated quote
    function sendWhatsAppQuote() {
      const dest = document.getElementById('calcDestination').selectedOptions[0].text;
      const vehicle = document.getElementById('calcVehicle').selectedOptions[0].text;
      const days = document.getElementById('calcDays').value;
      const adults = document.getElementById('calcAdults').value;
      const total = document.getElementById('totalPriceDisplay').textContent;

      const message = `Hello Planet Wanders Tours! I would like to book a safari based on your website calculator:%0A- Destination: ${dest}%0A- Vehicle: ${vehicle}%0A- Duration: ${days} Days%0A- Adults: ${adults}%0A- Estimated Total: ${total}%0APlease confirm availability for my dates!`;
      
      window.open(`https://wa.me/254712345678?text=${message}`, '_blank');
    }

    // Quick search bar submission
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

    // Itinerary Modal Opener
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
          <span class="text-gold fw-bold fs-5">${data.price}</span>
        </div>
        <p class="text-secondary">${data.overview}</p>
        <h5 class="fw-bold text-olive mt-4 mb-3 font-serif">Daily Itinerary Schedule</h5>
        ${scheduleHtml}
        <h5 class="fw-bold text-olive mt-4 mb-2 font-serif">Package Inclusions</h5>
        <ul class="list-unstyled mb-0">
          ${includesHtml}
        </ul>
      `;

      bookBtn.onclick = function() {
        const myModalEl = document.getElementById('itineraryModal');
        const modal = bootstrap.Modal.getInstance(myModalEl);
        if (modal) modal.hide();
        prefillBooking(data.title);
      };

      const myModal = new bootstrap.Modal(document.getElementById('itineraryModal'));
      myModal.show();
    }

    // Prefill booking button
    function prefillBooking(packageName) {
      document.getElementById('contact').scrollIntoView({ behavior: 'smooth' });
      const contactMsg = document.getElementById('contactMessage');
      contactMsg.value = `Hello, I am interested in booking the "${packageName}". Please provide available departure dates and the confirmation procedure.`;
      contactMsg.focus();
    }

    // Contact Form submission
    function handleContactSubmit(e) {
      e.preventDefault();
      const name = document.getElementById('contactName').value;
      const dest = document.getElementById('contactDestination').value;

      showStatusModal(
        'Inquiry Submitted!',
        `Thank you ${name}! Your safari inquiry for ${dest} has been dispatched to our booking agents in Narok & Nairobi. We will contact you via WhatsApp / email shortly.`
      );

      document.getElementById('contactForm').reset();
    }

    // Helper modal replacing alert()
    function showStatusModal(title, body) {
      document.getElementById('statusModalTitle').textContent = title;
      document.getElementById('statusModalBody').textContent = body;
      const modal = new bootstrap.Modal(document.getElementById('statusModal'));
      modal.show();
    }
  </script>
</body>
</html>