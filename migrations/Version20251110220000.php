<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add created_at and updated_at to users table
 */
final class Version20251110220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add created_at and updated_at columns to users table';
    }

    public function up(Schema $schema): void
    {
        // Add created_at and updated_at columns with default values
        $this->addSql('ALTER TABLE users ADD created_at DATETIME2(6) NOT NULL DEFAULT GETDATE()');
        $this->addSql('ALTER TABLE users ADD updated_at DATETIME2(6) NOT NULL DEFAULT GETDATE()');

        // Add metadata for Doctrine
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:datetime_immutable)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'users\', N\'COLUMN\', \'created_at\'');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:datetime_immutable)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'users\', N\'COLUMN\', \'updated_at\'');
    }

    public function down(Schema $schema): void
    {
        // Remove the columns
        $this->addSql('ALTER TABLE users DROP COLUMN created_at');
        $this->addSql('ALTER TABLE users DROP COLUMN updated_at');
    }
}
