### Kernel’de Yapılandırma

Symfony uygulamaları, varsayılan olarak `src/Kernel.php` konumunda bulunan **kernel sınıfı** tanımlar. Bu sınıf, çeşitli yapılandırılabilir seçenekleri içerir. Aşağıda bu seçeneklerin nasıl yapılandırılacağı ve Symfony’nin bu yapılandırmalara göre oluşturduğu **container parametreleri** açıklanmıştır.

---

#### **kernel.build_dir**

**Tür:** `string`

**Varsayılan:** `$this->getCacheDir()`

Bu parametre, Symfony uygulamanızın **build (inşa)** dizininin tam yolunu saklar. Bu dizin, yalnızca okuma amaçlı önbellek (derlenmiş container) ile yazılabilir önbelleği (ör. cache pool’ları) ayırmak için kullanılabilir.

Eğer uygulamanız **salt okunur bir dosya sistemine** (örneğin Docker veya AWS Lambda) dağıtılıyorsa, özel bir yol belirtmeniz önerilir.

Bu değer `Kernel` sınıfındaki `getBuildDir()` metodu aracılığıyla da erişilebilir ve isterseniz bu metodu **override** edebilirsiniz.

Ayrıca, ortam değişkeni olarak `APP_BUILD_DIR` tanımlayarak bu klasörü değiştirebilirsiniz.

---

#### **kernel.bundles**

**Tür:** `array`

**Varsayılan:** `[]`

Uygulamada kayıtlı tüm **bundle’ların listesi** ve ana bundle sınıflarının tam adlarını (FQCN) saklar:

```php
[
    'FrameworkBundle' => 'Symfony\Bundle\FrameworkBundle\FrameworkBundle',
    'TwigBundle' => 'Symfony\Bundle\TwigBundle\TwigBundle',
]
```

Bu değer `getBundles()` metodu aracılığıyla da alınabilir.

---

#### **kernel.bundles_metadata**

**Tür:** `array`

**Varsayılan:** `[]`

Uygulamada kayıtlı tüm bundle’ların yolları ve namespace bilgilerini içerir:

```php
[
    'FrameworkBundle' => [
        'path' => '/proje-dizini/vendor/symfony/framework-bundle',
        'namespace' => 'Symfony\Bundle\FrameworkBundle',
    ],
    'TwigBundle' => [
        'path' => '/proje-dizini/vendor/symfony/twig-bundle',
        'namespace' => 'Symfony\Bundle\TwigBundle',
    ],
]
```

Bu bilgi yalnızca **container parametresi** üzerinden erişilebilir.

---

#### **kernel.cache_dir**

**Tür:** `string`

**Varsayılan:** `$this->getProjectDir()/var/cache/$this->environment`

Uygulamanın önbellek dizinini belirtir. Symfony, aktif **ortama (environment)** göre bu yolu otomatik olarak oluşturur.

`getCacheDir()` metodu override edilerek değiştirilebilir.

---

#### **kernel.charset**

**Tür:** `string`

**Varsayılan:** `UTF-8`

Uygulamada kullanılan karakter setini belirtir.

Örneğin ISO-8859-1 kullanmak için:

```php
class Kernel extends BaseKernel
{
    public function getCharset(): string
    {
        return 'ISO-8859-1';
    }
}
```

---

#### **kernel.container_build_time**

**Tür:** `string`

**Varsayılan:** `time()` sonucu

Symfony, **reproducible builds (yeniden üretilebilir derlemeler)** felsefesini izler. Aynı kaynak koddan derleme yapıldığında aynı sonucu elde etmek için kullanılır.

Eğer container.build_time her derlemede değişiyorsa, **sabit bir zaman** belirterek bu durumu engelleyebilirsiniz:

```php
$container->setParameter('kernel.container_build_time', '1234567890');
```

---

#### **kernel.container_class**

**Tür:** `string`

**Varsayılan:** Ortam ve debug moduna göre otomatik üretilir.

Örneğin:

```
App_KernelDevDebugContainer
```

İsterseniz özel bir sınıf adı döndürebilirsiniz:

```php
public function getContainerClass(): string
{
    return sprintf('AcmeKernel%s', random_int(10000, 99999));
}
```

---

#### **kernel.debug**

**Tür:** `boolean`

Debug modunun açık olup olmadığını belirtir.

---

#### **kernel.default_locale** , **kernel.enabled_locales**

`framework.default_locale` ve `framework.enabled_locales` değerlerini saklar.

---

#### **kernel.environment**

**Tür:** `string`

Uygulamanın çalıştığı **konfigürasyon ortamını** belirtir (örneğin `dev`, `prod`).

`kernel.runtime_environment` ile karıştırılmamalıdır; runtime ortamı uygulamanın  **nerede dağıtıldığını** , environment ise **hangi ayarlarla çalıştığını** belirtir.

---

#### **kernel.logs_dir**

**Tür:** `string`

**Varsayılan:** `$this->getProjectDir()/var/log`

Uygulamanın log dosyalarının saklandığı dizindir.

`getLogDir()` metodu override edilebilir.

---

#### **kernel.project_dir**

**Tür:** `string`

**Varsayılan:** `composer.json` dosyasının bulunduğu dizin

Proje kök dizinini belirtir. Composer kullanmıyorsanız veya `composer.json` dosyasını taşıdıysanız aşağıdaki şekilde değiştirebilirsiniz:

```php
public function getProjectDir(): string
{
    return \dirname(__DIR__);
}
```

---

#### **kernel.runtime_environment**

**Tür:** `string`

**Varsayılan:** `%env(default:kernel.environment:APP_RUNTIME_ENV)%`

Uygulamanın **çalıştığı ortamın** adını belirtir (örneğin staging, production).

---

#### **kernel.runtime_mode**

**Tür:** `string`

**Varsayılan:** `%env(query_string:default:container.runtime_mode:APP_RUNTIME_MODE)%`

Uygulamanın çalıştığı modu tanımlar:

* `web=1&worker=0` → web modu
* `web=1&worker=1` → uzun süreli (worker) web sunucusu modu

---

#### **kernel.runtime_mode.web / cli / worker**

Uygulamanın hangi ortamda (web, CLI, worker) çalıştığını belirtir.

---

#### **kernel.secret**

**Tür:** `string`

**Varsayılan:** `%env(APP_SECRET)%`

`framework.secret` değerini içerir.

---

#### **kernel.trust_x_sendfile_type_header** ,  **kernel.trusted_hosts** , **kernel.trusted_proxies**

Bu parametreler, `framework` yapılandırmasında tanımlanan karşılıklarını saklar.

---

### 🧩 Özetle

Symfony Kernel yapılandırması, uygulamanızın çalışma şeklini, dosya yollarını, charset ayarlarını, ortam parametrelerini ve bundle yapılarını merkezi olarak kontrol etmenizi sağlar.

Bu ayarların çoğu `Kernel` sınıfında override edilerek özelleştirilebilir veya `.env` dosyası üzerinden dinamik hale getirilebilir.
