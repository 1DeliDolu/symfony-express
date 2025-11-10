
---

## 🧩 1. Gerekli Paketleri Kur

Eğer henüz yüklü değilse, `MakerBundle` ve `Doctrine ORM` kur:

```bash
composer require symfony/maker-bundle --dev
composer require symfony/orm-pack
composer require doctrine/doctrine-bundle
```

---

## 🧱 2. Entity (Varlık) Oluşturma

Yeni bir Entity oluşturmak için:

```bash
php bin/console make:entity
```

Komut senden aşağıdaki bilgileri ister:

* **Entity adı:** örn. `Product`
* **Alan adları (fields):**
  * `name` (string)
  * `price` (float)
  * `description` (text)
  * `createdAt` (datetime_immutable)

📌 Örnek çıktı:

```bash
created: src/Entity/Product.php
created: src/Repository/ProductRepository.php
```

Entity otomatik olarak Repository ile birlikte gelir.

---

## 🏗️ 3. Migration Dosyası Oluştur ve Veritabanına Uygula

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

---

## ⚙️ 4. Controller Oluşturma

Yeni bir Controller oluşturmak için:

```bash
php bin/console make:controller ProductController
```

Bu komut oluşturur:

```
src/Controller/ProductController.php
templates/product/index.html.twig
```

---

## 🧮 5. CRUD (Create, Read, Update, Delete) İşlemlerini Otomatik Oluşturma

Symfony, CRUD yapısını otomatik oluşturabilir:

```bash
php bin/console make:crud Product
```

Bu komut şunları yapar:

* `src/Controller/ProductController.php` dosyasını CRUD işlemleriyle doldurur
* `templates/product/` klasörüne otomatik Twig şablonları oluşturur (`index`, `new`, `edit`, `show`)
* `ProductType` form sınıfını oluşturur (`src/Form/ProductType.php`)

📁 Oluşan yapı:

```
src/
 ├── Controller/
 │    └── ProductController.php
 ├── Entity/
 │    └── Product.php
 ├── Form/
 │    └── ProductType.php
 └── Repository/
      └── ProductRepository.php
templates/
 └── product/
      ├── index.html.twig
      ├── new.html.twig
      ├── edit.html.twig
      └── show.html.twig
```

---

## 🧰 6. CRUD Komutunun İçeriği

Oluşturulan `ProductController` içeriği yaklaşık olarak şöyledir:

```php
<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index');
    }
}
```

---

## 🧩 7. Router Kontrolü

Tüm rotaları listelemek için:

```bash
php bin/console debug:router
```

---

## ✅ Özet

| İşlem                | Komut                                           |
| ---------------------- | ----------------------------------------------- |
| Entity oluştur        | `php bin/console make:entity`                 |
| Migration oluştur     | `php bin/console make:migration`              |
| Migration çalıştır | `php bin/console doctrine:migrations:migrate` |
| Controller oluştur    | `php bin/console make:controller`             |
| CRUD oluştur          | `php bin/console make:crud`                   |
