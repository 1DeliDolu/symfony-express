<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class NorthwindDashboardController extends AbstractController
{
    private Connection $connection;

    public function __construct(
        ManagerRegistry $registry,
    ) {
        // Use Northwind database connection
        $this->connection = $registry->getConnection('northwind');
    }

    #[Route('/admin/northwind', name: 'app_dashboard_northwind')]
    public function index(): Response
    {
        try {
            // Get statistics for dashboard
            $totalProducts = $this->executeCount('SELECT COUNT(*) as count FROM Products');
            $totalCustomers = $this->executeCount('SELECT COUNT(*) as count FROM Customers');
            $totalOrders = $this->executeCount('SELECT COUNT(*) as count FROM Orders');
            $totalSuppliers = $this->executeCount('SELECT COUNT(*) as count FROM Suppliers');
            $totalEmployees = $this->executeCount('SELECT COUNT(*) as count FROM Employees');

            // Get recent products (last 6)
            $recentProducts = $this->connection->fetchAllAssociative(
                'SELECT TOP 6 ProductID, ProductName, UnitPrice, UnitsInStock, Discontinued 
                FROM Products 
                ORDER BY ProductID DESC'
            );

            // Get recent orders (last 10)
            $recentOrders = $this->connection->fetchAllAssociative(
                'SELECT TOP 10 o.OrderID, o.CustomerID, o.OrderDate, o.ShippedDate, o.Freight 
                FROM Orders o
                ORDER BY o.OrderDate DESC'
            );

            // Get employee list
            $employees = $this->connection->fetchAllAssociative(
                'SELECT EmployeeID, FirstName, LastName, Title, City 
                FROM Employees 
                ORDER BY LastName'
            );

            return $this->render('dashboard/northwind.html.twig', [
                'totalProducts' => $totalProducts,
                'totalCustomers' => $totalCustomers,
                'totalOrders' => $totalOrders,
                'totalSuppliers' => $totalSuppliers,
                'totalEmployees' => $totalEmployees,
                'recentProducts' => $recentProducts,
                'recentOrders' => $recentOrders,
                'employees' => $employees,
                'database_available' => true,
            ]);
        } catch (\Exception $e) {
            // If Northwind database doesn't exist or has issues, show error
            return $this->render('dashboard/northwind.html.twig', [
                'totalProducts' => 0,
                'totalCustomers' => 0,
                'totalOrders' => 0,
                'totalSuppliers' => 0,
                'totalEmployees' => 0,
                'recentProducts' => [],
                'recentOrders' => [],
                'employees' => [],
                'database_available' => false,
                'error' => 'Northwind Datenbank ist nicht verfügbar. Bitte konfigurieren Sie die Datenbank.',
            ]);
        }
    }

    private function executeCount(string $sql): int
    {
        $result = $this->connection->fetchAssociative($sql);
        return (int) ($result['count'] ?? 0);
    }
}
