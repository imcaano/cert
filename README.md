# CertChain: Blockchain-Based Certification System

Complete blockchain-based system for secure online exam certification using PHP, Bootstrap, MySQL, and Ethereum.

## 🚀 Features
- Student/Admin portals with MetaMask authentication
- Public certificate verification
- Blockchain-stored certificate hashes
- Modern Bootstrap UI
- Secure exam system

## 📋 Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Node.js (v16+)
- Ganache (Local Ethereum blockchain)
- MetaMask (Browser extension)

## 🏗️ Installation

### Step 1: Database Setup
1. Start XAMPP (Apache + MySQL)
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Create database: `certchain`
4. Import `db.sql`

### Step 2: Blockchain Setup
```bash
# Install dependencies
cd C:\xampp\htdocs\cert
npm install --save-dev hardhat @nomicfoundation/hardhat-toolbox
cd blockchain && npm install ethers

# Deploy contract
npx hardhat run scripts/deploy.js --network ganache
# Save the contract address output!
```

### Step 3: Configure Blockchain
Edit `blockchain/store_hash.js`:
```javascript
const privateKey = 'YOUR_GANACHE_PRIVATE_KEY';
const contractAddress = '0x1234567890abcdef...'; // From deployment
```

### Step 4: MetaMask Setup
1. Install MetaMask extension
2. Add Ganache network:
   - Network Name: `Ganache`
   - RPC URL: `http://127.0.0.1:7545`
   - Chain ID: `1337`
3. Import account from Ganache

### Step 5: Create Admin
```sql
INSERT INTO users (username, email, ethereum_address, role, password_hash, created_at) 
VALUES ('admin', 'admin@example.com', 'YOUR_ETHEREUM_ADDRESS', 'admin', 'admin123', NOW());
```

## 🚀 Running the System

### Start Services
1. Start XAMPP (Apache + MySQL)
2. Start Ganache
3. Ensure MetaMask connected to Ganache

### Access URLs
- Frontend: `http://localhost/cert/`
- Login: `http://localhost/cert/login.php`
- Register: `http://localhost/cert/register.php`
- Verify: `http://localhost/cert/frontend/verify.php`

## 🔧 Blockchain Commands

### Store Certificate Hash
```bash
cd C:\xampp\htdocs\cert
node blockchain/store_hash.js <certId> <studentName> <certHash>

# Example:
node blockchain/store_hash.js CERT123 "John Doe" 0xabc123def456...
```

### Deploy Contract
```bash
npx hardhat run scripts/deploy.js --network ganache
```

## 🐛 Troubleshooting

### Common Issues
1. **"network does not support ENS"**: Check contract address in `store_hash.js`
2. **MetaMask issues**: Verify Ganache network settings
3. **Database errors**: Check XAMPP and database credentials
4. **Contract deployment failed**: Verify Ganache running and private key

### Debug Commands
```bash
# Check Ganache
curl http://127.0.0.1:7545

# Check Node.js
node --version

# Compile contracts
npx hardhat compile
```

## 📁 Project Structure
```
cert/
├── frontend/          # Landing, login, register, verify pages
├── backend/           # PHP backend logic
├── blockchain/        # Smart contracts and Node.js scripts
├── contracts/         # Solidity contracts
├── scripts/           # Deployment scripts
├── config.php         # Database config
├── db.sql            # Database schema
└── README.md         # This file
```

## 🔒 Security Features
- MetaMask authentication
- Blockchain certificate verification
- Admin-only blockchain writes
- Secure PHP sessions
- SQL injection protection

---

<<<<<<< HEAD
**Ready to use! 🚀** 
=======
**Ready to use! 🚀** 
>>>>>>> 02470bf (full updated)
