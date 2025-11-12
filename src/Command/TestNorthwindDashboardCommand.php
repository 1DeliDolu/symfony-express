<?php

namespace App\Command;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-northwind-dashboard',
    description: 'Test Northwind dashboard data',
)]
class TestNorthwindDashboardCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $registry
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $connection = $this->registry->getConnection('northwind');

        try {
            $io->title('Testing Northwind Dashboard Data');

            // Get statistics
            $totalProducts = $connection->fetchOne('SELECT COUNT(*) FROM Products');
            $totalCustomers = $connection->fetchOne('SELECT COUNT(*) FROM Customers');
            $totalOrders = $connection->fetchOne('SELECT COUNT(*) FROM Orders');
            $totalSuppliers = $connection->fetchOne('SELECT COUNT(*) FROM Suppliers');
            $totalEmployees = $connection->fetchOne('SELECT COUNT(*) FROM Employees');

            $io->section('Statistics');
            $io->table(
                ['Metric', 'Count'],
                [
                    ['Products', $totalProducts],
                    ['Customers', $totalCustomers],
                    ['Orders', $totalOrders],
                    ['Suppliers', $totalSuppliers],
                    ['Employees', $totalEmployees],
                ]
            );

            // Test recent products query
            $io->section('Testing Recent Products Query');
            $recentProducts = $connection->fetchAllAssociative(
                'SELECT TOP 3 ProductID, ProductName, UnitPrice, UnitsInStock 
                FROM Products 
                ORDER BY ProductID DESC'
            );

            $io->table(
                ['ID', 'Name', 'Price', 'Stock'],
                array_map(fn($p) => [$p['ProductID'], $p['ProductName'], $p['UnitPrice'], $p['UnitsInStock']], $recentProducts)
            );

            // Test recent orders query
            $io->section('Testing Recent Orders Query');
            $recentOrders = $connection->fetchAllAssociative(
                'SELECT TOP 3 OrderID, CustomerID, OrderDate, Freight 
                FROM Orders 
                ORDER BY OrderDate DESC'
            );

            $io->table(
                ['Order ID', 'Customer', 'Date', 'Freight'],
                array_map(fn($o) => [
                    $o['OrderID'],
                    $o['CustomerID'],
                    is_string($o['OrderDate']) ? $o['OrderDate'] : $o['OrderDate']->format('Y-m-d'),
                    number_format((float)$o['Freight'], 2)
                ], $recentOrders)
            );

            $io->success('All dashboard queries executed successfully!');
            $io->info('Dashboard URL: https://localhost:8000/admin/northwind');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
