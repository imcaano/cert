// login.js placeholder for MetaMask authentication 

// MetaMask login logic
async function connectMetaMask() {
    if (typeof window.ethereum !== 'undefined') {
        try {
            // Request account access
            const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
            const address = accounts[0];
            // Send address to backend for authentication
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
                    alert(data.message);
                }
            });
        } catch (error) {
            alert('MetaMask connection failed.');
        }
    } else {
        alert('MetaMask is not installed. Please install MetaMask and try again.');
    }
} 