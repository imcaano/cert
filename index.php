<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CertChain - Blockchain Exam Certification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(120deg, #f8fafc 60%, #e3e9ff 100%);
        }
        .glass-navbar {
            background: rgba(255,255,255,0.85) !important;
            box-shadow: 0 4px 24px rgba(44,62,80,0.10);
            backdrop-filter: blur(8px);
            border-radius: 0 0 24px 24px;
            transition: background 0.3s;
        }
        .navbar-brand span {
            color: #2563eb;
        }
        .navbar-logo {
            width: 38px;
            margin-right: 10px;
            vertical-align: middle;
            filter: drop-shadow(0 2px 6px #2563eb33);
        }
        .navbar .nav-link {
            color: #222 !important;
            font-weight: 500;
            margin-left: 10px;
            position: relative;
            transition: color 0.2s;
        }
        .navbar .nav-link.active, .navbar .nav-link:focus {
            color: #2563eb !important;
        }
        .navbar .nav-link::after {
            content: '';
            display: block;
            width: 0;
            height: 2px;
            background: #2563eb;
            transition: width 0.3s;
            position: absolute;
            left: 0; bottom: 0;
        }
        .navbar .nav-link:hover::after {
            width: 100%;
        }
        .navbar .nav-link:hover {
            color: #2563eb !important;
        }
        .login-btn {
            background: linear-gradient(90deg, #2563eb 60%, #6366f1 100%);
            color: #fff !important;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 28px;
            margin-left: 18px;
            box-shadow: 0 2px 8px #2563eb22;
            transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
        }
        .login-btn:hover {
            background: linear-gradient(90deg, #6366f1 60%, #2563eb 100%);
            color: #fff !important;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 6px 18px #6366f144;
        }
        .hero {
            background: linear-gradient(120deg, #2563eb 60%, #6366f1 100%);
            color: #fff;
            padding: 110px 0 80px 0;
            border-radius: 0 0 60px 60px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.08);
            animation: fadeInDown 1s;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #2563eb;
            transition: color 0.2s, transform 0.2s;
        }
        .card:hover .feature-icon {
            color: #6366f1;
            transform: scale(1.15) rotate(-8deg);
        }
        .how-section {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 4px 24px #2563eb11;
            padding: 48px 32px;
            margin-top: 40px;
            animation: fadeInUp 1.2s;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .how-step {
            display: flex;
            align-items: center;
            margin-bottom: 32px;
        }
        .how-step:last-child { margin-bottom: 0; }
        .how-icon {
            font-size: 2.2rem;
            color: #6366f1;
            margin-right: 18px;
            flex-shrink: 0;
            transition: color 0.2s, transform 0.2s;
        }
        .how-step:hover .how-icon {
            color: #2563eb;
            transform: scale(1.18) rotate(8deg);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg glass-navbar sticky-top py-2">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
      <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Logo" class="navbar-logo">
      <span>CertChain</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php"><i class="bi bi-house-door me-1"></i>Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="frontend/verify.php"><i class="bi bi-patch-check me-1"></i>Verify Certificate</a>
        </li>
        <li class="nav-item">
          <a class="login-btn nav-link" href="frontend/login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<section class="hero text-center">
  <div class="container">
    <h1 class="display-3 fw-bold mb-3">Blockchain-Based Online Exam Certification</h1>
    <p class="lead mb-4">Secure, verifiable, and tamper-proof certification for online exams using blockchain technology.<br>Empowering students, admins, and employers with trust and transparency.</p>
    <a href="frontend/login.php" class="btn btn-warning btn-lg px-4 me-2 cta-btn">Login</a>
    <a href="frontend/verify.php" class="btn btn-outline-light btn-lg px-4 cta-btn">Verify Certificate</a>
  </div>
</section>

<section class="container py-5">
  <div class="row text-center">
    <div class="col-md-4 mb-4">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <div class="feature-icon mb-3"><i class="bi bi-person-badge"></i></div>
          <h5 class="card-title">For Students</h5>
          <p class="card-text">Take exams and download blockchain-verified certificates. Your achievements are secure and easily verifiable anywhere.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <div class="feature-icon mb-3"><i class="bi bi-shield-lock"></i></div>
          <h5 class="card-title">For Admins</h5>
          <p class="card-text">Manage students, create exams, issue certificates, and store certificate hashes on the blockchain for maximum security and transparency.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <div class="feature-icon mb-3"><i class="bi bi-search"></i></div>
          <h5 class="card-title">For Employers</h5>
          <p class="card-text">Easily verify the authenticity of certificates using our public verification tool powered by blockchain technology.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="container how-section my-5">
  <h2 class="fw-bold text-center mb-4"><i class="bi bi-lightbulb me-2"></i>How It Works</h2>
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="how-step"><span class="how-icon"><i class="bi bi-person-plus"></i></span> <div><b>Admin registers students</b> and creates exams in the secure dashboard.</div></div>
      <div class="how-step"><span class="how-icon"><i class="bi bi-pencil-square"></i></span> <div><b>Students log in</b> and take online exams with a timer and MCQ questions.</div></div>
      <div class="how-step"><span class="how-icon"><i class="bi bi-award"></i></span> <div><b>Results are published</b> and certificates are generated with a unique blockchain hash.</div></div>
      <div class="how-step"><span class="how-icon"><i class="bi bi-patch-check"></i></span> <div><b>Anyone can verify</b> a certificate's authenticity using the public verification tool.</div></div>
    </div>
  </div>
</section>

<footer class="bg-primary text-white text-center py-4 mt-5">
  &copy; 2024 <span class="fw-bold">CertChain</span>. All rights reserved.
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 