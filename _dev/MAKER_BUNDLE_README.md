# Symfony MakerBundle Rehberi

## Genel Bakış

Symfony MakerBundle, boilerplate kod yazmanızı önleyen bir geliştirme aracıdır. Boş command'lar, controller'lar, form sınıfları, testler ve daha fazlasını oluşturmanıza yardımcı olur.

## Kurulum

```bash
composer require --dev symfony/maker-bundle
```

⚠️ **Not:** Bu bundle sadece development ortamı için kurulur (`--dev` flag'i ile).

## Temel Kullanım

### Tüm Make Komutlarını Listele

```bash
php bin/console list make
```

**Çıktı:**

```
make:command            Yeni console command sınıfı oluşturur
make:controller         Yeni controller sınıfı oluşturur
make:entity             Yeni Doctrine entity sınıfı oluşturur
make:form               Yeni form sınıfı oluşturur
make:validator          Yeni validator ve constraint sınıfı oluşturur
make:voter              Yeni security voter sınıfı oluşturur
make:crud               Entity için tam CRUD oluşturur
make:migration          Doctrine migration oluşturur
...
```

### Komut Yardımı

Her komut için detaylı yardım:

```bash
php bin/console make:controller --help
php bin/console make:entity --help
php bin/console make:crud --help
```

## Önemli Komutlar

### 1. Entity Oluşturma

```bash
php bin/console make:entity
```

**İnteraktif Mod:**

-   Entity adını sorar
-   Field'ları tek tek eklemenizi sağlar
-   Her field için tip, uzunluk, nullable vb. sorar

**Namespace ile:**

```bash
php bin/console make:entity Northwind/Products
php bin/console make:entity Blog/Article
```

**Gereksinimler:**

-   ⚠️ `doctrine/orm` yüklü ve yapılandırılmış olmalı
-   Sadece ORM desteklenir, ODM desteklenmez

### 2. CRUD Oluşturma

```bash
php bin/console make:crud
```

**Namespace ile:**

```bash
php bin/console make:crud Northwind\Products
php bin/console make:crud Blog\Article
```

**Oluşturduğu Dosyalar:**

-   Controller (index, new, show, edit, delete)
-   Form Type
-   Templates (index, new, show, edit, \_form, \_delete_form)

### 3. Controller Oluşturma

```bash
php bin/console make:controller
```

**Örnek:**

```bash
php bin/console make:controller ProductController
php bin/console make:controller Admin/DashboardController
```

### 4. Form Oluşturma

```bash
php bin/console make:form
```

**Entity ile bağlantılı form:**

```bash
php bin/console make:form ProductType Product
```

### 5. Command Oluşturma

```bash
php bin/console make:command
```

**Örnek:**

```bash
php bin/console make:command app:send-emails
php bin/console make:command ImportDataCommand
```

### 6. Migration Oluşturma

```bash
php bin/console make:migration
```

Entity değişikliklerini tespit eder ve migration dosyası oluşturur.

### 7. Voter Oluşturma (Security)

```bash
php bin/console make:voter
```

**Örnek:**

```bash
php bin/console make:voter PostVoter
```

### 8. Validator Oluşturma

```bash
php bin/console make:validator
```

**Örnek:**

```bash
php bin/console make:validator IsValidEmailValidator
```

### 9. Repository Oluşturma

```bash
php bin/console make:entity --regenerate
```

Var olan entity'ler için repository'leri yeniden oluşturur.

### 10. Test Oluşturma

```bash
php bin/console make:test
```

**Seçenekler:**

-   TestCase (Unit Test)
-   KernelTestCase (Integration Test)
-   WebTestCase (Functional Test)
-   ApiTestCase (API Test)

## Yapılandırma

### Temel Yapılandırma

```yaml
# config/packages/maker.yaml
when@dev:
    maker:
        root_namespace: "App"
        generate_final_classes: true
        generate_final_entities: false
```

### root_namespace

**Varsayılan:** `App`

Tüm oluşturulan sınıflar için kök namespace.

```yaml
# Değiştirirseniz:
root_namespace: "Acme"
# Sonuç:
# App\Entity\Product → Acme\Entity\Product
# App\Controller\ProductController → Acme\Controller\ProductController
```

### generate_final_classes

**Varsayılan:** `true` (Symfony 1.61+)

Tüm oluşturulan sınıfları `final` keyword ile oluşturur (entity'ler hariç).

```php
// generate_final_classes: true
final class MyVoter
{
    // ...
}

// generate_final_classes: false
class MyVoter
{
    // ...
}
```

**Ne işe yarar:**

-   Inheritance'ı engeller
-   Daha iyi performans
-   Kod güvenliği

### generate_final_entities

**Varsayılan:** `false` (Symfony 1.61+)

Doctrine entity'lerini `final` keyword ile oluşturur.

```php
// generate_final_entities: true
#[ORM\Entity(repositoryClass: TaskRepository::class)]
final class Task
{
    // ...
}
```

⚠️ **Dikkat:** Entity'leri final yapmak bazı durumlarda Doctrine proxy işlemlerini etkileyebilir.

## PHP-CS-Fixer Entegrasyonu

MakerBundle, oluşturulan PHP dosyaları için otomatik kod formatlaması yapar.

### Özel PHP-CS-Fixer Kullanma

**Environment Variables:**

```bash
# Windows (PowerShell)
$env:MAKER_PHP_CS_FIXER_BINARY_PATH="tools/vendor/bin/php-cs-fixer"
$env:MAKER_PHP_CS_FIXER_CONFIG_PATH=".php-cs-fixer.config.php"

# Linux/Mac
export MAKER_PHP_CS_FIXER_BINARY_PATH="tools/vendor/bin/php-cs-fixer"
export MAKER_PHP_CS_FIXER_CONFIG_PATH=".php-cs-fixer.config.php"
```

**Global Olarak Ayarlama:**

Windows'ta sistem environment variables'ına ekleyin:

```
MAKER_PHP_CS_FIXER_BINARY_PATH = C:\tools\php-cs-fixer.phar
MAKER_PHP_CS_FIXER_CONFIG_PATH = C:\projects\.php-cs-fixer.config.php
```

## Özel Maker Komutları Oluşturma

### Kendi Make Komutunuzu Yazın

**1. Maker Sınıfı Oluştur:**

```php
// src/Maker/MakeCustomCommand.php
namespace App\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

final class MakeCustomCommand extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:custom';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a custom class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'Custom class name')
            ->setHelp('This command creates a custom class');
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $nameArgument = $input->getArgument('name');

        $generator->generateClass(
            'App\\Custom\\' . $nameArgument,
            __DIR__ . '/../../templates/maker/Custom.tpl.php',
            [
                'class_name' => $nameArgument,
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // Gerekli bağımlılıklar varsa ekleyin
    }
}
```

**2. Template Oluştur:**

```php
// templates/maker/Custom.tpl.php
<?= "<?php\n" ?>

namespace <?= $namespace ?>;

class <?= $class_name ?>

{
    public function __construct()
    {
        // Constructor
    }
}
```

**3. Servisi Kaydet:**

```yaml
# config/services.yaml
services:
    # ...

    App\Maker\:
        resource: "../src/Maker/*"
        tags: ["maker.command"]
```

**Otomatik Servis Kaydı:**

Standart `services.yaml` yapılandırması kullanıyorsanız, `src/Maker/` altındaki tüm sınıflar otomatik olarak kaydedilir.

**4. Kullanım:**

```bash
php bin/console make:custom MyCustomClass
```

## En İyi Pratikler

### 1. Namespace Organizasyonu

```bash
# İyi Organizasyon
php bin/console make:entity Northwind/Products
php bin/console make:entity Blog/Post
php bin/console make:entity Admin/User

# Sonuç:
# src/Entity/Northwind/Products.php
# src/Entity/Blog/Post.php
# src/Entity/Admin/User.php
```

### 2. CRUD Oluşturma Sırası

```bash
# 1. Entity oluştur
php bin/console make:entity Northwind/Products

# 2. Migration oluştur
php bin/console make:migration

# 3. Migration'ı çalıştır
php bin/console doctrine:migrations:migrate

# 4. CRUD oluştur
php bin/console make:crud Northwind\Products

# 5. Cache temizle
php bin/console cache:clear
```

### 3. Test İle Birlikte Oluşturma

```bash
# Controller ile test oluştur
php bin/console make:controller ProductController
php bin/console make:test ProductControllerTest

# Entity ile functional test
php bin/console make:entity Product
php bin/console make:test ProductTest
```

### 4. Form ve Entity Birlikte

```bash
# Entity oluştur
php bin/console make:entity Product

# Form oluştur
php bin/console make:form ProductType Product
```

## Sık Karşılaşılan Hatalar ve Çözümleri

### Hata: "Entity doesn't exist"

**Çözüm:**

```bash
# Namespace'i doğru yazın
php bin/console make:crud Northwind\Products  # ✓ Doğru
php bin/console make:crud App\Entity\Northwind\Products  # ✗ Yanlış

# Cache temizle
php bin/console cache:clear
composer dump-autoload
```

### Hata: "Class already exists"

**Çözüm:**

```bash
# Dosyayı manuel sil veya farklı isim kullan
rm src/Controller/ProductController.php
php bin/console make:controller ProductController
```

### Hata: "doctrine/orm is required"

**Çözüm:**

```bash
# Doctrine ORM'i yükle
composer require symfony/orm-pack
composer require symfony/maker-bundle --dev
```

### Hata: "Unable to write to directory"

**Çözüm:**

```bash
# Klasör izinlerini kontrol et (Linux/Mac)
chmod -R 755 src/
chmod -R 755 config/

# Windows'ta yönetici olarak çalıştır
```

## Komut Referansı

| Komut                    | Açıklama                    | Örnek                 |
| ------------------------ | --------------------------- | --------------------- |
| `make:command`           | Console command oluşturur   | `app:import-data`     |
| `make:controller`        | Controller oluşturur        | `ProductController`   |
| `make:crud`              | CRUD oluşturur              | `Northwind\Products`  |
| `make:entity`            | Entity oluşturur            | `Northwind/Products`  |
| `make:form`              | Form type oluşturur         | `ProductType`         |
| `make:migration`         | Migration oluşturur         | -                     |
| `make:validator`         | Validator oluşturur         | `EmailValidator`      |
| `make:voter`             | Security voter oluşturur    | `PostVoter`           |
| `make:test`              | Test oluşturur              | `ProductTest`         |
| `make:subscriber`        | Event subscriber oluşturur  | `ExceptionSubscriber` |
| `make:twig-extension`    | Twig extension oluşturur    | `AppExtension`        |
| `make:user`              | User entity oluşturur       | `User`                |
| `make:registration-form` | Registration form oluşturur | -                     |
| `make:auth`              | Authentication oluşturur    | -                     |

## İpuçları

### 1. Non-Interactive Mod

```bash
# Prompt'ları atla
php bin/console make:entity --no-interaction Product
```

### 2. Overwrite Modu

```bash
# Var olan dosyaları üzerine yaz
php bin/console make:controller ProductController --overwrite
```

### 3. Help Her Zaman Arkadaşınız

```bash
php bin/console make:crud --help
php bin/console make:entity --help
```

### 4. Batch İşlemler

```powershell
# PowerShell
$entities = @('Products', 'Categories', 'Orders')
foreach ($entity in $entities) {
    php bin/console make:crud "Northwind\$entity"
}
```

```bash
# Bash
for entity in Products Categories Orders; do
    php bin/console make:crud "Northwind\\$entity"
done
```

## Kaynaklar

-   **Resmi Dokümantasyon:** https://symfony.com/doc/current/bundles/SymfonyMakerBundle/index.html
-   **GitHub Repository:** https://github.com/symfony/maker-bundle
-   **PHP Final Keyword:** https://www.php.net/manual/en/language.oop5.final.php
-   **Doctrine ORM:** https://www.doctrine-project.org/projects/orm.html

## Notlar

-   ⚠️ MakerBundle sadece development ortamında kullanılır
-   ⚠️ Production'a deploy etmeyin (`--dev` ile kurulu olduğu için zaten gitmez)
-   ✓ Oluşturulan kodu her zaman gözden geçirin
-   ✓ Test yazımı için make:test kullanın
-   ✓ Kod standartları için PHP-CS-Fixer yapılandırın
