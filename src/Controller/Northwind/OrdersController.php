<?php

declare(strict_types=1);

namespace App\Controller\Northwind;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/northwind/orders')]
#[IsGranted('ROLE_USER')]
class OrdersController extends AbstractController
{
    private Connection $connection;

    public function __construct(ManagerRegistry $registry)
    {
        $this->connection = $registry->getConnection('northwind');
    }

    #[Route('', name: 'app_northwind_orders', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Get all orders for Alpine.js client-side filtering (limit to reasonable amount)
        $sql = "SELECT TOP 200
                    [OrderID], [CustomerID], [EmployeeID], [OrderDate], [RequiredDate], 
                    [ShippedDate], [ShipVia], [Freight], [ShipName], [ShipAddress],
                    [ShipCity], [ShipRegion], [ShipPostalCode], [ShipCountry],
                    [CompanyName], [Address], [City], [Region], [PostalCode], [Country]
                FROM [northwind].[dbo].[Orders Qry]
                ORDER BY [OrderDate] DESC";

        try {
            $orders = $this->connection->executeQuery($sql)->fetchAllAssociative();
        } catch (\Exception $e) {
            throw new \RuntimeException("SQL Error: " . $e->getMessage() . "\nSQL: " . $sql);
        }

        // Get unique countries for filter dropdown
        $countries = $this->connection->executeQuery(
            "SELECT DISTINCT [ShipCountry] FROM [northwind].[dbo].[Orders Qry] WHERE [ShipCountry] IS NOT NULL ORDER BY [ShipCountry] ASC"
        )->fetchFirstColumn();

        return $this->render('northwind/orders/index.html.twig', [
            'orders' => $orders,
            'countries' => $countries,
        ]);
    }

    #[Route('/{id}', name: 'app_northwind_orders_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        // Get order details from view using raw SQL with full column names
        $order = $this->connection->executeQuery(
            "SELECT TOP 1 
                [OrderID], [CustomerID], [EmployeeID], [OrderDate], [RequiredDate], 
                [ShippedDate], [ShipVia], [Freight], [ShipName], [ShipAddress],
                [ShipCity], [ShipRegion], [ShipPostalCode], [ShipCountry],
                [CompanyName], [Address], [City], [Region], [PostalCode], [Country]
            FROM [northwind].[dbo].[Orders Qry] 
            WHERE [OrderID] = :id",
            ['id' => $id],
            ['id' => \PDO::PARAM_INT]
        )->fetchAssociative();

        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        // Get order details (line items) with JOIN
        $orderDetails = $this->connection->executeQuery(
            "SELECT 
                od.[OrderID], 
                od.[ProductID], 
                p.[ProductName], 
                od.[UnitPrice], 
                od.[Quantity], 
                od.[Discount],
                CAST(od.[UnitPrice] * od.[Quantity] * (1 - od.[Discount]) AS DECIMAL(19,2)) AS [ExtendedPrice]
             FROM [northwind].[dbo].[OrderDetails] od
             INNER JOIN [northwind].[dbo].[Products] p ON od.[ProductID] = p.[ProductID]
             WHERE od.[OrderID] = :id
             ORDER BY p.[ProductName]",
            ['id' => $id],
            ['id' => \PDO::PARAM_INT]
        )->fetchAllAssociative();

        return $this->render('northwind/orders/show.html.twig', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_northwind_orders_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        // Get order from Orders table (not view)
        $order = $this->connection->executeQuery(
            "SELECT TOP 1 
                [OrderID], [CustomerID], [EmployeeID], [OrderDate], [RequiredDate], 
                [ShippedDate], [ShipVia], [Freight], [ShipName], [ShipAddress],
                [ShipCity], [ShipRegion], [ShipPostalCode], [ShipCountry]
            FROM [northwind].[dbo].[Orders] 
            WHERE [OrderID] = :id",
            ['id' => $id],
            ['id' => \PDO::PARAM_INT]
        )->fetchAssociative();

        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        // Get all customers for dropdown
        $customers = $this->connection->executeQuery(
            "SELECT [CustomerID], [CompanyName] FROM [northwind].[dbo].[Customers] ORDER BY [CompanyName]"
        )->fetchAllAssociative();

        // Get all employees for dropdown
        $employees = $this->connection->executeQuery(
            "SELECT [EmployeeID], [FirstName], [LastName] FROM [northwind].[dbo].[Employees] ORDER BY [LastName], [FirstName]"
        )->fetchAllAssociative();

        // Get all shippers for dropdown
        $shippers = $this->connection->executeQuery(
            "SELECT [ShipperID], [CompanyName] FROM [northwind].[dbo].[Shippers] ORDER BY [CompanyName]"
        )->fetchAllAssociative();

        if ($request->isMethod('POST')) {
            try {
                $this->connection->update(
                    '[northwind].[dbo].[Orders]',
                    [
                        '[CustomerID]' => $request->request->get('customer_id'),
                        '[EmployeeID]' => $request->request->get('employee_id') ?: null,
                        '[OrderDate]' => $request->request->get('order_date') ?: null,
                        '[RequiredDate]' => $request->request->get('required_date') ?: null,
                        '[ShippedDate]' => $request->request->get('shipped_date') ?: null,
                        '[ShipVia]' => $request->request->get('ship_via') ?: null,
                        '[Freight]' => $request->request->get('freight') ?: null,
                        '[ShipName]' => $request->request->get('ship_name') ?: null,
                        '[ShipAddress]' => $request->request->get('ship_address') ?: null,
                        '[ShipCity]' => $request->request->get('ship_city') ?: null,
                        '[ShipRegion]' => $request->request->get('ship_region') ?: null,
                        '[ShipPostalCode]' => $request->request->get('ship_postal_code') ?: null,
                        '[ShipCountry]' => $request->request->get('ship_country') ?: null,
                    ],
                    ['[OrderID]' => $id]
                );

                $this->addFlash('success', 'Order updated successfully!');
                return $this->redirectToRoute('app_northwind_orders_show', ['id' => $id]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error updating order: ' . $e->getMessage());
            }
        }

        return $this->render('northwind/orders/edit.html.twig', [
            'order' => $order,
            'customers' => $customers,
            'employees' => $employees,
            'shippers' => $shippers,
        ]);
    }
}
