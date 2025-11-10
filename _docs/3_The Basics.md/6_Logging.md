
# Symfony Günlükleme (Logging)


```markdown


Symfony, iki minimal PSR-3 uyumlu günlükleyici (logger) ile birlikte gelir:  
- **Logger** (HTTP bağlamı için),  
- **ConsoleLogger** (CLI bağlamı için).  

🧩 **Twelve-Factor App** metodolojisine uygun olarak, bu günlükleyiciler **WARNING** seviyesinden itibaren olan mesajları `stderr`’e gönderir.

---

## 🔧 Minimum Günlük Seviyesi

Minimum günlük seviyesi `SHELL_VERBOSITY` ortam değişkeni ile ayarlanabilir:

| SHELL_VERBOSITY | Minimum Log Seviyesi |
|------------------|----------------------|
| -1               | ERROR                |
| 1                | NOTICE               |
| 2                | INFO                 |
| 3                | DEBUG                |

Ayrıca `Logger` ve `ConsoleLogger` sınıflarının kurucu metoduna uygun argümanlar göndererek minimum seviye, çıktı biçimi ve günlük formatı da değiştirilebilir.

> `Logger` sınıfı, `logger` servisi aracılığıyla erişilebilir.  
> Kendi yapılandırmanı geçirmek için `logger` servis tanımını ezebilirsin (override).

---

## 📝 Mesaj Günlüğe Kaydetmek

Bir mesaj kaydetmek için `LoggerInterface`’i controller ya da servisine enjekte et:

```php
use Psr\Log\LoggerInterface;

public function index(LoggerInterface $logger): Response
{
    $logger->info('I just got the logger');
    $logger->error('An error occurred');

    // placeholder (yer tutucu) içeren örnek
    $logger->debug('User {userId} has logged in', [
        'userId' => $this->getUserId(),
    ]);

    $logger->critical('I left the oven on!', [
        'cause' => 'in_hurry',
    ]);
}
```

### 🔹 Neden Placeholder Kullanılmalı?

* Log mesajlarını incelemek kolaylaşır (log gruplama yapılabilir).
* Mesajların çevirisi kolaylaşır.
* Güvenlik açısından daha iyidir, çünkü kaçış (escaping) işlemi bağlama göre yapılabilir.

`LoggerInterface`’te tüm log seviyeleri için metodlar bulunur (ör. `debug()`, `info()`, `error()`, `critical()` vb.).

---

## 🧱 Monolog Entegrasyonu

Symfony, en popüler PHP günlükleme kütüphanesi **Monolog** ile entegredir.

Monolog sayesinde logları farklı yerlere kaydedebilir, hata seviyesine göre farklı işlemler tetikleyebilirsin (ör. hata olduğunda e-posta gönderme).

Kurulum için:

```bash
composer require symfony/monolog-bundle
```

---

## 📂 Loglar Nerede Saklanır?

* **Geliştirme (dev)** ortamında: `var/log/dev.log`
* **Üretim (prod)** ortamında: `STDERR` akışı (container tabanlı dağıtımlar için ideal)

Eğer üretim loglarını dosyada saklamak istersen, log handler’ının yolunu (ör. `var/log/prod.log`) belirtmelisin.

---

## ⚙️ Handlers (Logların Yazıldığı Yerler)

Logger, bir **handler yığınına (stack)** sahiptir.

Her handler log girdilerini farklı yerlere (dosya, veritabanı, Slack vb.) yazabilir.

Ayrıca **kanallar (channels)** oluşturabilirsin — her kanal kendi handler'larına sahip olabilir.

Örnek:

```php
// config/packages/prod/monolog.php
use Psr\Log\LogLevel;
use Symfony\Config\MonologConfig;

return static function (MonologConfig $monolog): void {
    $monolog->handler('file_log')
        ->type('stream')
        ->path('%kernel.logs_dir%/%kernel.environment%.log')
        ->level(LogLevel::DEBUG);

    $monolog->handler('syslog_handler')
        ->type('syslog')
        ->level(LogLevel::ERROR);
};
```

Handler’lara **öncelik (priority)** değeri verebilirsin:

```php
$monolog->handler('syslog_handler')
    ->type('syslog')
    ->priority(10); // önce çalışır
```

---

## 🔁 Log Filtreleme ve Modifikasyon (fingers_crossed)

Bazı handler’lar logları filtreleyip başka handler’a yönlendirmek için kullanılır.

**fingers_crossed** handler’ı bunlardan biridir ve üretim ortamında varsayılan olarak aktiftir.

Örnek:

```php
$monolog->handler('filter_for_errors')
    ->type('fingers_crossed')
    ->actionLevel(LogLevel::ERROR)
    ->handler('file_log');

$monolog->handler('file_log')
    ->type('stream')
    ->path('%kernel.logs_dir%/%kernel.environment%.log')
    ->level(LogLevel::DEBUG);
```

➡️ Eğer bir `ERROR` veya daha yüksek seviye log oluşursa, o istekle ilgili **tüm loglar** kaydedilir.

Bu, hata ayıklamayı (debugging) çok daha kolay hale getirir.

---

## 🧰 Dahili (Built-in) Monolog Handler’ları

Monolog, e-posta, Loggly, Slack gibi birçok hedefe log gönderebilen handler’larla gelir.

Tüm liste için **Monolog Configuration** belgelerine bakabilirsin.

---

## 🔄 Log Dosyalarını Döndürme (Rotation)

Zamanla log dosyaları büyüyebilir.

Bunun için iki yöntem vardır:

1. **Linux logrotate** komutunu kullanmak,
2. **Monolog’un rotating_file handler’ını** kullanmak:

```php
$monolog->handler('main')
    ->type('rotating_file')
    ->path('%kernel.logs_dir%/%kernel.environment%.log')
    ->level(LogLevel::DEBUG)
    ->maxFiles(10); // en fazla 10 dosya tut
```

---

## 🧩 Servis İçinde Logger Kullanmak

Uygulaman otomatik servis yapılandırmasını (autoconfiguration) kullanıyorsa,

`Psr\Log\LoggerAwareInterface` uygulayan her servise otomatik olarak `setLogger()` çağrılır.

Belirli bir kanal için logger kullanmak istersen, `monolog.logger` etiketiyle tanımlayabilirsin.

---

## 🧾 Her Log’a Ek Veri Ekleme (Processor)

Monolog, log girdilerine dinamik olarak ekstra bilgi ekleyen **processor** işlevlerini destekler.

Örneğin, her isteğe özel bir kimlik (request token) ekleyebilirsin.

---

## 🕓 Uzun Süre Çalışan İşlemlerde Log Yönetimi

Uzun süreli işlemlerde Monolog bellekte log biriktirebilir.

Bu, bellek artışına veya mantıksız loglara neden olabilir.

Bu yüzden her iş arasında aşağıdaki gibi temizleme yapılmalıdır:

```php
$logger->reset();
```

---

✅ **Özetle:**

Symfony’nin logging sistemi, PSR-3 standartlarını izleyen güçlü bir yapı sunar.

Monolog ile birlikte kullanıldığında, uygulamanın tüm log yönetimi ihtiyaçlarını

– dosya rotasyonu, kanal yönetimi, filtreleme, processor ekleme – profesyonel düzeyde karşılar.
