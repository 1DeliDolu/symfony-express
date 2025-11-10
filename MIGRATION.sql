-- Migration SQL for Symfony Express Pubs Modernization
-- Database: pubs (SQL Server)
-- Date: 2025-11-10

-- ============================================================================
-- ADD TIMESTAMP COLUMNS TO USERS TABLE
-- ============================================================================

USE pubs;
GO

-- Check if columns already exist before adding
IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'[dbo].[users]') AND name = 'created_at')
BEGIN
    ALTER TABLE users
    ADD created_at DATETIME2 NOT NULL DEFAULT CURRENT_TIMESTAMP;

    PRINT 'Column created_at added to users table';
END
ELSE
BEGIN
    PRINT 'Column created_at already exists in users table';
END
GO

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'[dbo].[users]') AND name = 'updated_at')
BEGIN
    ALTER TABLE users
    ADD updated_at DATETIME2 NOT NULL DEFAULT CURRENT_TIMESTAMP;

    PRINT 'Column updated_at added to users table';
END
ELSE
BEGIN
    PRINT 'Column updated_at already exists in users table';
END
GO

-- ============================================================================
-- OPTIONAL: UPDATE EXISTING USERS TO HAVE ROLE_USER INSTEAD OF ROLE_ADMIN
-- ============================================================================

-- WARNING: This will remove ROLE_ADMIN from all users
-- Only run this if you want to reset all user roles
-- You can manually promote users to admin after this

-- Uncomment the following lines if you want to reset all roles:

/*
UPDATE users
SET roles = '["ROLE_USER"]'
WHERE roles = '["ROLE_ADMIN"]';

PRINT 'Updated all users to ROLE_USER. You will need to manually promote admin users.';
*/

-- ============================================================================
-- CREATE INDEXES FOR BETTER PERFORMANCE (OPTIONAL)
-- ============================================================================

-- Index on email for faster lookups
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IDX_users_email' AND object_id = OBJECT_ID('users'))
BEGIN
    CREATE INDEX IDX_users_email ON users(email);
    PRINT 'Index IDX_users_email created';
END
ELSE
BEGIN
    PRINT 'Index IDX_users_email already exists';
END
GO

-- Index on created_at for sorting/filtering
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IDX_users_created_at' AND object_id = OBJECT_ID('users'))
BEGIN
    CREATE INDEX IDX_users_created_at ON users(created_at);
    PRINT 'Index IDX_users_created_at created';
END
ELSE
BEGIN
    PRINT 'Index IDX_users_created_at already exists';
END
GO

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Check users table structure
SELECT
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'users'
ORDER BY ORDINAL_POSITION;

-- Count users by role
SELECT
    roles,
    COUNT(*) as user_count
FROM users
GROUP BY roles;

-- Show sample users
SELECT TOP 5
    id,
    email,
    roles,
    created_at,
    updated_at
FROM users
ORDER BY created_at DESC;

PRINT '';
PRINT '============================================================';
PRINT 'Migration completed successfully!';
PRINT '============================================================';
PRINT '';
PRINT 'Next steps:';
PRINT '1. Run: composer install';
PRINT '2. Run: php bin/console cache:clear';
PRINT '3. Run: vendor/bin/phpstan analyse';
PRINT '4. Test the application thoroughly';
PRINT '';
PRINT 'To promote a user to admin, run:';
PRINT 'UPDATE users SET roles = ''["ROLE_ADMIN", "ROLE_USER"]'' WHERE email = ''your@email.com'';';
PRINT '';
