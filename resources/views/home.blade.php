<!DOCTYPE html>
<html lang="en">

<head>
  <script>
    // Immediate theme application to prevent flash of wrong theme
    (function() {
      const savedTheme = localStorage.getItem('theme');
      let theme = 'light'; // default
      
      if (savedTheme) {
        theme = savedTheme;
      } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        theme = 'dark';
      }
      
      document.documentElement.setAttribute('data-theme', theme);
      document.documentElement.className = theme + '-theme';
    })();
  </script>

  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>LincHostel | Where Comfort Meets Community, Affordable, Inclusive.</title>
  <meta content="Student accommodation with comfort and community" name="description">
  <meta content="hostel, student accommodation, lincoln, affordable housing" name="keywords">

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('assets/css/home.css') }}" rel="stylesheet">
</head>

<body>
<!-- ======= Top Bar ======= -->
<section id="topbar" class="d-flex align-items-center">
  <div class="container d-flex justify-content-center justify-content-md-between">
    <div class="contact-info d-flex align-items-center">
      <i class="bi bi-envelope d-flex align-items-center">
        <a href="mailto:info@lincoln.edu.ng">info@lincoln.edu.ng</a>
      </i>
      <i class="bi bi-phone d-flex align-items-center ms-4">
        <span>+234 803 834 1496</span>
      </i>
    </div>
  </div>
</section>

<!-- ======= Header ======= -->
<header id="header" class="d-flex align-items-center">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="logo-container">
      <a href="/">
        <img src="{{ asset('assets/img/lincoln-logo.png') }}" alt="Lincoln University Logo">
      </a>
      <h1 class="logo-text">
        <a href="/" style="text-decoration: none; color: inherit;"><span></span></a>
      </h1>
    </div>

    <nav id="navbar" class="navbar">
      <ul>
        <li><a class="nav-link scrollto active" href="/">Home</a></li>
        <li><a class="nav-link scrollto" href="#about">About</a></li>
        <li><a class="nav-link scrollto" href="#services">Features</a></li>
        <li><a class="nav-link scrollto" href="#faq">Faqs</a></li>
        <li><a class="nav-link scrollto" href="#contact">Contact</a></li>
        <li><a class="nav-link scrollto" href="{{ route('check.application') }}" target="_blank">Check Application</a></li>
        <li><a class="nav-link scrollto" href="{{ route('hostel.apply') }}" target="_blank">Apply Now</a></li>
        
        <!-- Mobile-only items -->
        <li class="mobile-only">
          <div class="dropdown">
    @auth
        <!-- Show logout when user is authenticated -->
        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
        </a>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    @else
        <!-- Show login dropdown when user is not authenticated -->
        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">Login</a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('login') }}">Admins</a></li>
            <li><a class="dropdown-item" href="{{ route('student.login') }}">Students</a></li>
        </ul>
    @endauth
</div>
        </li>
        <li class="mobile-only">
          <div class="mobile-menu-item">
            <span>Theme</span>
            <button class="mobile-theme-toggle" id="mobileThemeToggle" type="button" aria-label="Toggle theme">
              <i class="fas fa-sun" id="mobileThemeIcon"></i>
            </button>
          </div>
        </li>
      </ul>

      <div class="navbar-actions d-flex align-items-center gap-3">
        <div class="dropdown">
          <a href="#" class="dropdown-toggle py-1 px-3" data-bs-toggle="dropdown">Login</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('login') }}" target="_blank">Admins</a></li>
            <li><a class="dropdown-item" href="{{ route('student.login') }}" target="_blank"  >Students</a></li>
          </ul>
        </div>
          
        <!-- Theme Toggle Button -->
        <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle theme">
          <i class="fas fa-sun" id="themeIcon"></i>
        </button>
      </div>

      <i class="bi bi-list mobile-nav-toggle"></i>
    </nav>
  </div>
</header>
<!-- End Header -->

<!-- ======= Hero Section ======= -->
<section id="hero" class="d-flex align-items-center">
  <div class="container" data-aos="zoom-out" data-aos-delay="100">
    <h1>Welcome to <span>LincHostel.</span></h1>
    <h2>Where Comfort Meets Community, Affordable, Inclusive.</h2>
    <div class="d-flex">
      <a href="{{ route('hostel.apply') }}" class="btn-get-started scrollto">Book Now</a>
    </div>
  </div>
</section>
<!-- End Hero -->

<main id="main">
  <!-- ======= Featured Services Section ======= -->
  <section id="featured-services" class="featured-services">
    <div class="container" data-aos="fade-up">
      <div class="row">
        <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
          <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
            <div class="icon"><i class="bx bxl-dribbble"></i></div>
            <h4 class="title"><a href="">Study-Friendly Zones</a></h4>
            <p class="description">Hit the books in peace! Dedicated study corners help you focus while excelling in your studies.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
          <div class="icon-box" data-aos="fade-up" data-aos-delay="200">
            <div class="icon"><i class="bx bx-file"></i></div>
            <h4 class="title"><a href="">Budget-Friendly Rates</a></h4>
            <p class="description">We understand student budgets. Enjoy comfortable living without breaking the bank</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
          <div class="icon-box" data-aos="fade-up" data-aos-delay="300">
            <div class="icon"><i class="bx bx-shield"></i></div>
            <h4 class="title"><a href="">Top-Notch Security</a></h4>
            <p class="description">We prioritize your safety with 24/7 surveillance, secure access points, and well-trained security personnel.</p>
          </div>
        </div>          

        <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
          <div class="icon-box" data-aos="fade-up" data-aos-delay="400">
            <div class="icon"><i class="bx bx-world"></i></div>
            <h4 class="title"><a href="">24/7 Access</a></h4>
            <p class="description">Our team is here for you. From local insights to assistance, we're your home away from home.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- End Featured Services Section -->

  <!-- ======= About Section ======= -->
  <section id="about" class="about section-bg">
    <div class="container" data-aos="fade-up">
      <div class="section-title">
        <h2>About</h2>
        <h3>Find Out More <span>About LincHostel</span></h3>
        <p>Your ultimate student haven! We're more than just a place to rest your head – we're a vibrant community designed to make your student years unforgettable.</p>
      </div>

      <div class="row">
        <div class="col-lg-6" data-aos="fade-right" data-aos-delay="100">
          <img src="assets/img/about.jpg" class="img-fluid" alt="About LincHostel">
        </div>
        <div class="col-lg-6 pt-4 pt-lg-0 content d-flex flex-column justify-content-center" data-aos="fade-up" data-aos-delay="100">
          <h3>What Sets Us Apart.</h3>
          <p class="fst-italic">
            Founded by students who understand the demands of student life, the LincHostel was born from a passion to provide a home away from home. We've experienced the challenges of balancing academics and social life, and we're here to make that journey smoother for you.
          </p>
          <ul>
            <li>
              <i class="bx bx-store-alt"></i>
              <div>
                <h5>Community:</h5>
                <p>We're not just a hostel; we're a diverse community of students from all disciplines. Forge lasting friendships, collaborate on projects, and grow together.</p>
              </div>
            </li>
            <li>
              <i class="bx bx-shape-polygon"></i>
              <div>
                <h5>Your Support System: </h5>
                <p>Our friendly staff is always here to assist you. Whether you need local recommendations or academic help, we're your extended family.</p>
              </div>
            </li>
          </ul>
          <p>
            We invite you to be part of our student-focused haven. Whether you're a freshman or a senior, the LincHostel is where you'll find comfort, connection, and countless stories to share.
          </p>
        </div>
      </div>
    </div>
  </section>
  <!-- End About Section -->

  <!-- ======= Counts Section ======= -->
  <section id="counts" class="counts">
    <div class="container" data-aos="fade-up">
      <div class="row">
<!-- Staff Count -->
<div class="col-lg-3 col-md-6">
    <div class="count-box">
        <i class="fas fa-id-card-alt"></i>
        <span data-purecounter-start="0" data-purecounter-end="{{ \App\Models\Staff::count() }}" data-purecounter-duration="1" class="purecounter"></span>
        <p>Staff Supervisors</p>
    </div>
</div>

<!-- Student Count -->
<div class="col-lg-3 col-md-6 mt-5 mt-md-0">
    <div class="count-box">
        <i class="fas fa-user-graduate"></i>
        <span data-purecounter-start="0" data-purecounter-end="{{ \App\Models\Student::count() }}" data-purecounter-duration="1" class="purecounter"></span>
        <p>Students</p>
    </div>
</div>

<!-- Total Rooms Count -->
<div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
    <div class="count-box">
        <i class="fas fa-bed"></i>
        <span data-purecounter-start="0" data-purecounter-end="{{ \App\Models\Room::count() }}" data-purecounter-duration="1" class="purecounter"></span>
        <p>Total Rooms</p>
    </div>
</div>

<!-- Available Rooms Count (if you have a status field) -->
<div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
    <div class="count-box">
        <i class="fas fa-door-open"></i>
        <span data-purecounter-start="0" data-purecounter-end="{{ \App\Models\Room::where('status', 'available')->count() }}" data-purecounter-duration="1" class="purecounter"></span>
        <p>Available Rooms</p>
    </div>
</div>
      </div>
    </div>
  </section>
  <!-- End Counts Section -->

  <!-- ======= Features Section ======= -->
  <section id="services" class="services">
    <div class="container" data-aos="fade-up">
      <div class="section-title">
        <h2>Features</h2>
        <h3>Check out our <span>Features</span></h3>
        <p>Whether you're here for a year or a semester, we believe in catering to your comfort and peace of mind.</p>
      </div>

      <div class="row">
        <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
          <div class="icon-box">
            <div class="icon"><i class="bx bxl-dribbble"></i></div>
            <h4><a>Friendly Management</a></h4>
            <p>Our management team is always available to attend to your concerns and ensure your stay is smooth and comfortable.</p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-md-0" data-aos="zoom-in" data-aos-delay="200">
          <div class="icon-box">
            <div class="icon"><i class="bx bx-home"></i></div>
            <h4><a>Clean Rooms</a></h4>
            <p>Each room is well-maintained with comfortable beds and basic furnishings to make your stay relaxing and enjoyable.</p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-lg-0" data-aos="zoom-in" data-aos-delay="300">
          <div class="icon-box">
            <div class="icon"><i class="bx bx-lock"></i></div>
            <h4><a>24/7 Security</a></h4>
            <p>We prioritize your safety with round-the-clock security personnel and secured gates for your peace of mind.</p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4" data-aos="zoom-in" data-aos-delay="100">
          <div class="icon-box">
            <div class="icon"><i class="bx bx-droplet"></i></div>
            <h4><a>Clean Water Supply</a></h4>
            <p>Enjoy access to clean water, both for drinking and daily use — no need to worry about germs.</p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4" data-aos="zoom-in" data-aos-delay="200">
          <div class="icon-box">
            <div class="icon"><i class="bx bx-basket"></i></div>
            <h4><a>Nearby Shops & Essentials</a></h4>
            <p>Quick access to provisions, snacks, and daily essentials within walking distance from the hostel.</p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4" data-aos="zoom-in" data-aos-delay="300">
          <div class="icon-box">
            <div class="icon"><i class="bx bx-wallet"></i></div>
            <h4><a>Affordable Rates</a></h4>
            <p>Enjoy comfortable living at prices that won't break the bank — perfect for students managing their budgets.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- End Features Section -->

  <!-- ======= Testimonials Section ======= -->
  <section id="testimonials" class="testimonials">
    <div class="container" data-aos="zoom-in">
      <div class="testimonials-slider swiper" data-aos="fade-up" data-aos-delay="100">
        <div class="swiper-wrapper">
          <div class="swiper-slide">
            <div class="testimonial-item">
              <div class="testimonial-image">
                <img src="assets/img/testimonials/debbietestimonial.jpeg" class="testimonial-img" alt="Deborah Chukwuokike">
              </div>
              <h3 class="testimonial-name">Deborah Chukuokike .C.</h3>
              <h4 class="testimonial-role">2nd Year</h4>
              <p class="testimonial-quote">
                <i class="bx bxs-quote-alt-left quote-icon-left" aria-hidden="true"></i>
                Staying at LincHostel made my semester so much easier. The rooms are neat, and the security gives me peace of mind. I love how it feels like a small family here.
                <i class="bx bxs-quote-alt-right quote-icon-right" aria-hidden="true"></i>
              </p>
            </div>
          </div>

          <div class="swiper-slide">
            <div class="testimonial-item">
              <div class="testimonial-image">
                <img src="assets/img/testimonials/Adatestimonial.jpeg" class="testimonial-img" alt="Gift Ibemere">
              </div>
              <h3 class="testimonial-name">Ibemere Gift</h3>
              <h4 class="testimonial-role">2nd Year</h4>
              <p class="testimonial-quote">
                <i class="bx bxs-quote-alt-left quote-icon-left" aria-hidden="true"></i>
                I really appreciate how organized the hostel management is. Booking was straightforward and the staff are always approachable. The curfew policy also helps maintain order.
                <i class="bx bxs-quote-alt-right quote-icon-right" aria-hidden="true"></i>
              </p>
            </div>
          </div>

          <div class="swiper-slide">
            <div class="testimonial-item">
              <div class="testimonial-image">
                <img src="assets/img/testimonials/marytestimonial.jpeg" class="testimonial-img" alt="Akinyode Mary">
              </div>
              <h3 class="testimonial-name">Akinyode Mary</h3>
              <h4 class="testimonial-role">2nd Year</h4>
              <p class="testimonial-quote">
                <i class="bx bxs-quote-alt-left quote-icon-left" aria-hidden="true"></i>
                LincHostel is honestly one of the best hostels I've stayed in. Clean rooms, reliable power supply, and proper security. Plus, the location is so close to school. I'll definitely stay here again next semester.
                <i class="bx bxs-quote-alt-right quote-icon-right" aria-hidden="true"></i>
              </p>
            </div>
          </div>

          <div class="swiper-slide">
            <div class="testimonial-item">
              <div class="testimonial-image">
                <img src="assets/img/testimonials/wisdomtestmonial.jpeg" class="testimonial-img" alt="Wisdom Daniel">
              </div>
              <h3 class="testimonial-name">Wisdom Daniel</h3>
              <h4 class="testimonial-role">2nd Year</h4>
              <p class="testimonial-quote">
                <i class="bx bxs-quote-alt-left quote-icon-left" aria-hidden="true"></i>
                The environment is calm and secure. I love the steady water supply and good network. It really makes academic work easier and stress-free.
                <i class="bx bxs-quote-alt-right quote-icon-right" aria-hidden="true"></i>
              </p>
            </div>
          </div>

          <div class="swiper-slide">
            <div class="testimonial-item">
              <div class="testimonial-image">
                <img src="assets/img/testimonials/Goldtestimonial.jpeg" class="testimonial-img" alt="Gold Omojo">
              </div>
              <h3 class="testimonial-name">Gold Omojo</h3>
              <h4 class="testimonial-role">1st Year</h4>
              <p class="testimonial-quote">
                <i class="bx bxs-quote-alt-left quote-icon-left" aria-hidden="true"></i>
                What I love most about LincHostel is how safe and homely it feels. The management actually listens to students and the facilities are well maintained. It's worth every naira.
                <i class="bx bxs-quote-alt-right quote-icon-right" aria-hidden="true"></i>
              </p>
            </div>
          </div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </section>
  <!-- End Testimonials Section -->

  <!-- ======= Frequently Asked Questions Section ======= -->
  <section id="faq" class="faq section-bg">
    <div class="container" data-aos="fade-up">
      <div class="section-title">
        <h2>F.A.Q</h2>
        <h3>Frequently Asked <span>Questions</span></h3>
        <p>Everything You Need to Know About LincHostel</p>
      </div>

      <div class="row justify-content-center">
        <div class="col-xl-10">
          <ul class="faq-list">
            <li>
              <div data-bs-toggle="collapse" class="collapsed question" href="#faq1">
                1. How much is the hostel fee per semester?
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="faq1" class="collapse" data-bs-parent=".faq-list">
                <p>
                  Hostel fees are ₦85,000 per semester and ₦250,000 per year, depending on the hostel you pick. Contact the hostel office for the latest rates.
                </p>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#faq2" class="collapsed question">
                2. How do I book a room?
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="faq2" class="collapse" data-bs-parent=".faq-list">
                <p>
                  Bookings can be made through the LincHostel website or directly at the hostel admin office on campus. Full payment confirms your booking and room allocation.
                </p>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#faq3" class="collapsed question">
                3. Is there a refund if I leave before the semester ends?
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="faq3" class="collapse" data-bs-parent=".faq-list">
                <p>
                  No — LincHostel operates a strict no-refund policy once payment is made and a room is allocated.
                </p>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#faq4" class="collapsed question">
                4. What time is the hostel curfew?
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="faq4" class="collapse" data-bs-parent=".faq-list">
                <p>
                  The hostel curfew is 10:00 PM daily. Students must be within the hostel premises before this time unless prior permission is granted by the warden.
                </p>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#faq5" class="collapsed question">
                5. How secure is the hostel environment?
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="faq5" class="collapse" data-bs-parent=".faq-list">
                <p>
                  LincHostel prioritizes student safety with 24/7 security personnel and strict visitor management protocols.
                </p>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#faq6" class="collapsed question">
                6. What amenities are provided in the hostel?
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="faq6" class="collapse" data-bs-parent=".faq-list">
                <p>
                  Our hostel provides clean water, reliable electricity, study areas, common rooms, and secure storage facilities for all residents.
                </p>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#faq7" class="collapsed question">
                7. Are visitors allowed inside the hostel?
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="faq7" class="collapse" data-bs-parent=".faq-list">
                <p>
                  Yes — visitors are allowed inside the hostel during designated hours and must sign in at the reception area.
                </p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
  <!-- End Frequently Asked Questions Section -->

  <!-- ======= Contact Section ======= -->
  <section id="contact" class="contact">
    <div class="container" data-aos="fade-up">
      <div class="section-title">
        <h2>Contact</h2>
        <h3><span>Contact</span> Us</h3>
        <p>If You have any query, suggestion or complaint please leave a message</p>
      </div>

      <div class="row" data-aos="fade-up" data-aos-delay="100">
        <div class="col-lg-6">
          <div class="info-box mb-4">
            <i class="bx bx-map"></i>
            <h3>Our Address</h3>
            <p>Lincoln University Malaysia admin block,<br> Nassarawa State University, Keffi.<br> Lincoln University Malaysia, Kumo Gombe State.</p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="info-box mb-4">
            <i class="bx bx-envelope"></i>
            <h3>Email Us</h3>
            <p>info@lincoln.edu.ng</p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="info-box mb-4">
            <i class="bx bx-phone-call"></i>
            <h3>Call Us</h3>
            <p>+234 803 834 1496</p>
          </div>
        </div>
      </div>

      <div class="row" data-aos="fade-up" data-aos-delay="100">
        <div class="col-lg-6">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3941.4938793952797!2d7.555965375090309!3d8.926553490584036!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x104e0f9db875403b%3A0x20bb27310124b188!2sLincoln%20college%20of%20science%20management%20and%20technology!5e0!3m2!1sen!2sng!4v1745935144894!5m2!1sen!2sng" width="100%" height="400px" style="border-radius: 30px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

        <div class="col-lg-6">
          <form action="{{ route('contact.send') }}" method="POST" role="form" class="php-email-form">
            @csrf  
            <div class="row">
              <div class="col form-group">
                <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required>
              </div>
              <div class="col form-group">
                <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
              </div>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required>
            </div>
            <div class="form-group">
              <textarea class="form-control" name="message" rows="5" placeholder="Message" required></textarea>
            </div>
            <div class="my-3">
              <div class="loading">Loading</div>
              <div class="error-message"></div>
              <div class="sent-message">Your message has been sent. Thank you!</div>
            </div>
            <div class="text-center"><button type="submit">Send Message</button></div>
          </form>
        </div>
      </div>
    </div>
  </section>
  <!-- End Contact Section -->
</main>
<!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer">
  <!-- ======= Terms and Conditions Section ======= -->
  <section id="terms" class="faq section-bg">
    <div class="container" data-aos="fade-up">
      <div class="section-title">
        <h2>Terms</h2>
        <h3><span>Terms</span> and <span>Conditions</span></h3>
        <p>Guidelines and Regulations for Hostel Residents</p>
      </div>

      <div class="row justify-content-center">
        <div class="col-xl-10">
          <ul class="faq-list">
            <li>
              <div data-bs-toggle="collapse" href="#term1" class="collapsed question">
                1. General Hostel Regulations
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="term1" class="collapse" data-bs-parent=".faq-list">
                <ul>
                  <li>These regulations ensure the safety, comfort, and well-being of all residents. Every student must read, understand, and adhere to these guidelines.</li>
                  <li>Violations attract disciplinary actions — verbal warnings, suspension, expulsion, or further actions as deemed necessary by Student Affairs.</li>
                </ul>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#term2" class="collapsed question">
                2. Visitation Policy and Curfew
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="term2" class="collapse" data-bs-parent=".faq-list">
                <ul>
                  <li>Visitors are allowed strictly between <strong>2:00 PM and 6:00 PM</strong>.</li>
                  <li>No overnight stays for non-resident students.</li>
                  <li>Visitors must register at the reception/security desk.</li>
                  <li>Overnight guests require written permission from Student Affairs.</li>
                  <li>Residents are responsible for their visitors' conduct.</li>
                  <li>Curfew is from <strong>9:00 PM to 7:00 AM</strong> — loud activities are forbidden.</li>
                  <li>Hostel gates are locked from <strong>9:00 PM to 6:00 AM</strong>. Defaulters face disciplinary actions.</li>
                </ul>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#term3" class="collapsed question">
                3. Prohibited Items and Conduct
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="term3" class="collapse" data-bs-parent=".faq-list">
                <ul>
                  <li>Prohibited: cultism, gang activities, smoking, gambling, alcohol, charms, illegal or immoral activity.</li>
                  <li>Possession of drugs, weapons, flammables, cigarettes, marijuana, or any illegal substance is a grave offense.</li>
                  <li>Violators face immediate disciplinary and legal action.</li>
                </ul>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#term4" class="collapsed question">
                4. Respect for Hostel Staff and Authority
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="term4" class="collapse" data-bs-parent=".faq-list">
                <ul>
                  <li>All residents must treat hostel staff with courtesy and respect.</li>
                  <li>Disrespect or refusal to comply attracts disciplinary action.</li>
                </ul>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#term5" class="collapsed question">
                5. Cleanliness and Maintenance
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="term5" class="collapse" data-bs-parent=".faq-list">
                <ul>
                  <li>Residents are responsible for cleaning their rooms and shared areas.</li>
                  <li>Report damages immediately. Responsible students will bear repair costs.</li>
                  <li>Littering and improper waste disposal are prohibited and punishable.</li>
                </ul>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#term6" class="collapsed question">
                6. Kitchen and Cooking Guidelines
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="term6" class="collapse" data-bs-parent=".faq-list">
                <ul>
                  <li>Clean up communal kitchens after use.</li>
                  <li>Properly store and label food.</li>
                  <li>Unapproved cooking appliances are forbidden in rooms.</li>
                </ul>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#term7" class="collapsed question">
                7. Safety, Security, and Personal Belongings
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="term7" class="collapse" data-bs-parent=".faq-list">
                <ul>
                  <li>Comply with safety regulations and cooperate during security checks.</li>
                  <li>Hostel management is not liable for lost or stolen items. Secure your belongings.</li>
                </ul>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#term8" class="collapsed question">
                8. Smoking and Alcohol Policy
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="term8" class="collapse" data-bs-parent=".faq-list">
                <ul>
                  <li>Smoking indoors is prohibited.</li>
                  <li>Alcohol consumption is restricted to designated areas only.</li>
                  <li>Unruly behavior caused by intoxication will attract punishment.</li>
                </ul>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#term9" class="collapsed question">
                9. Pets and Animals
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="term9" class="collapse" data-bs-parent=".faq-list">
                <ul>
                  <li>No pets or animals are allowed on hostel premises.</li>
                </ul>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#term10" class="collapsed question">
                10. Termination of Stay
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="term10" class="collapse" data-bs-parent=".faq-list">
                <ul>
                  <li>Management reserves the right to terminate a student's residency for breaches of regulations or disruptive behavior.</li>
                  <li>Decisions are final and officially communicated to the student and appropriate authorities.</li>
                </ul>
              </div>
            </li>

            <li>
              <div data-bs-toggle="collapse" href="#term11" class="collapsed question">
                11. Approved Personal Items
                <i class="bi bi-chevron-down icon-show"></i><i class="bi bi-chevron-up icon-close"></i>
              </div>
              <div id="term11" class="collapse" data-bs-parent=".faq-list">
                <ul>
                  <li>Residents are strictly required to provide and possess only the specified personal items listed below. Any additional or unauthorized items may be confiscated, and defaulters may face disciplinary action.</li>
                  <li><strong>Approved Items:</strong></li>
                  <ul>
                    <li>6 by 7 mattress for hostel bunk (1 pcs)</li>
                    <li>Plastic bucket (maximum 3 pcs)</li>
                    <li>Mini rechargeable fan (1 pcs)</li>
                    <li>Mopping stick (1 pcs)</li>
                    <li>Gas cylinder burner NOT exceeding 6KG in size (1 pcs)</li>
                    <li>Extension wire/socket box (1 pcs)</li>
                  </ul>
                  <li>Residents must ensure all items comply with hostel safety standards. Management reserves the right to inspect personal belongings at any time.</li>
                </ul>
              </div>
            </li>

          </ul>
        </div>
      </div>
    </div>
  </section>
  <!-- End Terms and Conditions Section -->

  <div class="footer-top">
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-6 footer-contact">
        <h3><a href="/" style="text-decoration: none; color: inherit;">LincHostel<span>.</span></a></h3>
        <p>Lincoln University Malaysia admin block,<br> Nassarawa State University, Keffi.<br> Lincoln University Malaysia, Kumo Gombe State.</p>
          <p>
            <!-- Along jikwoyi-karshi road Azhata,<br>
            Kurudu,<br>
            Federal Capital Territory.<br><br> -->
            <strong style="color: red;">Phone:</strong> +234 803 834 1496<br>
            <strong style="color: red;">Email:</strong> info@lincoln.edu.ng<br>
          </p>
        </div>

        <div class="col-lg-3 col-md-6 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><i class="bx bx-chevron-right"></i> <a href="#">Home</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#about">About us</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#services">Features</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#contact">Contact</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#footer">Terms and Conditions</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="{{ route('hostel.apply') }}" target="_blank">Apply For LincHostel</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6 footer-links">
          <h4>Our Features</h4>
          <ul>
            <li><i class="bx bx-chevron-right"></i> <a href="#services">Friendly Management</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#services">Clean Rooms</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#services">24/7 Security</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#services">Clean Water Supply</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#services">Nearby Shops & Essentials</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="#services">Affordable Rates</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6 footer-links">
          <h4>Our Social Networks</h4>
          <p>You can follow us through our social links too</p>
          <div class="social-links mt-3">
            <a href="https://college.lincoln.edu.ng" class="website" target="_blank"><i class="bx bx-globe"></i></a>
            <a href="https://facebook.com/lincolnschoolng" class="facebook" target="_blank"><i class="bx bxl-facebook"></i></a>
            <a href="https://instagram.com/lincoln_college_nigeria" class="instagram" target="_blank"><i class="bx bxl-instagram"></i></a>
            <a href="https://twitter.com/lincoln_nigeria" class="twitter" target="_blank"><i class="bx bxl-twitter"></i></a>
            <a href="https://linkedin.com/school/lincoln-university-college" class="linkedin" target="_blank"><i class="bx bxl-linkedin"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container py-4">
    <div class="copyright">
      &copy; Copyright <strong><span>LincHostel</span></strong> <script>document.write(new Date().getFullYear())</script>. All Rights Reserved.
    </div>
  </div>
</footer>
<!-- End Footer -->

<div id="preloader"></div>
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
<script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
<script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
<script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/waypoints/noframework.waypoints.js') }}"></script>

<!-- Main JS File -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- Theme Toggle JS -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Theme Management
    const themeToggle = document.getElementById('themeToggle');
    const mobileThemeToggle = document.getElementById('mobileThemeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const mobileThemeIcon = document.getElementById('mobileThemeIcon');
    
    // Get current theme (already applied by early script)
    const currentTheme = document.documentElement.getAttribute('data-theme') || 
                        localStorage.getItem('theme') || 
                        (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    
    // Ensure theme is properly set and icons are updated
    setTheme(currentTheme, false); // false = don't save again, just update UI
    
    // Theme toggle event listeners
    if (themeToggle) {
      themeToggle.addEventListener('click', toggleTheme);
    }
    if (mobileThemeToggle) {
      mobileThemeToggle.addEventListener('click', toggleTheme);
    }
    
    function toggleTheme() {
      const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      
      // Add transition class for smooth animation
      document.body.classList.add('theme-switching');
      
      setTimeout(() => {
        setTheme(newTheme, true); // true = save to localStorage
        document.body.classList.remove('theme-switching');
        document.body.classList.add('theme-transition');
        
        setTimeout(() => {
          document.body.classList.remove('theme-transition');
        }, 300);
      }, 50);
    }
    
    function setTheme(theme, saveToStorage = true) {
      // Apply to all possible elements for maximum compatibility
      document.documentElement.setAttribute('data-theme', theme);
      document.body.setAttribute('data-theme', theme);
      document.documentElement.className = theme + '-theme';
      
      // Save to localStorage only if requested
      if (saveToStorage) {
        localStorage.setItem('theme', theme);
      }
      
      // Update icons
      const iconClass = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
      const ariaLabel = theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
      
      if (themeIcon) {
        themeIcon.className = iconClass;
      }
      if (mobileThemeIcon) {
        mobileThemeIcon.className = iconClass;
      }
      if (themeToggle) {
        themeToggle.setAttribute('aria-label', ariaLabel);
      }
      if (mobileThemeToggle) {
        mobileThemeToggle.setAttribute('aria-label', ariaLabel);
      }
    }
    
    // Mobile menu functionality
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    const navbarUl = document.querySelector('#navbar ul');
    
    if (mobileNavToggle && navbarUl) {
      mobileNavToggle.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Toggle the mobile menu
        navbarUl.classList.toggle('navbar-mobile');
        
        // Toggle the hamburger/close icon
        this.classList.toggle('bi-list');
        this.classList.toggle('bi-x');
        
        // Prevent body scroll when menu is open
        if (navbarUl.classList.contains('navbar-mobile')) {
          document.body.style.overflow = 'hidden';
        } else {
          document.body.style.overflow = '';
        }
      });
    }
    
    // Close mobile menu when clicking on a link
    document.querySelectorAll('#navbar a').forEach(link => {
      link.addEventListener('click', () => {
        if (navbarUl && navbarUl.classList.contains('navbar-mobile')) {
          navbarUl.classList.remove('navbar-mobile');
          if (mobileNavToggle) {
            mobileNavToggle.classList.add('bi-list');
            mobileNavToggle.classList.remove('bi-x');
          }
          document.body.style.overflow = '';
        }
      });
    });
    
    // Handle dropdown in mobile menu
    document.querySelectorAll('.navbar .dropdown > a').forEach(dropdownToggle => {
      dropdownToggle.addEventListener('click', function(e) {
        if (navbarUl && navbarUl.classList.contains('navbar-mobile')) {
          e.preventDefault();
          const dropdownMenu = this.nextElementSibling;
          if (dropdownMenu) {
            dropdownMenu.classList.toggle('dropdown-active');
          }
        }
      });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
      if (navbarUl && navbarUl.classList.contains('navbar-mobile')) {
        if (!e.target.closest('#navbar') && !e.target.closest('.mobile-nav-toggle')) {
          navbarUl.classList.remove('navbar-mobile');
          if (mobileNavToggle) {
            mobileNavToggle.classList.add('bi-list');
            mobileNavToggle.classList.remove('bi-x');
          }
          document.body.style.overflow = '';
        }
      }
    });
    
    // Contact form handling
    const emailForm = document.querySelector('.php-email-form');
    if (emailForm) {
      emailForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const loading = emailForm.querySelector('.loading');
        const sentMessage = emailForm.querySelector('.sent-message');
        const errorMessage = emailForm.querySelector('.error-message');
        
        // Reset states
        if (loading) loading.style.display = 'block';
        if (sentMessage) sentMessage.style.display = 'none';
        if (errorMessage) errorMessage.style.display = 'none';
        
        const formData = new FormData(emailForm);
        const csrfToken = document.querySelector('input[name="_token"]');
        
        const headers = {};
        if (csrfToken) {
          headers['X-CSRF-TOKEN'] = csrfToken.value;
        }
        
        fetch(emailForm.action, {
          method: 'POST',
          headers: headers,
          body: formData
        })
        .then(response => {
          if (response.ok) {
            return response.text();
          }
          throw new Error(`HTTP error! status: ${response.status}`);
        })
        .then(data => {
          if (loading) loading.style.display = 'none';
          if (sentMessage) {
            sentMessage.style.display = 'block';
            sentMessage.innerHTML = 'Your message has been sent successfully!';
          }
          emailForm.reset();
          
          // Auto-hide success message after 5 seconds
          setTimeout(() => {
            if (sentMessage) sentMessage.style.display = 'none';
          }, 5000);
        })
        .catch(error => {
          if (loading) loading.style.display = 'none';
          if (errorMessage) {
            errorMessage.style.display = 'block';
            errorMessage.innerHTML = 'Something went wrong. Please try again!';
          }
          console.error('Contact form error:', error);
        });
      });
    }
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#' || href === '') return;
        
        const target = document.querySelector(href);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });
    
    // Back to top button functionality
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
      window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
          backToTop.classList.add('active');
        } else {
          backToTop.classList.remove('active');
        }
      });
      
      backToTop.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });
    }
  });
  
  // Handle page visibility changes
  document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
      // Page became visible, ensure theme is still correct
      const savedTheme = localStorage.getItem('theme');
      if (savedTheme && document.documentElement.getAttribute('data-theme') !== savedTheme) {
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.body.setAttribute('data-theme', savedTheme);
        document.documentElement.className = savedTheme + '-theme';
      }
    }
  });
  
</script>
</body>
</html>