# Symfony Entity Terminal Komutları

## Entity Oluşturma

### Yeni Entity Oluştur

```bash
php bin/console make:entity
```

İnteraktif mod ile yeni entity oluşturur. Her field için tip, uzunluk, nullable gibi özellikleri sorar.

### Northwind Namespace'inde Entity Oluştur

```bash
php bin/console make:entity Northwind/EntityName
```

**Örnekler:**

```bash
php bin/console make:entity Northwind/Products
php bin/console make:entity Northwind/Categories
php bin/console make:entity Northwind/Customers
```

### Var Olan Entity'ye Field Ekle

```bash
php bin/console make:entity Northwind/ExistingEntity
```

Aynı komutu kullanarak mevcut entity'ye yeni field'lar ekleyebilirsiniz.

## Database'den Entity Oluşturma (Reverse Engineering)

### Mapping Import (Annotation)

```bash
php bin/console doctrine:mapping:import "App\Entity\Northwind" annotation --path=src/Entity/Northwind
```

### Entity Generate (Deprecated - PHP 8 Attributes Önerilir)

```bash
# Doctrine DBAL üzerinden schema'yı al
php bin/console doctrine:schema:create --dump-sql

# Tüm tabloları entity olarak generate et
php bin/console make:entity --regenerate App\\Entity\\Northwind
```

## Migration İşlemleri

### Migration Oluştur

```bash
php bin/console make:migration
```

Entity değişikliklerini tespit eder ve migration dosyası oluşturur.

### Migration'ları Çalıştır

```bash
php bin/console doctrine:migrations:migrate
```

### Migration Status

```bash
php bin/console doctrine:migrations:status
```

### Son Migration'ı Geri Al

```bash
php bin/console doctrine:migrations:migrate prev
```

### Belirli Bir Version'a Git

```bash
php bin/console doctrine:migrations:migrate Version20251110000000
```

## Schema İşlemleri

### Schema Update (Production'da Kullanma!)

```bash
# Sadece SQL'i göster
php bin/console doctrine:schema:update --dump-sql

# Schema'yı güncelle (development only)
php bin/console doctrine:schema:update --force
```

### Schema Validate

```bash
php bin/console doctrine:schema:validate
```

Entity mapping ile database schema'nın uyumlu olup olmadığını kontrol eder.

## Entity ve Database Bilgileri

### Entity Listesi

```bash
php bin/console doctrine:mapping:info
```

### Entity Detayları

```bash
php bin/console doctrine:query:sql "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'northwind'"
```

### Database Connection Test

```bash
php bin/console doctrine:query:sql "SELECT 1"
```

## Northwind Multi-Database İçin Özel Komutlar

### Northwind Connection ile Schema Validate

```bash
php bin/console doctrine:schema:validate --em=northwind
```

### Northwind Connection ile Migration

```bash
php bin/console doctrine:migrations:migrate --em=northwind
```

### Northwind Database Bilgileri

```bash
php bin/console doctrine:query:sql "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_CATALOG = 'Northwind'" --em=northwind
```

## Cache ve Autoload

### Cache Temizle

```bash
php bin/console cache:clear
```

### Composer Autoload Yenile

```bash
composer dump-autoload
```

### Doctrine Metadata Cache Temizle

```bash
php bin/console doctrine:cache:clear-metadata
php bin/console doctrine:cache:clear-query
php bin/console doctrine:cache:clear-result
```

## Entity Geliştirme Workflow

### 1. Yeni Entity Oluştur

```bash
php bin/console make:entity Northwind/NewEntity
# Field'ları interaktif olarak ekle
```

### 2. Repository Oluştur (Otomatik)

Repository otomatik oluşturulur, manuel düzenlemek için:

```php
// src/Repository/Northwind/NewEntityRepository.php
public function __construct(ManagerRegistry $registry)
{
    $entityManager = $registry->getManager('northwind');
    parent::__construct($entityManager, $entityManager->getClassMetadata(NewEntity::class));
}
```

### 3. Migration Oluştur

```bash
php bin/console make:migration
```

### 4. Migration'ı İncele

```bash
# migrations/VersionXXXXXXXXXXXXXX.php dosyasını kontrol et
```

### 5. Migration'ı Çalıştır

```bash
php bin/console doctrine:migrations:migrate
```

### 6. CRUD Oluştur

```bash
php bin/console make:crud Northwind\NewEntity
```

### 7. Cache Temizle

```bash
php bin/console cache:clear
```

## Hata Giderme

### Entity Bulunamıyor

```bash
# Composer autoload'u yenile
composer dump-autoload

# Cache temizle
php bin/console cache:clear
```

### Mapping Hatası

```bash
# Mapping'i validate et
php bin/console doctrine:schema:validate

# Metadata cache'i temizle
php bin/console doctrine:cache:clear-metadata
```

### Connection Hatası

```bash
# Connection'ı test et
php bin/console doctrine:query:sql "SELECT 1" --em=northwind

# .env dosyasını kontrol et
cat .env | grep DATABASE
```

## Yararlı Alias'lar (PowerShell)

PowerShell profile'ınıza ekleyin:

```powershell
# $PROFILE dosyasını açın: notepad $PROFILE

function sf-entity { php bin/console make:entity $args }
function sf-crud { php bin/console make:crud $args }
function sf-migration { php bin/console make:migration }
function sf-migrate { php bin/console doctrine:migrations:migrate $args }
function sf-validate { php bin/console doctrine:schema:validate $args }
function sf-cc { php bin/console cache:clear }
```

Kullanım:

```powershell
sf-entity Northwind/Products
sf-crud Northwind\Products
sf-migration
sf-migrate
```

## Notlar

-   **Northwind entity'leri** için her zaman `Northwind/` veya `Northwind\` prefix'i kullanın
-   **make:entity** komutunda `/` kullanın: `Northwind/Products`
-   **make:crud** komutunda `\` kullanın: `Northwind\Products`
-   Entity oluşturduktan sonra **mutlaka migration oluşturun**
-   Production'da `doctrine:schema:update --force` **kullanmayın**, migration kullanın
-   Composite primary key entity'ler için CRUD manuel oluşturulmalı
