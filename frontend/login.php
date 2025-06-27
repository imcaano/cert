<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CertChain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(120deg, #f8fafc 60%, #e3e9ff 100%);
            min-height: 100vh;
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
        .login-section {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 420px;
            margin: 0 auto;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.12);
            border: none;
            animation: fadeInUp 1s;
            background: rgba(255,255,255,0.95);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .metamask-btn {
            background: linear-gradient(90deg, #f6851b 60%, #e2761b 100%);
            color: #fff;
            font-size: 1.2rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 16px 0;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px #f6851b22;
        }
        .metamask-btn:hover {
            background: linear-gradient(90deg, #e2761b 60%, #f6851b 100%);
            color: #fff;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 6px 18px #e2761b44;
        }
        .wallet-icon {
            font-size: 2.8rem;
            color: #f6851b;
            margin-bottom: 10px;
            filter: drop-shadow(0 2px 8px #f6851b33);
        }
        .login-title {
            font-weight: 700;
            color: #2563eb;
        }
        .error-message {
            display: none;
        }
    </style>
    <script src="../public/login.js"></script>
    <script>
    function showError(msg) {
        const err = document.getElementById('login-error');
        err.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + msg;
        err.style.display = 'block';
        err.className = 'alert alert-danger';
    }
    // Patch connectMetaMask to show error in the card
    window.connectMetaMask = async function() {
        if (typeof window.ethereum !== 'undefined') {
            try {
                const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                const address = accounts[0];
                fetch('../login_process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ethereum_address: address })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        showError(data.message);
                    }
                });
            } catch (error) {
                showError('MetaMask connection failed.');
            }
        } else {
            showError('MetaMask is not installed. Please install MetaMask and try again.');
        }
    }
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg glass-navbar sticky-top py-2">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="../index.php">
      <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Logo" class="navbar-logo">
      <span>CertChain</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link" href="../index.php"><i class="bi bi-house-door me-1"></i>Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="verify.php"><i class="bi bi-patch-check me-1"></i>Verify Certificate</a>
        </li>
        <li class="nav-item">
          <a class="login-btn nav-link active" aria-current="page" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="login-section">
  <div class="card login-card p-4">
    <div class="text-center mb-3">
      <div class="wallet-icon"><i class="bi bi-wallet2"></i></div>
      <h2 class="login-title mb-2">Login with MetaMask</h2>
      <div class="text-muted mb-3">Only registered students and admins can login.</div>
    </div>
    <div id="login-error" class="error-message"></div>
    <button class="metamask-btn w-100 mb-2" onclick="connectMetaMask()">
      <i class="bi bi-box-arrow-in-right me-1"></i> Connect MetaMask
    </button>
  </div>
</div>

<footer class="bg-primary text-white text-center py-4 mt-5">
  &copy; 2024 <span class="fw-bold">CertChain</span>. All rights reserved.
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 