### 🧩 **Symfony’de İlk Sayfanı Oluşturmak**

---

#### 📘 **Genel Bakış**

Symfony’de yeni bir sayfa oluşturmak – ister bir  **HTML sayfası** , ister bir **JSON endpoint** olsun – **iki temel adımdan** oluşur:

1. **Controller oluşturmak:**

   Gelen HTTP isteğini işleyip bir **Response** (yanıt) döndüren PHP sınıfıdır.

   Bu yanıt HTML, JSON veya bir dosya (ör. PDF, resim) olabilir.
2. **Route tanımlamak:**

   Sayfaya erişilecek **URL yolunu** (`/about`, `/lucky/number` gibi) belirler ve bu URL’yi controller’daki metoda yönlendirir.

---

### 🚀 **1. Adım – Controller Oluşturma**

Yeni bir sayfa için bir controller sınıfı oluşturun.

Örnek olarak `/lucky/number` sayfasını yapalım:

📄 **`src/Controller/LuckyController.php`**

```php
<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class LuckyController
{
    public function number(): Response
    {
        $number = random_int(0, 100);

        return new Response(
            '<html><body>Lucky number: ' . $number . '</body></html>'
        );
    }
}
```

Bu sınıfta:

* `number()` metodu çalıştığında **0 ile 100 arasında rastgele bir sayı** üretir.
* Ardından bu sayıyı HTML olarak döner.

---

### 🌐 **2. Adım – Route Tanımlama**

Şimdi bu controller metodunu belirli bir URL’ye bağlayalım.

Bunu PHP **attribute** (öznitelik) olarak ekleyeceğiz:

📄 **`src/Controller/LuckyController.php` (güncellenmiş)**

```php
<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LuckyController
{
    #[Route('/lucky/number')]
    public function number(): Response
    {
        $number = random_int(0, 100);

        return new Response(
            '<html><body>Lucky number: ' . $number . '</body></html>'
        );
    }
}
```

💡 Şimdi tarayıcıda şu adrese gidin:

👉 [http://localhost:8000/lucky/number](http://localhost:8000/lucky/number)

Ve karşınıza rastgele bir “Lucky number” çıktısı gelecektir 🎉

---

### 🧠 **Symfony’de Route ve Controller Mantığı**

* **Controller:** Sayfanın içeriğini üretir (HTML, JSON, dosya vb.)
* **Route:** Hangi URL isteğinin hangi controller metoduna gideceğini belirler.

Symfony, route’ları genellikle **attribute** şeklinde controller dosyasında tutmayı önerir.

Ama isterseniz route’ları ayrı dosyalarda da tanımlayabilirsiniz:

* `config/routes.yaml`
* `config/routes.php`
* `config/routes.xml`

---

### ⚙️ **Symfony Konsol Komutları**

Symfony projeleri güçlü bir CLI aracına sahiptir:

```bash
php bin/console
```

Bu komut, proje içinde kullanılabilecek tüm komutları listeler:

* Debugging
* Kod üretme (generate)
* Migration işlemleri
* ve daha fazlası…

Yeni route’ların yüklendiğini görmek için:

```bash
php bin/console debug:router
```

🧾 Örnek çıktı:

```
----------------  -------  -------  -----  --------------
Name              Method   Scheme   Host   Path
----------------  -------  -------  -----  --------------
app_lucky_number  ANY      ANY      ANY    /lucky/number
----------------  -------  -------  -----  --------------
```

Burada `app_lucky_number` senin yeni route’un adıdır.

---

### ⚡ **Otomatik Tamamlama (Console Completion)**

Eğer terminaliniz destekliyorsa, `bin/console` komutları için otomatik tamamlama (autocomplete) özelliğini de aktif edebilirsiniz.

Bu, komut isimlerini ve argümanlarını yazarken zaman kazandırır.

Ayrıntılı bilgi için Symfony’nin **Console belgesine** göz atabilirsiniz.

---

### ✅ **Özetle**

| Adım | Açıklama          | Örnek                                 |
| ----- | ------------------- | -------------------------------------- |
| 1️⃣ | Controller oluştur | `LuckyController::number()`          |
| 2️⃣ | Route tanımla      | `#[Route('/lucky/number')]`          |
| 3️⃣ | Çalıştır        | `symfony server:start`               |
| 4️⃣ | Test et             | `http://localhost:8000/lucky/number` |

---



### 🧰 **Symfony Web Debug Toolbar: Hata Ayıklamanın Rüya Hali**

---

#### 🧠 **Web Debug Toolbar Nedir?**

Symfony’nin en harika özelliklerinden biri olan  **Web Debug Toolbar** , geliştirme sırasında sayfanın alt kısmında beliren siyah bir çubuktur.

Bu araç çubuğu,  **performans** ,  **route bilgileri** ,  **veritabanı sorguları** ,  **loglar** , **cache durumu** ve daha fazlası hakkında anlık bilgi verir.

Bu özellik, Symfony ile birlikte gelen **`symfony/profiler-pack`** paketi sayesinde **otomatik** olarak aktif hale gelir.

💡 Tarayıcıda sayfanızı çalıştırdığınızda, alt kısımda koyu renkli bir bar göreceksiniz.

Farenizi ikonların üzerine getirin veya tıklayın: yönlendirmeler, performans ölçümleri ve hata loglarını görebilirsiniz.

---

### 🎨 **HTML Sayfası Render Etmek (Twig ile)**

Controller’dan HTML döndürürken genellikle **Twig** şablon motorunu kullanırız.

Twig, sade, güçlü ve eğlenceli bir şablon dilidir.

---

#### ⚙️ **1. Twig Kurulumu**

Twig paketini yükleyin:

```bash
composer require twig
```

Bu komut, gerekli Twig bağımlılıklarını yükler ve `templates/` dizinini otomatik olarak oluşturur.

---

#### 🧩 **2. Controller’ı Güncelleme**

`LuckyController` sınıfını Symfony’nin `AbstractController` sınıfından türetelim.

Bu sayede `$this->render()` metodunu kullanabileceğiz.

📄 **`src/Controller/LuckyController.php`**

```php
<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LuckyController extends AbstractController
{
    #[Route('/lucky/number')]
    public function number(): Response
    {
        $number = random_int(0, 100);

        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
    }
}
```

Burada `render()` metodu:

* `templates/lucky/number.html.twig` dosyasını işler,
* `number` değişkenini Twig’e aktarır.

---

#### 🧾 **3. Twig Template Oluşturma**

📄 **`templates/lucky/number.html.twig`**

```twig
{# templates/lucky/number.html.twig #}
<h1>Your lucky number is {{ number }}</h1>
```

`{{ number }}` ifadesi Twig’de değişkenleri yazdırmak için kullanılır.

Tarayıcıda şu adrese gidin:

👉 [http://localhost:8000/lucky/number](http://localhost:8000/lucky/number)

Ve karşınıza şanslı sayınız çıkacak 🍀

---

#### ⚠️ **Web Debug Toolbar Görünmüyor mu?**

Eğer sayfanın altında Web Debug Toolbar görünmüyorsa, sebebi şudur:

* Şu anki template’inizde  **`</body>` etiketi yok** .

Çözüm:

1. Template’e `<body>` etiketi ekleyin, **veya**
2. Symfony’nin varsayılan `base.html.twig` dosyasını **extend** edin.

```twig
{% extends 'base.html.twig' %}

{% block body %}
    <h1>Your lucky number is {{ number }}</h1>
{% endblock %}
```

Bu şekilde toolbar tekrar görünür hale gelecektir.

---

### 🏗️ **Proje Dizini Yapısı**

Artık Symfony’nin en önemli dizinlerini kullanmaya başladınız 👇

| Dizin                | Açıklama                                                                                        |
| -------------------- | ------------------------------------------------------------------------------------------------- |
| **config/**    | Route, servis ve paket yapılandırmaları burada yapılır.                                      |
| **src/**       | PHP kodlarının (controller, entity, repository vb.) bulunduğu ana dizindir.                    |
| **templates/** | Twig şablon dosyaları burada bulunur.                                                           |
| **bin/**       | `bin/console`dosyası ve diğer çalıştırılabilir komutlar burada.                          |
| **var/**       | Otomatik oluşturulan önbellek (`var/cache`) ve log (`var/log`) dosyaları burada saklanır. |
| **vendor/**    | Composer üzerinden yüklenen üçüncü parti kütüphaneler burada bulunur.                     |
| **public/**    | Projenin**web kök dizini**dir; dışarıdan erişilebilen dosyalar burada tutulur.         |

Yeni paketler yükledikçe, bu dizinler otomatik olarak güncellenir veya yenileri eklenir.

---

### 🌟 **Tebrikler!**

Artık Symfony uygulamanız:

* Bir controller içeriyor,
* Bir route’a sahip,
* Twig template render ediyor,
* Web Debug Toolbar ile hata ayıklamaya hazır 🎯

---

### 🔜 **Sıradaki Adımlar**

Symfony temellerini tamamlamak için şu konulara göz atın:

* 🛣 **Routing** – URL tanımlama ve parametre kullanımı
* 🎛 **Controller** – Mantıksal işlem ve veri döndürme
* 🧱 **Twig Templates** – Görsel yapı ve şablon kalıtımı
* 🎨 **Front-end Tools** – CSS & JavaScript yönetimi
* ⚙️ **Configuring Symfony** – Uygulama yapılandırması

Daha ileri konularda:

* 🧩 **Service Container**
* 🧾 **Forms**
* 💾 **Doctrine ORM (veritabanı işlemleri)**

  ve daha fazlasını keşfedebilirsiniz.

---
