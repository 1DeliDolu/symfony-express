# Yönlendirme (Routing)

*Bu sayfayı düzenle (Edit this page)*

Uygulamanız bir istek aldığında, yanıtı üretmek için bir controller action’ını çağırır. Yönlendirme (routing) yapılandırması, her gelen URL için hangi action’ın çalıştırılacağını tanımlar. Ayrıca, SEO dostu URL’ler üretmek (ör. `index.php?article_id=57` yerine `/read/intro-to-symfony`) gibi başka yararlı özellikler de sağlar.

## Rotaların Oluşturulması

Rotalar  **YAML** ,  **XML** , **PHP** veya **attribute** kullanılarak yapılandırılabilir. Tüm formatlar aynı özellikleri ve performansı sağlar; dolayısıyla istediğinizi seçebilirsiniz. Symfony, route ve controller’ı aynı yerde tutmak pratik olduğundan **attribute** kullanımını önerir.

## Rotaları Attribute Olarak Oluşturma

PHP attribute’ları, rotaları bu rotalarla ilişkili controller kodunun yanına tanımlamanıza izin verir.

Bunları kullanmadan önce projenize biraz yapılandırma eklemeniz gerekir. Projeniz Symfony Flex kullanıyorsa, bu dosya zaten oluşturulmuştur. Aksi takdirde aşağıdaki dosyayı elle oluşturun:

```yaml
# config/routes/attributes.yaml
controllers:
    resource:
        path: ../../src/Controller/
        namespace: App\Controller
    type: attribute

kernel:
    resource: App\Kernel
    type: attribute
```

Bu yapılandırma, Symfony’ye `App\Controller` namespace’i altında ve **PSR-4** standardına uyan `src/Controller/` dizinindeki sınıflarda **attribute** olarak tanımlanmış rotaları aramasını söyler. Kernel de bir controller gibi davranabilir; bu, özellikle Symfony’yi mikro framework olarak kullanan küçük uygulamalar için faydalıdır.

Uygulamanızda `/blog` URL’si için bir rota tanımlamak istediğinizi varsayalım. Bunu yapmak için aşağıdaki gibi bir controller sınıfı oluşturun:

```php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog_list')]
    public function list(): Response
    {
        // ...
    }
}
```

Bu yapılandırma, kullanıcı `/blog` URL’sini istediğinde eşleşen `blog_list` adlı bir rota tanımlar. Eşleşme gerçekleştiğinde uygulama `BlogController` sınıfının `list()` metodunu çalıştırır.

> Bir URL’nin **query string** kısmı rota eşleşmesinde dikkate alınmaz. Bu örnekte `/blog?foo=bar` ve `/blog?foo=bar&bar=foo` gibi URL’ler de `blog_list` rotasıyla eşleşir.

Aynı dosyada birden fazla PHP sınıfı tanımlarsanız, Symfony yalnızca **ilk** sınıfın rotalarını yükler, diğer tüm rotaları görmezden gelir.

Rota adı (`blog_list`) şu an için önemli değildir; ancak URL üretimi yaparken kritik olacaktır. Uygulamadaki her rota adının **benzersiz** olması gerektiğini unutmayın.

## Rotaları YAML, XML veya PHP Dosyalarında Oluşturma

Rotaları controller sınıflarında tanımlamak yerine, ayrı bir  **YAML** , **XML** veya **PHP** dosyasında tanımlayabilirsiniz. Başlıca avantajı ek bir bağımlılık gerektirmemesidir. Dezavantajı ise, bir controller action’ının yönlendirmesini incelerken birden fazla dosyayla çalışmanız gerekmesidir.

Aşağıdaki örnek, `BlogController::list()` action’ını `/blog` URL’siyle ilişkilendiren `blog_list` adlı bir rotanın YAML/XML/PHP ile nasıl tanımlanacağını gösterir:

```php
// config/routes.php
use App\Controller\BlogController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('blog_list', '/blog')
        // controller değeri [controller_class, method_name] formatındadır
        ->controller([BlogController::class, 'list'])

        // eğer action, controller sınıfının __invoke() metodu olarak
        // uygulanmışsa 'method_name' kısmını atlayabilirsiniz:
        // ->controller(BlogController::class)
    ;
};
```

Varsayılan olarak Symfony, YAML ve PHP formatlarında tanımlanmış rotaları yükler. Rotaları **XML** formatında tanımlarsanız, `src/Kernel.php` dosyasını güncellemeniz gerekir.

## HTTP Metotlarını Eşleştirme

Varsayılan olarak rotalar tüm HTTP fiilleriyle (GET, POST, PUT, vb.) eşleşir. Her rotanın yanıt vereceği fiilleri sınırlamak için `methods` seçeneğini kullanın:

```php
// config/routes.php
use App\Controller\BlogApiController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('api_post_show', '/api/posts/{id}')
        ->controller([BlogApiController::class, 'show'])
        ->methods(['GET', 'HEAD'])
    ;
    $routes->add('api_post_edit', '/api/posts/{id}')
        ->controller([BlogApiController::class, 'edit'])
        ->methods(['PUT'])
    ;
};
```

HTML formları yalnızca **GET** ve **POST** metotlarını destekler. Bir HTML formundan farklı bir metot ile rotayı çağırıyorsanız, kullanılacak metodu belirten **_method** adlı gizli bir alan ekleyin (ör. `<input type="hidden" name="_method" value="PUT">`). Formlarınızı Symfony Forms ile oluşturursanız, `framework.http_method_override` seçeneği **true** olduğunda bu işlem sizin için otomatik yapılır.

## Ortamları (Environments) Eşleştirme

Geçerli yapılandırma ortamı belirtilen değerle eşleştiğinde bir rotayı kaydetmek için `env` seçeneğini kullanın:

```php
// config/routes.php
use App\Controller\DefaultController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    if('dev' === $routes->env()) {
        $routes->add('tools', '/tools')
            ->controller([DefaultController::class, 'developerTools'])
        ;
    }
};
```

## Eşleştirme İfadeleri (Matching Expressions)

Rotaların bazı keyfi eşleştirme mantıklarına göre eşleşmesi gerekiyorsa `condition` seçeneğini kullanın:

```php
// config/routes.php
use App\Controller\DefaultController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('contact', '/contact')
        ->controller([DefaultController::class, 'contact'])
        ->condition('context.getMethod() in ["GET", "HEAD"] and request.headers.get("User-Agent") matches "/firefox/i"')
        // ifadeler yapılandırma parametrelerini de içerebilir:
        // ->condition('request.headers.get("User-Agent") matches "%app.allowed_browsers%"')
        // ifadeler ortam değişkenlerini de kullanabilir:
        // ->condition('context.getHost() == env("APP_MAIN_HOST")')
    ;
    $routes->add('post_show', '/posts/{id}')
        ->controller([DefaultController::class, 'showPost'])
        // ifadeler "params" değişkenini kullanarak rota parametre değerlerini alabilir
        ->condition('params["id"] < 1000')
    ;
};
```

`condition` seçeneğinin değeri, geçerli **expression language** söz dizimini kullanan bir ifadedir ve Symfony tarafından oluşturulan şu değişkenleri kullanabilir:

* **context**

  Route eşleştirmesiyle ilgili en temel bilgileri tutan `RequestContext` örneği.
* **request**

  Geçerli isteği temsil eden Symfony `Request` nesnesi.
* **params**

  Geçerli rota için eşleşen rota parametrelerinin yer aldığı bir dizi.

Ayrıca şu fonksiyonları da kullanabilirsiniz:

* `env(string $name)`

  Environment Variable Processors kullanarak bir değişkenin değerini döndürür.
* `service(string $alias)`

  Bir routing condition servisinin döndürülmesini sağlar.

Önce, route koşullarında kullanmak istediğiniz servislere `#[AsRoutingConditionService]` attribute’unu veya `routing.condition_service` etiketini ekleyin:

```php
use Symfony\Bundle\FrameworkBundle\Routing\Attribute\AsRoutingConditionService;
use Symfony\Component\HttpFoundation\Request;

#[AsRoutingConditionService(alias: 'route_checker')]
class RouteChecker
{
    public function check(Request $request): bool
    {
        // ...
    }
}
```

Sonra, koşullar içinde bu servise başvurmak için `service()` fonksiyonunu kullanın:

```php
// Controller (takma ad kullanarak):
#[Route(condition: "service('route_checker').check(request)")]
// Veya takma ad olmadan:
#[Route(condition: "service('App\\Service\\RouteChecker').check(request)")]
```

Arka planda ifadeler ham PHP’ye derlenir. Bu nedenle `condition` anahtarını kullanmak, alttaki PHP’nin çalışması için gereken zaman dışında ek bir maliyete yol açmaz.

> **Not:** Koşullar, (bu makalenin ilerleyen bölümünde açıklanan) URL üretimi sırasında dikkate alınmaz.

## Rotaları Hata Ayıklama (Debugging Routes)

Uygulamanız büyüdükçe çok sayıda rotanız olacaktır. Symfony, yönlendirme sorunlarını debug etmek için bazı komutlar içerir. Önce, `debug:router` komutu tüm uygulama rotalarınızı, Symfony’nin onları değerlendirdiği sırayla listeler:

```bash
php bin/console debug:router
```

```
----------------  -------  -------  -----  --------------------------------------------
Name              Method   Scheme   Host   Path
----------------  -------  -------  -----  --------------------------------------------
homepage          ANY      ANY      ANY    /
contact           GET      ANY      ANY    /contact
contact_process   POST     ANY      ANY    /contact
article_show      ANY      ANY      ANY    /articles/{_locale}/{year}/{title}.{_format}
blog              ANY      ANY      ANY    /blog/{page}
blog_show         ANY      ANY      ANY    /blog/{slug}
----------------  -------  -------  -----  --------------------------------------------
```

```bash
php bin/console debug:router --show-aliases
```

```bash
php bin/console debug:router --method=GET
php bin/console debug:router --method=ANY
```

**7.3**

`--method` seçeneği Symfony  **7.3** ’te tanıtılmıştır.

Bir rotanın adını (veya adının bir kısmını) argüman olarak vererek o rotanın detaylarını yazdırın:

```bash
php bin/console debug:router app_lucky_number
```

```
+-------------+---------------------------------------------------------+
| Property    | Value                                                   |
+-------------+---------------------------------------------------------+
| Route Name  | app_lucky_number                                        |
| Path        | /lucky/number/{max}                                     |
| ...         | ...                                                     |
| Options     | compiler_class: Symfony\Component\Routing\RouteCompiler |
|             | utf8: true                                              |
+-------------+---------------------------------------------------------+
```

Diğer komut `router:match` olup, verilen URL’nin hangi rotayla eşleşeceğini gösterir. Beklediğiniz controller action’ının neden çalışmadığını bulmak için kullanışlıdır:

```bash
php bin/console router:match /lucky/number/8
```

```
  [OK] Route "app_lucky_number" matches
```


# 🧭 Symfony’de Route (Yönlendirme) Parametreleri

Symfony’de rotalar, dinamik URL parçalarını yönetmek için **parametreler** kullanır.

Örneğin sabit bir `/blog` rotası yerine, dinamik içerikler için `/blog/{slug}` şeklinde bir yapı kullanabilirsiniz.

---

## 🧩 **1. Route Parametreleri (Variable Parts)**

Bir rotada değişken kısımlar `{ }` içinde tanımlanır.

Örneğin bir blog yazısını göstermek için:

```php
// config/routes.php
use App\Controller\BlogController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('blog_show', '/blog/{slug}')
        ->controller([BlogController::class, 'show']);
};
```

Bu örnekte kullanıcı `/blog/my-first-post` adresine gittiğinde:

* Symfony, `BlogController::show()` metodunu çalıştırır.
* `$slug` değişkenine `my-first-post` değerini otomatik olarak aktarır.

✅ Aynı route içerisinde bir parametre yalnızca **bir kez** kullanılabilir:

Örneğin `/blog/posts-about-{category}/page/{pageNumber}` geçerlidir.

---

## 🧮 **2. Parametre Doğrulama (Validation)**

Varsayılan olarak, tüm parametreler herhangi bir değeri kabul eder.

Bu bazen çakışmalara yol açabilir.

### 🔧 Örnek:

Aşağıdaki iki route’ta hem `{slug}` hem `{page}` parametresi aynı konumda olduğundan Symfony hangi rotayı seçeceğini bilemez.

```php
/blog/{slug}
/blog/{page}
```

### ✅ Çözüm:

`requirements()` ile parametreye bir **regex kısıtlaması** ekleyin:

```php
// config/routes.php
use App\Controller\BlogController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('blog_list', '/blog/{page}')
        ->controller([BlogController::class, 'list'])
        ->requirements(['page' => '\d+']); // sadece sayısal değerler
};
```

📘 **Sonuç:**

| URL                     | Route         | Parametre                   |
| ----------------------- | ------------- | --------------------------- |
| `/blog/2`             | `blog_list` | `$page = 2`               |
| `/blog/my-first-post` | `blog_show` | `$slug = 'my-first-post'` |

### 🧰 Requirement Enum

Symfony, sık kullanılan desenleri hazır olarak sunar:

```php
use Symfony\Component\Routing\Requirement\Requirement;

->requirements(['page' => Requirement::DIGITS])
```

Regex kalıplarını tekrar kullanmak için `config` parametreleriyle birleştirebilirsiniz.

---

## 🎯 **3. Inline Gereksinimler (Inlined Requirements)**

Kısıtlamaları doğrudan parametre tanımında da belirtebilirsiniz:

```php
$routes->add('blog_list', '/blog/{page<\d+>}')
```

Bu, route’u daha kısa hale getirir ancak okunabilirliği düşürebilir.

---

## 🧱 **4. Opsiyonel Parametreler (Optional Parameters)**

Bir parametre eklendiğinde varsayılan olarak  **zorunludur** .

Yani `/blog/{page}` varsa, `/blog` adresi eşleşmez.

### 🔧 Çözüm:

`defaults()` kullanarak varsayılan değer belirleyin:

```php
$routes->add('blog_list', '/blog/{page}')
    ->controller([BlogController::class, 'list'])
    ->defaults(['page' => 1])
    ->requirements(['page' => '\d+']);
```

Böylece `/blog` ziyaret edildiğinde `$page = 1` varsayılır.

#### 🧠 İpucu:

* Birden fazla opsiyonel parametre kullanılabilir (`/blog/{slug}/{page}` gibi),

  ancak **opsiyonel parametreden sonra gelen tüm parametreler de opsiyonel** olmalıdır.

#### 🔤 Kısa Söz Dizimi:

Varsayılan değeri inline olarak da yazabilirsiniz:

```php
/blog/{page<\d+>?1}
```

Boş `?` değeriyle `null` da atanabilir:

```php
/blog/{page?}
```

> Controller’da bu durumda tip tanımı `?int $page` olmalıdır.

---

## ⚖️ **5. Route Önceliği (Priority)**

Symfony rotaları tanımlandıkları sırayla değerlendirir.

Bazı genel rotalar özel rotaların önüne geçebilir.

### 🔧 Çözüm:

Attribute (PHP 8) rotalarında `priority` parametresiyle sıralamayı belirleyebilirsiniz:

```php
// src/Controller/BlogController.php
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog/{slug}', name: 'blog_show')]
    public function show(string $slug): Response
    {
        // ...
    }

    #[Route('/blog/list', name: 'blog_list', priority: 2)]
    public function list(): Response
    {
        // ...
    }
}
```

> Daha yüksek `priority` değeri, rotanın önce değerlendirilmesini sağlar.
>
> Varsayılan değer `0`’dır.

---

## 🔄 **6. Parametre Dönüştürme (Parameter Conversion)**

Bazı durumlarda route parametresi bir **nesneye** dönüştürülmelidir.

Örneğin `slug`’a göre bir blog yazısını otomatik olarak bulmak istiyoruz.

```php
// src/Controller/BlogController.php
use App\Entity\BlogPost;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog/{slug:post}', name: 'blog_show')]
    public function show(BlogPost $post): Response
    {
        // $post, slug ile eşleşen BlogPost nesnesidir.
    }
}
```

* Symfony, `$slug` parametresini kullanarak **veritabanından BlogPost** nesnesini bulur.
* Nesne bulunamazsa otomatik olarak **404 Not Found** döner.

### ⚙️ Gelişmiş Eşleştirme (7.3’ten itibaren)

Aynı isimli parametreler çakışma yaratabilir.

Bu durumda, parametreleri benzersiz adlarla tanımlayın:

```php
#[Route('/search-book/{authorName:author.name}/{categoryName:category.name}')]
```

Burada:

* `$author` nesnesi `authorName` parametresinden,
* `$category` nesnesi `categoryName` parametresinden alınır.

---

## 🧮 **7. Backed Enum Parametreleri**

Symfony, PHP **backed enum** tiplerini otomatik olarak çözümler:

```php
// src/Controller/OrderController.php
use App\Enum\OrderStatusEnum;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/orders/list/{status}', name: 'list_orders_by_status')]
    public function list(OrderStatusEnum $status = OrderStatusEnum::Paid): Response
    {
        // Enum otomatik olarak scalar değere çevrilir
    }
}
```

---

## ⚙️ **8. Özel (Special) Parametreler**

Symfony bazı özel parametreleri otomatik tanır:

| Parametre             | Açıklama                                                 |
| --------------------- | ---------------------------------------------------------- |
| **_controller** | Hangi controller/action çalışacağını belirtir.       |
| **_format**     | Response formatını belirler (`json`,`html`, vb.).    |
| **_fragment**   | URL’de `#`işaretinden sonra gelen kısmı temsil eder. |
| **_locale**     | Uygulamanın dilini (locale) belirler.                     |

Örnek:

```php
// config/routes.php
use App\Controller\ArticleController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('article_show', '/articles/{_locale}/search.{_format}')
        ->controller([ArticleController::class, 'search'])
        ->locale('en')
        ->format('html')
        ->requirements([
            '_locale' => 'en|fr',
            '_format' => 'html|xml',
        ]);
};
```

---

## ✅ **Özet**

| Özellik                     | Amaç                    | Örnek                                |
| ---------------------------- | ------------------------ | ------------------------------------- |
| **Parametre**          | Dinamik URL oluşturma   | `/blog/{slug}`                      |
| **Doğrulama**         | Regex ile filtreleme     | `->requirements(['page' => '\d+'])` |
| **Varsayılan Değer** | Opsiyonel parametre      | `->defaults(['page' => 1])`         |
| **Priority**           | Route sıralaması       | `priority: 2`                       |
| **Param Converter**    | Nesneye dönüştürme   | `{slug:post}`                       |
| **Enum**               | Enum tipleri desteklenir | `OrderStatusEnum`                   |
| **_locale / _format**  | Özel parametreler       | `/{_locale}/page.{_format}`         |

---


# 🧭 Symfony Routing: Ekstra Özellikler, Alias’lar ve Route Grupları

Symfony’nin routing sistemi oldukça esnek ve güçlüdür. Bu bölümde,  **ek parametreler** ,  **route alias** ,  **gruplama** ,  **ön ek (prefix)** ,  **redirect** , ve **özel durumları** öğreneceğiz.

---

## ⚙️ **1. Extra Parameters (Ek Parametreler)**

Bir route tanımında `defaults` içine **route path’inde yer almayan** parametreler ekleyebilirsiniz.

Böylece controller metoduna **ek değerler** aktarabilirsiniz.

```php
// config/routes.php
use App\Controller\BlogController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('blog_index', '/blog/{page}')
        ->controller([BlogController::class, 'index'])
        ->defaults([
            'page'  => 1,
            'title' => 'Hello world!', // path’te yok ama controller’a gider
        ]);
};
```

> Bu yöntem, sabit bir başlık, varsayılan veri veya sabit kimlik gibi değerleri controller’a göndermek için idealdir.

---

## 🔡 **2. Slash (/) Karakterleri Route Parametrelerinde**

Varsayılan olarak route parametreleri `/` karakterini  **içeremez** .

Çünkü bu karakter URL’nin parçalarını ayırmak için kullanılır.

Örnek:

```php
/share/{token}
```

Eğer `{token}` değeri içinde `/` varsa, route  **eşleşmez** .

### ✅ Çözüm:

Regex’i daha esnek hale getirin:

```php
$routes->add('share', '/share/{token}')
    ->controller([DefaultController::class, 'share'])
    ->requirements(['token' => '.+']);
```

#### ⚠️ Dikkat:

* Eğer birden fazla parametre `/` kabul ederse, beklenmedik sonuçlar doğabilir.

  Çünkü Symfony ilk `/.+/` ifadesini **en geniş** şekilde eşleştirir.
* `{_format}` parametresiyle birlikte `. +` kullanmayın, aksi halde format değeri bozulur.

  Bunun yerine:

  ```php
  ->requirements(['token' => '[^.]+'])
  ```

  kullanın (nokta hariç tüm karakterleri eşleştirir).

---

## 🔁 **3. Route Aliasing (Rota Takma Adı)**

Bazen mevcut bir route’u **yeni bir isimle** kullanmak isteyebilirsiniz.

Bu, **geriye dönük uyumluluk** (backward compatibility) sağlamak için harika bir yöntemdir.

### 🔧 Örnek:

```php
// config/routes.php
$routes->add('product_show', '/product/{id}')
    ->controller('App\Controller\ProductController::show');

// Yeni bir alias (takma ad) ekleyelim:
$routes->alias('product_details', 'product_show');
```

✅ Artık hem `product_show` hem `product_details` aynı route’u temsil eder.

> ⚙️ Not: PHP attribute ile alias oluşturma Symfony **7.3** sürümünden itibaren desteklenir.

---

### 🧓 **Alias’ları “Deprecated” Olarak İşaretleme**

Eğer eski route ismini kaldırmak istiyorsanız, alias’ı **depreke** edebilirsiniz:

```php
$routes->add('product_details', '/product/{id}')
    ->controller('App\Controller\ProductController::show');

$routes->alias('product_show', 'product_details')
    ->deprecate(
        'acme/package',
        '1.2',
        'The "%alias_id%" route alias is deprecated. Please use "product_details" instead.'
    );
```

> `%alias_id%` şablonu, alias ismiyle değiştirilir.
>
> Symfony, bu alias kullanıldığında otomatik olarak bir **uyarı** (deprecation notice) üretir.

---

## 🧱 **4. Route Grupları ve Prefix (Ön Ek)**

Birden fazla route’un ortak bir başlangıcı (ör. `/blog`) varsa, bunları **grup** olarak tanımlayabilirsiniz.

### 📂 Örnek:

```php
// config/routes/attributes.php
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('../../src/Controller/', 'attribute', false, '../../src/Controller/{Debug*Controller.php}')
        ->prefix('/blog') // tüm rotalar /blog ile başlar
        ->namePrefix('blog_') // tüm rotaların ismine blog_ eklenir
        ->requirements(['_locale' => 'en|es|fr']);
};
```

Bu durumda:

* `/blog/{_locale}` → `blog_index`
* `/blog/{_locale}/posts/{slug}` → `blog_show`

> Eğer boş bir path `/blog/` olarak sonlanmasın istiyorsanız:
>
> ```php
> ->prefix('/blog', false)
> ```
>
> şeklinde kullanın.

---

## 🔍 **5. Route Bilgilerini Controller İçinden Erişmek**

Symfony, her isteğin (Request) içine route bilgilerini saklar.

Controller içinde bu bilgilere ulaşabilirsiniz:

```php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog_list')]
    public function list(Request $request): Response
    {
        $routeName = $request->attributes->get('_route');
        $routeParameters = $request->attributes->get('_route_params');
        $allAttributes = $request->attributes->all();

        // ...
    }
}
```

### 💡 Template’lerde

Twig içinde:

```twig
{{ app.current_route }}
{{ app.current_route_parameters|json_encode }}
```

---

## 🔀 **6. Özel Rotalar (Special Routes)**

Symfony, bazı durumlar için **controller yazmadan** doğrudan işlem yapmanıza olanak tanır.

### 🎨 Template Render Etmek

Route içinden doğrudan Twig template render edebilirsiniz.

(Bu konu [Twig Template Routing](https://symfony.com/doc/current/templates.html#rendering-a-template-directly-from-a-route) bölümünde anlatılır.)

---

### 🔁 URL veya Route’a Redirect Etmek

Symfony’nin kendi `RedirectController` sınıfını kullanabilirsiniz:

```php
// config/routes.php
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    // Route’tan diğer route’a yönlendirme
    $routes->add('doc_shortcut', '/doc')
        ->controller(RedirectController::class)
        ->defaults([
            'route' => 'doc_page',      // yönlendirilecek rota
            'page' => 'index',
            'version' => 'current',
            'permanent' => true,         // 301 kalıcı yönlendirme
            'keepQueryParams' => true,   // query string korunsun
            'keepRequestMethod' => true, // 307/308 kodları
        ]);

    // URL’ye yönlendirme
    $routes->add('legacy_doc', '/legacy/doc')
        ->controller(RedirectController::class)
        ->defaults([
            'path' => 'https://legacy.example.com/doc',
            'permanent' => true,
        ]);
};
```

> Bu yapı, hem eski URL’leri hem yeni sayfalara yönlendirmeleri merkezi şekilde yönetmeyi sağlar.

---

## 🔚 **7. Trailing Slash (Son Slash) Yönetimi**

Symfony, `/foo` ve `/foo/` URL’lerini otomatik olarak düzenler:

| Route URL | `/foo`isteği | `/foo/`isteği |
| --------- | --------------- | ---------------- |
| `/foo`  | 200 (eşleşti) | 301 →`/foo`   |
| `/foo/` | 301 →`/foo/` | 200 (eşleşti)  |

Bu davranış **yalnızca GET ve HEAD isteklerinde** geçerlidir.

Yani tarayıcılar ve SEO açısından URL’ler tutarlı hale getirilir.

---

## ✅ **Özet Tablo**

| Özellik                     | Açıklama                           | Örnek                              |
| ---------------------------- | ------------------------------------ | ----------------------------------- |
| **defaults**           | Ek parametreler tanımlama           | `'title' => 'Hello world!'`       |
| **requirements**       | Slash veya karakter kısıtlamaları | `'token' => '.+'`                 |
| **alias**              | Route’a takma ad ekleme             | `$routes->alias('new', 'old')`    |
| **deprecate()**        | Eski route’u kaldırma uyarısı    | `.->deprecate('pkg','1.2','msg')` |
| **prefix()**           | Tüm URL’lere ön ek ekleme         | `/blog`                           |
| **namePrefix()**       | Tüm route isimlerine ek             | `blog_`                           |
| **RedirectController** | Route veya URL’ye yönlendirme      | `'route' => 'doc_page'`           |
| **Trailing Slash Fix** | `/foo`↔`/foo/`yönlendirmesi    | Otomatik                            |

---


# 🌐 Symfony Routing: Subdomain, Localization, Stateless & URL Generation

Bu bölümde Symfony’nin gelişmiş yönlendirme (routing) özelliklerini öğreneceğiz:

📍  **Sub-domain (alt alan adı) routing** ,

🌍  **Localized routes (çok dilli yönlendirme)** ,

🧠  **Stateless (oturumsuz) yönlendirme** ,

🔗 **URL oluşturma (generateUrl)**

---

## 🏠 **1. Sub-Domain Routing (Alt Alan Adı Yönlendirme)**

Bir route’a özel `host()` tanımlayarak, belirli bir domain veya subdomain için çalışmasını sağlayabilirsiniz.

```php
// config/routes.php
use App\Controller\MainController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    // Mobil site için özel alt alan adı
    $routes->add('mobile_homepage', '/')
        ->controller([MainController::class, 'mobileHomepage'])
        ->host('m.example.com');

    // Varsayılan alan adı
    $routes->add('homepage', '/')
        ->controller([MainController::class, 'homepage']);
};
```

📘 Bu örnekte:

* `/` adresine gelen istek `example.com` için `homepage`,
* `m.example.com` için `mobileHomepage` metodunu çalıştırır.

---

### 🧩 **Host Parametrelerini Dinamikleştirme**

Multi-tenant uygulamalarda `host` kısmında parametreler kullanabilirsiniz:

```php
$routes->add('mobile_homepage', '/')
    ->controller([MainController::class, 'mobileHomepage'])
    ->host('{subdomain}.example.com')
    ->defaults(['subdomain' => 'm'])
    ->requirements(['subdomain' => 'm|mobile']);
```

Bu durumda:

* Hem `m.example.com` hem de `mobile.example.com` istekleri bu route’a eşleşir.
* Varsayılan `subdomain` değeri `m` olarak ayarlanır.

➡️ Inline olarak da yazabilirsiniz:

```php
->host('{subdomain<m|mobile>?m}.example.com')
```

### 🧪 **Functional Testlerde Host Kullanımı**

Testlerde `HTTP_HOST` başlığını belirtmelisiniz:

```php
$crawler = $client->request(
    'GET',
    '/',
    [],
    [],
    ['HTTP_HOST' => 'm.example.com']
);
```

---

## 🌍 **2. Localized Routes (Çok Dilli Yönlendirme)**

Symfony, her dil için farklı URL tanımlamanıza izin verir —

örneğin `/about-us` (EN) ve `/over-ons` (NL).

```php
// config/routes.php
use App\Controller\CompanyController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('about_us', [
        'en' => '/about-us',
        'nl' => '/over-ons',
    ])
    ->controller([CompanyController::class, 'about']);
};
```

### 📦 **PHP Attribute ile Localized Route**

Attribute kullanırken `path` parametresi bir dizi olarak tanımlanır:

```php
#[Route(path: ['en' => '/about-us', 'nl' => '/over-ons'], name: 'about_us')]
```

Symfony, eşleşen route’un dilini (locale) otomatik olarak ayarlar

ve isteğin tamamı boyunca o locale geçerli olur.

---

### 🧭 **Locale Prefix Kullanımı**

URL’lerin başına dil kodunu eklemek için `prefix()` özelliğini kullanabilirsiniz:

```php
// config/routes/attributes.php
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('../../src/Controller/', 'attribute')
        ->prefix([
            'en' => '',     // varsayılan dil
            'nl' => '/nl',  // Hollandaca için /nl prefix’i
        ]);
};
```

Eğer bir route kendi `_locale` parametresini tanımlıyorsa, sadece o dile özel olarak yüklenir.

---

### 🌐 **Farklı Domain ile Locale Yönetimi**

Her dil için farklı domain kullanmak da mümkündür:

```php
$routes->import('../../src/Controller/', 'attribute')
    ->host([
        'en' => 'www.example.com',
        'nl' => 'www.example.nl',
    ]);
```

---

## 🧠 **3. Stateless Routes (Oturumsuz Rotalar)**

Bazı durumlarda (örneğin HTTP cache kullanırken), route’un session kullanmaması gerekir.

Bunu `stateless()` metodu ile tanımlayabilirsiniz:

```php
$routes->add('homepage', '/')
    ->controller([MainController::class, 'homepage'])
    ->stateless();
```

### 🔍 Davranış

* `kernel.debug = true` → **Session kullanılırsa hata fırlatır.**
* `kernel.debug = false` → **Sadece uyarı loglar.**

Bu sayede istemeden session başlatılan yerleri kolayca tespit edebilirsiniz.

---

## 🔗 **4. URL Oluşturma (Generating URLs)**

Routing sistemi çift yönlüdür:

1. URL’yi controller ile eşleştirir.
2. Route adından URL oluşturur. ✅

Bu sayede HTML içinde `<a href>` değerlerini manuel yazmak zorunda kalmazsınız.

---

### 🧩 **Controller’da URL Oluşturmak**

Eğer controller, `AbstractController`’dan türemişse

`generateUrl()` metodunu kullanabilirsiniz:

```php
// src/Controller/BlogController.php
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/blog', name: 'blog_list')]
public function list(): Response
{
    // Parametresiz route
    $signUpPage = $this->generateUrl('sign_up');

    // Parametreli route
    $profilePage = $this->generateUrl('user_profile', [
        'username' => $user->getUserIdentifier(),
    ]);

    // Tam URL (absolute URL)
    $signUpPage = $this->generateUrl('sign_up', [], UrlGeneratorInterface::ABSOLUTE_URL);

    // Localize edilmiş URL (dil belirterek)
    $signUpPageInDutch = $this->generateUrl('sign_up', ['_locale' => 'nl']);
}
```

---

### 🔢 **Extra Parametreler**

Route’da tanımlı olmayan parametreler query string olarak eklenir:

```php
$this->generateUrl('blog', ['page' => 2, 'category' => 'Symfony']);
// Çıktı: /blog/2?category=Symfony
```

> Nesne (ör. UUID) gönderiyorsanız, string’e dönüştürmelisiniz:

```php
$this->generateUrl('blog', ['uuid' => (string) $entity->getUuid()]);
```

---

### 🪄 **Otomatik Route İsimlendirme**

Eğer `name` verilmezse Symfony otomatik olarak oluşturur:

```php
#[Route('/', name: 'homepage')]
public function homepage(): Response {}
```

Symfony ayrıca `__invoke()` metodu veya tek route içeren controller’lar için

otomatik alias (ör. `App\Controller\MainController::homepage`) ekler.

---

## ✅ **Özet Tablo**

| Özellik                   | Açıklama                 | Örnek                              |
| -------------------------- | -------------------------- | ----------------------------------- |
| **host()**           | Belirli domain için route | `'m.example.com'`                 |
| **host parametresi** | Subdomain değişkeni      | `{subdomain}.example.com`         |
| **localized routes** | Çok dilli URL             | `/about-us`,`/over-ons`         |
| **prefix(locale)**   | Locale bazlı URL ön eki  | `/nl/...`                         |
| **stateless()**      | Session kullanılmaz       | Cache’li API’lerde                |
| **generateUrl()**    | Route’tan URL üretir     | `$this->generateUrl('blog_show')` |
| **extra params**     | Query string ekler         | `?category=Symfony`               |

---



# 🔗 Symfony Routing: URL Oluşturma, HTTPS, İmzalama ve Hata Yönetimi

Symfony’nin **URL üretimi (generate URL)** sistemi, hem controller hem servis hem de konsol komutlarında güçlü ve esnek bir şekilde çalışır.

Ayrıca HTTPS zorlaması, URI imzalama (signing) ve hata yakalama gibi gelişmiş özellikler de içerir.

---

## ⚙️ **1. Servislerde URL Oluşturma**

Servislerde URL üretmek için Symfony’nin `router` servisini kullanabilirsiniz.

Eğer **autowiring** aktifse, `UrlGeneratorInterface` tip ipucunu constructor’a eklemeniz yeterlidir:

```php
// src/Service/SomeService.php
namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SomeService
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function someMethod(): void
    {
        // Parametresiz URL
        $signUpPage = $this->urlGenerator->generate('sign_up');

        // Parametreli URL
        $userProfilePage = $this->urlGenerator->generate('user_profile', [
            'username' => $user->getUserIdentifier(),
        ]);

        // Mutlak URL (ABSOLUTE_URL)
        $signUpPage = $this->urlGenerator->generate('sign_up', [], UrlGeneratorInterface::ABSOLUTE_URL);

        // Localize edilmiş URL
        $signUpPageInDutch = $this->urlGenerator->generate('sign_up', ['_locale' => 'nl']);
    }
}
```

---

## 🧩 **2. Twig Template’lerde URL Oluşturma**

Twig’te iki fonksiyon kullanılır:

| Fonksiyon                     | Açıklama                         |
| ----------------------------- | ---------------------------------- |
| `path('route_name', {...})` | Göreli (relative) URL döndürür |
| `url('route_name', {...})`  | Tam (absolute) URL döndürür     |

```twig
<a href="{{ path('blog_show', {slug: 'my-blog-post'}) }}">Read more</a>
```

JavaScript içinde Twig ile dinamik route kullanmak isterseniz:

```twig
<script>
    const route = "{{ path('blog_show', {slug: 'my-blog-post'})|escape('js') }}";
</script>
```

> Pure JavaScript ile URL oluşturmak istiyorsanız, **FOSJsRoutingBundle** kullanmanız önerilir.

---

## 💻 **3. Komutlarda (Console) URL Oluşturma**

Komutlarda (örneğin `php bin/console`) HTTP isteği olmadığından, Symfony varsayılan olarak `http://localhost/` host’unu kullanır.

```php
// src/Command/MyCommand.php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsCommand(name: 'app:my-command')]
class MyCommand
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function __invoke(SymfonyStyle $io): int
    {
        $url = $this->urlGenerator->generate('sign_up', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $io->success($url);
        return 0;
    }
}
```

### 🔧 Gerçek Domain ile URL Üretmek

`config/packages/routing.php` dosyasına `defaultUri` ekleyin:

```php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->router()->defaultUri('https://example.org/my/path/');
};
```

---

## 🧭 **4. Bir Route’un Var Olup Olmadığını Kontrol Etmek**

Dinamik uygulamalarda route’un tanımlı olup olmadığını kontrol etmek için

`RouteNotFoundException` istisnasını yakalayabilirsiniz:

```php
use Symfony\Component\Routing\Exception\RouteNotFoundException;

try {
    $url = $this->urlGenerator->generate($routeName, $routeParameters);
} catch (RouteNotFoundException $e) {
    // route bulunamadı
}
```

> ⚠️ `getRouteCollection()` yöntemini kullanmayın; bu, routing cache’ini yeniden oluşturur ve performansı düşürür.

---

## 🔒 **5. HTTPS Zorlamak (Force HTTPS)**

Varsayılan olarak, oluşturulan URL’ler mevcut isteğin şemasını (`http` veya `https`) kullanır.

### 🔧 Global HTTPS Zorlaması:

```php
// config/services.php
$container->parameters()
    ->set('router.request_context.scheme', 'https')
    ->set('asset.request_context.secure', true);
```

### 🔧 Route Bazında HTTPS Zorlaması:

```php
// config/routes.php
use App\Controller\SecurityController;

$routes->add('login', '/login')
    ->controller([SecurityController::class, 'login'])
    ->schemes(['https']);
```

### 🔐 **Davranış:**

* Eğer istek HTTP üzerinden gelirse, Symfony otomatik olarak HTTPS’e yönlendirir.
* Twig’teki `path('login')` fonksiyonu, HTTP isteklerinde **mutlak HTTPS URL** döndürür.

```twig
{# HTTPS ise göreli URL #}
{{ path('login') }}
{# HTTP ise tam URL (https://example.com/login) #}
```

### 🌐 Tüm Controller’lar İçin HTTPS Zorlaması:

```php
// config/routes/attributes.php
$routes->import('../../src/Controller/', 'attribute')
    ->schemes(['https']);
```

---

## ✍️ **6. URL’leri İmzalama (Signing URIs)**

Symfony, `UriSigner` servisi ile güvenli imzalı linkler oluşturmanıza izin verir.

Bu sayede URL manipülasyonlarını tespit edebilirsiniz.

### 📦 Basit Örnek:

```php
// src/Service/SomeService.php
namespace App\Service;

use Symfony\Component\HttpFoundation\UriSigner;

class SomeService
{
    public function __construct(private UriSigner $uriSigner) {}

    public function signExample(): void
    {
        $url = 'https://example.com/foo/bar?sort=desc';

        // URL’yi imzala
        $signedUrl = $this->uriSigner->sign($url);
        // Sonuç: https://example.com/foo/bar?sort=desc&_hash=e4a21b9

        // İmzayı doğrula
        $isValid = $this->uriSigner->check($signedUrl);
    }
}
```

---

### ⏰ **Süreli (Expiring) İmzalar**

Symfony 7.1 ile gelen bu özellik, imzalı URL’lerin belirli bir sürede geçersiz olmasını sağlar.

```php
$signedUrl = $this->uriSigner->sign($url, new \DateInterval('PT10S')); // 10 saniye geçerli
// https://example.com/foo/bar?...&_expiration=1712414278&_hash=e4a21b9
```

### 🔍 **İmza Geçerliliğini Doğrulama (7.3+)**

```php
use Symfony\Component\HttpFoundation\Exception\ExpiredSignedUriException;
use Symfony\Component\HttpFoundation\Exception\UnsignedUriException;
use Symfony\Component\HttpFoundation\Exception\UnverifiedSignedUriException;

try {
    $uriSigner->verify($signedUrl);
} catch (UnsignedUriException) {
    // İmzalanmamış
} catch (UnverifiedSignedUriException) {
    // İmza geçersiz
} catch (ExpiredSignedUriException) {
    // Süresi dolmuş
}
```

> Eğer `symfony/clock` yüklüyse, testlerde zamanı sahteleyerek (mock) bu doğrulamaları kolayca yapabilirsiniz.

---

## ⚠️ **7. Yaygın Routing Hataları**

| Hata                                                                                                                                          | Sebep                             | Çözüm                                                             |
| --------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------- | -------------------------------------------------------------------- |
| `Controller requires $slug argument`             | Route path’inde `{slug}`yok   | Path’e `{slug}`ekleyin veya `$slug = null`yapın |                                   |                                                                      |
| `Some mandatory parameters are missing ("slug")`                                                                                            | URL oluştururken parametre eksik | `['slug' => 'slug-value']`ekleyin                                  |
| `HTTP/HTTPS redirect loop`                                                                                                                  | Proxy yapılandırması yanlış  | Symfony’yi proxy arkasında doğru ayarlayın (`trusted_proxies`) |

---

## ✅ **Özet Tablo**

| Özellik                         | Amaç                   | Örnek                              |
| -------------------------------- | ----------------------- | ----------------------------------- |
| **generate()**             | Serviste URL oluşturur | `$urlGenerator->generate('home')` |
| **path() / url()**         | Twig’te URL oluşturur | `{{ path('blog_show') }}`         |
| **defaultUri()**           | Komutlarda gerçek host | `'https://example.org/'`          |
| **schemes(['https'])**     | HTTPS zorlaması        | Route veya import bazlı            |
| **UriSigner**              | Güvenli URL imzalama   | `$uriSigner->sign($url)`          |
| **verify()**               | İmza kontrolü (7.3+)  | `$uriSigner->verify($url)`        |
| **RouteNotFoundException** | Dinamik kontrol         | try-catch ile yakala                |

---
