-- Database Update Script for CertChain
-- Run this script to add missing columns to your existing database

USE certchain;

-- Add missing columns to results table if they don't exist
ALTER TABLE results 
ADD COLUMN IF NOT EXISTS cert_id VARCHAR(255) UNIQUE,
ADD COLUMN IF NOT EXISTS certificate_hash VARCHAR(255),
ADD COLUMN IF NOT EXISTS total DECIMAL(5,2),
ADD COLUMN IF NOT EXISTS passed TINYINT(1) DEFAULT 0;

-- Update existing results to have default values
-- First check if marks column exists, if not use 0 as default
UPDATE results SET 
    total = COALESCE(marks, 0),
    passed = CASE WHEN COALESCE(average, 0) >= 50 THEN 1 ELSE 0 END
WHERE total IS NULL;

-- Add index for better performance
CREATE INDEX IF NOT EXISTS idx_results_cert_id ON results(cert_id);
CREATE INDEX IF NOT EXISTS idx_results_student_exam ON results(student_id, exam_id);

-- Verify the update
DESCRIBE results; 