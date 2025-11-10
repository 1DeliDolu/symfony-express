```markdown
# ⚡ Symfony Cache (Önbellek) Sistemi

Symfony’nin **Cache bileşeni**, uygulamanın hızını artırmak için güçlü ve esnek bir önbellekleme (caching) mekanizması sunar.  
Çok sayıda farklı **adapter (sürücü)** destekler — örneğin **Filesystem**, **Redis**, **Memcached**, **PDO**, **APCu** vb.

---

## 🚀 Temel Kullanım

```php
use Symfony\Contracts\Cache\ItemInterface;

// Callable yalnızca önbellekte veri bulunmadığında (cache miss) çalışır
$value = $pool->get('my_cache_key', function (ItemInterface $item): string {
    $item->expiresAfter(3600); // 1 saat geçerli

    // ... örneğin bir API çağrısı veya yoğun işlem
    $computedValue = 'foobar';

    return $computedValue;
});

echo $value; // 'foobar'

// ... önbellekten silmek için:
$pool->delete('my_cache_key');
```

> Symfony Cache bileşeni, **PSR-6** ve **PSR-16** standartlarını destekler.

---

## ⚙️ FrameworkBundle ile Cache Yapılandırması

Symfony Cache sistemi üç temel kavram üzerine kuruludur:

| Kavram             | Açıklama                                                                                                   |
| ------------------ | ------------------------------------------------------------------------------------------------------------ |
| **Pool**     | Etkileşimde bulunduğun önbellek havuzudur. Her pool’un kendine ait namespace’i ve anahtarları vardır. |
| **Adapter**  | Pool’un hangi tür depolamayı kullanacağını belirler (filesystem, redis vb.)                            |
| **Provider** | Bazı adapter’lar (Redis, Memcached gibi) için bağlantı servisidir.                                      |

Symfony’de iki havuz varsayılan olarak etkin gelir:

* `cache.app` → uygulamanın genel önbelleği
* `cache.system` → sistem ve framework önbelleği (örn. annotation, validator, serializer)

### 📦 Örnek Konfigürasyon

```php
// config/packages/cache.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->cache()
        ->app('cache.adapter.filesystem')
        ->system('cache.adapter.system');
};
```

> `cache.system` için varsayılan ayarları değiştirmen önerilmez.

---

## 🧰 Hazır Adapter’lar

Symfony aşağıdaki önbellek adapter’larını destekler:

| Adapter                           | Açıklama                                           |
| --------------------------------- | ---------------------------------------------------- |
| `cache.adapter.apcu`            | APCu tabanlı bellek içi cache                      |
| `cache.adapter.array`           | PHP oturumu boyunca geçici cache                    |
| `cache.adapter.doctrine_dbal`   | Doctrine DBAL üzerinden veri tabanı cache          |
| `cache.adapter.filesystem`      | Dosya tabanlı cache                                 |
| `cache.adapter.memcached`       | Memcached tabanlı cache                             |
| `cache.adapter.pdo`             | PDO (MySQL, PostgreSQL) tabanlı cache               |
| `cache.adapter.psr6`            | PSR-6 uyumlu dış cache sistemi                     |
| `cache.adapter.redis`           | Redis tabanlı cache                                 |
| `cache.adapter.redis_tag_aware` | Etiketlerle çalışan optimize edilmiş Redis cache |
| `cache.adapter.system`          | Symfony’nin sistemsel cache adaptörü              |

---

## 🔌 Provider Kısayolları

```php
// config/packages/cache.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->cache()
        ->directory('%kernel.cache_dir%/pools') // filesystem için
        ->defaultDoctrineDbalProvider('doctrine.dbal.default_connection')
        ->defaultPsr6Provider('app.my_psr6_service')
        ->defaultRedisProvider('redis://localhost')
        ->defaultMemcachedProvider('memcached://localhost')
        ->defaultPdoProvider('pgsql:host=localhost');
};
```

> 🆕 Symfony 7.1 itibarıyla `PDO adapter` için **DSN** kullanımı desteklenir.

---

## 🧱 Özel (Namespaced) Cache Pool’ları Oluşturma

Her cache pool, kendi bağımsız anahtar alanına sahiptir.

Yani aynı backend (örneğin Redis) kullanılsa bile anahtar çakışması olmaz.

```php
// config/packages/cache.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $cache = $framework->cache();
    $cache->defaultMemcachedProvider('memcached://localhost');

    // Uygulama cache yapılandırmasını kullanan özel pool
    $cache->pool('custom_thing.cache')
        ->adapters(['cache.app']);

    // Dosya tabanlı pool
    $cache->pool('my_cache_pool')
        ->adapters(['cache.adapter.filesystem']);

    // Memcached tabanlı pool
    $cache->pool('acme.cache')
        ->adapters(['cache.adapter.memcached']);

    // Bağlantı detaylarını özelleştir
    $cache->pool('foobar.cache')
        ->adapters(['cache.adapter.memcached'])
        ->provider('memcached://user:password@example.com');

    // 60 saniye ömürlü kısa süreli cache
    $cache->pool('short_cache')
        ->adapters(['foobar.cache'])
        ->defaultLifetime(60);
};
```

### 🧩 Servis Enjeksiyonu

Her özel pool bir **servis** olarak tanımlanır ve otomatik olarak autowire edilir:

```php
use Symfony\Contracts\Cache\CacheInterface;

public function listProducts(CacheInterface $customThingCache)
{
    // $customThingCache otomatik olarak 'custom_thing.cache' servisine karşılık gelir
}
```

> Örneğin `custom_thing.cache` için `$customThingCache` şeklinde autowire edilir.

---

## 🏷️ Cache Namespace Özelleştirme

Eğer üçüncü taraf uygulamalarla aynı cache namespace’ini kullanmak istiyorsan,

otomatik namespace üretimini kendin belirleyebilirsin:

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function(ContainerConfigurator $container): void {
    $container->services()
        ->set('app.cache.adapter.redis')
            ->parent('cache.adapter.redis')
            ->tag('cache.pool', ['namespace' => 'my_custom_namespace']);
};
```

---

## ⚙️ Özel Provider Seçenekleri

Bazı adapter’lar (örneğin Redis) **timeout** veya **retry_interval** gibi özel bağlantı seçenekleri alabilir.

```php
// config/packages/cache.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Config\FrameworkConfig;

return static function (ContainerBuilder $container, FrameworkConfig $framework): void {
    $framework->cache()
        ->pool('cache.my_redis')
            ->adapters(['cache.adapter.redis'])
            ->provider('app.my_custom_redis_provider');

    $container->register('app.my_custom_redis_provider', \Redis::class)
        ->setFactory([RedisAdapter::class, 'createConnection'])
        ->addArgument('redis://localhost')
        ->addArgument([
            'retry_interval' => 2,
            'timeout' => 10,
        ]);
};
```

---

## 🧾 Özet

| Özellik                  | Açıklama                                                                          |
| ------------------------- | ----------------------------------------------------------------------------------- |
| **Pool**            | Farklı uygulama bölümleri için bağımsız cache alanı oluşturur.             |
| **Adapter**         | Cache’in hangi tür depolamayı (Redis, FileSystem vb.) kullanacağını belirler. |
| **Provider**        | Adapter’ın depolama bağlantısını tanımlar (ör. Redis DSN).                  |
| **Namespace**       | Pool’lar arası anahtar çakışmasını önler.                                   |
| **DefaultLifetime** | Varsayılan cache ömrünü (TTL) belirler.                                         |
| **System Cache**    | Symfony çekirdek işlemleri (annotations, serializer) için kullanılır.          |

---

> 💡 **İpucu:**
>
> Geliştirme ortamında `cache:clear` komutunu sıkça çalıştırmak gerekebilir.
>
> Ancak Redis veya Memcached kullanıyorsan, Symfony otomatik olarak namespace değiştirerek eski cache’i etkisiz hale getirir, bu da performansı artırır.

---



```markdown
# ⚡ Symfony Cache — Gelişmiş Kullanım

Symfony Cache bileşeni sadece hızlı veri erişimi sağlamakla kalmaz, aynı zamanda **çok katmanlı (chain) cache**, **etiketleme (tags)**, **şifreleme (encryption)** ve **asenkron (async) cache yenileme** gibi güçlü özellikler sunar.  
Bu sayede yüksek trafikli, ölçeklenebilir uygulamalarda en yüksek performans elde edilir.

---

## 🔗 1. Cache Chain (Zincirli Önbellek)

Her cache adapter’ının avantajları ve dezavantajları vardır:  
örneğin `ArrayAdapter` çok hızlıdır ama geçicidir; `RedisAdapter` kalıcıdır ama nispeten daha yavaştır.  
Bu iki dünyayı birleştirmek için **Cache Chain** kullanılır.

### ⚙️ Yapılandırma

```php
// config/packages/cache.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->cache()
        ->pool('my_cache_pool')
            ->defaultLifetime(31536000) // 1 yıl
            ->adapters([
                'cache.adapter.array', // en hızlısı (RAM içinde)
                'cache.adapter.apcu',  // orta seviye
                ['name' => 'cache.adapter.redis', 'provider' => 'redis://user:password@example.com'], // kalıcı
            ]);
};
```

> 🔹 Symfony,  **okuma işlemlerinde ilk adapter’dan başlar** .
>
> Eğer veri bulunmazsa, sırayla diğerlerine geçer.
>
> 🔹 **Yazma işleminde** ise tüm adapter’lara sırayla yazar.
>
> 🔹 Herhangi bir hata durumunda Symfony diğerlerine yazmaya devam eder.

---

## 🏷️ 2. Cache Tags (Etiketleme)

Çok sayıda cache anahtarının olduğu uygulamalarda, verileri gruplamak ve topluca silmek (invalidate) için **cache tag** kullanılır.

Etiketler, belirli bir gruba ait cache öğelerini tek seferde geçersiz kılmanı sağlar.

### 🧩 Kullanım Örneği

```php
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class SomeClass
{
    public function __construct(private TagAwareCacheInterface $myCachePool) {}

    public function someMethod(): void
    {
        $value0 = $this->myCachePool->get('item_0', function (ItemInterface $item): string {
            $item->tag(['foo', 'bar']);
            return 'debug';
        });

        $value1 = $this->myCachePool->get('item_1', function (ItemInterface $item): string {
            $item->tag('foo');
            return 'debug';
        });

        // "bar" etiketiyle işaretlenmiş tüm cache kayıtlarını sil
        $this->myCachePool->invalidateTags(['bar']);
    }
}
```

### ⚙️ Tag Desteği Eklemek

```php
// config/packages/cache.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->cache()
        ->pool('my_cache_pool')
            ->tags(true)
            ->adapters(['cache.adapter.redis_tag_aware']);
};
```

> 🔸 `cache.adapter.redis_tag_aware` etiketlerle çalışmak için optimize edilmiştir.

### 🧱 Etiketleri Farklı Bir Pool’da Saklamak

```php
// config/packages/cache.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->cache()
        ->pool('my_cache_pool')
            ->tags('tag_pool')
            ->adapters(['cache.adapter.redis']);

    $framework->cache()
        ->pool('tag_pool')
            ->adapters(['cache.adapter.apcu']);
};
```

> `tag_pool` etiketlerin saklandığı ayrı bir cache havuzudur.
>
> Bu yöntem yüksek trafikli sistemlerde performans artışı sağlar.

---

## 🧹 3. Cache Temizleme (Clearing Cache)

Symfony önbelleklerini temizlemek için çeşitli komutlar sunar:

### 🎯 Tek bir pool’u temizleme

```bash
php bin/console cache:pool:clear my_cache_pool
```

### 🧩 Tüm özel pool’ları temizleme

```bash
php bin/console cache:pool:clear cache.app_clearer
```

### 🧱 Tüm cache’leri temizleme

```bash
php bin/console cache:pool:clear --all
```

### 🚫 Bazı cache’leri hariç tutarak temizleme

```bash
php bin/console cache:pool:clear --all --exclude=my_cache_pool --exclude=another_cache_pool
```

### 🌐 Sistem genelindeki cache’leri temizleme

```bash
php bin/console cache:pool:clear cache.global_clearer
```

### 🏷️ Etiket bazlı cache temizleme

```bash
php bin/console cache:pool:invalidate-tags tag1
php bin/console cache:pool:invalidate-tags tag1 tag2
php bin/console cache:pool:invalidate-tags tag1 tag2 --pool=cache.app
```

> `cache:pool:list` komutuyla tüm mevcut cache pool’larını görebilirsin.

---

## 🔐 4. Cache Şifreleme (Encryption)

Cache verilerini **libsodium** kullanarak şifrelemek için **SodiumMarshaller** kullanılabilir.

### 🔑 Anahtar Oluşturma

```bash
php -r 'echo base64_encode(sodium_crypto_box_keypair());'
```

Bu anahtarı `.env` veya secret store’da sakla:

```
CACHE_DECRYPTION_KEY=base64:...
```

### ⚙️ SodiumMarshaller Kaydı

```php
// config/packages/cache.php
use Symfony\Component\Cache\Marshaller\SodiumMarshaller;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Reference;

$container->setDefinition(SodiumMarshaller::class, new ChildDefinition('cache.default_marshaller'))
    ->addArgument(['env(base64:CACHE_DECRYPTION_KEY)'])
    // Anahtar döngüsü (key rotation) için birden fazla anahtar da ekleyebilirsin
    // ->addArgument(['env(base64:CACHE_DECRYPTION_KEY)', 'env(base64:OLD_CACHE_DECRYPTION_KEY)'])
    ->addArgument(new Reference('.inner'));
```

> ⚠️ Bu yöntem **yalnızca değerleri (values)** şifreler, **anahtarları (keys)** şifrelemez.
>
> Anahtar adlarında hassas bilgi bulundurma.

---

## 🔄 5. Asenkron Cache Yenileme (Async Computation)

Symfony, cache çökmesi (cache stampede) sorununa karşı **probabilistic early expiration** algoritmasını kullanır.

Bazı cache öğeleri “erken süresi dolmuş” olarak seçilir.

Bu durumda değer senkron olarak yeniden hesaplanmak yerine, **arka planda (background worker)** yeniden hesaplanabilir.

### 🧮 a) Hesaplama Servisi Oluşturma

```php
// src/Cache/CacheComputation.php
namespace App\Cache;

use Symfony\Contracts\Cache\ItemInterface;

class CacheComputation
{
    public function compute(ItemInterface $item): string
    {
        $item->expiresAfter(5);

        // Burada kendi hesaplamanı yap
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
}
```

---

### 🧭 b) Controller İçinde Cache Kullanımı

```php
// src/Controller/CacheController.php
namespace App\Controller;

use App\Cache\CacheComputation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;

class CacheController extends AbstractController
{
    #[Route('/cache', name: 'cache')]
    public function index(CacheInterface $asyncCache): Response
    {
        // Arka planda yenileme işlemi yapılır
        $cachedValue = $asyncCache->get('my_value', [CacheComputation::class, 'compute']);

        // ...
    }
}
```

---

### ⚙️ c) Cache Havuzunu Mesaj Kuyruğuna (Messenger) Bağlama

```php
// config/framework/framework.php
use Symfony\Component\Cache\Messenger\EarlyExpirationMessage;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $framework): void {
    $framework->cache()
        ->pool('async.cache')
            ->earlyExpirationMessageBus('messenger.default_bus');

    $framework->messenger()
        ->transport('async_bus')
            ->dsn(env('MESSENGER_TRANSPORT_DSN'))
        ->routing(EarlyExpirationMessage::class)
            ->senders(['async_bus']);
};
```

---

### ▶️ d) Consumer’ı Başlatma

```bash
php bin/console messenger:consume async_bus
```

> Artık cache öğeleri erken süresi dolduğunda,  **arka planda yenilenir** .
>
> Kullanıcı gecikme yaşamadan eski cache değeriyle yanıt alır, sonraki isteklerde yeni değer kullanılır.

---

## 📋 Özet

| Özellik                    | Açıklama                                                                |
| --------------------------- | ------------------------------------------------------------------------- |
| **Cache Chain**       | Farklı hızdaki adapter’ları zincirleyerek optimum performans sağlar. |
| **Cache Tags**        | Etiketlerle cache öğelerini grup halinde yönetmeni sağlar.            |
| **Cache Encryption**  | Verileri libsodium ile şifreler, güvenliği artırır.                  |
| **Async Computation** | Cache yenilemesini arka planda yaparak yanıt süresini kısaltır.       |
| **Cache Clearers**    | Tüm veya seçili cache havuzlarını temizler.                           |

---

> 💡 **Tavsiye:**
>
> Üretim ortamında `cache.adapter.redis_tag_aware` veya `cache.adapter.memcached` kullanarak
>
> zincirli yapı kurmak (ör. `array + redis`) performans ve esneklik açısından en verimli çözümdür.
