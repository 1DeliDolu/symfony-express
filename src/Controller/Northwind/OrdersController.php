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
        // Get filter parameters
        $country = $request->query->get('country');
        $customer = $request->query->get('customer');
        $limit = (int) $request->query->get('limit', 50);

        // Create QueryBuilder
        $qb = $this->connection->createQueryBuilder();
        $qb->select(
            'OrderID',
            'CustomerID',
            'EmployeeID',
            'OrderDate',
            'RequiredDate',
            'ShippedDate',
            'ShipVia',
            'Freight',
            'ShipName',
            'ShipCity',
            'ShipCountry',
            'CompanyName',
            'City',
            'Country'
        )
            ->from('[Orders Qry]')
            ->orderBy('OrderDate', 'DESC')
            ->setMaxResults($limit);

        // Apply filters
        if ($country) {
            $qb->andWhere('ShipCountry = :country')
                ->setParameter('country', $country);
        }

        if ($customer) {
            $qb->andWhere('CustomerID = :customer')
                ->setParameter('customer', $customer);
        }

        $orders = $qb->executeQuery()->fetchAllAssociative();

        // Get unique countries for filter dropdown
        $countriesQb = $this->connection->createQueryBuilder();
        $countries = $countriesQb->select('DISTINCT ShipCountry')
            ->from('[Orders Qry]')
            ->where('ShipCountry IS NOT NULL')
            ->orderBy('ShipCountry', 'ASC')
            ->executeQuery()
            ->fetchFirstColumn();

        return $this->render('northwind/orders/index.html.twig', [
            'orders' => $orders,
            'countries' => $countries,
            'filter_country' => $country,
            'filter_customer' => $customer,
            'total_count' => count($orders),
        ]);
    }

    #[Route('/{id}', name: 'app_northwind_orders_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        // Get order details from view
        $qb = $this->connection->createQueryBuilder();
        $order = $qb->select('*')
            ->from('[Orders Qry]')
            ->where('OrderID = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        // Get order details (line items)
        $detailsQb = $this->connection->createQueryBuilder();
        $orderDetails = $detailsQb->select(
            'od.OrderID',
            'od.ProductID',
            'p.ProductName',
            'od.UnitPrice',
            'od.Quantity',
            'od.Discount',
            '(od.UnitPrice * od.Quantity * (1 - od.Discount)) as ExtendedPrice'
        )
            ->from('[Order Details]', 'od')
            ->innerJoin('od', 'Products', 'p', 'od.ProductID = p.ProductID')
            ->where('od.OrderID = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->render('northwind/orders/show.html.twig', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }
}
