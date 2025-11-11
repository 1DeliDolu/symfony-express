<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fix users table schema - drop and recreate with correct column types
 */
final class Version20251111180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix users table schema with correct column types for SQL Server';
    }

    public function up(Schema $schema): void
    {
        // Drop the existing users table with incorrect schema
        $this->addSql('DROP TABLE IF EXISTS users');

        // Create users table with correct schema
        $this->addSql('CREATE TABLE users (
            id INT IDENTITY NOT NULL, 
            email NVARCHAR(180) NOT NULL, 
            roles VARCHAR(MAX) NOT NULL, 
            password NVARCHAR(255) NOT NULL, 
            created_at DATETIME2(6) NOT NULL, 
            updated_at DATETIME2(6) NOT NULL, 
            PRIMARY KEY (id)
        )');

        // Create unique index on email
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email) WHERE email IS NOT NULL');

        // Add extended properties for Doctrine type hints
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'users\', N\'COLUMN\', \'roles\'');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:datetime_immutable)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'users\', N\'COLUMN\', \'created_at\'');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:datetime_immutable)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'users\', N\'COLUMN\', \'updated_at\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS users');
    }
}
