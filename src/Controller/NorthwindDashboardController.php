<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class NorthwindDashboardController extends AbstractController
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    #[Route('/admin/northwind', name: 'app_dashboard_northwind')]
    public function index(): Response
    {
        try {
            // Get statistics for dashboard
            $totalProducts = $this->executeCount('SELECT COUNT(*) as count FROM products');
            $totalCustomers = $this->executeCount('SELECT COUNT(*) as count FROM customers');
            $totalOrders = $this->executeCount('SELECT COUNT(*) as count FROM orders');
            $totalSuppliers = $this->executeCount('SELECT COUNT(*) as count FROM suppliers');

            // Get recent products (last 6)
            $recentProducts = $this->connection->fetchAllAssociative(
                'SELECT TOP 6 ProductID, ProductName, UnitPrice, UnitsInStock, Discontinued 
                FROM products 
                ORDER BY ProductID DESC'
            );

            // Get recent orders (last 10)
            $recentOrders = $this->connection->fetchAllAssociative(
                'SELECT TOP 10 OrderID, CustomerID, OrderDate, ShippedDate, Freight 
                FROM orders 
                ORDER BY OrderDate DESC'
            );

            return $this->render('dashboard/northwind.html.twig', [
                'totalProducts' => $totalProducts,
                'totalCustomers' => $totalCustomers,
                'totalOrders' => $totalOrders,
                'totalSuppliers' => $totalSuppliers,
                'recentProducts' => $recentProducts,
                'recentOrders' => $recentOrders,
            ]);
        } catch (\Exception $e) {
            // If Northwind database doesn't exist or has issues, show empty dashboard
            return $this->render('dashboard/northwind.html.twig', [
                'totalProducts' => 0,
                'totalCustomers' => 0,
                'totalOrders' => 0,
                'totalSuppliers' => 0,
                'recentProducts' => [],
                'recentOrders' => [],
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
