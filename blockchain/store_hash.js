// store_hash.js
// Usage: node store_hash.js <certId> <studentName> <certHash>
// This script stores a certificate hash on the blockchain using ethers.js

const { ethers } = require('ethers');
const fs = require('fs');

// --- CONFIGURATION ---
const providerUrl = 'http://127.0.0.1:7545'; // Ganache default
const privateKey = '0x22c9321e7b51aa45db2ada9856214d442aa6596b71e23e69285bc4d978c3a647'; // Replace with your Ganache account private key
const contractAddress = '0xf8E633B8b4c0797D298952E9a5e0172266D18797'; // Deployed contract address
const abiPath = __dirname + '/CertificateRegistry_abi.json'; // ABI file path

const [,, certId, studentName, certHash] = process.argv;
if (!certId || !studentName || !certHash) {
    console.error('Usage: node store_hash.js <certId> <studentName> <certHash>');
    process.exit(1);
}

async function main() {
    // Load ABI
    const abi = JSON.parse(fs.readFileSync(abiPath));
    // Connect to provider and wallet
    const provider = new ethers.JsonRpcProvider(providerUrl);
    const wallet = new ethers.Wallet(privateKey, provider);
    // Connect to contract
    const contract = new ethers.Contract(contractAddress, abi, wallet);
    // Call issueCertificate
    const tx = await contract.issueCertificate(certId, studentName, certHash);
    await tx.wait();
    console.log('Certificate hash stored on blockchain:', certId);
}

main().catch(console.error); 