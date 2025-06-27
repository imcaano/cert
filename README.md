# CertChain: Blockchain-Based System for Secure Online Exam Certification and Identity Authentication

## Project Structure

```
cert/
│
├── frontend/
│   ├── index.php           # Landing page
│   ├── login.php           # Login page (students/admins)
│   ├── register.php        # Signup page (students/admins)
│   ├── verify.php          # Public certificate verification (no login)
│   ├── student_dashboard.php
│   ├── admin_dashboard.php
│   └── assets/             # CSS, JS, images, etc.
│
├── backend/
│   ├── db.php              # Database connection
│   ├── login_process.php   # Login logic
│   ├── register_process.php# Registration logic
│   ├── verify_process.php  # Certificate verification logic
│   └── ...                 # Other backend scripts
│
└── README.md
```

## Features
- Student and Admin login/registration
- Public certificate verification (no login required)
- Separate dashboards for students and admins
- Modern Bootstrap UI
- Ready for blockchain and exam/certificate features 

## Blockchain Integration
- When a certificate is generated, its hash is stored in the database and can be sent to the blockchain smart contract.
- To store a hash on the blockchain, run:

```
node blockchain/store_hash.js <certId> <studentName> <certHash>
```

## Certificate Verification
- Public users can verify certificates by entering the Certificate ID on the verification page.
- The system checks the hash and displays if the certificate is valid or invalid. 