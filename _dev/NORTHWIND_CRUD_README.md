# Northwind CRUD Generation Guide

## Problem

`make:crud` komutu sadece `App\Entity` namespace'i altındaki entity'leri buluyor. `App\Entity\Northwind` gibi alt namespace'lerdeki entity'leri bulamıyor.

## Çözüm

Entity adını `Northwind\EntityName` formatında yazarak komut çalıştırılmalı.

## CRUD Oluşturma Komutları

### Çalışan Komut Formatı

```bash
php bin/console make:crud Northwind\EntityName
```

### Tüm Northwind Entity'leri İçin Komutlar

**Her komuttan sonra:**

1. Controller adı soracak → **ENTER** basın (default kullanın)
2. Test oluşturulsun mu? → **no** yazın

#### Basit Entity'ler (Tek Primary Key)

```bash
# Products (ÖNEMLİ - en çok kullanılan)
php bin/console make:crud Northwind\Products

# Suppliers
php bin/console make:crud Northwind\Suppliers

# Shippers
php bin/console make:crud Northwind\Shippers

# Categories
php bin/console make:crud Northwind\Categories

# Customers
php bin/console make:crud Northwind\Customers

# Orders
php bin/console make:crud Northwind\Orders

# Employees
php bin/console make:crud Northwind\Employees

# Territories
php bin/console make:crud Northwind\Territories

# CustomerDemographics
php bin/console make:crud Northwind\CustomerDemographics
```

#### Composite Primary Key Entity'ler (Manuel Oluşturulmalı)

⚠️ **Bu entity'ler için `make:crud` ÇALIŞMAZ:**

-   `CustomerCustomerDemo` (CustomerID + CustomerTypeID)
-   `OrderDetails` (OrderID + ProductID)
-   `EmployeeTerritories` (EmployeeID + TerritoryID)

Bu entity'ler için controller, form ve template dosyaları manuel oluşturulmalıdır.

## Post-Generation Steps

### 1. Cache Temizle

```bash
php bin/console cache:clear
```

### 2. Routes Kontrol Et

```bash
php bin/console debug:router | findstr northwind
```

### 3. Entity Manager Düzeltmesi

Oluşturulan controller'larda `EntityManagerInterface` yerine `ManagerRegistry` kullanılmalı çünkü Northwind ayrı bir database connection kullanıyor.

**Değiştirilmesi gereken kısımlar:**

```php
// YANLIŞ
use Doctrine\ORM\EntityManagerInterface;

public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $entityManager->persist($entity);
    $entityManager->flush();
}

// DOĞRU
use Doctrine\Persistence\ManagerRegistry;

public function new(Request $request, ManagerRegistry $doctrine): Response
{
    $em = $doctrine->getManager('northwind');
    $em->persist($entity);
    $em->flush();
}
```

## Erişim URL'leri

Tüm Northwind CRUD'larına şu URL pattern'i ile erişilir:

```
http://localhost:8000/northwind/{entity-name}
```

**Örnekler:**

-   http://localhost:8000/northwind/products
-   http://localhost:8000/northwind/categories
-   http://localhost:8000/northwind/customers
-   http://localhost:8000/northwind/orders

## Oluşturulan Dosyalar

Her `make:crud` komutu şu dosyaları oluşturur:

```
src/
  Controller/
    Northwind/
      {Entity}Controller.php
  Form/
    Northwind/
      {Entity}Type.php
templates/
  northwind/
    {entity}/
      index.html.twig
      new.html.twig
      show.html.twig
      edit.html.twig
      _delete_form.html.twig
      _form.html.twig
```

## Notlar

1. **Template Base**: Tüm Northwind template'leri `northwind_base.html.twig` extend eder
2. **Repository**: Her entity kendi repository'sini kullanır ve `northwind` connection'ını kullanır
3. **Form Fields**: Otomatik oluşturulan formlarda tüm entity field'ları yer alır
4. **CSRF Protection**: Tüm delete işlemlerinde CSRF token koruması aktiftir

## Troubleshooting

### Entity Bulunamıyor Hatası

```
Entity "EntityName" doesn't exist
```

**Çözüm**: Entity adını `Northwind\EntityName` formatında yazın.

### Cache Hatası

**Çözüm**:

```bash
php bin/console cache:clear
composer dump-autoload
```

### Entity Manager Hatası

**Çözüm**: Controller'da `ManagerRegistry` kullanın ve `getManager('northwind')` ile northwind connection'ını alın.
