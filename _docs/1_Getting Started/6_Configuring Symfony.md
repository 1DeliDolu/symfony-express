### Symfony Yapılandırması (Configuring Symfony)

---

#### **Yapılandırma Dosyaları (Configuration Files)**

Symfony uygulamaları, varsayılan olarak aşağıdaki yapıya sahip olan `config/` dizininde bulunan dosyalarla yapılandırılır:

```
your-project/
├─ config/
│  ├─ packages/
│  ├─ bundles.php
│  ├─ routes.yaml
│  └─ services.yaml
```

* **routes.yaml** → yönlendirme yapılandırmasını tanımlar.
* **services.yaml** → servis konteynerindeki servisleri yapılandırır.
* **bundles.php** → uygulamada hangi paketlerin etkin olduğunu belirtir.
* **config/packages/** → yüklediğiniz her paketin yapılandırma dosyalarını içerir.

Symfony’de  **paketler (bundles)** , uygulamaya hazır özellikler ekleyen bileşenlerdir. Diğer sistemlerde bu yapılar “plugin” veya “modül” olarak da bilinir.

Symfony Flex varsayılan olarak etkindir. Paket yüklenirken `bundles.php` dosyasını ve `config/packages/` altındaki yapılandırma dosyalarını otomatik olarak günceller veya oluşturur.

Örneğin, **API Platform** paketi kurulduğunda aşağıdaki dosya oluşturulur:

```yaml
# config/packages/api_platform.yaml
api_platform:
    mapping:
        paths: ['%kernel.project_dir%/src/Entity']
```

Yapılandırmanın birçok küçük dosyaya bölünmesi başlangıçta karmaşık görünebilir, ancak kısa sürede alışılır. Üstelik genellikle paket kurulumu sonrası bu dosyaları sık sık değiştirmeniz gerekmez.

> Tüm yapılandırma seçeneklerini görmek için **Symfony Configuration Reference** belgelerine bakabilir veya `config:dump-reference` komutunu çalıştırabilirsiniz.

---

#### **Yapılandırma Biçimleri (Configuration Formats)**

Symfony, yapılandırma için tek bir format dayatmaz; şu biçimleri destekler:

* **YAML**
* **XML**
* **PHP**

Belgelerde genellikle bu üç formatta örnekler sunulur.

Performans açısından fark yoktur çünkü Symfony tüm formatları PHP’ye dönüştürüp önbelleğe alır.

**YAML** varsayılan olarak tercih edilir çünkü kısa ve okunaklıdır.

| Format         | Avantajlar                                      | Dezavantajlar                           |
| -------------- | ----------------------------------------------- | --------------------------------------- |
| **YAML** | Basit, temiz ve okunaklı                       | Bazı IDE’lerde otomatik tamamlama yok |
| **XML**  | Çoğu IDE tarafından desteklenir              | Fazla ayrıntılı ve uzun olabilir     |
| **PHP**  | Dinamik ve güçlü yapılandırma mümkündür | Daha fazla kod bilgisi gerektirir       |

Eğer yapılandırmayı XML formatında yazarsanız, `src/Kernel.php` içindeki `configureContainer()` ve/veya `configureRoutes()` metodlarını `.xml` uzantısını destekleyecek şekilde güncellemeniz gerekir.

---

#### **Yapılandırma Dosyalarını İçe Aktarma (Importing Configuration Files)**

Symfony yapılandırma dosyalarını **Config bileşeni** aracılığıyla yükler.

Bu bileşen, farklı formatlardaki dosyaları bile içe aktarma yeteneğine sahiptir:

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->import('legacy_config.php');

    // Birden fazla dosyayı yüklemek için glob ifadeleri kullanılabilir
    $container->import('/etc/myapp/*.yaml');

    // Üçüncü parametre 'ignore_errors' olarak belirtilir
    $container->import('my_config_file.yaml', null, 'not_found');
    $container->import('my_config_file.yaml', null, true);
};
```

---

#### **Yapılandırma Parametreleri (Configuration Parameters)**

Aynı yapılandırma değerini birden fazla dosyada kullanmak gerekiyorsa, **parametre** tanımlanabilir.

Parametreler genellikle `config/services.yaml` içinde `parameters` anahtarının altında tanımlanır:

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Entity\BlogPost;
use App\Enum\PostState;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('app.admin_email', 'something@example.com')
        ->set('app.enable_v2_protocol', true)
        ->set('app.supported_locales', ['en', 'es', 'fr'])
        ->set('app.some_parameter', 'This is a Bell char: \x07')
        ->set('app.some_constant', GLOBAL_CONSTANT)
        ->set('app.another_constant', BlogPost::MAX_ITEMS)
        ->set('app.some_enum', PostState::Published);
};
```

**XML kullanırken dikkat:**

`<parameter>` etiketleri arasındaki değerler varsayılan olarak  **trim edilmez** .

Yani aşağıdaki örnekte değer satır sonlarını da içerir:

```xml
<parameter key="app.admin_email">
    something@example.com
</parameter>
```

Eğer boşlukları kaldırmak istiyorsanız `trim="true"` ekleyin:

```xml
<parameter key="app.admin_email" trim="true">
    something@example.com
</parameter>
```

---

#### **Parametrelerin Kullanımı (Using Parameters)**

Tanımlanan bir parametre, diğer yapılandırma dosyalarında `%` işaretleri arasında kullanılır:

```php
// config/packages/some_package.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $container): void {
    $container->extension('some_package', [
        'email_address' => param('app.admin_email'),
        'email_address' => '%app.admin_email%',
    ]);
};
```

Eğer parametre değeri içinde `%` karakteri geçiyorsa, çift `%%` ile kaçırılmalıdır:

```php
$container->parameters()
    ->set('url_pattern', 'http://symfony.com/?foo=%%s&bar=%%d');
```

> Not: `%kernel.project_dir%` gibi parametreler dosya yolu içinde dinamik olarak kullanılamaz.

---

#### **Geçici Parametreler (Temporary Parameters)**

`.` (nokta) ile başlayan parametreler (örneğin `.mailer.transport`), yalnızca **container derlemesi sırasında** kullanılabilir.

Bu parametreler **Compiler Pass** işlemlerinde geçici veri olarak işe yarar.

---

#### **Parametre Doğrulama (Parameter Validation)**

Symfony 7.2 ile birlikte, belirli parametrelerin **boş olmamasını garanti altına almak** mümkündür:

```php
/** @var ContainerBuilder $container */
$container->parameterCannotBeEmpty(
    'app.private_key',
    'Did you forget to set a value for the "app.private_key" parameter?'
);
```

Bu doğrulama, derleme sırasında değil, parametre değeri alınırken çalışır.

Eğer değer `null`, boş string `''` veya boş dizi `[]` ise hata fırlatılır.

---




### Symfony Ortam Yapılandırması (Configuration Environments)

---

#### **Ortam Kavramı (What Are Environments?)**

Symfony’de tek bir uygulama olsa bile, bu uygulamanın **farklı durumlarda farklı şekilde çalışması** gerekir:

* **Geliştirme (dev):** Hataları görmek, debug araçlarını kullanmak, tüm olayları kaydetmek istersiniz.
* **Üretim (prod):** Performans odaklı, sadece hata kayıtları tutulur.
* **Test (test):** Otomatik testlerin çalıştığı özel bir ortamdır.

Bu farklı davranışlar, **hangi yapılandırma dosyalarının yüklendiği** ile kontrol edilir.

Symfony, bu dosyaları belirli bir sırayla yükler ve son yüklenen dosya öncekilerin değerlerini  **ezebilir (override)** .

---

#### **Varsayılan Üç Ortam (Default Environments)**

| Ortam Adı | Açıklama                   |
| ---------- | ---------------------------- |
| `dev`    | Yerel geliştirme ortamı    |
| `prod`   | Üretim sunucusu ortamı     |
| `test`   | Otomatik testler için ortam |

Symfony, yapılandırma dosyalarını şu sırayla yükler:

1. `config/packages/*.<extension>`
2. `config/packages/<environment-name>/*.<extension>`
3. `config/services.<extension>`
4. `config/services_<environment-name>.<extension>`

Örneğin:

* `config/packages/framework.yaml` → tüm ortamlar için geçerlidir.
* `config/packages/test/framework.yaml` → yalnızca test ortamında geçerli ek yapılandırmadır.

Bu sayede ortak yapılandırmalar `config/packages/` dizininde tutulur, sadece farklılıklar özel dizinlerde belirtilir.

---

#### **Aynı Dosya İçinde Ortama Göre Ayar (when / env koşulu)**

Aynı yapılandırma dosyasında farklı ortamlar için özel koşullar tanımlanabilir:

```php
// config/packages/framework.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\WebpackEncoreConfig;

return static function (WebpackEncoreConfig $webpackEncore, ContainerConfigurator $container): void {
    $webpackEncore
        ->outputPath('%kernel.project_dir%/public/build')
        ->strictMode(true)
        ->cache(false);

    // "prod" ortamında cache aktif
    if ('prod' === $container->env()) {
        $webpackEncore->cache(true);
    }

    // "test" ortamında strict mode kapalı
    if ('test' === $container->env()) {
        $webpackEncore->strictMode(false);
    }
};
```

---

#### **Etkin Ortamı Seçme (Selecting the Active Environment)**

Uygulama kök dizininde `.env` dosyası bulunur.

Bu dosya **ortam değişkenlerini** (environment variables) tanımlar.

```bash
# .env
APP_ENV=prod
```

Bu değişken hem web istekleri hem de CLI komutları için geçerlidir.

Ancak komut çalıştırırken geçici olarak değiştirebilirsiniz:

```bash
APP_ENV=prod php bin/console cache:clear
```

---

#### **Yeni Bir Ortam Oluşturma (Creating a New Environment)**

Yeni bir ortam (örneğin “staging”) tanımlamak için:

1. `config/packages/staging/` adında bir klasör oluşturun.
2. Bu dizine özel yapılandırma dosyalarınızı ekleyin.
3. `.env` dosyasında `APP_ENV=staging` olarak belirtin.

> Sık kullanılan bir yöntem: Benzer ortamlarda (`staging`, `preprod` gibi) dizinler arasında **symbolic link (sembolik bağlantı)** kullanmak.

Alternatif olarak, yeni ortam dizinleri oluşturmadan da ortam değişkenleri (env vars) üzerinden davranışı özelleştirebilirsiniz.

---

#### **Ortam Değişkenlerine Dayalı Yapılandırma (Configuration Based on Env Vars)**

**Environment variable (env var)** kullanmak, şu durumlarda faydalıdır:

* Uygulamanın çalıştığı yere göre değişen ayarlar (örneğin: veritabanı bağlantısı).
* Üretimde yeniden deploy etmeden değer değiştirme (örneğin: API anahtarı).

Kullanımı:

```php
// config/packages/framework.php
return static function (ContainerConfigurator $container): void {
    $container->extension('framework', [
        'secret' => '%env(APP_SECRET)%', // env var referansı
    ]);
};
```

Env var değerleri PHP’de şu şekilde de alınabilir:

```php
$databaseUrl = $_ENV['DATABASE_URL'];
$appEnv = $_SERVER['APP_ENV'];
```

Ancak Symfony içinde bu yöntemi kullanmak yerine **config dosyalarında %env(...)%** sözdizimi tercih edilir.

---

#### **Env Var Dönüştürücüler (Processors)**

Env var’lar sadece string olarak saklanabilir.

Symfony, bunları dönüştürmek için **env var processors** sağlar (örneğin string → int).

Eğer bir env var tanımlı değilse hata oluşur.

Varsayılan bir değer tanımlayarak bunu önleyebilirsiniz:

```php
// config/packages/framework.php
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Config\FrameworkConfig;

return static function (ContainerBuilder $container, FrameworkConfig $framework) {
    $container->setParameter('env(SECRET)', 'some_secret');
};
```

---

#### **.env Dosyalarında Ortam Değişkenlerini Tanımlama**

`.env` dosyası, proje kök dizininde bulunur ve **ortam değişkenlerini kolayca tanımlamanızı** sağlar.

```bash
# .env
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
```

Bu dosya  **commit edilebilir** , çünkü genellikle **geliştirme için varsayılan** değerleri içerir.

Üretim ortamı için kullanılmaz.

`.env` dosyası her istekte okunur, bu nedenle değişiklik sonrası önbelleği temizlemenize gerek yoktur.

---

#### **.env Dosyası Sözdizimi (Syntax)**

```bash
# Yorum satırı
DB_USER=root
DB_PASS=pass # gizli parola

# Diğer env var’ı kullanma
DB_PASS=${DB_USER}pass

# Varsayılan değer tanımlama
DB_PASS=${DB_USER:-root}pass

# Komut gömme (Windows desteklemez)
START_TIME=$(date)
```

Ayrıca `.env` dosyası bir shell betiği gibi **source** edilip dışarıda da kullanılabilir:

```bash
source .env
```

---

#### **.env.local ile Değerleri Ezme (Overriding Values)**

Makineye özel değerleri `.env.local` dosyasında tanımlayın:

```bash
# .env.local
DATABASE_URL="mysql://root:@127.0.0.1:3306/my_database"
```

`.env.local` dosyası **git tarafından yok sayılır** (`.gitignore` içinde).

Ek olarak şu dosyalar da mevcuttur:

| Dosya Adı          | Kapsam                         | Commit Edilir mi |
| ------------------- | ------------------------------ | ---------------- |
| `.env`            | Varsayılan değerler          | ✅               |
| `.env.local`      | Yerel makineye özel           | ❌               |
| `.env.test`       | Test ortamı için genel       | ✅               |
| `.env.test.local` | Test ortamında makineye özel | ❌               |

> Gerçek sistem ortam değişkenleri, `.env` dosyalarındaki değerlerin  **önüne geçer** .

---

#### **Sistem Değişkenlerini Ezmek (Overriding System Vars)**

Eğer sistemde tanımlı bir değişkeni geçersiz kılmak istiyorsanız:

```php
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/.env', overrideExistingVars: true);
```

Bu yöntem sistemdeki değişkenleri ezer ancak `.env` içindekileri etkilemez.

---

#### **Üretim Ortamında Env Var Tanımlama (Production Configuration)**

Üretimde `.env` dosyaları da yüklenir.

Ancak en iyi yöntem, sunucuda `.env.local` dosyası oluşturup üretim değerlerini burada tanımlamaktır.

Performans için şu komutu çalıştırabilirsiniz:

```bash
composer dump-env prod
```

Bu, `.env` dosyalarını önceden derleyip tek bir PHP dosyasına dönüştürür ve  **yükleme hızını artırır** .

---

### 🧭 Özet

| Amaç                         | Kullanım                               |
| ----------------------------- | --------------------------------------- |
| Ortam belirleme               | `APP_ENV=prod`                        |
| Ortam değişkeni kullanma    | `%env(DB_HOST)%`                      |
| Ortam dosyası                | `.env`,`.env.local`,`.env.prod`   |
| Üretim performansı          | `composer dump-env prod`              |
| Varsayılan değer tanımlama | `setParameter('env(VAR)', 'default')` |

---


### Symfony Ortam Yapılandırması (Configuration Environments)

---

#### **Ortam Kavramı (What Are Environments?)**

Symfony’de tek bir uygulama olsa bile, bu uygulamanın **farklı durumlarda farklı şekilde çalışması** gerekir:

* **Geliştirme (dev):** Hataları görmek, debug araçlarını kullanmak, tüm olayları kaydetmek istersiniz.
* **Üretim (prod):** Performans odaklı, sadece hata kayıtları tutulur.
* **Test (test):** Otomatik testlerin çalıştığı özel bir ortamdır.

Bu farklı davranışlar, **hangi yapılandırma dosyalarının yüklendiği** ile kontrol edilir.

Symfony, bu dosyaları belirli bir sırayla yükler ve son yüklenen dosya öncekilerin değerlerini  **ezebilir (override)** .

---

#### **Varsayılan Üç Ortam (Default Environments)**

| Ortam Adı | Açıklama                   |
| ---------- | ---------------------------- |
| `dev`    | Yerel geliştirme ortamı    |
| `prod`   | Üretim sunucusu ortamı     |
| `test`   | Otomatik testler için ortam |

Symfony, yapılandırma dosyalarını şu sırayla yükler:

1. `config/packages/*.<extension>`
2. `config/packages/<environment-name>/*.<extension>`
3. `config/services.<extension>`
4. `config/services_<environment-name>.<extension>`

Örneğin:

* `config/packages/framework.yaml` → tüm ortamlar için geçerlidir.
* `config/packages/test/framework.yaml` → yalnızca test ortamında geçerli ek yapılandırmadır.

Bu sayede ortak yapılandırmalar `config/packages/` dizininde tutulur, sadece farklılıklar özel dizinlerde belirtilir.

---

#### **Aynı Dosya İçinde Ortama Göre Ayar (when / env koşulu)**

Aynı yapılandırma dosyasında farklı ortamlar için özel koşullar tanımlanabilir:

```php
// config/packages/framework.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\WebpackEncoreConfig;

return static function (WebpackEncoreConfig $webpackEncore, ContainerConfigurator $container): void {
    $webpackEncore
        ->outputPath('%kernel.project_dir%/public/build')
        ->strictMode(true)
        ->cache(false);

    // "prod" ortamında cache aktif
    if ('prod' === $container->env()) {
        $webpackEncore->cache(true);
    }

    // "test" ortamında strict mode kapalı
    if ('test' === $container->env()) {
        $webpackEncore->strictMode(false);
    }
};
```

---

#### **Etkin Ortamı Seçme (Selecting the Active Environment)**

Uygulama kök dizininde `.env` dosyası bulunur.

Bu dosya **ortam değişkenlerini** (environment variables) tanımlar.

```bash
# .env
APP_ENV=prod
```

Bu değişken hem web istekleri hem de CLI komutları için geçerlidir.

Ancak komut çalıştırırken geçici olarak değiştirebilirsiniz:

```bash
APP_ENV=prod php bin/console cache:clear
```

---

#### **Yeni Bir Ortam Oluşturma (Creating a New Environment)**

Yeni bir ortam (örneğin “staging”) tanımlamak için:

1. `config/packages/staging/` adında bir klasör oluşturun.
2. Bu dizine özel yapılandırma dosyalarınızı ekleyin.
3. `.env` dosyasında `APP_ENV=staging` olarak belirtin.

> Sık kullanılan bir yöntem: Benzer ortamlarda (`staging`, `preprod` gibi) dizinler arasında **symbolic link (sembolik bağlantı)** kullanmak.

Alternatif olarak, yeni ortam dizinleri oluşturmadan da ortam değişkenleri (env vars) üzerinden davranışı özelleştirebilirsiniz.

---

#### **Ortam Değişkenlerine Dayalı Yapılandırma (Configuration Based on Env Vars)**

**Environment variable (env var)** kullanmak, şu durumlarda faydalıdır:

* Uygulamanın çalıştığı yere göre değişen ayarlar (örneğin: veritabanı bağlantısı).
* Üretimde yeniden deploy etmeden değer değiştirme (örneğin: API anahtarı).

Kullanımı:

```php
// config/packages/framework.php
return static function (ContainerConfigurator $container): void {
    $container->extension('framework', [
        'secret' => '%env(APP_SECRET)%', // env var referansı
    ]);
};
```

Env var değerleri PHP’de şu şekilde de alınabilir:

```php
$databaseUrl = $_ENV['DATABASE_URL'];
$appEnv = $_SERVER['APP_ENV'];
```

Ancak Symfony içinde bu yöntemi kullanmak yerine **config dosyalarında %env(...)%** sözdizimi tercih edilir.

---

#### **Env Var Dönüştürücüler (Processors)**

Env var’lar sadece string olarak saklanabilir.

Symfony, bunları dönüştürmek için **env var processors** sağlar (örneğin string → int).

Eğer bir env var tanımlı değilse hata oluşur.

Varsayılan bir değer tanımlayarak bunu önleyebilirsiniz:

```php
// config/packages/framework.php
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Config\FrameworkConfig;

return static function (ContainerBuilder $container, FrameworkConfig $framework) {
    $container->setParameter('env(SECRET)', 'some_secret');
};
```

---

#### **.env Dosyalarında Ortam Değişkenlerini Tanımlama**

`.env` dosyası, proje kök dizininde bulunur ve **ortam değişkenlerini kolayca tanımlamanızı** sağlar.

```bash
# .env
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
```

Bu dosya  **commit edilebilir** , çünkü genellikle **geliştirme için varsayılan** değerleri içerir.

Üretim ortamı için kullanılmaz.

`.env` dosyası her istekte okunur, bu nedenle değişiklik sonrası önbelleği temizlemenize gerek yoktur.

---

#### **.env Dosyası Sözdizimi (Syntax)**

```bash
# Yorum satırı
DB_USER=root
DB_PASS=pass # gizli parola

# Diğer env var’ı kullanma
DB_PASS=${DB_USER}pass

# Varsayılan değer tanımlama
DB_PASS=${DB_USER:-root}pass

# Komut gömme (Windows desteklemez)
START_TIME=$(date)
```

Ayrıca `.env` dosyası bir shell betiği gibi **source** edilip dışarıda da kullanılabilir:

```bash
source .env
```

---

#### **.env.local ile Değerleri Ezme (Overriding Values)**

Makineye özel değerleri `.env.local` dosyasında tanımlayın:

```bash
# .env.local
DATABASE_URL="mysql://root:@127.0.0.1:3306/my_database"
```

`.env.local` dosyası **git tarafından yok sayılır** (`.gitignore` içinde).

Ek olarak şu dosyalar da mevcuttur:

| Dosya Adı          | Kapsam                         | Commit Edilir mi |
| ------------------- | ------------------------------ | ---------------- |
| `.env`            | Varsayılan değerler          | ✅               |
| `.env.local`      | Yerel makineye özel           | ❌               |
| `.env.test`       | Test ortamı için genel       | ✅               |
| `.env.test.local` | Test ortamında makineye özel | ❌               |

> Gerçek sistem ortam değişkenleri, `.env` dosyalarındaki değerlerin  **önüne geçer** .

---

#### **Sistem Değişkenlerini Ezmek (Overriding System Vars)**

Eğer sistemde tanımlı bir değişkeni geçersiz kılmak istiyorsanız:

```php
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/.env', overrideExistingVars: true);
```

Bu yöntem sistemdeki değişkenleri ezer ancak `.env` içindekileri etkilemez.

---

#### **Üretim Ortamında Env Var Tanımlama (Production Configuration)**

Üretimde `.env` dosyaları da yüklenir.

Ancak en iyi yöntem, sunucuda `.env.local` dosyası oluşturup üretim değerlerini burada tanımlamaktır.

Performans için şu komutu çalıştırabilirsiniz:

```bash
composer dump-env prod
```

Bu, `.env` dosyalarını önceden derleyip tek bir PHP dosyasına dönüştürür ve  **yükleme hızını artırır** .

---

### 🧭 Özet

| Amaç                         | Kullanım                               |
| ----------------------------- | --------------------------------------- |
| Ortam belirleme               | `APP_ENV=prod`                        |
| Ortam değişkeni kullanma    | `%env(DB_HOST)%`                      |
| Ortam dosyası                | `.env`,`.env.local`,`.env.prod`   |
| Üretim performansı          | `composer dump-env prod`              |
| Varsayılan değer tanımlama | `setParameter('env(VAR)', 'default')` |

---


### Symfony Ortam Değişkenlerini Yönetme (Advanced Environment Variable Management)

---

#### **Composer Olmadan Ortam Değişkenlerini Derleme (Dumping Environment Variables without Composer)**

Üretim ortamında Composer kurulu değilse, `dotenv:dump` komutunu kullanabilirsiniz.

Bu komut **Symfony Flex 1.2+** sürümünde mevcuttur, ancak  **varsayılan olarak kayıtlı değildir** .

Öncelikle servis olarak kaydedilmelidir:

```yaml
# config/services.yaml
services:
    Symfony\Component\Dotenv\Command\DotenvDumpCommand: ~
```

Daha sonra aşağıdaki komut çalıştırılır:

```bash
APP_ENV=prod APP_DEBUG=0 php bin/console dotenv:dump
```

Bu komut çalıştırıldığında Symfony, `.env.local.php` dosyasını oluşturur ve artık `.env` dosyalarını  **her istek sırasında ayrıştırmaz** .

Bu sayede  **uygulama açılış süresi kısalır** .

> 💡 **Not:** Her deploy işleminden sonra `dotenv:dump` komutunu otomatik çalıştıracak şekilde deployment süreçlerinizi güncelleyin.

---

#### **Ortam Değişkenlerini Farklı Dosyalarda Saklama (Storing Env Vars in Other Files)**

Varsayılan olarak tüm env değişkenleri proje kök dizinindeki `.env` dosyasında bulunur.

Ancak Symfony bu dosyanın yolunu özelleştirmenize izin verir.

##### **1️⃣ Runtime bileşeni kullanarak:**

`composer.json` içine aşağıdaki ayarı ekleyin:

```json
{
    "extra": {
        "runtime": {
            "dotenv_path": "my/custom/path/to/.env"
        }
    }
}
```

##### **2️⃣ bootstrap.php içinde doğrudan yükleme:**

```php
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(dirname(__DIR__).'my/custom/path/to/.env');
```

Bu durumda Symfony, belirtilen özel `.env` dosyasını ve o ortama özel `.env.local` veya `.env.<environment>` dosyalarını da yükler.

> 📘 **Yeni Özellik (Symfony 7.1):**
>
> Symfony şu anda hangi `.env` dosyasını kullandığını `SYMFONY_DOTENV_PATH` ortam değişkeninde saklar.

---

#### **Gizli Değerleri Şifreleme (Encrypting Env Vars / Secrets)**

Eğer ortam değişkeniniz hassas bir bilgi içeriyorsa (örneğin API anahtarı veya veritabanı parolası), bunu `.env` içine açıkça yazmak yerine Symfony’nin **secrets management** sistemini kullanabilirsiniz.

Bu özellik ortam değişkenlerini **şifreli olarak** saklar ve yalnızca çalıştırma anında çözülür.

---

#### **Ortam Değişkenlerini Listeleme (Listing Environment Variables)**

Symfony’nin hangi ortam değişkenlerini nasıl çözdüğünü görmek için şu komutu kullanın:

```bash
php bin/console debug:dotenv
```

Örnek çıktı:

```
Dotenv Variables & Files
========================

Scanned Files
--------------------------------------
* ⨯ .env.local.php
* ⨯ .env.dev.local
* ✓ .env.dev
* ⨯ .env.local
* ✓ .env

Variables
--------------------------------------
 Variable   Value   .env.dev   .env
 FOO        BAR     n/a        BAR
 ALICE      BOB     BOB        bob
```

Tek bir değişkeni görmek için:

```bash
php bin/console debug:dotenv foo
```

Ayrıca container’daki tüm environment değişkenlerini ve kullanım sayılarını görmek için:

```bash
php bin/console debug:container --env-vars
```

Örnek çıktı:

```
------------ ----------------- ------------------------------------ -------------
 Name         Default value     Real value                           Usage count
------------ ----------------- ------------------------------------ -------------
 APP_SECRET   n/a               "471a62e2d601a8952deb186e44186cb3"   2
 BAR          n/a               n/a                                  1
 BAZ          n/a               "value"                              0
 FOO          "[1, "2.5", 3]"   n/a                                  1
```

Belirli bir değişkeni görmek için:

```bash
php bin/console debug:container --env-var=FOO
```

---

#### **Kendi Ortam Yükleme Mantığını Oluşturma (Creating Custom EnvVar Loader)**

Varsayılan Symfony env yükleme mekanizması ihtiyaçlarınıza uygun değilse,

kendi sınıfınızı tanımlayabilirsiniz.

Yeni servisiniz **EnvVarLoaderInterface** arayüzünü uygulamalıdır.

Örnek olarak `env.json` adlı bir dosyadaki değişkenleri yükleyen bir sınıf:

```php
namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\EnvVarLoaderInterface;

final class JsonEnvVarLoader implements EnvVarLoaderInterface
{
    private const ENV_VARS_FILE = 'env.json';

    public function loadEnvVars(): array
    {
        $fileName = __DIR__ . \DIRECTORY_SEPARATOR . self::ENV_VARS_FILE;
        if (!is_file($fileName)) {
            // hata fırlatabilir veya sadece atlayabilirsiniz
        }

        $content = json_decode(file_get_contents($fileName), true);

        return $content['vars'];
    }
}
```

Böylece Symfony artık `.env` dosyalarına ek olarak `env.json` içeriğini de yükler.

> Eğer bir ortamda değeri sıfırlamak ve loader’ların devreye girmesini istiyorsanız:
>
> ```bash
> # .env
> APP_ENV=prod
>
> # .env.prod
> APP_ENV=
> ```

---

#### **Yapılandırma Parametrelerine Erişim (Accessing Configuration Parameters)**

Tüm parametrelere erişmek için:

```bash
php bin/console debug:container --parameters
```

##### **Controller içinde erişim:**

```php
// src/Controller/UserController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    public function index(): Response
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $adminEmail = $this->getParameter('app.admin_email');
    }
}
```

##### **Service içinde parametre enjeksiyonu:**

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Service\MessageGenerator;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('app.contents_dir', '/var/data');

    $container->services()
        ->get(MessageGenerator::class)
            ->arg('$contentsDir', '%app.contents_dir%');
};
```

##### **Tekrarlayan parametreler için bind kullanımı:**

```php
$container->services()
    ->defaults()
        ->bind('$projectDir', '%kernel.project_dir%');
```

##### **Tüm parametreleri almak için ContainerBagInterface:**

```php
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class MessageGenerator
{
    public function __construct(private ContainerBagInterface $params) {}

    public function someMethod(): void
    {
        $sender = $this->params->get('mailer_sender');
    }
}
```

---

#### **PHP ConfigBuilders Kullanımı**

Büyük yapılandırmalarda uzun diziler yerine **ConfigBuilder** nesneleri kullanılabilir.

Symfony, her bundle için bu sınıfları otomatik üretir.

```php
// config/packages/security.php
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $security->firewall('main')
        ->pattern('^/*')
        ->lazy(true)
        ->security(false);

    $security
        ->roleHierarchy('ROLE_ADMIN', ['ROLE_USER'])
        ->roleHierarchy('ROLE_SUPER_ADMIN', ['ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH'])
        ->accessControl()
            ->path('^/user')
            ->roles('ROLE_USER');
};
```

> Bu sınıflar `var/cache/dev/Symfony/Config/` dizininde bulunur.
>
> IDE’nizin bu dizini hariç tutmadığından emin olun, aksi halde autocompletion çalışmaz.

---

### 🎯 **Özet**

| Konu                             | Komut / Dosya                                   | Açıklama                                      |
| -------------------------------- | ----------------------------------------------- | ----------------------------------------------- |
| Env dump (Composer olmadan)      | `dotenv:dump`                                 | `.env.local.php`oluşturur                    |
| Ortam değişkenlerini listeleme | `debug:dotenv`,`debug:container --env-vars` | Tüm env değişkenlerini gösterir             |
| Özel env yükleyici             | `EnvVarLoaderInterface`                       | `env.json`gibi özel kaynaklardan yükleme    |
| Parametre erişimi               | `getParameter()`,`ContainerBagInterface`    | Controller ve service içinde erişim           |
| ConfigBuilder                    | `Symfony\Config\*`                            | PHP yapılandırmasında autocompletion sağlar |

---
