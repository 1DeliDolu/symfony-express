
### 📘 Veritabanları ve Doctrine ORM

Symfony, veritabanlarıyla çalışmak için ihtiyaç duyulan tüm araçları sağlar. Bu araçlar, ilişkisel veritabanları (ör.  **MySQL** ,  **PostgreSQL** ) ve NoSQL veritabanları ( **MongoDB** ) için destek sunar. Doctrine kütüphanesi, PHP için en güçlü veritabanı araç takımıdır.

#### 📚 Bölümler

Bu konu üç ayrı makalede ele alınır:

1. Bu makale, Symfony uygulamalarında ilişkisel veritabanlarıyla çalışma yöntemini açıklar.
2. Düşük seviyeli SQL sorguları (PDO benzeri) çalıştırmanız gerekiyorsa başka bir makaleye bakın.
3. MongoDB ile çalışıyorsanız **DoctrineMongoDBBundle** belgelerine göz atın.

---

### ⚙️ Doctrine Kurulumu

Doctrine ORM ve MakerBundle paketlerini yükleyin:

```bash
composer require symfony/orm-pack
composer require --dev symfony/maker-bundle
```

---

### 🧩 Veritabanı Yapılandırması

Bağlantı bilgileri `.env` dosyasında bulunan `DATABASE_URL` ortam değişkeninde saklanır.

```bash
# .env (veya .env.local içinde özelleştirerek commit’ten koruyun)

DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8.0.37"
```

Alternatif olarak:

* **MariaDB:**
  ```bash
  DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=10.5.8-MariaDB"
  ```
* **SQLite:**
  ```bash
  DATABASE_URL="sqlite:///%kernel.project_dir%/var/app.db"
  ```
* **PostgreSQL:**
  ```bash
  DATABASE_URL="postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=12.19&charset=utf8"
  ```
* **Oracle:**
  ```bash
  DATABASE_URL="oci8://db_user:db_password@127.0.0.1:1521/db_name"
  ```

> 💡 Önemli: Kullanıcı adı, parola veya veritabanı adında `:/?#[]@!$&'()*+,;=` gibi özel karakterler varsa, bunları **urlencode()** ile kodlayın.

Doctrine bağlantısı ayarlandıktan sonra veritabanını oluşturabilirsiniz:

```bash
php bin/console doctrine:database:create
```

Daha fazla yapılandırma için `config/packages/doctrine.yaml` dosyasına göz atabilirsiniz. Burada `server_version` gibi değerleri (ör. `8.0.37`) belirlemek Doctrine’un çalışma şeklini etkileyebilir.

Tüm Doctrine komutlarını görmek için:

```bash
php bin/console list doctrine
```

---

### 🏗️ Entity (Varlık) Sınıfı Oluşturma

Bir **Product (Ürün)** nesnesi oluşturmak istediğinizi varsayalım. Doctrine’de bu bir “entity” sınıfıdır. `make:entity` komutuyla otomatik oluşturabilirsiniz:

```bash
php bin/console make:entity
```

Sistemin sorduğu sorulara şu şekilde yanıt verebilirsiniz:

```
Class name of the entity to create or update:
> Product

New property name:
> name

Field type [string]:
> string

Field length [255]:
> 255

Nullable? [no]:
> no

New property name:
> price

Field type [string]:
> integer

Nullable? [no]:
> no
```

Böylece aşağıdaki dosya oluşturulur:

```php
// src/Entity/Product.php
namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    // ... getter ve setter metodları
}
```

---

### 🧱 Ek Bilgiler

* **UUID/ULID kullanımı:**

  MakerBundle 1.57.0+ sürümünde `--with-uuid` veya `--with-ulid` parametreleri ile ID alanını int yerine **Uuid** veya **Ulid** türünde oluşturabilirsiniz.
* **PHP Attribute desteği:**

  MakerBundle 1.44.0 sürümünden itibaren sadece **PHP Attributes** (ör. `#[ORM\Column]`) desteklenmektedir.
* **Fiyat Alanı Neden Integer?**

  Örnek olarak gösterilmiştir. Uygulamalarda fiyatları `integer` olarak (ör. `100 = 1.00$`) saklamak  **yuvarlama hatalarını önler** .
* **MySQL 5.6 ve utf8mb4 için uyarı:**

  255 karakter uzunluğundaki `unique=true` alanlar 767 byte sınırını aşabilir. Bu durumda **maksimum uzunluğu 190 karaktere düşürün.**

---

### 🔍 Özet

Doctrine ile:

* Veritabanı bağlantısı `.env` üzerinden yapılır.
* `make:entity` komutu entity sınıflarını hızlıca oluşturur.
* Doctrine, entity sınıfındaki her özelliği tablo sütununa otomatik eşler.
* Komut satırından veritabanı oluşturma, migrate etme ve yönetme mümkündür.

---



![1762792423779](image/1_DatabasesandtheDoctrineORM/1762792423779.png)


### 🧱 Doctrine ORM – Entity, Migration ve Veri Kaydetme Rehberi

`make:entity` komutu, kod üretimini kolaylaştırmak için kullanılır. Ancak bu yalnızca bir başlangıçtır — sen daha sonra bu sınıfta alan (field) ekleyebilir, kaldırabilir, metotlar tanımlayabilir veya konfigürasyonu güncelleyebilirsin.

Doctrine birçok **alan türünü (field type)** destekler. Her türün kendine özgü seçenekleri vardır.

📖 Ayrıntılı liste için: **Doctrine Mapping Types** belgesine bakabilirsin.

Eğer **attribute** yerine **XML yapılandırması** kullanmak istersen, `config/packages/doctrine.yaml` içinde aşağıdaki gibi tanımla:

```yaml
doctrine:
    orm:
        mappings:
            App:
                type: xml
                dir: '%kernel.project_dir%/config/doctrine'
```

---

### ⚠️ SQL Anahtar Kelimelerine Dikkat

Tablo veya sütun isimlerinde **SQL’in ayrılmış (reserved)** kelimelerini kullanmaktan kaçın.

Örnek: `GROUP`, `USER` gibi kelimeler sorun çıkarabilir.

* Bu durumda tablo adını değiştirebilirsin:
  ```php
  #[ORM\Table(name: 'groups')]
  ```
* Ya da sütun adını şu şekilde belirtebilirsin:
  ```php
  #[ORM\Column(name: 'group_name')]
  ```

---

## 🧩 Migration (Veritabanı Şeması Oluşturma)

Artık `Product` sınıfın hazır, ama veritabanında `product` tablosu henüz yok.

DoctrineMigrationsBundle ile bu tabloyu oluşturabiliriz:

```bash
php bin/console make:migration
```

> 💡 MakerBundle v1.56.0 itibariyle `--formatted` parametresiyle daha okunabilir migration dosyaları oluşturabilirsin.

Oluşturma başarılıysa:

```
SUCCESS!
Next: Review the new migration "migrations/Version20211116204726.php"
Then: Run the migration with php bin/console doctrine:migrations:migrate
```

Migration dosyasını çalıştırmak için:

```bash
php bin/console doctrine:migrations:migrate
```

Bu komut, veritabanını güncelleyecek SQL sorgularını çalıştırır.

Production’a deploy yaparken de mutlaka bu komutu çalıştırmalısın.

---

## ➕ Yeni Alan (Field) Eklemek

Diyelim ki `Product` sınıfına yeni bir alan eklemek istiyorsun:  **description** .

```bash
php bin/console make:entity
```

Komut çıktısı şu şekilde olur:

```
Class name of the entity to create or update:
> Product

New property name:
> description

Field type [string]:
> text

Nullable? [no]:
> no
```

Bu, entity’ne şu satırları ekler:

```php
use Doctrine\DBAL\Types\Types;

class Product
{
    // ...

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    // getDescription() & setDescription() otomatik eklenir
}
```

Bu özellik koda eklendi ama tabloya henüz yansımadı.

Yeni migration oluştur:

```bash
php bin/console make:migration
```

Migration dosyasında aşağıdaki gibi bir SQL yer alır:

```sql
ALTER TABLE product ADD description LONGTEXT NOT NULL;
```

Ve ardından çalıştır:

```bash
php bin/console doctrine:migrations:migrate
```

> ⚠️ **SQLite kullanıyorsan** şu hatayı alabilirsin:
>
> `Cannot add a NOT NULL column with default value NULL`
>
> Çözüm: `nullable=true` ekle:
>
> ```php
> #[ORM\Column(type: Types::TEXT, nullable: true)]
> ```

Doctrine, migration sürümlerini izlemek için arka planda `migration_versions` tablosunu kullanır.

---

### 🔁 Schema Güncelleme Rutini

Her şema değişikliğinde şu iki adımı uygula:

1. Migration oluştur:
   ```bash
   php bin/console make:migration
   ```
2. Migration’ı çalıştır:
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

> 💾 Migration dosyalarını **commit etmeyi** ve deployment sonrası **çalıştırmayı** unutma.

---

## 🧠 Getter ve Setter’ları Yeniden Üretmek

Alanları manuel eklediysen ama getter/setter metodlarını oluşturmak istiyorsan:

```bash
php bin/console make:entity --regenerate
```

Var olan metotları da yenilemek istersen:

```bash
php bin/console make:entity --regenerate --overwrite
```

---

## 💾 Nesneleri Veritabanına Kaydetme

Artık `Product` entity’sini veritabanına kaydedebiliriz.

Yeni bir controller oluştur:

```bash
php bin/console make:controller ProductController
```

Ve içine şu kodu ekle:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'create_product')]
    public function createProduct(EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $product->setName('Keyboard');
        $product->setPrice(1999);
        $product->setDescription('Ergonomic and stylish!');

        // Doctrine'e kaydetmek istediğimizi belirtiyoruz (henüz sorgu yapılmaz)
        $entityManager->persist($product);

        // INSERT sorgusunu gerçekten çalıştırır
        $entityManager->flush();

        return new Response('Saved new product with id '.$product->getId());
    }
}
```

Tarayıcıdan şu adresi aç:

```
http://localhost:8000/product
```

Ve tebrikler 🎉

Artık `product` tablosunda ilk satırın oluştu!

Veritabanını kontrol etmek için:

```bash
php bin/console dbal:run-sql 'SELECT * FROM product'
```

---

### 🔍 Kod Analizi

| Satır           | Açıklama                                                                                                                |
| ---------------- | ------------------------------------------------------------------------------------------------------------------------- |
| **13**     | Symfony,`EntityManagerInterface`parametresiyle Doctrine Entity Manager nesnesini controller’a enjekte eder.            |
| **15–18** | PHP nesnesi gibi `$product`üzerinde işlem yapılır.                                                                  |
| **21**     | `persist($product)`çağrısı, Doctrine’e bu nesneyi yönetmesi gerektiğini söyler. Henüz sorgu yapılmaz.         |
| **24**     | `flush()`çağrısı, yönetilen nesneleri kontrol eder ve gerekli olanlara `INSERT`veya `UPDATE`sorgusu gönderir. |

> Eğer `flush()` başarısız olursa `Doctrine\ORM\ORMException` hatası alınır. (Bkz. Transactions and Concurrency)

Doctrine, nesnenin yeni mi güncel mi olduğunu otomatik algılar — yani aynı kod `INSERT` veya `UPDATE` işlemi için geçerlidir.

---


### ✅ Nesne Doğrulama (Validating Objects)

Symfony’nin **Validator** bileşeni, **Doctrine metadata** bilgisini kullanarak otomatik doğrulama kuralları uygulayabilir.

Bunun için `auto_mapping` seçeneği etkinleştirilmelidir. Bu sayede Symfony, entity’lerdeki alanları otomatik olarak analiz eder ve uygun kısıtlamaları ekler.

---

### ⚙️ Örnek Kullanım

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'create_product')]
    public function createProduct(ValidatorInterface $validator): Response
    {
        $product = new Product();

        // ... ürün verilerini bir form veya API'den al

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        // ... doğrulama hatası yoksa işlem devam eder
    }
}
```

Eğer `Product` entity’si `auto_mapping` listesinde tanımlanmışsa, Symfony otomatik olarak Doctrine metadata’sına göre doğrulama kuralları ekler.

Örneğin, `name` alanı veritabanında `nullable=false` olarak tanımlıysa, Symfony bu alana **NotNull** kısıtlamasını otomatik ekler.

---

### 🔗 Doctrine - Symfony Doğrulama Eşleştirmesi

| Doctrine Özelliği | Symfony Constraint | Açıklama                          |
| ------------------- | ------------------ | ----------------------------------- |
| `nullable=false`  | `NotNull`        | PropertyInfo bileşenini gerektirir |
| `type`            | `Type`           | PropertyInfo bileşenini gerektirir |
| `unique=true`     | `UniqueEntity`   |                                     |
| `length`          | `Length`         |                                     |

Bu otomatik doğrulamalar;

* **Form Component** ve
* **API Platform**

tarafından da otomatik kullanılır.

Yani hem web formları hem de API uç noktalarınızda aynı doğrulama kuralları geçerli olur.

> ⚠️ **Not:** Bu özellik üretkenliği artırır ama tam doğrulama yerine geçmez.
>
> Kullanıcı girdilerinin geçerliliğini garanti altına almak için özel  **Validation Constraint** ’leri manuel olarak eklemelisin.

---

## 🔍 Nesneleri Veritabanından Getirme (Fetching Objects)

Doctrine’de veritabanından nesne çekmek oldukça kolaydır.

Örneğin `/product/1` adresine gidildiğinde ilgili ürünü göstermek isteyelim:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/product/{id}', name: 'product_show')]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        return new Response('Check out this great product: '.$product->getName());

        // veya bir Twig şablonu render edebilirsin:
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }
}
```

---

### 🔄 Repository Kullanarak Nesne Getirme

Symfony’nin otomatik **dependency injection** (autowiring) özelliği sayesinde `ProductRepository` doğrudan controller’a enjekte edilebilir:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/product/{id}', name: 'product_show')]
    public function show(ProductRepository $productRepository, int $id): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found.');
        }

        return new Response('Product: '.$product->getName());
    }
}
```

Uygulamayı test et:

```
http://localhost:8000/product/1
```

---

## 🧭 Repository Nedir?

Bir  **repository** , belirli bir entity türünü veritabanından almakla görevli PHP sınıfıdır.

Doctrine her entity için otomatik bir repository oluşturur.

Bu repository’ler birçok yardımcı metoda sahiptir:

```php
$repository = $entityManager->getRepository(Product::class);

// ID’ye göre tek kayıt bul
$product = $repository->find($id);

// Belirli bir kritere göre tek kayıt bul
$product = $repository->findOneBy(['name' => 'Keyboard']);

// Birden fazla kriterle sorgula
$product = $repository->findOneBy([
    'name' => 'Keyboard',
    'price' => 1999,
]);

// Belirli kritere uyan birden fazla kayıt getir (fiyatına göre sırala)
$products = $repository->findBy(
    ['name' => 'Keyboard'],
    ['price' => 'ASC']
);

// Tüm ürünleri getir
$products = $repository->findAll();
```

> 💡 Ayrıca, `ProductRepository` içine kendi özel sorgu metotlarını da ekleyebilirsin (ör. `findExpensiveProducts()`).

---

### 🧰 Debug Bilgileri

Bir HTML sayfası render edildiğinde, sayfanın alt kısmındaki  **Web Debug Toolbar** :

* Çalıştırılan sorgu sayısını
* Sorguların toplam çalışma süresini

gösterir. Bu, performans izleme açısından oldukça yararlıdır. ✅

---



![1762792517197](image/1_DatabasesandtheDoctrineORM/1762792517197.png)


### 🧠 Symfony – Doctrine **EntityValueResolver** (Otomatik Nesne Getirme)

Symfony, veritabanı nesnelerini (entity) **otomatik olarak** controller metoduna enjekte edebilir.

Bu özellik **DoctrineBundle 2.7.1** sürümüyle gelen **EntityValueResolver** sayesinde kullanılabilir.

---

## 🎯 Web Debug Toolbar ve Profiler

Symfony Profiler, sorguların sayısını ve sürelerini gösterir.

Eğer yapılan sorgu sayısı çok fazlaysa, toolbar’daki veritabanı ikonu **sarıya döner** ⚠️ — bu, performansla ilgili bir uyarıdır.

Profiler’a tıklayarak:

* Çalıştırılan SQL sorgularını,
* Sorgu sürelerini,
* Sorgu detaylarını

görebilirsin.

> Eğer toolbar görünmüyorsa şu paketi yükle:
>
> ```bash
> composer require --dev symfony/profiler-pack
> ```

📖 Ayrıntılı bilgi: [Symfony Profiler belgeleri](https://symfony.com/doc/current/profiler.html)

---

## ⚙️ EntityValueResolver ile Otomatik Getirme

Birçok durumda artık `find()` veya `findOneBy()` çağrısı yazmana gerek yok!

Symfony, route parametrelerini kullanarak entity’yi otomatik sorgular.

### Örnek

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/product/{id}')]
    public function show(Product $product): Response
    {
        // Entity otomatik olarak veritabanından getirilir
        return new Response('Product: '.$product->getName());
    }
}
```

🔹 `Product` entity’si, `{id}` route parametresiyle otomatik olarak sorgulanır.

🔹 Eğer kayıt bulunmazsa Symfony otomatik olarak **404** hatası döndürür.

---

## 🚫 404 Davranışını Özelleştirme

Entity bulunamadığında 404 döndürülmesini istemiyorsan, argümanı **opsiyonel** hale getir:

```php
#[Route('/product/{id}')]
public function show(?Product $product): Response
{
    if ($product === null) {
        // Kendi özel yanıtını oluştur
        return new Response('Product not found', 404);
    }

    // ...
}
```

---

## 🧩 Özelliği Devre Dışı Bırakma

Global olarak etkinse bile, belirli bir controller için devre dışı bırakabilirsin:

```php
public function show(
    #[CurrentUser]
    #[MapEntity(disabled: true)]
    User $user
): Response {
    // EntityValueResolver bu argümanı çözümlemez
}
```

---

## 🔁 Otomatik Getirme Türleri

Symfony, route parametreleriyle entity özelliklerini  **otomatik eşleştirir** .

### 1️⃣ ID’ye göre getirme

```php
#[Route('/product/{id}')]
public function showByPk(Product $product): Response
{
}
```

### 2️⃣ Slug’a göre getirme

```php
#[Route('/product/{slug:product}')]
public function showBySlug(Product $product): Response
{
}
```

Bu durumda:

* `{id}` varsa `find()` metodu çalışır.
* Diğer route parametreleri entity özellikleriyle eşleşiyorsa `findOneBy()` kullanılır.
* `{slug:product}` ifadesi, route parametresi `slug`’u `$product` argümanına bağlar ve `slug` alanına göre arama yapılır.

> 🧱 Route parametre eşleme özelliği **Symfony 7.1** sürümünde eklenmiştir.

---

## ⚙️ MapEntity Attribute ile Özelleştirme

Otomatik eşleme yetersizse, `#[MapEntity]` attribute’u ile sorgu davranışını kontrol edebilirsin.

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\Product;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/product/{slug}')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Product $product
    ): Response {
        return new Response('Product: '.$product->getName());
    }
}
```

---

## 🧮 Expression ile Getirme

Daha karmaşık sorgular gerekiyorsa, **ExpressionLanguage** bileşenini kullanarak dinamik ifadeler tanımlayabilirsin:

```php
#[Route('/product/{product_id}')]
public function show(
    #[MapEntity(expr: 'repository.find(product_id)')]
    Product $product
): Response {
}
```

Burada:

* `repository` → entity’nin repository sınıfıdır.
* Route parametreleri (`{product_id}` vb.) değişken olarak kullanılabilir.

### Birden fazla nesne getirme örneği:

```php
#[Route('/posts_by/{author_id}')]
public function authorPosts(
    #[MapEntity(class: Post::class, expr: 'repository.findBy({"author": author_id}, {}, 10)')]
    iterable $posts
): Response {
}
```

> 🔸 Liste türü çözümleme özelliği **Symfony 7.1** sürümünde eklenmiştir.

---

## 🧠 Çoklu Entity Çözümleme

Aynı route içinde birden fazla entity çözümlemesi yapılabilir:

```php
#[Route('/product/{id}/comments/{comment_id}')]
public function show(
    Product $product,
    #[MapEntity(expr: 'repository.find(comment_id)')]
    Comment $comment
): Response {
}
```

Burada:

* `$product` otomatik olarak çözülür.
* `$comment` ise `MapEntity` ile özel olarak belirtilmiştir.

---

## 📥 Request Bilgilerini Kullanma

Route parametreleri dışında, **request query parametreleri** de ifadede kullanılabilir:

```php
#[Route('/product/{id}/comments')]
public function show(
    Product $product,
    #[MapEntity(expr: 'repository.findOneBy({"product": id}, {"createdAt": request.query.get("sort", "DESC")})')]
    Comment $comment
): Response {
}
```

Bu örnekte `?sort=ASC` veya `?sort=DESC` sorgu parametresiyle farklı yorumlar getirilir.

---

## 🧩 Arayüz Üzerinden Entity Çözümleme

Eğer `Product` sınıfın bir **interface** (ör. `ProductInterface`) uyguluyorsa, controller’ı entity sınıfına doğrudan bağımlı hale getirmeden çalıştırabilirsin.

Bunun için `resolve_target_entities` seçeneğini tanımla.

Sonrasında controller’da şu şekilde kullanabilirsin:

```php
public function show(
    #[MapEntity]
    ProductInterface $product
): Response {
    // ...
}
```

> ⚙️ Bu özellik **Symfony 7.3** sürümünde eklenmiştir.

---

## ⚙️ MapEntity Attribute Seçenekleri

| Seçenek          | Açıklama                                                                |
| ----------------- | ------------------------------------------------------------------------- |
| `id`            | Route parametresini primary key olarak kullanır.                         |
| `mapping`       | Route parametrelerini Doctrine property isimleriyle eşleştirir.         |
| `stripNull`     | `findOneBy()`sorgusunda `null`değerleri hariç tutar.                |
| `objectManager` | Varsayılan yerine özel Doctrine manager’ı kullanır.                  |
| `evictCache`    | Cache yerine her zaman veritabanından fetch eder.                        |
| `disabled`      | Bu argüman için EntityValueResolver’ı devre dışı bırakır.        |
| `message`       | Entity bulunamazsa (development ortamında) özel hata mesajı gösterir. |

### Örnekler:

#### 🔹 id parametresiyle:

```php
#[Route('/product/{product_id}')]
public function show(
    #[MapEntity(id: 'product_id')]
    Product $product
): Response {
}
```

#### 🔹 mapping ile:

```php
#[Route('/product/{category}/{slug}/comments/{comment_slug}')]
public function show(
    #[MapEntity(mapping: ['category' => 'category', 'slug' => 'slug'])]
    Product $product,
    #[MapEntity(mapping: ['comment_slug' => 'slug'])]
    Comment $comment
): Response {
}
```

#### 🔹 Özel mesajla:

```php
#[Route('/product/{product_id}')]
public function show(
    #[MapEntity(id: 'product_id', message: 'The product does not exist')]
    Product $product
): Response {
}
```

> 💬 `message` seçeneği **Symfony 7.1** sürümünde eklenmiştir.

---

### 🧩 Özet

* 🔹 **EntityValueResolver** otomatik olarak entity’leri route parametrelerine göre getirir.
* 🔹 Elle `find()` çağırmaya gerek kalmaz.
* 🔹 `MapEntity` ile davranış detaylı şekilde özelleştirilebilir.
* 🔹 **ExpressionLanguage** kullanarak dinamik sorgular yazılabilir.
* 🔹 Symfony 7.1 ve sonrası sürümlerle birlikte  **liste çözümleme** , **route parametre eşleme** ve **özel mesaj** desteği vardır.

---


### 🧱 Doctrine ORM – Nesneleri Güncelleme, Silme ve Sorgulama

Doctrine ile veritabanı nesneleriyle çalışmak PHP nesneleriyle çalışmak kadar basittir.

Aşağıda sırasıyla  **güncelleme** , **silme** ve **sorgulama** işlemleri anlatılmaktadır.

---

## ✏️ Bir Nesneyi Güncelleme (Updating an Object)

Doctrine’den bir nesne getirildikten sonra, onu normal bir PHP nesnesi gibi güncelleyebilirsin.

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/product/edit/{id}', name: 'product_edit')]
    public function update(EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $product->setName('New product name!');
        $entityManager->flush();

        return $this->redirectToRoute('product_show', [
            'id' => $product->getId()
        ]);
    }
}
```

### 🔍 Doctrine’de güncelleme üç adımda yapılır:

1. **Nesneyi getir:** `$entityManager->getRepository()->find($id)`
2. **Nesne üzerinde değişiklik yap:** `$product->setName('...')`
3. **Veritabanını güncelle:** `$entityManager->flush()`

> `persist()` çağırmana gerek yoktur; Doctrine zaten bu nesneyi “izlemektedir”.

---

## ❌ Bir Nesneyi Silme (Deleting an Object)

Bir nesneyi silmek, güncelleme kadar basittir:

```php
$entityManager->remove($product);
$entityManager->flush();
```

🧩 `remove()` metodu Doctrine’e bu nesnenin silinmesi gerektiğini bildirir.

Veritabanındaki `DELETE` sorgusu, **`flush()` çağrıldığında** gerçekten çalıştırılır.

---

## 🔎 Nesne Sorgulama (Querying for Objects)

Repository nesneleri, entity’leri veritabanından sorgulamak için kullanılır.

### Basit örnek:

```php
// Controller içinde
$repository = $entityManager->getRepository(Product::class);
$product = $repository->find($id);
```

Doctrine, entity oluşturulduğunda **otomatik bir repository sınıfı** da üretir:

```php
// src/Repository/ProductRepository.php
namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
}
```

---

## 🧮 Özel Sorgular – DQL (Doctrine Query Language)

Doctrine, SQL’e benzeyen ama PHP entity’leriyle çalışan **DQL** sorgularını destekler.

Örneğin, fiyatı belirli bir değerden büyük olan ürünleri bulmak için:

```php
// src/Repository/ProductRepository.php
namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function findAllGreaterThanPrice(int $price): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
             FROM App\Entity\Product p
             WHERE p.price > :price
             ORDER BY p.price ASC'
        )->setParameter('price', $price);

        // Product nesnelerinin bir dizisini döner
        return $query->getResult();
    }
}
```

Ve controller’dan şu şekilde çağrılır:

```php
$minPrice = 1000;
$products = $entityManager
    ->getRepository(Product::class)
    ->findAllGreaterThanPrice($minPrice);
```

> 💡 DQL, SQL'e benzer ama tablo isimleri yerine **PHP sınıf isimleri** ve property’ler kullanır.

---

## 🧱 Query Builder ile Sorgulama

Doctrine, dinamik olarak sorgular oluşturmak için **QueryBuilder** adında nesne tabanlı bir API sunar.

```php
// src/Repository/ProductRepository.php
namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ProductRepository extends ServiceEntityRepository
{
    public function findAllGreaterThanPrice(int $price, bool $includeUnavailableProducts = false): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.price > :price')
            ->setParameter('price', $price)
            ->orderBy('p.price', 'ASC');

        if (!$includeUnavailableProducts) {
            $qb->andWhere('p.available = TRUE');
        }

        $query = $qb->getQuery();

        return $query->execute();

        // Sadece bir sonuç almak için:
        // return $query->setMaxResults(1)->getOneOrNullResult();
    }
}
```

🔹 `createQueryBuilder('p')` → `Product` tablosu için “p” kısaltmasını (alias) oluşturur.

🔹 Dinamik koşullar (`if`, `switch`, vs.) kullanarak sorguyu PHP içinde kontrol edebilirsin.

---

## 💾 SQL ile Sorgulama (Raw SQL)

Doctrine DQL veya QueryBuilder yerine **doğrudan SQL** kullanmanı da sağlar.

```php
// src/Repository/ProductRepository.php
namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ProductRepository extends ServiceEntityRepository
{
    public function findAllGreaterThanPrice(int $price): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM product p
            WHERE p.price > :price
            ORDER BY p.price ASC
        ';

        $resultSet = $conn->executeQuery($sql, ['price' => $price]);

        // Ham veri döner (Product nesnesi değil)
        return $resultSet->fetchAllAssociative();
    }
}
```

> ⚠️ Bu yöntemle dönen sonuçlar  **array** ’dir, entity nesnesi değildir.
>
> Eğer entity istiyorsan, Doctrine’in **NativeQuery** özelliğini kullan.

---

## ⚙️ Ek Bilgi ve Kaynaklar

* 🔧 **Doctrine yapılandırması:**

  Ayrıntılı referans için [Doctrine Config Reference](https://symfony.com/doc/current/reference/configuration/doctrine.html)
* 🔗 **İlişkiler (Relations):**

  Doctrine, `ManyToOne`, `OneToMany`, `OneToOne`, `ManyToMany` ilişkilerini destekler.

  Detaylı bilgi: [How to Work with Doctrine Associations / Relations](https://symfony.com/doc/current/doctrine/associations.html)
* 🧪 **Veritabanı Testleri:**

  Doctrine ile veritabanına dokunan kodların nasıl test edileceğini öğren:

  [Testing code that interacts with the database](https://symfony.com/doc/current/testing/database.html)
* 🕒 **Doctrine Extensions:**

  Doctrine topluluğu, sık kullanılan özellikler için eklentiler (ör.  **Timestampable** ,  **Translatable** ) geliştirmiştir.

  Bu eklentileri Symfony’ye entegre etmek için:

  ```bash
  composer require stof/doctrine-extensions-bundle
  ```

---

### 🧩 Özet

| İşlem                     | Adımlar                                          | Not                                                  |
| --------------------------- | ------------------------------------------------- | ---------------------------------------------------- |
| **Güncelleme**       | 1️⃣ find() → 2️⃣ değiştir → 3️⃣ flush() | persist() gerekmez                                   |
| **Silme**             | remove() → flush()                               | DELETE sorgusu flush’ta çalışır                 |
| **Sorgulama**         | DQL / QueryBuilder / SQL                          | Nesne veya ham veri döner                           |
| **Custom Repository** | Kendi sorgularını tanımla                      | Doctrine’in ServiceEntityRepository’sini genişlet |
