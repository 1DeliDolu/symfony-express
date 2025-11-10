### 🇹🇷 **Symfony Framework’in Kurulumu ve Yapılandırılması**

---

#### 🧰 **Teknik Gereksinimler**

Symfony projesine başlamadan önce aşağıdakilerin kurulu olduğundan emin olun:

* **PHP 8.2 veya üstü**

  ve şu uzantılar (çoğu PHP kurulumunda varsayılan olarak yüklüdür):

  `Ctype`, `iconv`, `PCRE`, `Session`, `SimpleXML`, `Tokenizer`
* **Composer** → PHP paket yöneticisi
* **Symfony CLI (önerilir)** → Symfony uygulamanızı yerel ortamda çalıştırmak için gerekli araçları sunar.

Kurulum sonrası, sistem gereksinimlerini kontrol etmek için terminalde şu komutu çalıştırın:

```bash
symfony check:requirements
```

CLI açık kaynaklıdır, GitHub’da katkıda bulunabilirsiniz:

👉 [symfony-cli/symfony-cli](https://github.com/symfony-cli/symfony-cli)

---

#### 🚀 **Yeni Symfony Projesi Oluşturma**

Aşağıdaki komutlardan biriyle yeni bir Symfony projesi oluşturabilirsiniz:

```bash
symfony new my_project_directory --version="7.3.x" --webapp
```

veya

```bash
symfony new my_project_directory --version="7.3.x"
```

**Fark:**

`--webapp` seçeneği, web uygulaması geliştirmek için gerekli ek paketleri otomatik yükler.

Eğer Symfony CLI kullanmıyorsanız, Composer ile aynı işlemi şu şekilde yapabilirsiniz:

```bash
composer create-project symfony/skeleton:"7.3.x" my_project_directory
cd my_project_directory
composer require webapp
```

Kurulum tamamlandığında proje dizini (`my_project_directory/`) aşağıdakileri içerir:

* Temel klasör yapısı
* Gerekli bağımlılıklar
* Çalışmaya hazır Symfony uygulaması

📁 **Not:**

`var/cache/` ve `var/log/` dizinleri web sunucusu tarafından yazılabilir olmalıdır.

Erişim hatası yaşarsanız Symfony izin ayarları belgesine göz atın.

---

#### 🔄 **Var Olan Bir Symfony Projesini Kurmak**

Halihazırda mevcut bir Symfony projesiyle çalışacaksanız şu adımları izleyin:

```bash
cd projects/
git clone <repo_url>
cd my-project/
composer install
```

Daha sonra `.env` dosyasını kendi ortamınıza göre düzenleyin (ör. veritabanı bilgileri).

İlk kez çalıştırmadan önce proje hakkında bilgi almak için şu komutu kullanabilirsiniz:

```bash
php bin/console about
```

---

#### 💻 **Symfony Uygulamasını Çalıştırma**

Üretim ortamında **Apache** veya **Nginx** kullanılmalıdır.

Ancak **geliştirme** sürecinde en kolay yol Symfony’nin kendi yerel sunucusudur:

```bash
cd my-project/
symfony server:start
```

Ardından tarayıcıda şu adrese gidin:

👉 [http://localhost:8000](http://localhost:8000/)

Sunucuyu durdurmak için terminalde `Ctrl + C` tuşlarına basın.

> Bu sunucu yalnızca Symfony için değil, herhangi bir PHP projesi için de kullanılabilir.

---

#### 🐳 **Symfony Docker Entegrasyonu**

Symfony’yi Docker ile çalıştırmak isterseniz:

📘 [Using Docker with Symfony](https://symfony.com/doc/current/setup/docker.html) sayfasına bakın.

---

#### 📦 **Paket (Bundle) Kurulumu**

Symfony projelerinde özellik eklemek için **bundle** denilen paketler kullanılır.

Kurulum sırasında yapılandırma işlemlerini kolaylaştırmak için **Symfony Flex** kullanılır.

Flex, Composer ile entegredir ve şu komutları geliştirir:

```bash
composer require
composer update
composer remove
```

Örnek:

```bash
composer require logger
```

Flex yüklü değilse bu komut hata verir.

Ancak Flex varsa, Symfony’nin resmi “logger” paketlerini ve yapılandırmasını otomatik yükler.

Flex tarafından yapılan tüm işlemler `symfony.lock` dosyasına kaydedilir ve Git’e dahil edilmelidir.

---

#### 📚 **Symfony Flex Recipe Depoları**

Flex, yüklenebilir paketler için "recipe" adı verilen talimatlar kullanır.

* **Ana depo:** Güvenilir, bakımı yapılan paketler.
* **Contrib deposu:** Topluluk tarafından sağlanan ek tarifler (bazı paketler bakımsız olabilir).

---

#### 🧩 **Symfony Pack’leri**

Birden fazla paketin birlikte kurulması gerektiğinde, Symfony “pack” yapısını kullanır.

Örneğin hata ayıklama araçlarını eklemek için:

```bash
composer require --dev debug
```

Bu, `symfony/debug-pack` meta paketini yükler ve otomatik olarak şu bağımlılıkları içerir:

* `symfony/debug-bundle`
* `symfony/monolog-bundle`
* `symfony/var-dumper`

  vb.

Flex, bu “pack” içeriğini **ayrıştırarak** yalnızca gerçek paketleri `composer.json` dosyasına ekler.

---

✅ **Özetle:**

* Symfony CLI + Composer ile kurulum yapın
* Geliştirmede `symfony server:start` ile çalıştırın
* Paket yönetiminde Symfony Flex kullanın
* Gerektiğinde Docker veya web sunucusu entegrasyonu yapın

---


## [Checking Security Vulnerabilities](https://symfony.com/doc/current/setup.html#checking-security-vulnerabilities "Permalink to this headline")

The `symfony` binary created when you installed the [Symfony CLI](https://symfony.com/doc/current/setup.html#setup-symfony-cli) provides a command to check whether your project's dependencies contain any known security vulnerability:

Copy

```
symfony check:security
```

A good security practice is to execute this command regularly to be able to update or replace compromised dependencies as soon as possible. The security check is done locally by fetching the public [PHP security advisories database](https://github.com/FriendsOfPHP/security-advisories), so your `composer.lock` file is not sent on the network.

The `check:security` command terminates with a non-zero exit code if any of your dependencies is affected by a known security vulnerability. This way you can add it to your project build process and your continuous integration workflows to make them fail when there are vulnerabilities.

In continuous integration services you can check security vulnerabilities by running the `composer audit` command. This uses the same data internally as `check:security` but does not require installing the entire Symfony CLI during CI or on CI workers.

## [Symfony LTS Versions](https://symfony.com/doc/current/setup.html#symfony-lts-versions "Permalink to this headline")

According to the [Symfony release process](https://symfony.com/doc/current/contributing/community/releases.html), "long-term support" (or LTS for short) versions are published every two years. Check out the [Symfony releases](https://symfony.com/releases) to know which is the latest LTS version.

By default, the command that creates new Symfony applications uses the latest stable version. If you want to use an LTS version, add the `--version` option:

```

symfony new my_project_directory --version=lts


symfony new my_project_directory --version=next


symfony new my_project_directory --version="6.4.*"
```

The `lts` and `next` shortcuts are only available when using Symfony to create new projects. If you use Composer, you need to tell the exact version:

Copy

```
composer create-project symfony/skeleton:"6.4.*" my_project_directory
```

## [The Symfony Demo application](https://symfony.com/doc/current/setup.html#the-symfony-demo-application "Permalink to this headline")

[The Symfony Demo Application](https://github.com/symfony/demo) is a fully-functional application that shows the recommended way to develop Symfony applications. It's a great learning tool for Symfony newcomers and its code contains tons of comments and helpful notes.

Run this command to create a new project based on the Symfony Demo application:

Copy

```
symfony new my_project_directory --demo
```

## [Start Coding!](https://symfony.com/doc/current/setup.html#start-coding "Permalink to this headline")

With setup behind you, it's time to [Create your first page in Symfony](https://symfony.com/doc/current/page_creation.html).


### 🛡️ **Symfony'de Güvenlik Açıkları Kontrolü**

---

#### 🔍 **Güvenlik Açıklarını Kontrol Etme**

Symfony CLI ile birlikte gelen **`symfony`** komutu, projenizdeki bağımlılıkların bilinen güvenlik açıklarını kontrol etmenizi sağlar:

```bash
symfony check:security
```

Bu komutu düzenli olarak çalıştırmak iyi bir güvenlik uygulamasıdır.

Bu sayede, **tehlikeli veya güvenlik açığı içeren bağımlılıkları** hızlıca güncelleyebilir veya değiştirebilirsiniz.

🧠 **Önemli Notlar:**

* Güvenlik kontrolü **yerel olarak** yapılır.
* `composer.lock` dosyanız  **internet üzerinden gönderilmez** .
* Eğer bir bağımlılıkta bilinen bir güvenlik açığı varsa, komut **sıfırdan farklı (non-zero)** bir çıkış kodu döndürür.

  → Bu sayede CI (Continuous Integration) sistemlerinde güvenlik açıkları tespit edildiğinde  **build işlemini durdurabilirsiniz** .

---

#### ⚙️ **CI/CD Süreçlerinde Güvenlik Kontrolü**

Sürekli entegrasyon (CI) ortamlarında Symfony CLI kurulumuna gerek kalmadan aynı kontrolü şu komutla yapabilirsiniz:

```bash
composer audit
```

Bu komut da **aynı güvenlik veri tabanını** kullanır ve bağımlılıklarınızı denetler.

CI ortamında `symfony check:security` yerine `composer audit` kullanmak daha pratiktir.

---

### 🧩 **Symfony LTS (Long Term Support) Sürümleri**

Symfony sürüm planına göre her **iki yılda bir** uzun süreli destek (LTS) sürümü yayınlanır.

📅 En son LTS sürümünü öğrenmek için Symfony’nin resmi **[releases sayfasına](https://symfony.com/releases)** göz atabilirsiniz.

Yeni bir proje oluştururken, **LTS sürümünü** kullanmak istiyorsanız:

```bash
symfony new my_project_directory --version=lts
```

Diğer seçenekler:

```bash
symfony new my_project_directory --version=next
symfony new my_project_directory --version="6.4.*"
```

> 💡 `lts` ve `next` kısayolları yalnızca **Symfony CLI** komutlarında geçerlidir.
>
> Composer kullanıyorsanız tam sürümü belirtmeniz gerekir:

```bash
composer create-project symfony/skeleton:"6.4.*" my_project_directory
```

---

### 🧱 **Symfony Demo Uygulaması**

Symfony’nin resmi  **Demo Uygulaması** , örnek bir proje yapısı ve en iyi uygulamaları gösterir.

Yeni başlayanlar için harika bir öğrenme aracıdır ve kod içinde bolca açıklama bulunur.

Kurmak için:

```bash
symfony new my_project_directory --demo
```

Kurulum tamamlandıktan sonra, projeyi çalıştırabilir ve Symfony'nin nasıl yapılandığını canlı olarak görebilirsiniz.

---

### 🚀 **Artık Kod Yazmaya Başlama Zamanı!**

Kurulum tamamlandıysa, bir sonraki adım:

👉 **“Symfony’de İlk Sayfanı Oluştur”**

Bu noktadan sonra `Controller`, `Route` ve `Template` yapılarıyla Symfony’nin temel mimarisine giriş yapabilirsiniz.

---
