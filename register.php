<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register with MetaMask - CertChain</title>
    <script src="public/login.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    let ethAddress = '';
    async function getMetaMaskAddress() {
        if (typeof window.ethereum !== 'undefined') {
            try {
                const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                ethAddress = accounts[0];
                document.getElementById('eth_address').value = ethAddress;
                document.getElementById('eth_status').innerText = 'Connected: ' + ethAddress;
            } catch (error) {
                alert('MetaMask connection failed.');
            }
        } else {
            alert('MetaMask is not installed. Please install MetaMask and try again.');
        }
    }
    async function submitRegistration(e) {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const role = document.getElementById('role').value;
        if (!ethAddress) {
            alert('Please connect MetaMask first.');
            return;
        }
        fetch('register_process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ethereum_address: ethAddress, username, role })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Registration successful! You can now login.');
                window.location.href = 'login.php';
            } else {
                alert(data.message);
            }
        });
    }
    </script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-4">Register with MetaMask</h3>
                        <button class="btn btn-warning btn-lg w-100 mb-3" onclick="getMetaMaskAddress()">
                            <img src="https://raw.githubusercontent.com/MetaMask/brand-resources/master/SVG/metamask-fox.svg" alt="MetaMask" style="width:24px;vertical-align:middle;margin-right:8px;"> Connect MetaMask
                        </button>
                        <div id="eth_status" class="mb-3 text-success"></div>
                        <form onsubmit="submitRegistration(event)">
                            <input type="hidden" id="eth_address" name="eth_address">
                            <div class="mb-3 text-start">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3 text-start">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="user">Student</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                        <div class="mt-3">
                            <small>Already have an account? <a href="login.php">Login</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 