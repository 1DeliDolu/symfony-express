### zSymfony — Twig Şablonlarının Oluşturulması ve Kullanımı

Twig, Symfony’nin HTML (ve diğer metin tabanlı) çıktıları oluşturmak için kullandığı güçlü, güvenli ve esnek bir şablon motorudur.

Aşağıda Twig kurulumu, temel sözdizimi, yapılandırma ve şablon oluşturma adımları detaylı olarak açıklanmıştır.

---

## 🧩 **Kurulum**

Symfony Flex kullanan projelerde Twig’i kurmak için terminalde aşağıdaki komutu çalıştırın:

```bash
composer require symfony/twig-bundle
```

Bu komut, Twig dil desteğini ve Symfony ile bütünleşmesini sağlar.

---

## 🧠 **Twig Sözdizimi**

Twig üç temel yapıdan oluşur:

| Kullanım     | Amaç                                                      | Örnek                       |
| ------------- | ---------------------------------------------------------- | ---------------------------- |
| `{{ ... }}` | Değişken veya ifadelerin çıktısını göstermek için | `{{ page_title }}`         |
| `{% ... %}` | Koşullar, döngüler gibi mantıksal işlemler için      | `{% if user.isLoggedIn %}` |
| `{# ... #}` | Şablon yorumları (çıktıya dahil edilmez)              | `{# This is a comment #}`  |

**Örnek Twig dosyası:**

```twig
<!DOCTYPE html>
<html>
  <head>
    <title>Welcome to Symfony!</title>
  </head>
  <body>
    <h1>{{ page_title }}</h1>

    {% if user.isLoggedIn %}
      Hello {{ user.name }}!
    {% endif %}
  </body>
</html>
```

> 💡 Twig içinde **PHP kodu** çalıştırılamaz, ancak Twig filtreleriyle içerik üzerinde işlem yapılabilir:
>
> ```twig
> {{ title|upper }}
> ```
>
> `upper` filtresi, metni **büyük harfe** çevirir.

---

## ⚙️ **Twig Yapılandırması**

Twig’in tarih, sayı biçimlendirme, önbellekleme gibi çeşitli ayarları vardır.

`config/packages/twig.yaml` dosyası üzerinden özelleştirilebilir.

Örneğin:

```yaml
twig:
  default_path: '%kernel.project_dir%/templates'
  strict_variables: false
```

---

## 🧱 **Şablon Oluşturma**

### 1️⃣ `templates/` dizininde Twig dosyası oluşturun

```twig
{# templates/user/notifications.html.twig #}
<h1>Hello {{ user_first_name }}!</h1>
<p>You have {{ notifications|length }} new notifications.</p>
```

### 2️⃣ Controller’da bu şablonu çağırın

```php
// src/Controller/UserController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    public function notifications(): Response
    {
        $userFirstName = 'John';
        $userNotifications = ['Message 1', 'Message 2'];

        return $this->render('user/notifications.html.twig', [
            'user_first_name' => $userFirstName,
            'notifications' => $userNotifications,
        ]);
    }
}
```

---

## 🧾 **Şablon İsimlendirme Kuralları**

* **Snake case** (küçük harf + alt çizgi) kullanın:

  `blog_posts.html.twig`, `admin/default_theme/blog/index.html.twig`
* **İki uzantı** kullanın:

  Formatı belirtmek için: `index.html.twig`, `feed.xml.twig`

---

## 📁 **Şablon Konumu**

Varsayılan dizin:

```
<project_root>/templates/
```

Örneğin:

`return $this->render('product/index.html.twig')`

dosya olarak `templates/product/index.html.twig`’i arar.

---

## 🧮 **Değişkenler ve Nesne Erişimi**

Twig, değişkenlere şu öncelik sırasıyla erişir:

1. `$foo['bar']`
2. `$foo->bar`
3. `$foo->bar()`
4. `$foo->getBar()`
5. `$foo->isBar()`
6. `$foo->hasBar()`

Eğer hiçbiri yoksa `null` döner.

```twig
<p>{{ user.name }} added this comment on {{ comment.publishedAt|date }}</p>
```

---

## 🔗 **Sayfalara Bağlantı Oluşturma**

### Örnek rota:

```php
// config/routes.php
use App\Controller\BlogController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('blog_index', '/')
        ->controller([BlogController::class, 'index']);

    $routes->add('blog_post', '/articles/{slug}')
        ->controller([BlogController::class, 'show']);
};
```

### Twig’te bağlantı oluşturma:

```twig
<a href="{{ path('blog_index') }}">Homepage</a>

{% for post in blog_posts %}
  <h1><a href="{{ path('blog_post', {slug: post.slug}) }}">{{ post.title }}</a></h1>
  <p>{{ post.excerpt }}</p>
{% endfor %}
```

🔸 `path()` — **göreli URL** üretir

🔸 `url()` — **tam URL** üretir (örneğin e-posta şablonlarında)

---

## 🖼️ **CSS, JS ve Görselleri Bağlamak**

Önce gerekli paketi kurun:

```bash
composer require symfony/asset
```

Ardından Twig’te `asset()` fonksiyonunu kullanın:

```twig
<img src="{{ asset('images/logo.png') }}" alt="Symfony!">
<link href="{{ asset('css/blog.css') }}" rel="stylesheet">
<script src="{{ asset('bundles/acme/js/loader.js') }}"></script>
```

### Avantajları:

* **Önbellek yönetimi (versioning)**
* **Uygulama taşınabilirliği (subdirectory desteği)**

Tam URL gerekiyorsa:

```twig
<img src="{{ absolute_url(asset('images/logo.png')) }}" alt="Symfony!">
```

---

## 🎯 **Özet**

| Özellik              | Açıklama                                   |
| --------------------- | -------------------------------------------- |
| Şablon Motoru        | Twig                                         |
| Dosya Yolu            | `templates/`                               |
| Değişken Gösterimi | `{{ variable }}`                           |
| Mantık Yapıları    | `{% if %}`,`{% for %}`,`{% include %}` |
| Yorumlar              | `{# comment #}`                            |
| URL Üretimi          | `path()`,`url()`                         |
| Asset Bağlantısı   | `asset()`,`absolute_url()`               |

---


### 🧭 Symfony — `app` Global Değişkeni, Global Twig Değerleri ve Şablonların Render Edilmesi

Symfony, her Twig şablonuna otomatik olarak **`app`** isimli bir global değişken ekler.

Bu değişken, uygulamanın **bağlam (context)** bilgilerini taşır.

---

## 🌍 **`app` Global Değişkeni**

Twig içinde doğrudan erişebileceğiniz `app` değişkeni, **`AppVariable`** sınıfının bir örneğidir.

Uygulamanın aktif durumu, kullanıcı, oturum ve istek (request) bilgilerini içerir.

### 🔹 Örnek Kullanım:

```twig
<p>Username: {{ app.user.username ?? 'Anonymous user' }}</p>

{% if app.debug %}
    <p>Request method: {{ app.request.method }}</p>
    <p>Application Environment: {{ app.environment }}</p>
{% endif %}
```

---

### 📋 **`app` İçeriği**

| Özellik                         | Açıklama                                          |
| -------------------------------- | --------------------------------------------------- |
| `app.user`                     | Giriş yapmış kullanıcı nesnesi (veya `null`) |
| `app.request`                  | Geçerli HTTP isteği (`Request`nesnesi)          |
| `app.session`                  | Kullanıcı oturumu (`Session`nesnesi)            |
| `app.flashes`                  | Flash mesajlarının listesi                        |
| `app.flashes('notice')`        | Belirli türdeki flash mesajları                   |
| `app.environment`              | Aktif ortam adı (`dev`,`prod`,`test`)        |
| `app.debug`                    | Uygulama debug modunda mı (`true/false`)         |
| `app.token`                    | Kullanıcı güvenlik token'ı (`TokenInterface`) |
| `app.current_route`            | Mevcut route’un adı                               |
| `app.current_route_parameters` | Mevcut route’un parametreleri                      |
| `app.locale`                   | Aktif dil kodu                                      |
| `app.enabled_locales`          | Uygulamada etkin diller listesi                     |

---

## ⚙️ **Global Twig Değişkenleri**

Symfony, Twig’e **otomatik global değişkenler** eklemenize de izin verir.

Bu değişkenler **tüm Twig şablonlarında** otomatik olarak kullanılabilir.

### 📁 Tanımlama (config/packages/twig.php)

```php
// config/packages/twig.php
use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig): void {
    // Sabit bir değer tanımlama
    $twig->global('ga_tracking')->value('UA-xxxxx-x');
};
```

> Artık `ga_tracking` değişkenine her şablonda erişebilirsiniz:

```twig
<p>The Google tracking code is: {{ ga_tracking }}</p>
```

---

### 🧱 Servisleri Global Twig Değişkeni Olarak Ekleme

Bir servisi Twig’e global olarak ekleyebilirsiniz.

Ancak **Twig yüklendiğinde servis anında oluşturulur** (lazy değil).

```php
// config/packages/twig.php
use Symfony\Config\TwigConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (TwigConfig $twig): void {
    $twig->global('uuid')->value(service('App\Generator\UuidGenerator'));
};
```

### Twig’te Kullanımı:

```twig
UUID: {{ uuid.generate }}
```

---

## 🧩 **Twig Component (Bileşenleri)**

Twig Components, her şablonun bir **bileşen sınıfı** ile eşleştiği yeni bir yöntemdir.

Örneğin bir “alert”, “modal” veya “kategori paneli” gibi küçük tekrar kullanılabilir alanlar oluşturabilirsiniz.

* Daha düzenli ve **yeniden kullanılabilir** yapı sağlar.
* **Live Components** ile kullanıcı etkileşiminde **otomatik güncellenen** (Ajax ile) alanlar oluşturabilirsiniz.

📘 Ayrıntılı bilgi:

* [UX Twig Component](https://symfony.com/bundles/ux-twig-component/current/index.html)
* [UX Live Component](https://symfony.com/bundles/ux-live-component/current/index.html)

---

## 🧾 **Şablonların Render Edilmesi**

### 🧱 Controller İçinde Render

`AbstractController` sınıfından türeyen controller’larda `render()` metodunu kullanabilirsiniz:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'category' => 'Books',
            'promotions' => ['Sale', 'Discount'],
        ]);
    }
}
```

> 🔹 `render()` bir **Response** döner.
>
> 🔹 `renderView()` yalnızca **HTML içeriğini** döner.

```php
$contents = $this->renderView('product/index.html.twig', [
    'category' => 'Books',
    'promotions' => ['Sale', 'Discount'],
]);

return new Response($contents);
```

---

### 🏷️ **#[Template] Attribute Kullanımı**

Symfony 7.2 ile gelen modern bir yöntemdir:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Template('product/index.html.twig')]
    public function index(): array
    {
        return [
            'category' => 'Electronics',
            'promotions' => ['Flash Sale', 'Clearance'],
        ];
    }
}
```

> Burada `#[Template]` etiketi, `Response` nesnesini otomatik oluşturur.
>
> Metot sadece değişken dizisini döndürür.

---

### 🧱 **Şablon Bloklarını Render Etme**

Belirli bir Twig bloğunu (`{% block ... %}`) render etmek isterseniz:

```php
return $this->renderBlock('product/index.html.twig', 'price_block', [
    'price' => 99.99,
]);
```

veya sadece HTML içeriğini almak için:

```php
$content = $this->renderBlockView('product/index.html.twig', 'price_block', [
    'price' => 99.99,
]);
return new Response($content);
```

Yeni Symfony 7.2 sözdizimiyle blok belirtmek için:

```php
#[Template('product.html.twig', block: 'price_block')]
public function price(): array
{
    return ['price' => 99.99];
}
```

---

## 🧰 **Servislerde Twig Kullanımı**

Twig’i servis içinde kullanmak için `Environment` servisini inject edin:

```php
// src/Service/SomeService.php
namespace App\Service;

use Twig\Environment;

class SomeService
{
    public function __construct(private Environment $twig) {}

    public function someMethod(): void
    {
        $html = $this->twig->render('product/index.html.twig', [
            'category' => 'Books',
            'promotions' => ['Discount'],
        ]);
    }
}
```

---

## 🎯 **Özet Tablo**

| Konu                       | Açıklama                                 |
| -------------------------- | ------------------------------------------ |
| `app`Değişkeni         | Uygulama bağlamına erişim sağlar       |
| Global Twig Değişkenleri | `twig.globals`içinde tanımlanır       |
| Twig Component             | Yeniden kullanılabilir bileşen yapısı  |
| `render()`               | Controller içinde şablonu döndürür    |
| `#[Template]`            | Modern attribute tabanlı şablon bağlama |
| `renderBlock()`          | Şablon bloğu render eder                 |
| `Twig Environment`       | Servislerde Twig render işlemi sağlar    |

---



### 🧩 Symfony — Twig ile Şablonların Gelişmiş Kullanımı

Bu bölümde Symfony’de Twig şablonlarının  **doğrudan rota üzerinden render edilmesi** ,  **şablon varlığının kontrolü** ,  **debug araçları** , **şablon parçalarının yeniden kullanımı** ve **controller gömme (embedding)** konuları açıklanmaktadır.

---

## 📧 **E-postalarda Twig Şablonu Kullanmak**

Symfony’nin Mailer bileşeni Twig ile entegre çalışır.

E-posta içeriklerini Twig şablonlarıyla oluşturabilir, HTML veya metin tabanlı içerik gönderebilirsiniz.

📘 Ayrıntılı bilgi: [Mailer &amp; Twig Integration](https://symfony.com/doc/current/mailer.html#twig-integration)

---

## 🧱 **Route Üzerinden Şablon Render Etme**

Bazı durumlarda, controller yazmadan doğrudan bir Twig dosyasını route üzerinden render etmek isteyebilirsiniz.

Bunun için Symfony’nin **`TemplateController`** sınıfını kullanın.

### Örnek:

```php
// config/routes.php
use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('acme_privacy', '/privacy')
        ->controller(TemplateController::class)
        ->defaults([
            // render edilecek şablonun yolu
            'template'  => 'static/privacy.html.twig',

            // HTTP durum kodu (varsayılan: 200)
            'statusCode' => 200,

            // Önbellek ayarları
            'maxAge'    => 86400,
            'sharedAge' => 86400,
            'private'   => true,

            // Şablona gönderilecek parametreler
            'context' => [
                'site_name' => 'ACME',
                'theme' => 'dark',
            ],

            // HTTP başlıkları (Symfony 7.2 ile eklendi)
            'headers' => [
                'Content-Type' => 'text/html',
            ]
        ]);
};
```

> ✅ Bu yöntem özellikle **statik sayfalar (ör. Gizlilik Politikası, Hakkımızda)** için idealdir.

---

## 🔍 **Şablonun Mevcut Olup Olmadığını Kontrol Etme**

Twig’in **template loader** servisi, belirli bir şablonun var olup olmadığını kontrol etmeyi sağlar.

### Örnek:

```php
use Twig\Environment;

class YourService
{
    public function __construct(Environment $twig)
    {
        $loader = $twig->getLoader();

        if ($loader->exists('theme/layout_responsive.html.twig')) {
            // şablon mevcutsa yapılacak işlemler
        }
    }
}
```

---

## 🧪 **Twig Şablonlarını Debug Etme**

### 🧩 1️⃣ **Twig Linter**

Şablonların sözdizimi hatalarını kontrol etmek için kullanılır:

```bash
php bin/console lint:twig
```

Belirli bir dizini veya dosyayı kontrol etmek için:

```bash
php bin/console lint:twig templates/email/
php bin/console lint:twig templates/article/recent_list.html.twig
```

Deprecation (kaldırılacak özellik) uyarılarını görmek için:

```bash
php bin/console lint:twig --show-deprecations templates/email/
```

Belirli klasörleri hariç tutmak için (Symfony 7.1+):

```bash
php bin/console lint:twig templates/ --excludes=data_collector --excludes=dev_tool
```

GitHub Actions çıktısına uygun biçimde çalıştırmak için:

```bash
php bin/console lint:twig --format=github
```

---

### 🧩 2️⃣ **Twig Bilgilerini Görüntüleme**

Tüm Twig fonksiyonlarını, filtrelerini ve global değişkenleri listelemek için:

```bash
php bin/console debug:twig
```

Belirli bir filtre veya fonksiyonu filtrelemek için:

```bash
php bin/console debug:twig --filter=date
```

Belirli bir şablonu incelemek için:

```bash
php bin/console debug:twig @Twig/Exception/error.html.twig
```

---

### 🧩 3️⃣ **dump() ile Twig Değişkenlerini İnceleme**

`dump()` Twig’te `var_dump()`’un gelişmiş bir alternatifidir.

Sadece **dev** ve **test** ortamlarında çalışır.

Öncelikle kurulum:

```bash
composer require --dev symfony/debug-bundle
```

#### Kullanım:

```twig
{# templates/article/recent_list.html.twig #}

{% dump articles %} {# Debug Toolbar’a gönderir #}

{% for article in articles %}
    {{ dump(article) }} {# Sayfada gösterir #}
    {{ dump(blog_posts: articles, user: app.user) }}

    <a href="/article/{{ article.slug }}">{{ article.title }}</a>
{% endfor %}
```

> ⚠️ `dump()` **prod ortamında** çalışmaz (güvenlik amacıyla).

---

## ♻️ **Şablon İçeriklerini Yeniden Kullanmak**

### 🧩 **Template Fragment (Parçaları) Dahil Etmek**

Tekrarlanan Twig kodlarını **ayrı dosyaya çıkararak** include() ile ekleyebilirsiniz.

Örneğin kullanıcı profilini birçok sayfada göstermek istiyorsunuz:

#### Adım 1 — Parça oluşturun:

```twig
{# templates/blog/_user_profile.html.twig #}
<div class="user-profile">
    <img src="{{ user.profileImageUrl }}" alt="{{ user.fullName }}">
    <p>{{ user.fullName }} - {{ user.email }}</p>
</div>
```

#### Adım 2 — Diğer şablonlarda kullanın:

```twig
{# templates/blog/index.html.twig #}
{{ include('blog/_user_profile.html.twig') }}
```

> `_` öneki, bunun bir “parça (partial)” olduğunu belirtmek için kullanılır (zorunlu değil).

---

### 🧩 **Include İçine Değişken Gönderme**

Eğer parça `user` değişkeni bekliyor ama sizde `blog_post.author` varsa:

```twig
{{ include('blog/_user_profile.html.twig', {user: blog_post.author}) }}
```

---

## 🚀 **Controller Sonuçlarını Twig’te Gömme (Embedding Controllers)**

Bazı parçalar **veritabanı sorgusu veya dinamik işlem** gerektirebilir.

Bu durumda `include()` yerine controller sonucunu render edebilirsiniz.

### Adım 1 — Controller oluşturun:

```php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController
{
    public function recentArticles(int $max = 3): Response
    {
        $articles = ['Post 1', 'Post 2', 'Post 3'];

        return $this->render('blog/_recent_articles.html.twig', [
            'articles' => $articles
        ]);
    }
}
```

### Adım 2 — Twig fragment dosyası oluşturun:

```twig
{# templates/blog/_recent_articles.html.twig #}
{% for article in articles %}
    <a href="{{ path('blog_show', {slug: article.slug}) }}">{{ article.title }}</a>
{% endfor %}
```

### Adım 3 — Herhangi bir şablona gömün:

```twig
<div id="sidebar">
    {{ render(path('latest_articles', {max: 3})) }}
    {{ render(controller('App\\Controller\\BlogController::recentArticles', {max: 3})) }}
</div>
```

> `render(path())` — tanımlı bir route üzerinden çağırır
>
> `render(controller())` — doğrudan controller metodunu çalıştırır (gizli route olmadan)

---

### ⚙️ **Fragment Route Ayarı**

Controller gömme (`controller()`) işlemleri özel bir `_fragment` rotası kullanır.

Bunu `framework` konfigürasyonunda tanımlayın:

```php
// config/packages/framework.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->fragments()->path('/_fragment');
};
```

> ⚠️ Çok sayıda controller embed etmek performansı düşürebilir.
>
> Bu nedenle **fragment caching** önerilir.

---

## 🧭 **Özet Tablo**

| Konu                    | Açıklama                                             |
| ----------------------- | ------------------------------------------------------ |
| Route Üzerinden Render | `TemplateController`ile statik sayfalar oluşturulur |
| Template Exists         | `$twig->getLoader()->exists()`ile kontrol edilir     |
| Linting                 | `php bin/console lint:twig`sözdizimi kontrolü      |
| Debugging               | `dump()`ile değişken inceleme                      |
| Include                 | Kod parçalarını yeniden kullanma                    |
| Controller Embedding    | Dinamik içerikleri controller ile çağırma          |
| Fragment Path           | `framework.fragments.path`ile yapılandırılır     |

---



### ⚡ Symfony — **hinclude.js** ile Asenkron İçerik Gömme ve Gelişmiş Twig Özellikleri

Symfony, sayfa içindeki belirli bölümleri (örneğin kenar çubuğu, widget, haber listesi gibi) **asenkron olarak** yüklemek için hafif bir JavaScript kütüphanesi olan  **hinclude.js** ’i destekler.

Bu yöntem, sayfanın ana HTML içeriği yüklendikten sonra ek bölümlerin sonradan yüklenmesini sağlayarak performansı artırır.

---

## 🚀 1. **hinclude.js ile Asenkron İçerik Gömme**

### 🧩 Adım 1 — `hinclude.js` Kütüphanesini Dahil Et

Kütüphaneyi doğrudan Twig şablonuna ekleyebilir veya **AssetMapper** üzerinden projeye dahil edebilirsiniz:

```twig
<script src="{{ asset('js/hinclude.js') }}"></script>
```

Ya da build sürecinde JavaScript dosyalarınıza ekleyebilirsiniz.

---

### 🧩 Adım 2 — Twig İçinde `render_hinclude()` Kullanımı

Senkron yükleme yapan `render()` yerine, içerikleri **asenkron** yüklemek için `render_hinclude()` kullanılır:

```twig
{{ render_hinclude(controller('App\\Controller\\BlogController::recentArticles')) }}
```

Bir route üzerinden çağırmak için:

```twig
{{ render_hinclude(url('latest_articles', {max: 3})) }}
```

> ⚠️ `controller()` fonksiyonunu kullanıyorsanız, **fragment path** ayarının yapılmış olması gerekir:
>
> ```php
> // config/packages/framework.php
> use Symfony\Config\FrameworkConfig;
>
> return static function (FrameworkConfig $framework): void {
>     $framework->fragments()->path('/_fragment');
> };
> ```

---

### 🧩 Adım 3 — Varsayılan (Fallback) İçerik Belirleme

JavaScript devre dışıysa veya geç yükleniyorsa, kullanıcıya gösterilecek bir **varsayılan şablon** veya metin tanımlayabilirsiniz.

#### Global Varsayılan Şablon:

```php
// config/packages/framework.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->fragments()
        ->hincludeDefaultTemplate('hinclude.html.twig');
};
```

#### Tekil Çağrımda Şablon Belirleme:

```twig
{{ render_hinclude(controller('App\\Controller\\BlogController::recentArticles'), {
    default: 'default/content.html.twig'
}) }}
```

#### Sade Metin Kullanımı:

```twig
{{ render_hinclude(controller('App\\Controller\\BlogController::recentArticles'), {
    default: 'Yükleniyor...'
}) }}
```

---

### 🧩 Adım 4 — hinclude.js Özelliklerini Ayarlama

`render_hinclude()` fonksiyonu `attributes` seçeneği ile ek parametreler alabilir:

```twig
{# Çapraz site isteklerinde kimlik doğrulama bilgilerini (cookie, header vb.) kullanmak için #}
{{ render_hinclude(controller('App\\Controller\\BlogController::recentArticles'), {
    attributes: {'data-with-credentials': 'true'}
}) }}

{# Yüklenen içeriğin içindeki JavaScript kodlarını çalıştırmak için #}
{{ render_hinclude(controller('App\\Controller\\BlogController::recentArticles'), {
    attributes: {evaljs: 'true'}
}) }}
```

---

## 🧱 2. **Twig Şablon Kalıtımı (Template Inheritance) ve Layoutlar**

Twig, şablonlar arasında **kalıtım** (inheritance) yapmanızı sağlar.

Bu sayede, farklı sayfalar arasında tekrarlayan öğeleri (ör. header, footer, sidebar) kolayca paylaşabilirsiniz.

Symfony, orta ve büyük ölçekli uygulamalar için **3 seviyeli** bir yapı önerir:

| Seviye | Şablon                        | Amaç                                      |
| ------ | ------------------------------ | ------------------------------------------ |
| 1️⃣  | `templates/base.html.twig`   | Ortak HTML iskeleti (head, header, footer) |
| 2️⃣  | `templates/layout.html.twig` | Sayfa düzeni (ör. içerik + yan menü)   |
| 3️⃣  | `templates/*.html.twig`      | Sayfaların kendisi                        |

---

### 🧩 Örnek — Temel Şablon (Base Template)

```twig
{# templates/base.html.twig #}
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>{% block title %}Uygulamam{% endblock %}</title>
    {% block stylesheets %}
      <link rel="stylesheet" href="/css/base.css">
    {% endblock %}
  </head>
  <body>
    {% block body %}
      <div id="sidebar">
        {% block sidebar %}
          <ul>
            <li><a href="{{ path('homepage') }}">Ana Sayfa</a></li>
            <li><a href="{{ path('blog_index') }}">Blog</a></li>
          </ul>
        {% endblock %}
      </div>

      <div id="content">
        {% block content %}{% endblock %}
      </div>
    {% endblock %}
  </body>
</html>
```

---

### 🧩 Örnek — Blog Düzeni

```twig
{# templates/blog/layout.html.twig #}
{% extends 'base.html.twig' %}

{% block content %}
  <h1>Blog</h1>
  {% block page_contents %}{% endblock %}
{% endblock %}
```

---

### 🧩 Örnek — Blog Sayfası

```twig
{# templates/blog/index.html.twig #}
{% extends 'blog/layout.html.twig' %}

{% block title %}Blog Anasayfa{% endblock %}

{% block page_contents %}
  {% for article in articles %}
    <h2>{{ article.title }}</h2>
    <p>{{ article.body }}</p>
  {% endfor %}
{% endblock %}
```

> ⚠️ Çocuk (child) şablonlarda, **block** dışında doğrudan HTML yazılamaz.

---

## 🔒 3. **Çıktı Kaçışlama (Output Escaping) ve XSS Koruması**

Twig, XSS saldırılarını önlemek için çıktıyı **otomatik olarak kaçışlar (escape eder).**

### Örnek:

```twig
<p>Merhaba {{ name }}</p>
```

Eğer `name = "<script>alert('hack')</script>"` ise, Twig şu çıktıyı verir:

```html
<p>Merhaba <script>alert('hack')</script></p>
```

> ✅ Varsayılan olarak güvenlidir.
>
> ⚠️ Güvenilir ve HTML içeren verileri olduğu gibi göstermek için `raw` filtresi kullanılır:
>
> ```twig
> <h1>{{ product.title|raw }}</h1>
> ```

---

## 🧭 4. **Twig Namespace Kullanımı**

Şablonları farklı klasörlerde saklamak istiyorsanız, Twig namespace sistemiyle organize edebilirsiniz.

### Yapılandırma:

```php
// config/packages/twig.php
use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig): void {
    $twig->path('email/default/templates', 'email');
    $twig->path('backend/templates', 'admin');
};
```

### Kullanım:

```twig
{% include '@email/layout.html.twig' %}
{% include '@admin/dashboard.html.twig' %}
```

> 🔹 Aynı namespace altında birden fazla klasör tanımlanabilir.
>
> 🔹 Her bundle otomatik olarak kendi namespace’ine sahiptir (ör. `@AcmeBlog/user/profile.html.twig`).

---

## 🧮 5. **Özel Twig Uzantıları (Twig Extensions) Oluşturma**

Twig’e kendi özel **filter (filtre)** veya **function (fonksiyon)** ekleyebilirsiniz.

### Örnek — Özel Fiyat Filtresi

```php
// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Attribute\AsTwigFilter;

class AppExtension
{
    #[AsTwigFilter('price')]
    public function formatPrice(float $number, int $decimals = 0, string $decPoint = '.', string $thousandsSep = ','): string
    {
        return '$' . number_format($number, $decimals, $decPoint, $thousandsSep);
    }
}
```

#### Twig’te Kullanımı:

```twig
{{ product.price|price(2, ',', '.') }}
```

---

### Örnek — Özel Fonksiyon

```php
// src/Twig/AppExtension.php
use Twig\Attribute\AsTwigFunction;

class AppExtension
{
    #[AsTwigFunction('area')]
    public function calculateArea(int $width, int $length): int
    {
        return $width * $length;
    }
}
```

#### Twig’te Kullanımı:

```twig
{{ area(5, 10) }}
```

> 🆕 Symfony 7.3 ile birlikte `#[AsTwigFilter]` ve `#[AsTwigFunction]` öznitelikleri (attribute) otomatik olarak uzantınızı kaydeder.

---

## 🧠 6. **Lazy-Loaded (Gecikmeli Yüklenen) Twig Uzantıları**

Yeni attribute temelli uzantılar **otomatik olarak lazy-loaded** çalışır.

Ancak eski yöntemle (`AbstractExtension`) oluşturulan uzantılar, Twig yüklenirken  **hemen başlatılır** .

Performansı artırmak için:

```php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Twig\AppRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [AppRuntime::class, 'formatPrice']),
        ];
    }
}
```

```php
// src/Twig/AppRuntime.php
namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    public function formatPrice(float $number, int $decimals = 0, string $decPoint = '.', string $thousandsSep = ','): string
    {
        return '$' . number_format($number, $decimals, $decPoint, $thousandsSep);
    }
}
```

---

## 🧾 **Özet Tablo**

| Özellik                    | Açıklama                                            |
| --------------------------- | ----------------------------------------------------- |
| `render_hinclude()`       | Controller veya route sonuçlarını asenkron yükler |
| `hincludeDefaultTemplate` | Global varsayılan içerik tanımlar                  |
| `attributes`              | hinclude.js davranış ayarlarını belirler          |
| Şablon Kalıtımı         | Ortak layout yapısı oluşturur                      |
| Çıktı Kaçışlama       | XSS saldırılarına karşı koruma sağlar           |
| Twig Namespace              | Şablonları mantıksal dizinlerde düzenler          |
| Twig Uzantıları           | Özel filtre ve fonksiyon ekler                       |
| Lazy Twig Extension         | Performans için gecikmeli yükleme sağlar           |

---

İstersen bu özellikleri kullanarak **“Son Yazılar” asenkron kenar çubuğu örneği** içeren bir demo Twig + Controller kodu hazırlayabilirim.

Hazırlayayım mı?
