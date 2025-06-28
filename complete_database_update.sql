-- Complete Database Update Script for CertChain
-- This script will safely update your database regardless of current structure

USE certchain;

-- Step 1: Check current table structure
DESCRIBE results;

-- Step 2: Add missing columns safely
ALTER TABLE results 
ADD COLUMN IF NOT EXISTS cert_id VARCHAR(255) UNIQUE,
ADD COLUMN IF NOT EXISTS certificate_hash VARCHAR(255),
ADD COLUMN IF NOT EXISTS total DECIMAL(5,2),
ADD COLUMN IF NOT EXISTS passed TINYINT(1) DEFAULT 0;

-- Step 3: Update existing records with safe defaults
UPDATE results SET 
    total = 0,
    passed = 0
WHERE total IS NULL;

-- Step 4: Add performance indexes
CREATE INDEX IF NOT EXISTS idx_results_cert_id ON results(cert_id);
CREATE INDEX IF NOT EXISTS idx_results_student_exam ON results(student_id, exam_id);

-- Step 5: Verify the final structure
DESCRIBE results;

-- Step 6: Show sample data
SELECT * FROM results LIMIT 5; 