<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251114212909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dbo.Categories (CategoryID INT IDENTITY NOT NULL, CategoryName NVARCHAR(15) NOT NULL, Description VARCHAR(MAX), Picture VARBINARY(MAX), PRIMARY KEY (CategoryID))');
        $this->addSql('CREATE TABLE dbo.CustomerCustomerDemo (CustomerID NVARCHAR(5) NOT NULL, CustomerTypeID NVARCHAR(10) NOT NULL, PRIMARY KEY (CustomerID, CustomerTypeID))');
        $this->addSql('CREATE TABLE dbo.CustomerDemographics (CustomerTypeID NVARCHAR(10) NOT NULL, CustomerDesc VARCHAR(MAX), PRIMARY KEY (CustomerTypeID))');
        $this->addSql('CREATE TABLE dbo.Customers (CustomerID NVARCHAR(5) NOT NULL, CompanyName NVARCHAR(40) NOT NULL, ContactName NVARCHAR(30), ContactTitle NVARCHAR(30), Address NVARCHAR(60), City NVARCHAR(15), Region NVARCHAR(15), PostalCode NVARCHAR(10), Country NVARCHAR(15), Phone NVARCHAR(24), Fax NVARCHAR(24), PRIMARY KEY (CustomerID))');
        $this->addSql('CREATE TABLE dbo.Employees (EmployeeID INT IDENTITY NOT NULL, LastName NVARCHAR(20) NOT NULL, FirstName NVARCHAR(20) NOT NULL, Title NVARCHAR(30), TitleOfCourtesy NVARCHAR(25), BirthDate DATETIME2(6), HireDate DATETIME2(6), Address NVARCHAR(60), City NVARCHAR(15), Region NVARCHAR(15), PostalCode NVARCHAR(10), Country NVARCHAR(15) NOT NULL, HomePhone NVARCHAR(24), Extension NVARCHAR(4), Photo VARBINARY(MAX), Notes VARCHAR(MAX), PhotoPath NVARCHAR(255), ReportsTo INT, PRIMARY KEY (EmployeeID))');
        $this->addSql('CREATE INDEX IDX_4995004D54E08D1 ON dbo.Employees (ReportsTo)');
        $this->addSql('CREATE TABLE dbo.OrderDetails (OrderID INT NOT NULL, ProductID INT NOT NULL, UnitPrice NUMERIC(10, 4) NOT NULL, Quantity SMALLINT NOT NULL, Discount DOUBLE PRECISION NOT NULL, PRIMARY KEY (OrderID, ProductID))');
        $this->addSql('CREATE TABLE dbo.Orders (OrderID INT IDENTITY NOT NULL, CustomerID NVARCHAR(5), EmployeeID INT, OrderDate DATETIME2(6), RequiredDate DATETIME2(6), ShippedDate DATETIME2(6), ShipVia INT, Freight NUMERIC(10, 4), ShipName NVARCHAR(40), ShipAddress NVARCHAR(60), ShipCity NVARCHAR(15), ShipRegion NVARCHAR(15), ShipPostalCode NVARCHAR(10), ShipCountry NVARCHAR(15), PRIMARY KEY (OrderID))');
        $this->addSql('CREATE TABLE dbo.Products (ProductID INT IDENTITY NOT NULL, ProductName NVARCHAR(40) NOT NULL, SupplierID INT, CategoryID INT, QuantityPerUnit NVARCHAR(20), UnitPrice NUMERIC(10, 4), UnitsInStock SMALLINT, UnitsOnOrder SMALLINT, ReorderLevel SMALLINT, Discontinued BIT NOT NULL, PRIMARY KEY (ProductID))');
        $this->addSql('CREATE TABLE dbo.Shippers (ShipperID INT IDENTITY NOT NULL, CompanyName NVARCHAR(40) NOT NULL, Phone NVARCHAR(24), PRIMARY KEY (ShipperID))');
        $this->addSql('CREATE TABLE dbo.Suppliers (SupplierID INT IDENTITY NOT NULL, CompanyName NVARCHAR(40) NOT NULL, ContactName NVARCHAR(30), ContactTitle NVARCHAR(30), Address NVARCHAR(60), City NVARCHAR(15), Region NVARCHAR(15), PostalCode NVARCHAR(10), Country NVARCHAR(15), Phone NVARCHAR(24), Fax NVARCHAR(24), HomePage VARCHAR(MAX), PRIMARY KEY (SupplierID))');
        $this->addSql('CREATE TABLE dbo.Territories (TerritoryID NVARCHAR(20) NOT NULL, TerritoryDescription NVARCHAR(50) NOT NULL, RegionID INT NOT NULL, PRIMARY KEY (TerritoryID))');
        $this->addSql('ALTER TABLE dbo.Employees ADD CONSTRAINT FK_4995004D54E08D1 FOREIGN KEY (ReportsTo) REFERENCES dbo.Employees (EmployeeID)');
        $this->addSql('ALTER TABLE Employees DROP CONSTRAINT FK_4995004D54E08D1');
        $this->addSql('DROP TABLE Customers');
        $this->addSql('DROP TABLE Employees');
        $this->addSql('DROP TABLE Products');
        $this->addSql('DROP TABLE Shippers');
        $this->addSql('DROP TABLE Territories');
        $this->addSql('DROP TABLE Categories');
        $this->addSql('DROP TABLE CustomerCustomerDemo');
        $this->addSql('DROP TABLE CustomerDemographics');
        $this->addSql('DROP TABLE OrderDetails');
        $this->addSql('DROP TABLE Orders');
        $this->addSql('DROP TABLE Suppliers');
        $this->addSql('ALTER TABLE authors DROP CONSTRAINT DF_8E0C2A51_444F97DD');
        $this->addSql('ALTER TABLE authors ALTER COLUMN phone NVARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE authors ADD CONSTRAINT DF_8E0C2A51_444F97DD DEFAULT \'UNKNOWN\' FOR phone');
        $this->addSql('ALTER TABLE authors ALTER COLUMN state NVARCHAR(2)');
        $this->addSql('ALTER TABLE authors ALTER COLUMN zip NVARCHAR(5)');
        $this->addSql('ALTER TABLE discounts ALTER COLUMN stor_id NVARCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE discounts ADD PRIMARY KEY (discounttype, stor_id)');
        $this->addSql('DROP INDEX employee_ind ON employee');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT DF_5D9F75A1_83FDE077');
        $this->addSql('ALTER TABLE employee ALTER COLUMN pub_id NVARCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT DF_5D9F75A1_83FDE077 DEFAULT \'9952\' FOR pub_id');
        $this->addSql('ALTER TABLE employee ALTER COLUMN minit NVARCHAR(1)');
        $this->addSql('ALTER TABLE jobs ALTER COLUMN min_lvl SMALLINT NOT NULL min_lvl >= 10');
        $this->addSql('ALTER TABLE jobs ALTER COLUMN max_lvl SMALLINT NOT NULL max_lvl <= 250');
        $this->addSql('ALTER TABLE pub_info ALTER COLUMN pub_id NVARCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE publishers ALTER COLUMN pub_id NVARCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE publishers ALTER COLUMN state NVARCHAR(2)');
        $this->addSql('DROP INDEX titleidind ON roysched');
        $this->addSql('ALTER TABLE roysched ADD PRIMARY KEY (title_id)');
        $this->addSql('ALTER TABLE sales ALTER COLUMN stor_id NVARCHAR(4) NOT NULL');
        $this->addSql('EXEC sp_rename N\'sales.titleidind\', N\'IDX_6B817044A9F87BD\', N\'INDEX\'');
        $this->addSql('ALTER TABLE stores ALTER COLUMN stor_id NVARCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE stores ALTER COLUMN state NVARCHAR(2)');
        $this->addSql('ALTER TABLE stores ALTER COLUMN zip NVARCHAR(5)');
        $this->addSql('EXEC sp_rename N\'titleauthor.auidind\', N\'IDX_F7668223A791E56C\', N\'INDEX\'');
        $this->addSql('EXEC sp_rename N\'titleauthor.titleidind\', N\'IDX_F7668223A9F87BD\', N\'INDEX\'');
        $this->addSql('DROP INDEX titleind ON titles');
        $this->addSql('ALTER TABLE titles ALTER COLUMN pub_id NVARCHAR(4)');
        $this->addSql('ALTER TABLE titles DROP CONSTRAINT DF_C14541A3_8CDE5729');
        $this->addSql('ALTER TABLE titles ALTER COLUMN type NVARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE titles ADD CONSTRAINT DF_C14541A3_8CDE5729 DEFAULT \'UNDECIDED\' FOR type');
        $this->addSql('ALTER TABLE titles ALTER COLUMN price NUMERIC(19, 4)');
        $this->addSql('ALTER TABLE titles ALTER COLUMN advance NUMERIC(19, 4)');
        $this->addSql('ALTER TABLE users ADD is_verified BIT NOT NULL');
        $this->addSql('ALTER TABLE users ADD verification_token NVARCHAR(64)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA db_accessadmin');
        $this->addSql('CREATE SCHEMA db_backupoperator');
        $this->addSql('CREATE SCHEMA db_datareader');
        $this->addSql('CREATE SCHEMA db_datawriter');
        $this->addSql('CREATE SCHEMA db_ddladmin');
        $this->addSql('CREATE SCHEMA db_denydatareader');
        $this->addSql('CREATE SCHEMA db_denydatawriter');
        $this->addSql('CREATE SCHEMA db_owner');
        $this->addSql('CREATE SCHEMA db_securityadmin');
        $this->addSql('CREATE TABLE Customers (CustomerID NVARCHAR(5) COLLATE Latin1_General_CI_AS NOT NULL, CompanyName NVARCHAR(40) COLLATE Latin1_General_CI_AS NOT NULL, ContactName NVARCHAR(30) COLLATE Latin1_General_CI_AS, ContactTitle NVARCHAR(30) COLLATE Latin1_General_CI_AS, Address NVARCHAR(60) COLLATE Latin1_General_CI_AS, City NVARCHAR(15) COLLATE Latin1_General_CI_AS, Region NVARCHAR(15) COLLATE Latin1_General_CI_AS, PostalCode NVARCHAR(10) COLLATE Latin1_General_CI_AS, Country NVARCHAR(15) COLLATE Latin1_General_CI_AS, Phone NVARCHAR(24) COLLATE Latin1_General_CI_AS, Fax NVARCHAR(24) COLLATE Latin1_General_CI_AS, PRIMARY KEY (CustomerID))');
        $this->addSql('CREATE TABLE Employees (EmployeeID INT IDENTITY NOT NULL, LastName NVARCHAR(20) COLLATE Latin1_General_CI_AS NOT NULL, FirstName NVARCHAR(20) COLLATE Latin1_General_CI_AS NOT NULL, Title NVARCHAR(30) COLLATE Latin1_General_CI_AS, TitleOfCourtesy NVARCHAR(25) COLLATE Latin1_General_CI_AS, BirthDate DATETIME2(6), HireDate DATETIME2(6), Address NVARCHAR(60) COLLATE Latin1_General_CI_AS, City NVARCHAR(15) COLLATE Latin1_General_CI_AS, Region NVARCHAR(15) COLLATE Latin1_General_CI_AS, PostalCode NVARCHAR(10) COLLATE Latin1_General_CI_AS, Country NVARCHAR(15) COLLATE Latin1_General_CI_AS NOT NULL, HomePhone NVARCHAR(24) COLLATE Latin1_General_CI_AS, Extension NVARCHAR(4) COLLATE Latin1_General_CI_AS, Photo VARBINARY(MAX), Notes VARCHAR(MAX) COLLATE Latin1_General_CI_AS, PhotoPath NVARCHAR(255) COLLATE Latin1_General_CI_AS, ReportsTo INT, PRIMARY KEY (EmployeeID))');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_4995004D54E08D1 ON Employees (ReportsTo)');
        $this->addSql('CREATE TABLE Products (ProductID INT IDENTITY NOT NULL, ProductName NVARCHAR(40) COLLATE Latin1_General_CI_AS NOT NULL, SupplierID INT, CategoryID INT, QuantityPerUnit NVARCHAR(20) COLLATE Latin1_General_CI_AS, UnitPrice NUMERIC(10, 4), UnitsInStock SMALLINT, UnitsOnOrder SMALLINT, ReorderLevel SMALLINT, Discontinued BIT NOT NULL, PRIMARY KEY (ProductID))');
        $this->addSql('CREATE TABLE Shippers (ShipperID INT IDENTITY NOT NULL, CompanyName NVARCHAR(40) COLLATE Latin1_General_CI_AS NOT NULL, Phone NVARCHAR(24) COLLATE Latin1_General_CI_AS, PRIMARY KEY (ShipperID))');
        $this->addSql('CREATE TABLE Territories (TerritoryID NVARCHAR(20) COLLATE Latin1_General_CI_AS NOT NULL, TerritoryDescription NVARCHAR(50) COLLATE Latin1_General_CI_AS NOT NULL, RegionID INT NOT NULL, PRIMARY KEY (TerritoryID))');
        $this->addSql('CREATE TABLE Categories (CategoryID INT IDENTITY NOT NULL, CategoryName NVARCHAR(15) COLLATE Latin1_General_CI_AS NOT NULL, Description VARCHAR(MAX) COLLATE Latin1_General_CI_AS, Picture VARBINARY(MAX), PRIMARY KEY (CategoryID))');
        $this->addSql('CREATE TABLE CustomerCustomerDemo (CustomerID NVARCHAR(5) COLLATE Latin1_General_CI_AS NOT NULL, CustomerTypeID NVARCHAR(10) COLLATE Latin1_General_CI_AS NOT NULL, PRIMARY KEY (CustomerID, CustomerTypeID))');
        $this->addSql('CREATE TABLE CustomerDemographics (CustomerTypeID NVARCHAR(10) COLLATE Latin1_General_CI_AS NOT NULL, CustomerDesc VARCHAR(MAX) COLLATE Latin1_General_CI_AS, PRIMARY KEY (CustomerTypeID))');
        $this->addSql('CREATE TABLE OrderDetails (OrderID INT NOT NULL, ProductID INT NOT NULL, UnitPrice NUMERIC(10, 4) NOT NULL, Quantity SMALLINT NOT NULL, Discount DOUBLE PRECISION NOT NULL, PRIMARY KEY (OrderID, ProductID))');
        $this->addSql('CREATE TABLE Orders (OrderID INT IDENTITY NOT NULL, CustomerID NVARCHAR(5) COLLATE Latin1_General_CI_AS, EmployeeID INT, OrderDate DATETIME2(6), RequiredDate DATETIME2(6), ShippedDate DATETIME2(6), ShipVia INT, Freight NUMERIC(10, 4), ShipName NVARCHAR(40) COLLATE Latin1_General_CI_AS, ShipAddress NVARCHAR(60) COLLATE Latin1_General_CI_AS, ShipCity NVARCHAR(15) COLLATE Latin1_General_CI_AS, ShipRegion NVARCHAR(15) COLLATE Latin1_General_CI_AS, ShipPostalCode NVARCHAR(10) COLLATE Latin1_General_CI_AS, ShipCountry NVARCHAR(15) COLLATE Latin1_General_CI_AS, PRIMARY KEY (OrderID))');
        $this->addSql('CREATE TABLE Suppliers (SupplierID INT IDENTITY NOT NULL, CompanyName NVARCHAR(40) COLLATE Latin1_General_CI_AS NOT NULL, ContactName NVARCHAR(30) COLLATE Latin1_General_CI_AS, ContactTitle NVARCHAR(30) COLLATE Latin1_General_CI_AS, Address NVARCHAR(60) COLLATE Latin1_General_CI_AS, City NVARCHAR(15) COLLATE Latin1_General_CI_AS, Region NVARCHAR(15) COLLATE Latin1_General_CI_AS, PostalCode NVARCHAR(10) COLLATE Latin1_General_CI_AS, Country NVARCHAR(15) COLLATE Latin1_General_CI_AS, Phone NVARCHAR(24) COLLATE Latin1_General_CI_AS, Fax NVARCHAR(24) COLLATE Latin1_General_CI_AS, HomePage VARCHAR(MAX) COLLATE Latin1_General_CI_AS, PRIMARY KEY (SupplierID))');
        $this->addSql('ALTER TABLE Employees ADD CONSTRAINT FK_4995004D54E08D1 FOREIGN KEY (ReportsTo) REFERENCES Employees (EmployeeID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dbo.Employees DROP CONSTRAINT FK_4995004D54E08D1');
        $this->addSql('DROP TABLE dbo.Categories');
        $this->addSql('DROP TABLE dbo.CustomerCustomerDemo');
        $this->addSql('DROP TABLE dbo.CustomerDemographics');
        $this->addSql('DROP TABLE dbo.Customers');
        $this->addSql('DROP TABLE dbo.Employees');
        $this->addSql('DROP TABLE dbo.OrderDetails');
        $this->addSql('DROP TABLE dbo.Orders');
        $this->addSql('DROP TABLE dbo.Products');
        $this->addSql('DROP TABLE dbo.Shippers');
        $this->addSql('DROP TABLE dbo.Suppliers');
        $this->addSql('DROP TABLE dbo.Territories');
        $this->addSql('ALTER TABLE users DROP COLUMN is_verified');
        $this->addSql('ALTER TABLE users DROP COLUMN verification_token');
        $this->addSql('ALTER TABLE authors DROP CONSTRAINT DF_8E0C2A51_444F97DD');
        $this->addSql('ALTER TABLE authors ALTER COLUMN phone NCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE authors ADD CONSTRAINT DF_8E0C2A51_444F97DD DEFAULT \'UNKNOWN\' FOR phone');
        $this->addSql('ALTER TABLE authors ALTER COLUMN state NCHAR(2)');
        $this->addSql('ALTER TABLE authors ALTER COLUMN zip NCHAR(5)');
        $this->addSql('ALTER TABLE publishers ALTER COLUMN pub_id NCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE publishers ALTER COLUMN state NCHAR(2)');
        $this->addSql('ALTER TABLE titles ALTER COLUMN pub_id NCHAR(4)');
        $this->addSql('ALTER TABLE titles DROP CONSTRAINT DF_C14541A3_8CDE5729');
        $this->addSql('ALTER TABLE titles ALTER COLUMN type NCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE titles ADD CONSTRAINT DF_C14541A3_8CDE5729 DEFAULT \'UNDECIDED\' FOR type');
        $this->addSql('ALTER TABLE titles ALTER COLUMN price INT');
        $this->addSql('ALTER TABLE titles ALTER COLUMN advance INT');
        $this->addSql('CREATE NONCLUSTERED INDEX titleind ON titles (title)');
        $this->addSql('EXEC sp_rename N\'titleauthor.idx_f7668223a791e56c\', N\'auidind\', N\'INDEX\'');
        $this->addSql('EXEC sp_rename N\'titleauthor.idx_f7668223a9f87bd\', N\'titleidind\', N\'INDEX\'');
        $this->addSql('ALTER TABLE stores ALTER COLUMN stor_id NCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE stores ALTER COLUMN state NCHAR(2)');
        $this->addSql('ALTER TABLE stores ALTER COLUMN zip NCHAR(5)');
        $this->addSql('ALTER TABLE sales ALTER COLUMN stor_id NCHAR(4) NOT NULL');
        $this->addSql('EXEC sp_rename N\'sales.idx_6b817044a9f87bd\', N\'titleidind\', N\'INDEX\'');
        $this->addSql('DROP INDEX [primary] ON roysched');
        $this->addSql('CREATE NONCLUSTERED INDEX titleidind ON roysched (title_id)');
        $this->addSql('DROP INDEX [primary] ON discounts');
        $this->addSql('ALTER TABLE discounts ALTER COLUMN stor_id NCHAR(4)');
        $this->addSql('ALTER TABLE jobs ALTER COLUMN min_lvl SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE jobs ALTER COLUMN max_lvl SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE pub_info ALTER COLUMN pub_id NCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT DF_5D9F75A1_83FDE077');
        $this->addSql('ALTER TABLE employee ALTER COLUMN pub_id NCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT DF_5D9F75A1_83FDE077 DEFAULT \'9952\' FOR pub_id');
        $this->addSql('ALTER TABLE employee ALTER COLUMN minit NCHAR(1)');
        $this->addSql('CREATE CLUSTERED INDEX employee_ind ON employee (lname, fname, minit)');
    }
}
