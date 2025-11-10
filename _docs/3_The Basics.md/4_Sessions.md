```markdown
# Symfony Oturumları (Sessions)

Symfony’nin **HttpFoundation** bileşeni, kullanıcıyla ilgili bilgileri istekler arasında saklamanı sağlayan güçlü ve esnek bir **oturum yönetim sistemi (session subsystem)** içerir.  
Bu sistem, `$_SESSION` süper global değişkeninin ve `session_start()`, `session_regenerate_id()`, `session_id()`, `session_name()` ve `session_destroy()` gibi PHP’nin yerleşik oturum fonksiyonlarının yerini almak üzere tasarlanmıştır.

> ⚙️ **Oturumlar yalnızca veriye erişildiğinde (okuma/yazma) başlatılır.**

---

## 🧩 Kurulum

Oturumları yönetmek için `HttpFoundation` bileşenini yüklemen gerekir:

```bash
composer require symfony/http-foundation
```

---

## 🧠 Temel Kullanım

Oturum nesnesine `Request` nesnesi veya `RequestStack` servisi üzerinden erişebilirsin.

```php
use Symfony\Component\HttpFoundation\RequestStack;

class SomeService
{
    public function __construct(
        private RequestStack $requestStack,
    ) {}

    public function someMethod(): void
    {
        $session = $this->requestStack->getSession();
        // ...
    }
}
```

Kontrolcü (controller) içinde doğrudan `Request` nesnesiyle de erişebilirsin:

```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

public function index(Request $request): Response
{
    $session = $request->getSession();
    // ...
}
```

---

## 📦 Oturum Özellikleri (Session Attributes)

PHP’de oturum yönetimi genellikle `$_SESSION` değişkeniyle yapılır. Ancak bu yaklaşım test edilebilirliği ve nesne yönelimli kapsüllemeyi zorlaştırır.

Symfony bu sorunu çözmek için **session bag** (oturum çantası) kavramını kullanır.

```php
// Oturuma bir veri kaydetme
$session->set('attribute-name', 'attribute-value');

// Oturumdan veri alma
$foo = $session->get('foo');

// Varsayılan değerle veri alma
$filters = $session->get('filters', []);
```

> Bu veriler, kullanıcının oturumu açık kaldığı sürece saklanır.

Oturum, veriye **eriştiğin anda** başlatılır. Bu nedenle anonim kullanıcılar için oturum başlatılmasını istemiyorsan oturum verisine erişmemelisin.

---

## 💬 Flash Mesajları

"Flash" mesajları, bir kez gösterilip otomatik olarak silinen özel mesajlardır.

Özellikle form gönderimlerinden sonra kullanıcıya geri bildirim göstermek için idealdir.

```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

public function update(Request $request): Response
{
    if ($form->isSubmitted() && $form->isValid()) {
        $this->addFlash('notice', 'Değişiklikleriniz kaydedildi!');
        return $this->redirectToRoute('home');
    }

    return $this->render('form.html.twig');
}
```

### Twig içinde flash mesajlarını göstermek:

```twig
{% for message in app.flashes('notice') %}
    <div class="flash-notice">
        {{ message }}
    </div>
{% endfor %}
```

Tüm mesaj tiplerini göstermek istersen:

```twig
{% for label, messages in app.flashes %}
    {% for message in messages %}
        <div class="flash-{{ label }}">
            {{ message }}
        </div>
    {% endfor %}
{% endfor %}
```

> Flash mesajlarına erişmek oturumu başlatır. Bu nedenle bu sayfalar genellikle HTTP önbellekleriyle (HTTP cache) önbelleğe alınmaz.

---

## ⚙️ Yapılandırma (Configuration)

Symfony projelerinde oturumlar varsayılan olarak etkindir.

`config/packages/framework.php` dosyasında yapılandırılabilir:

```php
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->session()
        ->enabled(true)
        ->handlerId(null)
        ->cookieSecure('auto')
        ->cookieSamesite(Cookie::SAMESITE_LAX)
        ->storageFactoryId('session.storage.factory.native');
};
```

Symfony varsayılan olarak PHP’nin kendi oturum mekanizmasını kullanır.

Ancak Symfony’nin kendi dosya tabanlı yöneticisini kullanmak istersen:

```php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->session()
        ->handlerId('session.handler.native_file')
        ->savePath('%kernel.project_dir%/var/sessions/%kernel.environment%');
};
```

> `session.auto_start = 1` PHP ayarının  **kapalı olması gerekir** . Aksi takdirde Symfony oturum sistemiyle çakışma yaşanır.

---

## ⏱️ Oturum Boşta Kalma Süresi (Session Idle Time / Keep Alive)

Kullanıcı uzun süre işlem yapmazsa oturumu otomatik olarak sonlandırmak güvenlik açısından önemlidir.

Bu, **garbage collection (çöp toplama)** veya **manuel kontrol** ile yapılabilir.

```php
$session->start();
if (time() - $session->getMetadataBag()->getLastUsed() > $maxIdleTime) {
    $session->invalidate();
    throw new SessionExpired(); // Oturum süresi doldu sayfasına yönlendirme
}
```

Oturumun oluşturulma, son kullanım zamanı ve yaşam süresi bilgilerine erişebilirsin:

```php
$session->getMetadataBag()->getCreated();
$session->getMetadataBag()->getLastUsed();
$session->getMetadataBag()->getLifetime();
```

---

## 🧹 Garbage Collection (GC) Ayarları

PHP, oturum çöp toplama işlemini rastgele olasılıkla çalıştırır.

Bu olasılık `session.gc_probability / session.gc_divisor` ayarlarıyla belirlenir.

Örnek:

* `5/100` → %5 olasılıkla çalışır.
* `3/4` → %75 olasılıkla çalışır.

Symfony varsayılan olarak `php.ini` içindeki değerleri kullanır.

Ancak doğrudan Symfony üzerinden de yapılandırabilirsin:

```yaml
# config/packages/framework.yaml
framework:
    session:
        gc_probability: 1
```

Ayrıca `gc_divisor` ve `gc_maxlifetime` değerlerini de `NativeSessionStorage` yapıcısına veya `setOptions()` metoduna aktarabilirsin.

---

## 🧾 Önemli Notlar

* `sid_length` ve `sid_bits_per_character` ayarları **Symfony 7.2’de kullanımdan kaldırılmıştır** ve Symfony 8.0’da yok sayılacaktır.
* Oturum çerezi (cookie), `Response` nesnesi üzerinden de erişilebilir (CLI veya RoadRunner gibi ortamlarda yararlıdır).

---



```markdown
# 💾 Symfony'de Oturumları (Sessions) Veritabanında Saklama

Symfony varsayılan olarak oturumları **dosyalarda** saklar.  
Eğer uygulaman birden fazla sunucuda (load balancer, cluster vb.) çalışıyorsa, oturumların **veritabanında** tutulması gerekir ki kullanıcı hangi sunucuya yönlendirilirse yönlendirilsin oturumu aktif kalsın.

Symfony, oturumları hem **ilişkisel (MariaDB, MySQL, PostgreSQL)** hem de **NoSQL (MongoDB)** veya **anahtar-değer (Redis, Memcached)** veritabanlarında saklayabilir.  
Performans açısından **Redis** veya **Memcached** kullanılması önerilir.

---

## 🚀 Redis ile Oturum Saklama

Redis, hızlı erişim için **en çok önerilen yöntemdir**.  
Bu yöntem için bir Redis sunucusunun ve `phpredis` eklentisinin kurulu olması gerekir.

### 🔧 1. PHP ile Redis yapılandırması (`php.ini`)

```ini
; php.ini
session.save_handler = redis
session.save_path = "tcp://192.168.0.178:6379?auth=REDIS_PASSWORD"
```

---

### ⚙️ 2. Symfony tarafında Redis yapılandırması

`config/services.php` dosyasında Redis bağlantısını tanımla:

```php
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;

$container
    ->register('Redis', \Redis::class)
    ->addMethodCall('connect', ['%env(REDIS_HOST)%', '%env(int:REDIS_PORT)%'])
    // ->addMethodCall('auth', ['%env(REDIS_PASSWORD)%']) // parola gerekiyorsa

    ->register(RedisSessionHandler::class)
    ->addArgument(
        new Reference('Redis'),
        // ['prefix' => 'my_prefix', 'ttl' => 600], // opsiyonel
    )
;
```

Symfony’ye bu handler’ı kullanmasını söyle:

```php
// config/packages/framework.php
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->session()
        ->handlerId(RedisSessionHandler::class);
};
```

> ⚠️ **Uyarı:** Redis oturumlarda *kilitleme (locking)* yapmaz.
>
> Bu nedenle paralel isteklerde **race condition** oluşabilir (ör. "Invalid CSRF token" hatası).

---

## 🗃️ İlişkisel Veritabanında (MySQL, MariaDB, PostgreSQL) Oturum Saklama

Symfony, bu işlem için `PdoSessionHandler` sınıfını sağlar.

### 🔧 1. Servisi tanımla

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(PdoSessionHandler::class)
        ->args([
            env('DATABASE_URL'),
        ]);
};
```

### ⚙️ 2. Symfony yapılandırması

```php
// config/packages/framework.php
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->session()
        ->handlerId(PdoSessionHandler::class);
};
```

---

### 🧱 Tablo ve Kolon Adlarını Özelleştirme

Varsayılan tablo adı `sessions`’tır, ancak değiştirilebilir:

```php
// config/services.php
$services->set(PdoSessionHandler::class)
    ->args([
        env('DATABASE_URL'),
        ['db_table' => 'customer_session', 'db_id_col' => 'guid'],
    ]);
```

| Parametre                 | Varsayılan Değer     | Açıklama                    |
| ------------------------- | ---------------------- | ----------------------------- |
| `db_table`              | `sessions`           | Oturum tablosunun adı        |
| `db_id_col`             | `sess_id`            | Oturum kimliği sütunu       |
| `db_data_col`           | `sess_data`          | Oturum verisi sütunu         |
| `db_time_col`           | `sess_time`          | Oturum oluşturulma zamanı   |
| `db_lifetime_col`       | `sess_lifetime`      | Oturum ömrü sütunu         |
| `lock_mode`             | `LOCK_TRANSACTIONAL` | Kilitleme stratejisi          |
| `db_connection_options` | `[]`                 | PDO sürücüye özel ayarlar |

---

### 🧰 Veritabanı Tablosu Oluşturma

Eğer tablo yoksa, Doctrine Migration veya manuel SQL kullanabilirsin.

#### ✅ MySQL / MariaDB

```sql
CREATE TABLE `sessions` (
    `sess_id` VARBINARY(128) NOT NULL PRIMARY KEY,
    `sess_data` BLOB NOT NULL,
    `sess_lifetime` INTEGER UNSIGNED NOT NULL,
    `sess_time` INTEGER UNSIGNED NOT NULL,
    INDEX `sess_lifetime_idx` (`sess_lifetime`)
) COLLATE utf8mb4_bin ENGINE = InnoDB;
```

> `BLOB` tipi 64 KB sınırına sahiptir. Daha fazla veri gerekirse `MEDIUMBLOB` kullan.

#### ✅ PostgreSQL

```sql
CREATE TABLE sessions (
    sess_id VARCHAR(128) NOT NULL PRIMARY KEY,
    sess_data BYTEA NOT NULL,
    sess_lifetime INTEGER NOT NULL,
    sess_time INTEGER NOT NULL
);
CREATE INDEX sess_lifetime_idx ON sessions (sess_lifetime);
```

#### ✅ Microsoft SQL Server

```sql
CREATE TABLE sessions (
    sess_id VARCHAR(128) NOT NULL PRIMARY KEY,
    sess_data NVARCHAR(MAX) NOT NULL,
    sess_lifetime INTEGER NOT NULL,
    sess_time INTEGER NOT NULL,
    INDEX sess_lifetime_idx (sess_lifetime)
);
```

---

## 🧩 MongoDB ile Oturum Saklama (NoSQL)

Symfony, `MongoDbSessionHandler` sınıfını sağlar.

MongoDB bağlantın `DoctrineMongoDBBundle` üzerinden yapılandırılmış olmalıdır.

### 🔧 1. Servis tanımı

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\MongoDbSessionHandler;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(MongoDbSessionHandler::class)
        ->args([
            service('doctrine_mongodb.odm.default_connection'),
            ['database' => '%env("MONGODB_DB")%', 'collection' => 'sessions']
        ]);
};
```

### ⚙️ 2. Symfony yapılandırması

```php
// config/packages/framework.php
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MongoDbSessionHandler;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->session()
        ->handlerId(MongoDbSessionHandler::class);
};
```

### 🗂️ Alan Adlarını Özelleştirme

```php
$services->set(MongoDbSessionHandler::class)
    ->args([
        service('doctrine_mongodb.odm.default_connection'),
        [
            'database' => '%env("MONGODB_DB")%',
            'collection' => 'sessions',
            'id_field' => '_guid',
            'expiry_field' => 'eol',
        ],
    ]);
```

| Parametre        | Varsayılan    | Açıklama             |
| ---------------- | -------------- | ---------------------- |
| `id_field`     | `_id`        | Oturum kimliği alanı |
| `data_field`   | `data`       | Oturum verisi          |
| `time_field`   | `time`       | Oluşturulma zamanı   |
| `expiry_field` | `expires_at` | Oturum süresi alanı  |

> Performans için `expires_at` alanına bir **index** eklenmelidir:
>
> ```bash
> use session_db
> db.session.createIndex({ "expires_at": 1 }, { expireAfterSeconds: 0 })
> ```

---

## 🔄 Oturum Yöneticileri Arasında Geçiş (Migration)

Eğer oturum verilerini farklı bir sistemde tutmaya geçiyorsan,

`MigratingSessionHandler` sınıfını kullanarak veri kaybı olmadan geçiş yapabilirsin.

### Adımlar:

1. **Yeni handler’ı yazma (write-only)** olarak ekle:

```php
$sessionStorage = new MigratingSessionHandler($oldSessionStorage, $newSessionStorage);
```

2. **GC süresinden sonra** , yeni verinin doğruluğunu kontrol et.
3. **Okuma yönünü değiştir** (rollback kolaylığı için):

```php
$sessionStorage = new MigratingSessionHandler($newSessionStorage, $oldSessionStorage);
```

4. Her şey düzgünse, artık sadece yeni handler’ı kullan.

---

## 📚 Özet

| Tür                | Handler Sınıfı           | Avantajı                 | Dezavantajı              |
| ------------------- | --------------------------- | ------------------------- | ------------------------- |
| **Redis**     | `RedisSessionHandler`     | En hızlı erişim        | Kilitleme yok             |
| **Memcached** | `MemcachedSessionHandler` | Hafif ve ölçeklenebilir | Sınırlı özellik       |
| **PDO (SQL)** | `PdoSessionHandler`       | ACID uyumlu               | Disk I/O yüksek          |
| **MongoDB**   | `MongoDbSessionHandler`   | Şemadan bağımsız      | Karmaşık yapılandırma |

---



```markdown
# ⏱️ Symfony Oturum TTL (Time-To-Live) ve Locale Yönetimi

Symfony varsayılan olarak PHP’nin `session.gc_maxlifetime` ayarını **oturum ömrü (TTL)** olarak kullanır.  
Ancak oturumları bir **veritabanında** saklıyorsan, TTL’yi Symfony yapılandırması üzerinden ya da **çalışma anında (runtime)** dinamik olarak belirleyebilirsin.

---

## ⚙️ Statik TTL Ayarlama

Oturum yöneticine (örneğin Redis handler’ı) `ttl` parametresi eklenir:

```php
// config/services.php
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;

$services
    ->set(RedisSessionHandler::class)
    ->args([
        service('Redis'),
        ['ttl' => 600], // saniye cinsinden (10 dakika)
    ]);
```

> Bu örnekte her oturum 600 saniye (10 dakika) sonra sona erer.

---

## 🧠 Dinamik TTL (Kullanıcıya Göre TTL Belirleme)

Farklı kullanıcılar için farklı TTL süreleri tanımlamak istiyorsan, bir **callback (geri çağırma)** fonksiyonu kullanabilirsin.

Bu callback oturum yazılmadan hemen önce çağrılır ve TTL değeri olarak bir **tamsayı** döndürmelidir.

```php
// config/services.php
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;

$services
    ->set(RedisSessionHandler::class)
    ->args([
        service('Redis'),
        ['ttl' => closure(service('my.ttl.handler'))],
    ]);

$services
    // TTL hesaplaması yapan sınıf
    ->set('my.ttl.handler', 'App\Service\SessionTtlHandler')
    ->args([service('security')]);
```

`App\Service\SessionTtlHandler` sınıfında `__invoke()` metodunu tanımlayarak dinamik TTL üretebilirsin:

```php
namespace App\Service;

use Symfony\Component\Security\Core\Security;

class SessionTtlHandler
{
    public function __construct(private Security $security) {}

    public function __invoke(): int
    {
        $user = $this->security->getUser();

        // Yönetici kullanıcılar için 1 saat, diğerleri için 10 dakika
        return ($user && $user->isAdmin()) ? 3600 : 600;
    }
}
```

---

## 🌍 Kullanıcının Locale (Dil) Ayarını Oturumda Saklama

Symfony, dili (`locale`) her istekte `Request` nesnesi üzerinden alır.

Varsayılan olarak bu değer **oturumda kalıcı (sticky)** değildir.

Ancak locale bilgisini oturumda saklayarak kullanıcı dilini sonraki isteklerde de koruyabilirsin.

---

### 🧩 LocaleSubscriber Oluşturma

```php
// src/EventSubscriber/LocaleSubscriber.php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(private string $defaultLocale = 'en') {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        if ($locale = $request->attributes->get('_locale')) {
            // Route parametresinden gelen locale değeri varsa oturuma kaydet
            $request->getSession()->set('_locale', $locale);
        } else {
            // Aksi halde oturumdan oku
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
```

> Symfony varsayılan `services.yaml` yapılandırmasında bu sınıfı otomatik olarak tanır.
>
> Ek yapılandırma gerekmez.

---

### 🔧 Manuel Servis Tanımı (isteğe bağlı)

```php
// config/services.php
use App\EventSubscriber\LocaleSubscriber;

$container->register(LocaleSubscriber::class)
    ->addArgument('%kernel.default_locale%');
```

---

### 🧭 Controller içinde locale okuma

```php
use Symfony\Component\HttpFoundation\Request;

public function index(Request $request): void
{
    $locale = $request->getLocale();
}
```

---

## 👤 Kullanıcı Tercihlerine Göre Locale Belirleme

Kullanıcının veritabanında kayıtlı `locale` alanını kullanmak istersen, oturum açma işlemi sonrasında locale değerini oturuma kaydetmelisin.

Bunun için `LoginSuccessEvent` olayını dinleyen bir event subscriber oluşturabilirsin.

---

### 🪄 UserLocaleSubscriber

```php
// src/EventSubscriber/UserLocaleSubscriber.php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Kullanıcı giriş yaptıktan sonra locale bilgisini oturuma kaydeder.
 */
class UserLocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(private RequestStack $requestStack) {}

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (null !== $user->getLocale()) {
            $this->requestStack->getSession()->set('_locale', $user->getLocale());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }
}
```

> Kullanıcı dilini güncellediğinde, oturumda da bu değeri güncellemelisin ki değişiklik hemen etkili olsun.

---

## 🧱 Session Proxy Mekanizması

Symfony, oturum yönetimini genişletmek için **SessionHandlerProxy** sınıfını sağlar.

Bu sayede özel oturum davranışları (örneğin şifreli oturumlar, salt-okunur misafir oturumları) oluşturabilirsin.

### Örnek: Özel Oturum Handler’ı Tanımlama

```php
// src/Session/CustomSessionHandler.php
namespace App\Session;

use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

class CustomSessionHandler extends SessionHandlerProxy
{
    // özel işlemler (şifreleme, kayıt, vb.) buraya eklenir
}
```

Ve Symfony’ye bildir:

```php
// config/packages/framework.php
use App\Session\CustomSessionHandler;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->session()
        ->handlerId(CustomSessionHandler::class);
};
```

---

## 📋 Özet

| Özellik                       | Açıklama                                                                    |
| ------------------------------ | ----------------------------------------------------------------------------- |
| **TTL (Time-To-Live)**   | Oturumun ne kadar süreyle geçerli olacağını belirler.                    |
| **Statik TTL**           | Her kullanıcı için sabit bir süre (örn. 600 sn).                         |
| **Dinamik TTL**          | Kullanıcıya veya oturum durumuna göre değişken süre.                    |
| **LocaleSubscriber**     | Dil bilgisini oturumda saklar.                                                |
| **UserLocaleSubscriber** | Giriş yapan kullanıcının dil tercihine göre oturumu günceller.          |
| **Session Proxy**        | Oturum yöneticisini özelleştirmeni sağlar (örneğin şifreli oturumlar). |

---




```markdown
# 🔐 Symfony Oturum Verilerinin Şifrelenmesi ve Misafir Oturumları (Read-Only Sessions)

Symfony oturum sistemi, varsayılan olarak oturum verilerini **şifrelemeden (plain text)** saklar.  
Ancak güvenliğin artırılması gereken durumlarda (örneğin kullanıcı bilgileri, token’lar, hassas session payload’ları) oturum verilerini **şifreleyip çözmek (encrypt/decrypt)** mümkündür.  
Bunun için Symfony’nin **SessionHandlerProxy** veya **Marshaller dekorasyonu** mekanizmaları kullanılabilir.

---

## 🔒 1. SessionHandlerProxy ile Oturum Verisini Şifreleme

Aşağıdaki örnek, [`php-encryption`](https://github.com/defuse/php-encryption) kütüphanesini kullanarak oturum verilerini okuma/yazma sırasında otomatik olarak şifreler.

### 🧩 EncryptedSessionProxy Sınıfı

```php
// src/Session/EncryptedSessionProxy.php
namespace App\Session;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

class EncryptedSessionProxy extends SessionHandlerProxy
{
    public function __construct(
        private \SessionHandlerInterface $handler,
        private Key $key
    ) {
        parent::__construct($handler);
    }

    public function read($id): string
    {
        $data = parent::read($id);
        return $data ? Crypto::decrypt($data, $this->key) : '';
    }

    public function write($id, $data): string
    {
        $data = Crypto::encrypt($data, $this->key);
        return parent::write($id, $data);
    }
}
```

Bu sınıf, Symfony’nin oturum handler’ını sarmalayarak tüm okuma/yazma işlemlerini şifreli hale getirir.

---

### ⚙️ Symfony Yapılandırması

```php
// config/packages/framework.php
use App\Session\EncryptedSessionProxy;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->session()
        ->handlerId(EncryptedSessionProxy::class);
};
```

> 🔑 Şifreleme anahtarını (`Key` objesi) güvenli biçimde depolaman gerekir.
>
> Genellikle `.env.local` veya gizli anahtar yönetimi (Vault, AWS Secrets Manager) kullanılır.

---

## 🧬 2. SodiumMarshaller ile Oturum Şifreleme (Alternatif Yöntem)

Symfony’nin **session.marshaller** servisini **dekoratör (decorator)** ile sarmalayarak

oturum verilerini otomatik olarak **Sodium** kütüphanesiyle şifreleyebilirsin.

### 🔑 Güvenli Anahtar Üretimi

Önce terminalde güvenli bir anahtar oluştur ve `.env` veya secret store’a kaydet:

```bash
php -r 'echo base64_encode(sodium_crypto_box_keypair());'
```

Örneğin `.env` dosyana şu şekilde ekle:

```
SESSION_DECRYPTION_FILE=/path/to/keyfile
```

---

### ⚙️ SodiumMarshaller Servisini Kaydetme

```php
// config/services.php
use Symfony\Component\Cache\Marshaller\SodiumMarshaller;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(SodiumMarshaller::class)
        ->decorate('session.marshaller')
        ->args([
            [env('file:resolve:SESSION_DECRYPTION_FILE')],
            service('.inner'),
        ]);
};
```

> Bu yöntem yalnızca **oturum değerlerini (values)** şifreler, **anahtarları (keys)** şifrelemez.
>
> Bu nedenle anahtar adlarında gizli bilgi (örneğin e-posta, token) kullanmamaya dikkat etmelisin.

---

## 🚫 3. Misafir (Guest) Kullanıcılar için Read-Only Oturumlar

Bazı uygulamalarda,  **oturum gereklidir ancak misafir kullanıcıların verisi kalıcı olarak saklanmamalıdır** .

Bu durumda oturumun **yazma aşamasına müdahale ederek** kaydı engelleyebilirsin.

### 🧩 ReadOnlySessionProxy Sınıfı

```php
// src/Session/ReadOnlySessionProxy.php
namespace App\Session;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

class ReadOnlySessionProxy extends SessionHandlerProxy
{
    public function __construct(
        private \SessionHandlerInterface $handler,
        private Security $security
    ) {
        parent::__construct($handler);
    }

    public function write($id, $data): string
    {
        if ($this->getUser() && $this->getUser()->isGuest()) {
            // Misafir kullanıcılar için oturumu kaydetme
            return;
        }

        return parent::write($id, $data);
    }

    private function getUser(): ?User
    {
        $user = $this->security->getUser();
        return is_object($user) ? $user : null;
    }
}
```

Bu yapı sayesinde, misafir kullanıcılar oturum açmadan önce geçici (non-persistent) session kullanır.

Yani sayfa yenilendiğinde veya sekme kapatıldığında oturum bilgisi kaybolur.

---

## 🧩 4. Legacy Uygulamalarla Entegrasyon (PHP Bridge)

Symfony’yi eski bir uygulamayla entegre ediyorsan (örneğin `session_start()` doğrudan çağrılıyorsa),

`php_bridge` storage factory’sini kullanarak Symfony’nin session yönetimini eski sisteme uyarlayabilirsin.

### 🔧 a) Uygulama kendi handler’ını kullanıyorsa:

```php
// config/packages/framework.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->session()
        ->storageFactoryId('session.storage.factory.php_bridge')
        ->handlerId(null);
};
```

### 🔧 b) PHP `session_start()` çağırıyor ama Symfony handler kullanmak istiyorsan:

```php
// config/packages/framework.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->session()
        ->storageFactoryId('session.storage.factory.php_bridge')
        ->handlerId('session.handler.native_file');
};
```

> 🧠 Not: Eğer eski sistem `session_start()` çağırıyor ve kendi handler’ını kullanıyorsa,
>
> Symfony oturum handler’ını **değiştirmemelisin** (`handler_id: ~`).
>
> Çünkü oturum zaten PHP tarafından başlatılmış olacaktır.

---

## 📋 Özet

| Özellik                        | Açıklama                                                                          |
| ------------------------------- | ----------------------------------------------------------------------------------- |
| **EncryptedSessionProxy** | `php-encryption`kullanarak session verilerini okuma/yazma aşamasında şifreler. |
| **SodiumMarshaller**      | Symfony’nin marshaller servisini dekorasyonla şifreli hale getirir.               |
| **ReadOnlySessionProxy**  | Misafir kullanıcıların oturum verilerini diske yazmaz, geçici tutar.            |
| **PHP Bridge**            | Legacy PHP uygulamalarıyla Symfony session sistemini entegre eder.                 |

---

> 🔐 **Tavsiye:**
>
> Şifreleme anahtarlarını asla kod deposunda tutma.
>
> Onları `.env.local`, `vault` veya bir gizli yönetim sistemiyle (ör. AWS Secrets Manager) sakla.
