### 🎯 Symfony Controller (Denetleyici) Nedir?

Bir  **controller** , Symfony’deki en temel yapılardan biridir.

Görevi: **Request (istek)** nesnesinden bilgi alıp, **Response (yanıt)** nesnesi oluşturmaktır.

Yanıt, bir  **HTML sayfası** ,  **JSON/XML verisi** ,  **dosya indirmesi** ,  **redirect (yönlendirme)** , hatta **404 hatası** bile olabilir.

Controller, uygulamanın belirli bir URL isteğine karşı ne yapacağını belirleyen “mantığı” içerir.

---

### 🧱 Basit Bir Controller Örneği

```php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LuckyController
{
    #[Route('/lucky/number/{max}', name: 'app_lucky_number')]
    public function number(int $max): Response
    {
        $number = random_int(0, $max);

        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }
}
```

**Açıklama:**

* `namespace App\Controller;` → Controller sınıfının ad alanını belirler.
* `use Symfony\Component\HttpFoundation\Response;` → Döndürülecek HTTP yanıt nesnesi.
* `#[Route('/lucky/number/{max}')]` → Bu metodu `/lucky/number/{max}` URL’sine bağlar.
* `public function number(int $max)` → `{max}` parametresi otomatik olarak `$max` değişkenine atanır.
* `return new Response(...)` → Tarayıcıya gönderilecek yanıtı oluşturur.

📍 **URL’den test etmek için:**

```
http://localhost:8000/lucky/number/100
```

---

### 🧩 AbstractController Kullanımı

Symfony, geliştiricilere kolaylık sağlamak için `AbstractController` adlı bir sınıf sunar.

Bu sınıf, `$this->render()`, `$this->redirectToRoute()` gibi birçok yardımcı metoda erişim sağlar.

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LuckyController extends AbstractController
{
    #[Route('/lucky/number/{max}', name: 'app_lucky_number')]
    public function number(int $max): Response
    {
        $number = random_int(0, $max);

        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
    }
}
```

---

### 🔗 URL Oluşturma

Bir route’un (yolun) URL’sini programatik olarak oluşturmak için:

```php
$url = $this->generateUrl('app_lucky_number', ['max' => 10]);
```

---

### 🔀 Yönlendirme (Redirect)

Kullanıcıyı başka bir route veya URL’ye yönlendirmek için:

```php
use Symfony\Component\HttpFoundation\RedirectResponse;

public function index(): RedirectResponse
{
    // route’a yönlendirir
    return $this->redirectToRoute('homepage');

    // parametreli yönlendirme
    return $this->redirectToRoute('app_lucky_number', ['max' => 10]);

    // dış siteye yönlendirme
    return $this->redirect('https://symfony.com/doc');
}
```

⚠️ `redirect()` metodu hedef URL’yi kontrol etmez.

Kullanıcı girdisine bağlı yönlendirmelerde dikkatli olun — aksi halde “open redirect” güvenlik açığı oluşabilir.

---

### 🖼️ Şablon (Template) Render Etme

Twig kullanarak HTML sayfalarını oluşturmak:

```php
return $this->render('lucky/number.html.twig', [
    'number' => $number,
]);
```

---

### ⚙️ Servis (Service) Kullanımı

Symfony birçok **servis** ile gelir: logger, mailer, database bağlantısı vb.

Bir servisi kullanmak için, metot parametresine tip belirterek Symfony’den otomatik olarak enjekte edilmesini sağlayabilirsin.

```php
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

#[Route('/lucky/number/{max}')]
public function number(int $max, LoggerInterface $logger): Response
{
    $logger->info('We are logging!');
    // ...
}
```

---

### 🧩 `#[Autowire]` ile Özel Servis Enjeksiyonu

Belirli bir servis ya da parametre değerini doğrudan enjekte edebilirsin:

```php
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;

class LuckyController extends AbstractController
{
    public function number(
        int $max,

        // belirli bir logger servisini enjekte et
        #[Autowire(service: 'monolog.logger.request')]
        LoggerInterface $logger,

        // proje dizinini parametre olarak al
        #[Autowire('%kernel.project_dir%')]
        string $projectDir
    ): Response
    {
        $logger->info('We are logging!');
        // ...
    }
}
```

---

### 🔍 Faydalı Komutlar

Tüm servisleri ve autowiring yapılarını görmek için:

```bash
php bin/console debug:autowiring
```

---

### 🧠 Özet

| İşlem          | Yöntem                      |
| ---------------- | ---------------------------- |
| Sayfa oluşturma | `return new Response()`    |
| Twig ile render  | `$this->render()`          |
| URL oluşturma   | `$this->generateUrl()`     |
| Yönlendirme     | `$this->redirectToRoute()` |
| Servis kullanma  | Tip belirterek injection     |
| Özel servis     | `#[Autowire]`              |

---


### ⚙️ Symfony Controller Oluşturma (Generating Controllers)

Symfony projende controller sınıflarını elle yazmak yerine, **Symfony Maker Bundle** ile kolayca oluşturabilirsin.

---

### 🚀 Yeni Bir Controller Oluşturma

```bash
php bin/console make:controller BrandNewController
```

Bu komut iki dosya oluşturur:

```
created: src/Controller/BrandNewController.php
created: templates/brandnew/index.html.twig
```

✔️ `src/Controller/BrandNewController.php` → Yeni controller sınıfın

✔️ `templates/brandnew/index.html.twig` → Twig şablon dosyan

---

### 🧱 CRUD (Create, Read, Update, Delete) Controller Oluşturma

Bir Doctrine Entity için tam CRUD yapısı oluşturmak istersen:

```bash
php bin/console make:crud Product
```

Bu işlem şu dosyaları otomatik olarak üretir:

```
created: src/Controller/ProductController.php
created: src/Form/ProductType.php
created: templates/product/_delete_form.html.twig
created: templates/product/_form.html.twig
created: templates/product/edit.html.twig
created: templates/product/index.html.twig
created: templates/product/new.html.twig
created: templates/product/show.html.twig
```

> 💡 Bu CRUD yapısı: listeleme, yeni kayıt ekleme, düzenleme, silme ve detay sayfasını içerir.

---

### ❌ Hata Yönetimi ve 404 Sayfaları

Eğer bir kayıt veritabanında bulunamazsa, **404 hatası** döndürmek için şu şekilde bir istisna fırlatılır:

```php
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

public function index(): Response
{
    $product = ...;

    if (!$product) {
        throw $this->createNotFoundException('The product does not exist');

        // veya daha uzun hali:
        // throw new NotFoundHttpException('The product does not exist');
    }

    return $this->render(/* ... */);
}
```

* `createNotFoundException()` → 404 yanıtı döndüren yardımcı metot.
* `throw new \Exception('Something went wrong!')` → 500 (Internal Server Error) döndürür.

🧩 Symfony, hata durumlarında geliştirici modunda ayrıntılı hata sayfası, kullanıcıya ise sade bir hata sayfası gösterir.

👉 Özelleştirme için: **How to Customize Error Pages** makalesine bak.

---

### 📨 Request Nesnesine Erişim

Bir controller içinde istek parametrelerini (query string, form verisi, header vb.) almak için:

```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

public function index(Request $request): Response
{
    $page = $request->query->get('page', 1);
    // ...
}
```

---

## 🧭 Request Nesnesinin Otomatik Eşleştirilmesi (Automatic Mapping)

Symfony 7.3 ile gelen güçlü bir özellik sayesinde, gelen HTTP istekleri otomatik olarak controller parametrelerine **eşleştirilebilir.**

---

### 🔹 Tekil Query Parametrelerini Eşleme

URL:

```
https://example.com/dashboard?firstName=John&lastName=Smith&age=27
```

Controller:

```php
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

public function dashboard(
    #[MapQueryParameter] string $firstName,
    #[MapQueryParameter] string $lastName,
    #[MapQueryParameter] int $age,
): Response
{
    // ...
}
```

💡 Desteklenen türler:

`string`, `int`, `float`, `bool`, `array`, `\BackedEnum`, `AbstractUid`

---

### 🔹 Filtrelerle Validasyon (Doğrulama)

```php
public function dashboard(
    #[MapQueryParameter(filter: \FILTER_VALIDATE_REGEXP, options: ['regexp' => '/^\w+$/'])] string $firstName,
    #[MapQueryParameter] string $lastName,
    #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int $age,
): Response
{
    // ...
}
```

---

### 🔹 Tüm Query String’i Bir Nesneye Eşleme

Bir DTO (Data Transfer Object) oluşturalım:

```php
// src/Model/UserDto.php
namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $firstName,

        #[Assert\NotBlank]
        public string $lastName,

        #[Assert\GreaterThan(18)]
        public int $age,
    ) {}
}
```

Controller tarafı:

```php
use App\Model\UserDto;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

public function dashboard(
    #[MapQueryString] UserDto $userDto
): Response
{
    // ...
}
```

💡 Validasyon başarısız olursa varsayılan olarak **404** döner.

İstersen özel durum ve validasyon grubu belirtebilirsin:

```php
#[MapQueryString(
    validationGroups: ['strict', 'edit'],
    validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY
)] UserDto $userDto
```

---

### 🔹 Nested Query Dizilerini Eşleme (Symfony 7.3+)

```php
public function dashboard(
    #[MapQueryString(key: 'search')] SearchDto $searchDto
): Response
{
    // ?search[firstName]=John&search[lastName]=Doe
}
```

---

### 🔹 Boş Query String için Varsayılan DTO

```php
public function dashboard(
    #[MapQueryString] UserDto $userDto = new UserDto()
): Response
{
    // ...
}
```

---

## 🔸 Request Payload Eşleme (JSON API’ler için)

Bir `POST` veya `PUT` isteğinde JSON payload’ı doğrudan DTO’ya map edebilirsin:

```php
use App\Model\UserDto;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

public function dashboard(
    #[MapRequestPayload] UserDto $userDto
): Response
{
    // ...
}
```

---

### 🔧 Gelişmiş Ayarlar

```php
public function dashboard(
    #[MapRequestPayload(
        acceptFormat: 'json',
        validationGroups: ['strict', 'read'],
        validationFailedStatusCode: Response::HTTP_NOT_FOUND
    )] UserDto $userDto
): Response
{
    // ...
}
```

> Varsayılan hata kodu: **422 (Unprocessable Entity)**

Route’u JSON formatında tanımla ki hata durumunda HTML yerine JSON döndürsün:

```php
#[Route('/dashboard', name: 'dashboard', format: 'json')]
```

---

### 🧩 Nested Array DTO’lar (Symfony 7.1+)

```php
public function dashboard(
    #[MapRequestPayload(type: UserDto::class)] array $users
): Response
{
    // JSON body içeriği doğrudan DTO dizisine dönüştürülür
}
```

```json
[
  {"firstName": "John", "lastName": "Smith", "age": 28},
  {"firstName": "Jane", "lastName": "Doe", "age": 30}
]
```

---

### 🧠 Özet Tablo

| Özellik                       | Açıklama                                |
| ------------------------------ | ----------------------------------------- |
| `make:controller`            | Boş controller oluşturur                |
| `make:crud`                  | Doctrine Entity için tam CRUD oluşturur |
| `createNotFoundException()`  | 404 hata döndürür                      |
| `Request`                    | İstek verilerine erişim sağlar         |
| `#[MapQueryParameter]`       | Tekil query parametresi map eder          |
| `#[MapQueryString]`          | Tüm query’yi DTO’ya map eder           |
| `#[MapRequestPayload]`       | JSON payload’ı DTO’ya map eder         |
| `validationFailedStatusCode` | Validasyon hatasında dönecek HTTP kodu  |

---


### 🗂️ Symfony’de Dosya Yükleme ve Oturum Yönetimi

Symfony 7.1 itibarıyla gelen yeni özelliklerle dosya yükleme, session yönetimi ve JSON yanıt üretimi çok daha kolay hale gelmiştir.

Aşağıda bu konuları adım adım açıklayalım 👇

---

## 📤 1. Dosya Yükleme (Mapping Uploaded Files)

Symfony, controller parametrelerine dosya nesnelerini **otomatik olarak map etmek** için

`#[MapUploadedFile]` attribute’unu sunar.

### 🔹 Tek Dosya Yükleme

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/user/picture', methods: ['PUT'])]
    public function changePicture(
        #[MapUploadedFile] UploadedFile $picture,
    ): Response {
        // $picture değişkeni, yüklenen dosyayı temsil eder
        // $picture->getClientOriginalName(), $picture->move() gibi işlemler yapılabilir
        return new Response('File uploaded successfully!');
    }
}
```

📌 Eğer dosya yüklenmemişse, Symfony `HttpException` fırlatır.

Eğer dosya opsiyonel ise, değişkeni `nullable` tanımlayabilirsin:

```php
#[MapUploadedFile]
?UploadedFile $document
```

---

### 🔹 Dosya Tipi ve Boyut Doğrulama (Validation)

Dosya yüklenmeden önce `Symfony\Component\Validator\Constraints` sınıfı ile kısıtlamalar tanımlanabilir:

```php
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/user/picture', methods: ['PUT'])]
public function changePicture(
    #[MapUploadedFile([
        new Assert\File(mimeTypes: ['image/png', 'image/jpeg']),
        new Assert\Image(maxWidth: 3840, maxHeight: 2160),
    ])]
    UploadedFile $picture,
): Response {
    // ...
}
```

🚫 Eğer dosya belirtilen koşulları karşılamazsa, Symfony otomatik olarak bir hata (HttpException) döndürür.

---

### 🔹 Birden Fazla Dosya Yükleme

```php
#[MapUploadedFile(new Assert\File(mimeTypes: ['application/pdf']))]
array $documents
```

veya

```php
#[MapUploadedFile(new Assert\File(mimeTypes: ['application/pdf']))]
UploadedFile ...$documents
```

🧩 Belirtilen `constraint`, tüm dosyalar için uygulanır.

Bir tanesi bile geçersizse, tamamı reddedilir.

---

### 🔹 Dosya Adını Özelleştirme

Yüklenen dosyayı belirli bir adla almak için `name` parametresi kullanılabilir:

```php
#[MapUploadedFile(name: 'something-else')]
UploadedFile $document
```

---

### 🔹 Doğrulama Başarısız Olduğunda Dönen HTTP Kodu Değiştirme

```php
#[MapUploadedFile(
    constraints: new Assert\File(maxSize: '2M'),
    validationFailedStatusCode: Response::HTTP_REQUEST_ENTITY_TOO_LARGE
)]
UploadedFile $document
```

> 🆕 Bu özellik Symfony **7.1** ile gelmiştir.

---

## 💬 2. Oturum Yönetimi (Session Management)

Symfony, kullanıcı oturumlarına **“flash” mesajlar** eklemeni sağlar.

Flash mesajlar sadece  **bir kez gösterilir** , sonra otomatik olarak silinir.

### 🔹 Flash Mesaj Örneği

```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

public function update(Request $request): Response
{
    if ($form->isSubmitted() && $form->isValid()) {
        // İşlem başarılıysa flash mesaj ekle
        $this->addFlash('notice', 'Your changes were saved!');

        // Yönlendirme
        return $this->redirectToRoute('homepage');
    }

    return $this->render('form.html.twig');
}
```

🟢 Flash mesajları Twig içinde göstermek için:

```twig
{% for message in app.flashes('notice') %}
    <div class="alert alert-success">{{ message }}</div>
{% endfor %}
```

---

## 📦 3. Request Nesnesi

Symfony, HTTP istek verilerini `Request` nesnesi üzerinden yönetir.

### 🔹 Örnek Kullanım

```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

public function index(Request $request): Response
{
    $request->isXmlHttpRequest(); // AJAX isteği mi?
    $request->getPreferredLanguage(['en', 'fr']); // Tercih edilen dil

    // GET ve POST parametreleri
    $request->query->get('page');
    $request->getPayload()->get('page');

    // SERVER değişkenleri
    $request->server->get('HTTP_HOST');

    // Yüklenen dosya
    $request->files->get('foo');

    // Cookie değeri
    $request->cookies->get('PHPSESSID');

    // HTTP Header bilgileri
    $request->headers->get('content-type');
}
```

---

## 📬 4. Response Nesnesi

Controller her zaman bir `Response` döndürmelidir.

```php
use Symfony\Component\HttpFoundation\Response;

// Basit bir yanıt
$response = new Response('Hello '.$name, Response::HTTP_OK);

// Header ayarlama
$response->headers->set('Content-Type', 'text/css');
```

> 🔎 Response nesnesi, `ResponseHeaderBag` üzerinden tüm header’ları yönetir.

---

## ⚙️ 5. Config Parametrelerine Erişim

Controller içinden konfigürasyon değerlerine ulaşmak için:

```php
public function index(): Response
{
    $contentsDir = $this->getParameter('kernel.project_dir').'/contents';
    // ...
}
```

---

## 🧾 6. JSON Response Döndürme

JSON yanıtı döndürmek için `json()` yardımcı metodunu kullanabilirsin:

```php
use Symfony\Component\HttpFoundation\JsonResponse;

public function index(): JsonResponse
{
    return $this->json(['username' => 'jane.doe']);
    // Alternatif:
    // return $this->json($data, $status = 200, $headers = [], $context = []);
}
```

🧩 Eğer Serializer servisi aktifse, `json_encode()` yerine otomatik olarak Symfony Serializer kullanılır.

---

## 🧠 Özet Tablo

| Özellik               | Attribute / Metot                    | Açıklama                               |
| ---------------------- | ------------------------------------ | ---------------------------------------- |
| Tek dosya yükleme     | `#[MapUploadedFile]`               | Controller parametresine dosya map eder  |
| Çoklu dosya yükleme  | `array`veya `...$documents`      | Birden fazla dosyayı map eder           |
| Flash mesaj            | `$this->addFlash()`                | Oturumda geçici mesaj saklar            |
| Request erişimi       | `Request $request`                 | GET, POST, FILES, HEADERS erişimi       |
| Response döndürme    | `new Response()`/`$this->json()` | Yanıt üretir                           |
| Konfigürasyon değeri | `$this->getParameter()`            | parametre.yml veya .env değerini çeker |

---



### 📦 Symfony’de Dosya Yayınlama ve Erken Yanıt (Streaming & Early Hints)

Symfony’de  **dosya indirme** , **tarayıcıda gösterme** ve **performans optimizasyonu** (Early Hints) işlemleri oldukça kolaydır.

Bu bölümde `file()` ve `sendEarlyHints()` yardımcı metodlarını detaylı inceleyelim 👇

---

## 🗂️ 1. Dosya Yayınlama (Streaming File Responses)

Controller içinde bir dosyayı indirmek veya tarayıcıda görüntülemek için

`$this->file()` yardımcı metodunu kullanabilirsin.

### 🔹 Basit Dosya İndirme

```php
use Symfony\Component\HttpFoundation\BinaryFileResponse;

public function download(): BinaryFileResponse
{
    // Belirtilen dosyayı gönderir ve indirmeyi zorlar
    return $this->file('/path/to/some_file.pdf');
}
```

📌 Bu metot, **BinaryFileResponse** döndürür ve dosya içeriğini tarayıcıya akış (stream) şeklinde gönderir.

---

### 🔹 İndirilen Dosyanın Adını Değiştirme

```php
use Symfony\Component\HttpFoundation\File\File;

public function download(): BinaryFileResponse
{
    $file = new File('/path/to/some_file.pdf');

    // Kullanıcıya "custom_name.pdf" adıyla indirilsin
    return $this->file($file, 'custom_name.pdf');
}
```

---

### 🔹 Dosyayı Tarayıcıda Görüntüleme (INLINE)

```php
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

public function download(): BinaryFileResponse
{
    return $this->file(
        'invoice_3241.pdf',
        'my_invoice.pdf',
        ResponseHeaderBag::DISPOSITION_INLINE // tarayıcıda aç
    );
}
```

📘 `ResponseHeaderBag::DISPOSITION_INLINE` dosyayı doğrudan tarayıcıda açar

📕 `ResponseHeaderBag::DISPOSITION_ATTACHMENT` (varsayılan) indirmeyi zorlar

---

### 🧠 Özet Tablo: `file()` Parametreleri

| Parametre                | Açıklama                               |
| ------------------------ | ---------------------------------------- |
| `File                    | string $file`                            |
| `?string $fileName`    | Kullanıcıya gösterilecek ad           |
| `?string $disposition` | INLINE (göster) veya ATTACHMENT (indir) |

---

## ⚡ 2. Erken İpucu Gönderimi (Sending Early Hints)

 **Early Hints (HTTP 103)** , tarayıcının gerekli dosyaları (CSS, JS, font)

ana yanıt gelmeden önce indirmeye başlamasını sağlar.

Bu, özellikle **yüksek performanslı sayfalarda** ilk yükleme süresini azaltır.

### 🔹 Örnek Kullanım

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\WebLink\Link;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $response = $this->sendEarlyHints([
            new Link(rel: 'preconnect', href: 'https://fonts.google.com'),
            (new Link(href: '/style.css'))->withAttribute('as', 'style'),
            (new Link(href: '/script.js'))->withAttribute('as', 'script'),
        ]);

        // Normal response içeriği render edilir
        return $this->render('homepage/index.html.twig', response: $response);
    }
}
```

🧩 `sendEarlyHints()` metodu:

* HTTP **103** kodlu “bilgilendirici” bir yanıt gönderir.
* Tarayıcıya gerekli kaynakları **önceden yükleme** talimatı verir.
* Ardından tam Response nesnesi döndürülür.

---

### 🖇️ Erken Yüklenebilecek Kaynak Tipleri

| Tür                  | Açıklama                                  |
| --------------------- | ------------------------------------------- |
| `rel: 'preconnect'` | Harici servise bağlantıyı önceden kurar |
| `as: 'style'`       | CSS dosyaları için                        |
| `as: 'script'`      | JavaScript dosyaları için                 |
| `as: 'font'`        | Font dosyaları için                       |
| `as: 'image'`       | Görseller için                            |

💡 Early Hints özelliğini kullanmak için PHP SAPI’nin (örneğin  **FrankenPHP** ) bu özelliği desteklemesi gerekir.

---

## 📘 3. Genel Değerlendirme

Symfony Controller yapısı, HTTP yanıt sürecini güçlü şekilde kontrol etmene olanak tanır:

| İşlem                | Metot                                                            | Açıklama                            |
| ---------------------- | ---------------------------------------------------------------- | ------------------------------------- |
| Dosya indirme          | `$this->file()`                                                | Dosyayı binary olarak gönderir      |
| Tarayıcıda gösterme | `$this->file(..., ..., ResponseHeaderBag::DISPOSITION_INLINE)` | Dosyayı açar                        |
| Erken ipucu gönderme  | `$this->sendEarlyHints()`                                      | Tarayıcıya önceden kaynak bildirir |
| Render etme            | `$this->render()`                                              | Twig şablonuyla HTML üretir         |
| 404 döndürme         | `$this->createNotFoundException()`                             | “Sayfa bulunamadı” yanıtı        |
| JSON döndürme        | `$this->json()`                                                | JSON yanıt üretir                   |

---

## 🧩 Sonuç

Bir Symfony controller:

* HTTP isteklerini kabul eder
* İlgili işlemi yapar (dosya, veritabanı, render vb.)
* Bir `Response` nesnesi döndürür

Symfony’nin sunduğu  **`AbstractController`** , bu işlemleri kolaylaştırmak için

`render()`, `redirectToRoute()`, `json()`, `file()` ve `sendEarlyHints()` gibi

kullanışlı yardımcı metodlarla gelir.

---
