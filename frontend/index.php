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
        .hero {
            background: linear-gradient(120deg, #2563eb 60%, #6366f1 100%);
            color: #fff;
            padding: 110px 0 80px 0;
            border-radius: 0 0 60px 60px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.08);
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #2563eb;
        }
        .navbar-brand span {
            color: #ffc107;
        }
        .cta-btn {
            min-width: 180px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Cert<span>Chain</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="login.php">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="verify.php">Verify Certificate</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<section class="hero text-center">
  <div class="container">
    <h1 class="display-3 fw-bold mb-3">Blockchain-Based Online Exam Certification</h1>
    <p class="lead mb-4">Secure, verifiable, and tamper-proof certification for online exams using blockchain technology.<br>Empowering students, admins, and employers with trust and transparency.</p>
    <a href="login.php" class="btn btn-warning btn-lg px-4 me-2 cta-btn">Login</a>
    <a href="verify.php" class="btn btn-outline-light btn-lg px-4 cta-btn">Verify Certificate</a>
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

<footer class="bg-primary text-white text-center py-4 mt-5">
  &copy; 2024 <span class="fw-bold">CertChain</span>. All rights reserved.
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 