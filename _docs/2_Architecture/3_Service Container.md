### 🧩 Hizmet (Service) Konteyneri

Symfony uygulamanızda birçok faydalı nesne vardır:

örneğin bir **Mailer** nesnesi e-posta göndermenize yardım ederken, başka bir nesne veritabanına veri kaydetmenize yardımcı olur.

Aslında uygulamanızın yaptığı hemen her şey bu nesneler tarafından gerçekleştirilir.

Symfony'de bu faydalı nesnelere **servis (service)** denir ve her servis özel bir nesne içinde yaşar:  **servis konteyneri (service container)** .

Bu konteyner, nesnelerin nasıl oluşturulduğunu merkezileştirir; hayatınızı kolaylaştırır, güçlü bir mimari sağlar ve oldukça hızlı çalışır.

---

### ⚙️ Servisleri Kullanma

Symfony uygulamanız çalıştığı anda konteyner zaten birçok servisi içerir.

Bu servisler birer araç gibidir; ihtiyacınız olduğunda kullanabilirsiniz.

Denetleyicinizde (controller) bir servisi kullanmak için, o servisin sınıfını veya arayüzünü **type-hint** olarak tanımlamanız yeterlidir.

Örneğin bir şey kaydetmek istediğinizde:

```php
// src/Controller/ProductController.php
namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/products')]
    public function list(LoggerInterface $logger): Response
    {
        $logger->info('Bak, bir servisi kullandım!');

        // ...
    }
}
```

Kullanılabilir servisleri görmek için:

```bash
php bin/console debug:autowiring
```

Bu komut, otomatik olarak bağlanabilecek servislerin listesini gösterir.

Örneğin:

```
Psr\Log\LoggerInterface - alias: logger
Symfony\Component\HttpFoundation\RequestStack - alias: request_stack
Symfony\Component\Routing\RouterInterface - alias: router.default
```

Bu tip tanımlamaları hem controller metodlarında hem de kendi servisleriniz içinde kullanabilirsiniz; Symfony otomatik olarak doğru nesneyi size aktarır.

Tüm servislerin tam listesini görmek isterseniz:

```bash
php bin/console debug:container
```

---

### 🏗️ Servis Oluşturma ve Konfigürasyonu

Diyelim ki kullanıcılarınıza rastgele mutlu mesajlar göstermek istiyorsunuz.

Bunu controller içine yazmak yerine yeniden kullanılabilir bir sınıf oluşturalım:

```php
// src/Service/MessageGenerator.php
namespace App\Service;

class MessageGenerator
{
    public function getHappyMessage(): string
    {
        $messages = [
            'Başardın! Sistemi güncelledin! Harika!',
            'Bugün gördüğüm en iyi güncellemelerden biriydi!',
            'Mükemmel iş! Devam et!',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }
}
```

Bu servisi hemen controller içinde kullanabiliriz:

```php
// src/Controller/ProductController.php
use App\Service\MessageGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/products/new')]
    public function new(MessageGenerator $messageGenerator): Response
    {
        $message = $messageGenerator->getHappyMessage();
        $this->addFlash('success', $message);
        // ...
    }
}
```

Symfony konteyneri bu servisi yalnızca ihtiyaç duyulduğunda oluşturur ve aynı nesneyi tekrar kullanır — böylece bellek ve hız açısından verimlidir.

---

### ⚡ services.php ile Otomatik Servis Yükleme

Yeni Symfony projelerinde servis ayarları genelde şu şekilde yapılandırılmıştır:

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function(ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()      // Servislerin bağımlılıklarını otomatik enjekte eder.
            ->autoconfigure() // Servisleri komut, event subscriber vb. olarak otomatik tanımlar.
    ;

    // src/ klasöründeki tüm sınıfları servis olarak kullanılabilir hale getirir
    $services->load('App\\', '../src/');

    // Yeni servisleri bunun altına ekleyebilirsiniz
};
```

Bu sayede `src/` içindeki her sınıf otomatik olarak bir servis haline gelir, ekstra tanımlama gerekmez.

Eğer bazı klasörlerin servis olmamasını istiyorsanız:

```php
$services->load('App\\', '../src/')
    ->exclude('../src/{SomeDirectory,AnotherDirectory,Kernel.php}');
```

---

### 🌍 Servisleri Ortama Göre Sınırlamak

Symfony 7.2 ile birlikte `#[When]` ve `#[WhenNot]` öznitelikleri sayesinde servisleri yalnızca belirli ortamlarda aktif hale getirebilirsiniz.

```php
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'dev')]
class SomeClass
{
    // Sadece 'dev' ortamında aktif olur
}

#[When(env: 'dev')]
#[When(env: 'test')]
class AnotherClass
{
    // 'dev' ve 'test' ortamlarında aktif olur
}
```

Eğer bir servisin belirli bir ortamda **aktif olmamasını** istiyorsanız:

```php
use Symfony\Component\DependencyInjection\Attribute\WhenNot;

#[WhenNot(env: 'dev')]
class SomeClass
{
    // 'dev' dışında tüm ortamlarda aktif olur
}
```

---

### 📘 Özet

* Symfony’de tüm servisler **Service Container** içinde yönetilir.
* `autowire` ve `autoconfigure` sayesinde çoğu servis otomatik bağlanır.
* Servisler yalnızca kullanıldığında oluşturulur (lazy loading).
* `#[When]` ve `#[WhenNot]` ile ortam bazlı servis tanımları yapılabilir.

---




### 🧩 Servis veya Konfigürasyonu Başka Bir Servise Enjekte Etmek

Bazen bir servisin içinde başka bir servise erişmeniz gerekebilir.

Örneğin `MessageGenerator` servisi içinde **logger** servisini kullanmak istiyorsunuz.

Yapmanız gereken tek şey, sınıfın yapıcısına (`__construct()`) bir `LoggerInterface` parametresi eklemektir:

```php
// src/Service/MessageGenerator.php
namespace App\Service;

use Psr\Log\LoggerInterface;

class MessageGenerator
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function getHappyMessage(): string
    {
        $this->logger->info('Mutlu bir mesaj bulunmak üzere!');
        // ...
    }
}
```

Hepsi bu kadar!

Symfony konteyneri, bu sınıf oluşturulduğunda `logger` servisini otomatik olarak aktarır.

Bunu **autowiring** özelliği sayesinde yapar.

Buradaki kilit nokta, `__construct()` metodundaki **type-hint** (`LoggerInterface`) ve servis yapılandırmasındaki `autowire: true` ayarıdır.

Symfony bu type-hint’i gördüğünde, ilgili servisi otomatik olarak bulur ve aktarır.

> Bu yöntem “ **bağımlılık enjeksiyonu (dependency injection)** ” olarak adlandırılır.

Kullanılabilir type-hint’leri görmek için:

```bash
php bin/console debug:autowiring
```

---

### ⚙️ Servislere Sabit veya Değer (Config) Aktarmak

Sadece servisleri değil, aynı zamanda  **sayı** ,  **metin** ,  **boolean** , **sabit** ya da **koleksiyon** değerlerini de servislere aktarabilirsiniz:

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(App\Service\SomeService::class)
        ->arg(0, 'Foo')             // string
        ->arg(1, true)              // boolean
        ->arg(2, 7)                 // integer
        ->arg(3, 3.14)              // float
        ->arg(4, E_ALL)             // sabit (constant)
        ->arg(5, \PDO::FETCH_NUM)
        ->arg(6, Symfony\Component\HttpKernel\Kernel::VERSION)
        ->arg(7, App\Config\SomeEnum::SomeCase)
        ->arg(8, service('some-service-id')) // belirli bir servisi aktar
        ->arg(9, new Reference('some-service-id', Reference::IGNORE_ON_INVALID_REFERENCE)) // servis yoksa null döner
        ->arg(10, [
            'first' => true,
            'second' => 'Foo',
        ]);
};
```

---

### 📬 Birden Fazla Servis Enjekte Etmek

Diyelim ki her site güncellemesinde yöneticinize e-posta göndermek istiyorsunuz.

Yeni bir servis oluşturalım:

```php
// src/Service/SiteUpdateManager.php
namespace App\Service;

use App\Service\MessageGenerator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SiteUpdateManager
{
    public function __construct(
        private MessageGenerator $messageGenerator,
        private MailerInterface $mailer,
    ) {
    }

    public function notifyOfSiteUpdate(): bool
    {
        $happyMessage = $this->messageGenerator->getHappyMessage();

        $email = (new Email())
            ->from('admin@example.com')
            ->to('manager@example.com')
            ->subject('Site update just happened!')
            ->text('Birisi siteyi güncelledi. Şöyle dedik: '.$happyMessage);

        $this->mailer->send($email);

        return true;
    }
}
```

Ve controller içinde şöyle kullanabilirsiniz:

```php
// src/Controller/SiteController.php
namespace App\Controller;

use App\Service\SiteUpdateManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SiteController extends AbstractController
{
    public function new(SiteUpdateManager $siteUpdateManager): Response
    {
        if ($siteUpdateManager->notifyOfSiteUpdate()) {
            $this->addFlash('success', 'Bildirim e-postası başarıyla gönderildi.');
        }

        // ...
    }
}
```

Symfony otomatik olarak `SiteUpdateManager` nesnesini oluşturur ve gerekli bağımlılıkları (`MessageGenerator`, `MailerInterface`) enjekte eder.

---

### 🧱 Manuel Argüman Tanımlama

Bazı durumlarda autowiring işe yaramaz — örneğin, yönetici e-posta adresi dinamik olmalıdır:

```php
class SiteUpdateManager
{
    public function __construct(
        private MessageGenerator $messageGenerator,
        private MailerInterface $mailer,
        private string $adminEmail
    ) {
    }

    public function notifyOfSiteUpdate(): bool
    {
        $email = (new Email())
            ->to($this->adminEmail)
            // ...
        ;
    }
}
```

Bu durumda hata alırsınız:

```
Cannot autowire service "App\Service\SiteUpdateManager": argument "$adminEmail" of method "__construct()" must have a type-hint or be given a value explicitly.
```

Symfony bu değeri nereden alacağını bilemez.

Çözüm: `services.php` dosyasında açıkça belirtin.

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Service\SiteUpdateManager;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->load('App\\', '../src/')
        ->exclude('../src/{DependencyInjection,Entity,Kernel.php}');

    $services->set(SiteUpdateManager::class)
        ->arg('$adminEmail', 'manager@example.com');
};
```

Artık `$adminEmail` parametresine `"manager@example.com"` değeri atanır.

Diğer argümanlar yine otomatik olarak autowire edilir.

Eğer `$adminEmail`’i yeniden adlandırırsanız (örneğin `$mainEmail`), Symfony bu değişikliği fark eder ve açık bir hata mesajı gösterir.

---

### ⚙️ Servis Parametreleri

Konteyner sadece servis nesnelerini değil, aynı zamanda **parametreleri (config değerlerini)** de tutar.

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Service\MessageGenerator;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(MessageGenerator::class)
        ->args([service('logger')]);
};
```

Parametrelere erişmek için konteyner metodlarını kullanabilirsiniz:

```php
$container->hasParameter('mailer.transport'); // parametre tanımlı mı?
$container->getParameter('mailer.transport'); // değerini al
$container->setParameter('mailer.transport', 'sendmail'); // yeni parametre ekle
```

> Symfony’de parametre isimlerinde `.` noktası kullanmak bir gelenektir — okunabilirliği artırır.
>
> Parametreler düz (flat) key-value çiftleridir, yani iç içe yapılamaz.
>
> Ayrıca parametreler yalnızca konteyner derlenmeden önce tanımlanabilir (runtime sırasında değiştirilemez).

---

### 📘 Özet

* Servisler diğer servisleri veya değerleri **autowiring** ile alabilir.
* `__construct()` içine type-hint ile tanımlamak yeterlidir.
* Otomatik eşleştirme yapılamıyorsa `services.php` dosyasında `->arg()` ile manuel değer atayın.
* Konteyner, hem servisleri hem de yapılandırma parametrelerini yönetir.

---



### 🎯 Belirli Bir Servisi Seçmek

Daha önce oluşturduğumuz `MessageGenerator` servisi, yapıcısında (`__construct`) bir `LoggerInterface` bağımlılığı alıyordu:

```php
// src/Service/MessageGenerator.php
namespace App\Service;

use Psr\Log\LoggerInterface;

class MessageGenerator
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }
    // ...
}
```

Fakat konteynerde **birden fazla** `LoggerInterface` implementasyonu olabilir:

örneğin `logger`, `monolog.logger.request`, `monolog.logger.php` gibi.

Peki Symfony hangisini kullanacağını nasıl biliyor?

Symfony, genellikle **varsayılan olarak** bir tanesini seçer — bu durumda `logger`.

Ama isterseniz bunu  **manuel olarak belirleyebilirsiniz** :

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Service\MessageGenerator;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(MessageGenerator::class)
        ->arg('$logger', service('monolog.logger.request'));
};
```

Bu yapılandırma, `__construct()` metodundaki `$logger` parametresine

`monolog.logger.request` servisini geçirir.

Kullanılabilir tüm logger servislerini görmek için:

```bash
php bin/console debug:autowiring logger
```

Tüm servislerin listesini görmek için:

```bash
php bin/console debug:container
```

---

### 🗑️ Servisleri Kaldırmak

Bazen belirli ortamlarda (örneğin `test` ortamında) bazı servislerin devre dışı kalmasını isteyebilirsiniz.

Bu durumda `remove()` metodunu kullanabilirsiniz:

```php
// config/services_test.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\RemovedService;

return function(ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->remove(RemovedService::class);
};
```

Bu örnekte `App\RemovedService`, test ortamında konteynerden tamamen kaldırılır.

---

### 🔁 Bir Closure (Anonim Fonksiyon) Enjekte Etmek

Servise **callable (çağrılabilir)** bir argüman (örneğin bir closure) enjekte etmek de mümkündür.

Aşağıdaki örnekte `MessageGenerator`’a bir closure ekliyoruz:

```php
// src/Service/MessageGenerator.php
namespace App\Service;

use Psr\Log\LoggerInterface;

class MessageGenerator
{
    private string $messageHash;

    public function __construct(
        private LoggerInterface $logger,
        callable $generateMessageHash,
    ) {
        $this->messageHash = $generateMessageHash();
    }
}
```

Closure’ı sağlayan servis:

```php
// src/Hash/MessageHashGenerator.php
namespace App\Hash;

class MessageHashGenerator
{
    public function __invoke(): string
    {
        // Hash hesapla ve döndür
    }
}
```

Yapılandırma:

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Service\MessageGenerator;

return function(ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(MessageGenerator::class)
        ->arg('$logger', service('monolog.logger.request'))
        ->arg('$generateMessageHash', closure('App\Hash\MessageHashGenerator'));
};
```

Burada `closure()` fonksiyonu, `App\Hash\MessageHashGenerator`’ı çağrılabilir olarak enjekte eder.

Autowiring ve özel attribute’larla closure’lar da otomatik bağlanabilir.

---

### 🔗 Argümanları İsme veya Tipe Göre Bağlamak

Symfony’de `bind()` anahtar sözcüğüyle argümanları  **isimle** , **tip ile** veya **ikisiyle birlikte** bağlayabilirsiniz:

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Psr\Log\LoggerInterface;

return function(ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->bind('$adminEmail', 'manager@example.com')
            ->bind('$requestLogger', service('monolog.logger.request'))
            ->bind(LoggerInterface::class, service('monolog.logger.request'))
            ->bind('string $adminEmail', 'manager@example.com')
            ->bind(LoggerInterface::class.' $requestLogger', service('monolog.logger.request'))
            ->bind('iterable $rules', tagged_iterator('app.foo.rule'));
};
```

Bu tanımlar, dosyada yer alan  **tüm servisler için geçerlidir** .

`bind()` ile:

* `$adminEmail` gibi isimle,
* `LoggerInterface::class` gibi tipe göre,
* veya ikisini birlikte (`LoggerInterface $requestLogger`) tanımlayabilirsiniz.

`bind()` ayrıca yalnızca belirli bir servise veya `load()` yöntemiyle toplu olarak yüklenen servislere de uygulanabilir.

---

### 🧱 Soyut (Abstract) Servis Argümanları

Bazı servis argümanları yapılandırma dosyalarında tanımlanamayabilir, çünkü **çalışma zamanında** belirlenirler (örneğin compiler pass veya bundle extension tarafından).

Bu durumda `abstract_arg()` kullanabilirsiniz:

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Service\MyService;

return function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(MyService::class)
        ->arg('$rootNamespace', abstract_arg('should be defined by Pass'));
};
```

Eğer bu argüman runtime sırasında tanımlanmazsa, Symfony şu hatayı verir:

```
Argument "$rootNamespace" of service "App\Service\MyService" is abstract: should be defined by Pass.
```

---

### ⚙️ `autowire` Seçeneği

`services.yaml` veya `services.php` dosyalarında genellikle `_defaults` altında `autowire: true` bulunur.

Bu sayede `__construct()` metodunda type-hint kullandığınızda Symfony ilgili servisi otomatik bulur ve enjekte eder.

Yani:

* `autowire` aktifse, servis bağımlılıklarını otomatik olarak alır.
* Manuel argüman tanımlamaya genellikle gerek kalmaz.

---

### 🪄 `autoconfigure` Seçeneği

`autoconfigure: true` ise, servislerinizi sınıf türüne göre  **otomatik olarak etiketler (auto-tagging)** .

Örneğin, bir Twig uzantısı (`Twig\Extension\ExtensionInterface`) oluşturduğunuzda,

manuel olarak `twig.extension` etiketi eklemenize gerek kalmaz.

Symfony bunu sizin yerinize yapar.

Ayrıca `#[AsMessageHandler]`, `#[AsEventListener]`, `#[AsCommand]` gibi attribute’lar da

**autoconfiguration** tarafından otomatik olarak algılanır ve uygun tag’ler eklenir.

---

### 🧰 Servis Tanımlarını Doğrulamak

Uygulamanızı canlıya almadan önce konteyner tanımlarınızı doğrulamak için:

```bash
php bin/console lint:container
```

Ortam değişkenlerini de çözümleyerek doğrulamak isterseniz (Symfony 7.2 ile geldi):

```bash
php bin/console lint:container --resolve-env-vars
```

> Not: Bu doğrulama işlemleri çalışma zamanında performansı etkileyebilir,
>
> çünkü `CheckTypeDeclarationsPass` ve `CheckAliasValidityPass` gibi compiler pass’leri etkinleştirir.
>
> Ancak üretim öncesi (CI/CD) ortamlarda çalıştırmak oldukça faydalıdır.

---

### 📘 Özet

* Birden fazla servis aynı interface’i implemente ediyorsa, `->arg()` ile hangisinin kullanılacağını belirleyebilirsiniz.
* Servisleri kaldırmak için `remove()`, closure enjekte etmek için `closure()` kullanılır.
* `bind()` ile argümanları isme veya tipe göre otomatik eşleştirebilirsiniz.
* `abstract_arg()` çalışma zamanında tanımlanacak argümanlar için kullanılır.
* `autowire` bağımlılıkları otomatik bağlar, `autoconfigure` ise uygun etiketleri ekler.
* `lint:container` komutu, servis tanımlarınızı doğrulamak için mükemmel bir araçtır.

---



### 🧩 **Public (Genel) ve Private (Özel) Servisler**

Symfony’de tanımlanan **her servis varsayılan olarak “private”** (özel) olur.

Bir servis **özel (private)** olduğunda, onu `$container->get()` metodu ile **doğrudan** konteynerden çağıramazsınız.

👉 **En iyi uygulama:**

Servislere **dependency injection** (bağımlılık enjeksiyonu) yoluyla erişmelisiniz,

`$container->get()` kullanmaktan kaçının.

Eğer servislere **lazy (tembel yükleme)** yöntemiyle erişmek istiyorsanız,

**public servis** yapmak yerine bir **service locator** kullanmanız önerilir.

Ama yine de bir servisin **public** olması gerekiyorsa, ayarı şu şekilde değiştirebilirsiniz:

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Service\PublicService;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    // servisi public olarak işaretle
    $services->set(PublicService::class)
        ->public();
};
```

Ya da bunu doğrudan sınıf üzerinde `#[Autoconfigure]` attribute’u ile belirtebilirsiniz:

```php
// src/Service/PublicService.php
namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
class PublicService
{
    // ...
}
```

---

### 📦 **Birden Fazla Servisi resource ile İçeri Aktarmak**

Daha önce gördüğümüz gibi, `resource` anahtarıyla birden fazla servisi tek seferde içeri aktarabilirsiniz:

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    // src/ klasöründeki tüm sınıfları servis olarak erişilebilir yapar
    $services->load('App\\', '../src/')
        ->exclude('../src/{DependencyInjection,Entity,Kernel.php}');
};
```

Burada `resource` ve `exclude` değerleri birer **glob pattern** olabilir.

Yani belirli dosyaları ya da dizinleri hariç tutabilirsiniz.

> Eğer yalnızca birkaç sınıfı hariç tutmak istiyorsanız,
>
> doğrudan sınıf üzerine `#[Exclude]` attribute’unu ekleyebilirsiniz.

Bu yöntemle tüm `src/` klasörü “servis olarak kullanılabilir” hale gelir,

ancak **gerçekten kullanılan servisler** dışında kalanlar,

**nihai konteynere dahil edilmez** — bu sayede performans korunur.

---

### 🧩 **Aynı Namespace Altında Birden Fazla Servis Tanımı**

YAML konfigürasyonunda PHP namespace anahtar olarak kullanılır.

Bu yüzden aynı namespace altında birden fazla servis tanımlamak isterseniz,

her biri için **farklı bir namespace anahtarı** kullanmanız gerekir:

```yaml
# config/services.yaml
services:
    command_handlers:
        namespace: App\Domain\
        resource: '../src/Domain/*/CommandHandler'
        tags: [command_handler]

    event_subscribers:
        namespace: App\Domain\
        resource: '../src/Domain/*/EventSubscriber'
        tags: [event_subscriber]
```

---

### ⚙️ **Servisleri ve Argümanları Manuel Olarak Tanımlamak**

Autowiring her durumda yeterli olmayabilir.

Bazen aynı sınıfın iki farklı konfigürasyona sahip versiyonunu oluşturmanız gerekebilir.

Örneğin `SiteUpdateManager` sınıfı için iki farklı e-posta adresi kullanmak istiyorsunuz:

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Service\MessageGenerator;
use App\Service\SiteUpdateManager;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    // 1️⃣ Super admin için
    $services->set('site_update_manager.superadmin', SiteUpdateManager::class)
        ->autowire(false)
        ->args([
            service(MessageGenerator::class),
            service('mailer'),
            'superadmin@example.com',
        ]);

    // 2️⃣ Normal kullanıcılar için
    $services->set('site_update_manager.normal_users', SiteUpdateManager::class)
        ->autowire(false)
        ->args([
            service(MessageGenerator::class),
            service('mailer'),
            'contact@example.com',
        ]);

    // Varsayılan olarak "superadmin" versiyonunu kullandır
    $services->alias(SiteUpdateManager::class, 'site_update_manager.superadmin');
};
```

Artık konteynerde iki servis mevcut:

* `site_update_manager.superadmin`
* `site_update_manager.normal_users`

`SiteUpdateManager` tipine göre otomatik enjeksiyon yapılırsa, **alias** sayesinde `superadmin` sürümü kullanılacaktır.

Eğer `alias` tanımlamazsanız, `src/`’dan otomatik yüklenen servis kullanılabilir — bu yüzden `alias` oluşturmak en iyi yöntemdir.

---

### 🌍 **Ortam (Environment) Değerini Otomatik Olarak Enjekte Etmek**

Servisleri yapılandırırken, bulunduğunuz ortam (`dev`, `test`, `prod`) değerini almak isterseniz,

konfigürasyon fonksiyonuna `$env` parametresi eklemeniz yeterlidir:

```php
// config/packages/my_config.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function(ContainerConfigurator $containerConfigurator, string $env): void {
    // `$env` değeri otomatik olarak doldurulur
};
```

---

### 🧠 **Fonksiyonel Arayüzler için Adapter (Ara Katman) Üretmek**

Bir **fonksiyonel arayüz (functional interface)** yalnızca tek bir metot içerir.

Symfony, bu tür arayüzler için bir “adapter” sınıfı otomatik olarak üretebilir.

```php
// src/Service/MessageFormatterInterface.php
namespace App\Service;

interface MessageFormatterInterface
{
    public function format(string $message, array $parameters): string;
}
```

Bu arayüzü implemente eden metodu olan bir sınıfınız olduğunu varsayalım:

```php
// src/Service/MessageUtils.php
namespace App\Service;

class MessageUtils
{
    public function format(string $message, array $parameters): string
    {
        // ...
    }
}
```

Artık `#[AutowireCallable]` attribute’u ile `MessageUtils::format()` metodunu

`MessageFormatterInterface` için bir adapter olarak bağlayabilirsiniz:

```php
// src/Service/Mail/Mailer.php
namespace App\Service\Mail;

use App\Service\MessageFormatterInterface;
use App\Service\MessageUtils;
use Symfony\Component\DependencyInjection\Attribute\AutowireCallable;

class Mailer
{
    public function __construct(
        #[AutowireCallable(service: MessageUtils::class, method: 'format')]
        private MessageFormatterInterface $formatter
    ) {}

    public function sendMail(string $message, array $parameters): string
    {
        return $this->formatter->format($message, $parameters);
    }
}
```

Aynı işlemi **konfigürasyonla** da yapabilirsiniz:

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Service\MessageFormatterInterface;
use App\Service\MessageUtils;

return function(ContainerConfigurator $container) {
    $container
        ->set('app.message_formatter', MessageFormatterInterface::class)
        ->fromCallable([inline_service(MessageUtils::class), 'format'])
        ->alias(MessageFormatterInterface::class, 'app.message_formatter');
};
```

Symfony, `MessageFormatterInterface`’i implemente eden bir **adapter sınıf** oluşturur

ve bu sınıf, `MessageUtils::format()` metoduna yönlendirme yapar.

---

### 📘 **Özet**

* Her servis **varsayılan olarak private** olur.
  * Servislere erişim: **dependency injection** ile olmalıdır.
* Public servis gerekli ise `.public()` ya da `#[Autoconfigure(public: true)]` kullanın.
* `resource` ile toplu servis yükleyebilir, `exclude` ile hariç tutabilirsiniz.
* Aynı sınıfın farklı versiyonlarını `->set()` ile ayrı ID’lerle tanımlayın, `alias()` ile yönetin.
* `$env` otomatik olarak ortam değerini sağlar.
* `#[AutowireCallable]` ya da `fromCallable()` ile fonksiyonel arayüzleri kolayca bağlayabilirsiniz.
