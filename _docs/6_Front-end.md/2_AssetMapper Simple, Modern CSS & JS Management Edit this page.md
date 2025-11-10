

# 🧩 AssetMapper: Basit ve Modern CSS & JS Yönetimi


```markdown


**AssetMapper** bileşeni, karmaşık bir “bundler” kullanmadan modern JavaScript ve CSS yazmanı sağlar.  
Modern tarayıcılar zaten **import ifadeleri** ve **ES6 sınıfları** gibi birçok özelliği destekler. Ayrıca, **HTTP/2** protokolü, dosyaları birleştirerek istek sayısını azaltma ihtiyacını da ortadan kaldırmıştır.  
Bu bileşen, dosyalarını doğrudan tarayıcıya sunmanı kolaylaştıran hafif bir katmandır.

---

## 🚀 Temel Özellikler

1. **Varlıkların (Asset’lerin) Haritalanması ve Versiyonlanması**
   - `assets/` klasörü içindeki tüm dosyalar halka açık hale getirilir ve versiyonlanır.  
   - Örneğin, `assets/images/product.jpg` dosyasını Twig içinde şu şekilde referanslayabilirsin:  
     ```twig
     {{ asset('images/product.jpg') }}
     ```
   - Bu çağrı sonucunda URL şu şekilde olur:  
     `/assets/images/product-3c16d92m.jpg`

2. **Importmaps**
   - JavaScript’in `import` ifadesini (örneğin `import { Modal } from 'bootstrap'`) build sistemi olmadan kullanmanı sağlar.  
   - Tüm modern tarayıcılarda desteklenir (shim sayesinde) ve HTML standardının bir parçasıdır.

---

## ⚙️ Kurulum

Aşağıdaki komutla AssetMapper bileşenini yükleyebilirsin:

```bash
composer require symfony/asset-mapper symfony/asset symfony/twig-pack
```

Bu işlem ayrıca **Asset Component** ve **Twig** bileşenlerini de yükler.

Symfony Flex kullanıyorsan, kurulum otomatik olarak aşağıdaki dosyaları ekler:

* `assets/app.js` → Ana JavaScript dosyan
* `assets/styles/app.css` → Ana CSS dosyan
* `config/packages/asset_mapper.yaml` → Varlık yollarının tanımlandığı dosya
* `importmap.php` → Importmap yapılandırma dosyası
* `templates/base.html.twig` → İçine şu satır eklenir:
  ```twig
  {% block javascripts %}
      {% block importmap %}{{ importmap('app') }}{% endblock %}
  {% endblock %}
  ```

Flex kullanmıyorsan, bu dosyaları manuel oluşturman gerekir.

Son sürüm “recipe” içeriğine bakarak birebir oluşturabilirsin.

---

## 🗺️ Asset’leri Haritalama ve Kullanma

`asset_mapper.yaml` sayesinde `assets/` dizini varsayılan olarak haritalanır.

Bir örnek:

```bash
assets/images/duck.png
```

Bu dosyayı Twig içinde şu şekilde çağırabilirsin:

```twig
<img src="{{ asset('images/duck.png') }}">
```

HTML çıktısında bu şöyle görünür:

`/assets/images/duck-3c16d92m.png`

Dosyada değişiklik yaparsan, hash (versiyon) otomatik olarak değişir.

---

## 🔄 Geliştirme ve Üretim Ortamı

### 🧩 Geliştirme (dev)

`/assets/...` URL’si Symfony uygulaman tarafından dinamik olarak sunulur.

### 🚀 Üretim (prod)

Dağıtımdan önce aşağıdaki komutu çalıştırmalısın:

```bash
php bin/console asset-map:compile
```

Bu, tüm dosyaları `public/assets/` dizinine kopyalar, böylece web sunucun doğrudan sunabilir.

> Eğer bu komutu geliştirme ortamında çalıştırırsan, değişiklikler sayfaya yansımaz.
>
> Çözüm: `public/assets/` içeriğini sil → Symfony tekrar dinamik olarak sunar.

---

## 🧰 Gelişmiş Ayarlar

* Derlenmiş varlıkları başka bir yere (örneğin S3) yüklemek istiyorsan,

  `Symfony\Component\AssetMapper\Path\PublicAssetsFilesystemInterface` arayüzünü

  uygulayan bir servis tanımlayıp `asset_mapper.local_public_assets_filesystem` olarak kaydedebilirsin.

---

## 🔍 Hata Ayıklama (Debugging)

Tüm haritalanmış varlıkları görmek için:

```bash
php bin/console debug:asset-map
```

Örnek çıktı:

```
AssetMapper Paths
------------------
 Path      Namespace prefix
--------- ------------------
assets

Mapped Assets
-------------
 Logical Path       Filesystem Path
------------------ ----------------------------------------------------
 app.js             assets/app.js
 styles/app.css     assets/styles/app.css
 images/duck.png    assets/images/duck.png
```

Belirli filtreleme seçenekleri de kullanılabilir:

```bash
php bin/console debug:asset-map bootstrap.js
php bin/console debug:asset-map --ext=css
php bin/console debug:asset-map --no-vendor
```

> Bu filtreleme seçenekleri Symfony 7.2 sürümünde eklendi.

---

## 📦 Importmaps & Modern JavaScript Kullanımı

Tarayıcılar artık `import` ve `class` yapısını destekler.

Yani şu kod doğrudan çalışır:

```js
// assets/app.js
import Duck from './duck.js';

const duck = new Duck('Waddles');
duck.quack();

// assets/duck.js
export default class {
    constructor(name) {
        this.name = name;
    }
    quack() {
        console.log(`${this.name} says: Quack!`);
    }
}
```

Twig içinde şu satır, `app.js` dosyasının yüklenmesini sağlar:

```twig
{{ importmap('app') }}
```

> Not: Tarayıcı ortamında `import` yaparken `.js` uzantısını eklemeyi unutma!

---

## 📦 Üçüncü Taraf JavaScript Paketleri (npm)

Bootstrap gibi bir npm paketini kullanmak için:

```js
import { Alert } from 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/+esm';
```

Bu URL’leri manuel yazmak zor olduğundan, importmap ile şu şekilde ekleyebilirsin:

```bash
php bin/console importmap:require bootstrap
```

> Sadece deneme yapmak istiyorsan:
>
> `php bin/console importmap:require bootstrap --dry-run`

Bu işlem `importmap.php` dosyana şunu ekler:

```php
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'bootstrap' => [
        'version' => '5.3.0',
    ],
];
```

Bağımlılıklar (örneğin `@popperjs/core`) otomatik olarak eklenir.

Paketin ana CSS dosyası varsa, o da eklenir.

---

## 🌐 Ağ Sorunları ve Proxy Ayarı

Eğer şu hata çıkarsa:

> Connection was reset for "[https://cdn.jsdelivr.net/npm/](https://cdn.jsdelivr.net/npm/)..."

Bir proxy ayarı yapabilirsin:

```yaml
# config/packages/framework.yaml
framework:
    http_client:
        default_options:
            proxy: '185.250.180.238:8080'
            extra:
                curl:
                    '61': true  # CURLOPT_HTTPPROXYTUNNEL
```

---

## 💾 Paketleri Yönetme

* **Eksik dosyaları indir:**
  ```bash
  php bin/console importmap:install
  ```
* **Güncellemeleri kontrol et:**
  ```bash
  php bin/console importmap:outdated
  ```
* **Paketleri güncelle:**
  ```bash
  php bin/console importmap:update
  php bin/console importmap:update bootstrap lodash
  ```
* **Paket kaldır:**
  ```bash
  php bin/console importmap:remove lodash
  php bin/console importmap:install
  ```

> Kaldırma işlemi, JavaScript dosyalarındaki `import` ifadelerini otomatik olarak silmez.
>
> Kodunu manuel olarak güncellemelisin.

---

✅ **Sonuç:**

 **AssetMapper** , modern web standartlarını kullanarak Symfony’de JavaScript ve CSS yönetimini kolaylaştırır.

Ne Webpack’e ne de karmaşık “build” adımlarına gerek kalmadan, temiz ve performanslı bir yapı sunar.




# Importmap Nasıl Çalışır?


```markdown


**importmap.php** dosyası Bootstrap’i içe aktarmanı nasıl sağlar? Bunun cevabı, **base.html.twig** içindeki `{{ importmap() }}` Twig fonksiyonudur; bu fonksiyon bir importmap çıktısı üretir:

```html
<script type="importmap">{
    "imports": {
        "app": "/assets/app-4e986c1a.js",
        "/assets/duck.js": "/assets/duck-1b7a64b3.js",
        "bootstrap": "/assets/vendor/bootstrap/bootstrap.index-f093544d.js"
    }
}</script>
```

 **Import maps** , tarayıcıların yerel bir özelliğidir. JavaScript’ten `bootstrap` içe aktardığında, tarayıcı importmap’i kontrol eder ve paketin ilişkili yoldan getirilmesi gerektiğini görür.

Peki **/assets/duck.js** import girdisi nereden geldi? Bu **importmap.php** içinde yaşamıyor. Harika soru!

Yukarıdaki **assets/app.js** dosyası `./duck.js` dosyasını içe aktarır. Bir dosyayı **göreli yolla** içe aktardığında, tarayıcı o dosyayı içe aktaran dosyaya göre arar. Yani  **/assets/duck.js** ’i arar. Bu URL doğru olurdu; ancak **duck.js** dosyası sürümlenmiştir (versioned). Neyse ki **AssetMapper** bileşeni bu import’u görür ve  **/assets/duck.js** ’den doğru, sürümlü dosya adına bir eşleme ekler. Sonuç: `./duck.js` içe aktarması “kendiliğinden” çalışır!

`importmap()` fonksiyonu ayrıca daha eski tarayıcıların importmap’leri anlaması için bir **ES module shim** de çıktı olarak verir (polyfill yapılandırmasına bakın).

---

## "app" Entrypoint’i ve Preloading

Bir  **entrypoint** , tarayıcının yüklediği ana JavaScript dosyasıdır ve uygulaman varsayılan olarak bir entrypoint ile başlar:

```php
// importmap.php
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    // ...
];
```

**base.html.twig** içindeki `{{ importmap('app') }}` sadece importmap’i değil, birkaç başka şeyi de çıktılar:

```html
<script type="module">import 'app';</script>
```

Bu satır tarayıcıya **app** importmap girdisini yüklemesini söyler; bu da **assets/app.js** içindeki kodun çalışmasına neden olur.

`importmap()` fonksiyonu ayrıca bir dizi **"preload"** etiketi de üretir:

```html
<link rel="modulepreload" href="/assets/app-4e986c1a.js">
<link rel="modulepreload" href="/assets/duck-1b7a64b3.js">
```

Bu bir performans optimizasyonudur; bununla ilgili daha fazla bilgiyi **Performans: Preloading Ekleme** bölümünde öğrenebilirsin.

---

## 3. Parti Paketten Belirli Dosyaları İçe Aktarmak

Bazen bir paketten belirli bir dosyayı içe aktarman gerekir. Örneğin, **highlight.js** entegre ederken sadece çekirdeği ve belirli bir dili içe aktarmak isteyebilirsin:

```js
import hljs from 'highlight.js/lib/core';
import javascript from 'highlight.js/lib/languages/javascript';

hljs.registerLanguage('javascript', javascript);
hljs.highlightAll();
```

Bu durumda, **highlight.js** paketini  **importmap.php** ’ye eklemek tek başına işe yaramaz: içe aktardığın şey — örn. `highlight.js/lib/core` — **importmap.php** dosyasındaki bir girişle  **tam olarak eşleşmelidir** .

Bunun yerine **importmap:require** komutunu kullan ve ihtiyaç duyduğun tam yolları geçir. Bu, tek seferde birden fazla paketi nasıl gerektirebileceğini de gösterir:

```bash
php bin/console importmap:require highlight.js/lib/core highlight.js/lib/languages/javascript
```

---

## jQuery Gibi Global Değişkenler

`$` (jQuery) gibi global değişkenlere güvenmeye alışık olabilirsin:

```js
// assets/app.js
import 'jquery';

// app.js veya başka bir dosya
$('.something').hide(); // ÇALIŞMAZ!
```

Fakat modül ortamında (AssetMapper ile olduğu gibi) `jquery` gibi bir kütüphaneyi içe aktardığında  **global değişken oluşturmaz** . Bunun yerine, ihtiyaç duyduğun her dosyada içe aktar ve bir değişkene ata:

```js
import $ from 'jquery';
$('.something').hide();
```

Bunu **inline** bir script etiketi içinde bile yapabilirsin:

```html
<script type="module">
    import $ from 'jquery';
    $('.something').hide();
</script>
```

Eğer bir şeyi global değişken yapmak  **zorundaysan** , bunu **app.js** içinde manuel olarak yap:

```js
import $ from 'jquery';
// "window" üzerindeki şeyler global olur
window.$ = $;
```

---

## CSS’i Yönetmek

CSS, bir JavaScript dosyasından içe aktarılarak sayfaya eklenebilir. Varsayılan **assets/app.js** zaten  **assets/styles/app.css** ’i içe aktarır:

```js
// assets/app.js
import '../styles/app.css';

// ...
```

**base.html.twig** içinde `importmap('app')` çağırdığında, **AssetMapper** `assets/app.js` dosyasını (ve onun içe aktardığı JS dosyalarını) **CSS import ifadeleri** için tarar. Son CSS koleksiyonu, **içe aktarıldıkları sırayla** sayfaya `<link>` etiketleri olarak render edilir.

CSS dosyası içe aktarmak, JavaScript modülleri tarafından  **yerel olarak desteklenmez** . AssetMapper bunu, her CSS dosyası için özel bir importmap girdisi ekleyerek sağlar. Bu özel girdiler geçerlidir ama bir şey yapmaz. AssetMapper her CSS için bir `<link>` etiketi ekler; JavaScript `import` çalıştığında ek bir şey olmaz.

---

## 3. Parti CSS’i Yönetmek

Bazen bir JavaScript paketi bir veya daha fazla CSS dosyası içerir. Örneğin, **bootstrap** paketinin `dist/css/bootstrap.min.css` dosyası vardır.

CSS dosyalarını da JavaScript dosyaları gibi **require** edebilirsin:

```bash
php bin/console importmap:require bootstrap/dist/css/bootstrap.min.css
```

Sayfaya dahil etmek için bir JavaScript dosyasından içe aktar:

```js
// assets/app.js
import 'bootstrap/dist/css/bootstrap.min.css';

// ...
```

Bazı paketler — örneğin **bootstrap** — bir CSS dosyası içerdiğini ilan eder. Bu gibi durumlarda `importmap:require bootstrap` yaptığında CSS dosyası da **kolaylık olması için** `importmap.php` dosyasına eklenir. Bir paket **package.json** içindeki `style` alanında CSS dosyasını ilan etmiyorsa, eklemeleri için paket bakımcısıyla iletişime geçmeyi deneyin.

---

## CSS Dosyaları İçindeki Yollar

CSS içinden, normal `url()` fonksiyonu ve hedef dosyaya göreli bir yol ile başka dosyalara referans verebilirsin:

```css
/* assets/styles/app.css */
.quack {
    /* dosya assets/images/duck.png konumunda */
    background-image: url('../images/duck.png');
}
```

Son **app.css** dosyasındaki yol, **duck.png** için otomatik olarak sürümlü (versioned) URL’yi içerir:

```css
/* public/assets/styles/app-3c16d92m.css */
.quack {
    background-image: url('../images/duck-3c16d92m.png');
}
```

---

## Tailwind CSS Kullanımı

**AssetMapper** ile **Tailwind CSS** kullanmak için  **symfonycasts/tailwind-bundle** ’a göz atın.

## Sass Kullanımı

**AssetMapper** ile **Sass** kullanmak için  **symfonycasts/sass-bundle** ’a göz atın.

---

## CSS’i JavaScript’ten “Tembel” (Lazy) İçe Aktarmak

Bazı CSS’leri tembel yüklemek istersen, normal “dinamik import” söz dizimini kullanabilirsin:

```js
// assets/any-file.js
import('./lazy.css');

// ...
```

Bu durumda, **lazy.css** asenkron olarak indirilir ve sayfaya eklenir. Dinamik import ile **JavaScript** dosyasını tembel yüklersen ve o dosya **(dinamik olmayan)** bir import ile bir CSS dosyası içe aktarırsa, o CSS dosyası da asenkron olarak indirilecektir.

---

## Sorunlar ve Hata Ayıklama

### Eksik importmap Girdisi

En yaygın hatalardan biri tarayıcı konsolunda şöyle görünebilir:

> Failed to resolve module specifier " bootstrap". Relative references must start with either "/", "./", or "../".

Veya:

> The specifier "bootstrap" was a bare specifier, but was not remapped to anything. Relative module specifiers must start with "./", "../" or "/".

Bu, JavaScript’inde bir 3. parti paketi (örn. `import 'bootstrap'`) içe aktardığın anlamına gelir; tarayıcı bu paketi importmap dosyanda arar ama bulamaz.

Çözüm çoğunlukla paketi importmap’e eklemektir:

```bash
php bin/console importmap:require bootstrap
```

Bazı tarayıcılar (Firefox gibi) bu “import” kodunun nerede olduğunu gösterir; Chrome gibi bazıları ise şu anda göstermeyebilir.

---

### JavaScript, CSS veya Görsel Dosyası için 404 Not Found

Bazen içe aktardığın bir JavaScript dosyası (örn. `import './duck.js'`) veya referans verdiğin bir CSS/görsel dosyası bulunamaz ve tarayıcı konsolunda 404 görürsün. Ayrıca 404 URL’sinin dosya adında **sürüm hash’i** olmadığını fark edersin (örn. `/assets/duck-1b7a64b3.js` yerine `/assets/duck.js`).

Bu genelde yolun yanlış olduğu anlamına gelir. Dosyayı Twig şablonundan doğrudan referans veriyorsan:

```twig
<img src="{{ asset('images/duck.png') }}">
```

`asset()`’e verdiğin yolun, dosyanın **mantıksal yolu** (logical path) olması gerekir. Uygulamadaki tüm geçerli mantıksal yolları görmek için **debug:asset-map** komutunu kullan.

Daha olası senaryo, hatalı varlığı bir **CSS** dosyasından (örn. `@import url('other.css')`) veya bir **JavaScript** dosyasından içe aktarıyor olmandır:

```js
// assets/controllers/farm-controller.js
import '../farm/chicken.js';
```

Bunu yaparken yol, **içe aktaran dosyaya göre göreli** olmalı (ve JavaScript dosyalarında `./` veya `../` ile başlamalı). Bu örnekte `../farm/chicken.js`,  **assets/farm/chicken.js** ’e işaret eder. Uygulamadaki geçersiz tüm import’ların listesini görmek için:

```bash
php bin/console cache:clear
php bin/console debug:asset-map
```

Geçersiz import’lar ekranın üst kısmında uyarı olarak görünür ( **symfony/monolog-bundle** kurulu olduğundan emin olun):

```
WARNING   [asset_mapper] Unable to find asset "../images/ducks.png" referenced in "assets/styles/app.css".
WARNING   [asset_mapper] Unable to find asset "./ducks.js" imported from "assets/app.js".
```

---

### Yorum Satırlarına Alınmış Kodlarda Eksik Varlık Uyarıları

 **AssetMapper** , JavaScript dosyalarında import satırlarını bulup bunları importmap’e otomatik eklemek için **regex** kullanır. Bu çok iyi çalışsa da mükemmel değildir. Bir import’u yorum satırına alsan bile, yine de bulunup importmap’e eklenebilir. Bu zararlı değildir, ancak şaşırtıcı olabilir.


# 🚀 AssetMapper Bileşeni ile Dağıtım (Deployment)


```markdown


Hazırsan, varlıklarını (“assets”) derlemek için aşağıdaki komutu çalıştır:

```bash
php bin/console asset-map:compile
```

Bu komut, **tüm versiyonlanmış varlık dosyalarını** `public/assets/` dizinine yazar.

Ayrıca birkaç JSON dosyası (`manifest.json`, `importmap.json` vb.) oluşturur.

Böylece **importmap** çok hızlı şekilde işlenebilir.

---

## ⚡ Performans Optimizasyonu

**AssetMapper** destekli siteni maksimum hızda çalıştırmak için birkaç optimizasyon yapmalısın.

Kısa yoldan gitmek istersen **Cloudflare** gibi bir servis çoğunu senin yerine yapar.

### 1️⃣ HTTP/2 veya HTTP/3 Kullan

Web sunucun **HTTP/2** veya **HTTP/3** ile çalışmalıdır.

Bu sayede tarayıcı, varlıkları **paralel** olarak indirebilir.

* **Caddy** : Otomatik etkin.
* **Nginx / Apache** : Manuel olarak etkinleştirilebilir.
* **Cloudflare** : Proxy olarak kullanırsan otomatik etkinleştirir.

---

### 2️⃣ Varlıkları Sıkıştır (gzip, Brotli vs.)

Web sunucun, varlıkları (JS, CSS, görseller) **gzip** veya benzeri biçimlerle sıkıştırmalıdır.

* **Caddy** : Varsayılan olarak etkin.
* **Nginx / Apache** : Manuel etkinleştirilebilir.
* **Cloudflare** : Varsayılan olarak sıkıştırır.

Ayrıca, AssetMapper **önceden sıkıştırılmış dosyaları (precompressed)** sunmayı da destekler.

---

### 3️⃣ Uzun Ömürlü Cache (Cache-Control)

Sunucunda varlıklar için uzun ömürlü bir `Cache-Control` başlığı ayarla.

Çünkü AssetMapper dosya adlarına bir **versiyon hash** eklediği için güvenle uzun süreli cache kullanılabilir:

```text
Cache-Control: max-age=31536000
```

---

### 🔍 Performans Ölçümü

Yukarıdaki adımları tamamladıktan sonra, **Google Lighthouse** ile sitenin performansını test edebilirsin.

---

## ⚙️ Preloading (Ön Yükleme) Mantığı

Lighthouse bazen şu uyarıyı verebilir:

> Avoid Chaining Critical Requests

### 🔗 Örnek Durum

```
assets/app.js  →  imports ./duck.js  
assets/duck.js →  imports bootstrap
```

Eğer ön yükleme (preload) olmazsa tarayıcı sırasıyla şunları yapar:

1. `app.js` dosyasını indirir,
2. İçindeki `./duck.js` import’unu görür, onu indirir,
3. Sonra `bootstrap` import’unu görür, onu indirir.

➡️ Sonuç: dosyalar **ardışık** olarak indirilir.

Bu da performansı düşürür.

---

### ✅ AssetMapper’ın Çözümü

AssetMapper, `{{ importmap('app') }}` çağrısını gördüğünde:

**A)** `assets/app.js` dosyasını analiz eder ve onun import ettiği tüm JS dosyalarını (ve onların import ettiği dosyaları) bulur.

**B)** Her biri için aşağıdaki gibi `<link rel="preload">` etiketleri üretir:

```html
<link rel="modulepreload" href="/assets/app-4e986c1a.js">
<link rel="modulepreload" href="/assets/duck-1b7a64b3.js">
```

Tarayıcı bu dosyaları  **önceden indirmeye başlar** , performans artar.

Ayrıca **WebLink Component** kuruluysa Symfony, CSS dosyaları için de `Link` HTTP başlığı ekler.

---

## 🗜️ Varlıkları Önceden Sıkıştırma (Pre-Compressing)

> Symfony 7.3 ile tanıtıldı.

Çoğu sunucu (Caddy, Nginx, Apache, FrankenPHP) ve servis (Cloudflare) varlık sıkıştırmayı destekler,

ancak **AssetMapper** ayrıca dosyaları **önceden sıkıştırmanı** sağlar.

### 🎯 Avantaj

Bu sayede varlıklar, **yüksek sıkıştırma oranlarıyla** daha önce sıkıştırılır.

Sunucu, istemciye gönderirken CPU harcamadan bu dosyaları doğrudan iletir.

---

### 🔧 Desteklenen Formatlar

| Format              | Gereken PHP/CLI                                  |
| ------------------- | ------------------------------------------------ |
| **Brotli**    | `brotli`CLI veya PHP eklentisi                 |
| **Zstandard** | `zstd`CLI veya PHP eklentisi                   |
| **gzip**      | `gzip`veya `zopfli`CLI,`zlib`PHP eklentisi |

---

### ⚙️ Yapılandırma

```yaml
# config/packages/asset_mapper.yaml
framework:
    asset_mapper:
        precompress:
            format: 'zstandard'       # veya ['brotli', 'zstandard']
            extensions: ['css', 'js', 'json', 'svg', 'xml']
```

Bu ayarla `asset-map:compile` çalıştırıldığında belirtilen uzantılara sahip tüm dosyalar `.zst`, `.br` veya `.gz` olarak sıkıştırılır.

Sunucuna bu dosyaları kullanmasını belirt:

```conf
file_server {
    precompressed br zstd gzip
}
```

Ayrıca aşağıdaki komutla el ile de sıkıştırma yapabilirsin:

```bash
php bin/console assets:compress
```

veya `asset_mapper.compressor` servisini doğrudan kullanarak uygulamada dinamik dosyaları (örneğin kullanıcı yüklemeleri) sıkıştırabilirsin.

---

## ❓ Sık Sorulan Sorular (FAQ)

### 🔸 AssetMapper varlıkları birleştiriyor mu?

Hayır.

Modern tarayıcılar ve HTTP/2 sayesinde dosyaların ayrı kalması performans kaybı yaratmaz.

Aksine, bir dosya değiştiğinde diğerleri cache’te kalabilir.

---

### 🔸 Varlıkları küçültüyor (minify) mu?

Hayır.

Genellikle sunucu sıkıştırması yeterlidir.

Ancak ek olarak küçültme yapmak istersen,  **SensioLabs Minify Bundle** ’ı kullanabilirsin.

```bash
composer require sensiolabs/minify-bundle
```

Bu paket, `asset-map:compile` sırasında otomatik olarak tüm varlıkları küçültür.

---

### 🔸 Üretim için hazır mı?

Evet ✅

**AssetMapper** modern tarayıcı teknolojilerini (importmaps, native imports) ve HTTP/2 paralel indirme yeteneğini kullanır.

Örneğin, [https://ux.symfony.com](https://ux.symfony.com/) sitesi AssetMapper ile çalışır ve  **Google Lighthouse skoru %99** ’dur.

---

### 🔸 Tüm tarayıcılarda çalışır mı?

Evet.

Tüm modern tarayıcılar `import` ve `importmap` özelliklerini destekler.

AssetMapper, eski tarayıcılar için **ES module shim** içerir.

> Ancak dinamik import (`import('./file.js')`) en eski tarayıcılarda çalışmaz.
>
> Bu durumda `importShim()` fonksiyonunu kullanabilirsin:
>
> [es-module-shims](https://www.npmjs.com/package/es-module-shims#user-content-polyfill-edge-case-dynamic-import)

---

### 🔸 Tailwind veya Sass kullanabilir miyim?

Evet.

* Tailwind için: `symfonycasts/tailwind-bundle`
* Sass için: `symfonycasts/sass-bundle`

---

### 🔸 TypeScript ile kullanılabilir mi?

Evet, `sensiolabs/typescript-bundle` ile.

---

### 🔸 JSX veya Vue kullanılabilir mi?

Kısmen.

React veya Svelte gibi frameworklerle büyük projelerde **Webpack Encore** kullanmak daha mantıklıdır.

* **React JSX** dosyaları derlenmiş halde çalışabilir.
* **Vue .vue** tek dosya bileşenleri (SFC) derleme gerektirir → Encore önerilir.

---

### 🔸 Kodumu lint ve formatlayabilir miyim?

Evet, ama AssetMapper’ın kendi özelliği değildir.

`kocal/biome-js-bundle` yükleyerek hızlıca lint/format yapabilirsin.

---

## 🧩 3. Parti Paketler ve Özel Asset Yolları

Tüm paketler (bundle), `Resources/public/` veya `public/` dizinleri içeriyorsa,

bu yollar otomatik olarak “asset path” olarak eklenir.

Örneğin:

```twig
<link rel="stylesheet" href="{{ asset('bundles/babdevpagerfanta/css/pagerfanta.css') }}">
```

Bu dosya  **otomatik olarak versiyonlanır** :

```html
<link rel="stylesheet" href="/assets/bundles/babdevpagerfanta/css/pagerfanta-ea64fc9c.css">
```

---

### 🔁 3. Parti Dosyaları Ezmek (Override)

Bir bundle’ın varlığını ezmek istersen, aynı yolda kendi dosyanı oluştur:

```
assets/bundles/babdevpagerfanta/css/pagerfanta.css
```

Bu dosya orijinalin yerine kullanılır.

> Ancak bazı paketler (örneğin EasyAdminBundle) özel “asset package” kullanıyorsa,
>
> AssetMapper bunlar için devreye girmez.

---

## 📂 assets/ Dışındaki Dosyaları İçe Aktarmak

Aşağıdaki gibi, `assets/` dışındaki dosyaları içe aktarabilirsin:

```css
/* assets/styles/app.css */
@import url('../../vendor/babdev/pagerfanta-bundle/Resources/public/css/pagerfanta.css');
```

Eğer şu hatayı alırsan:

```
The "app" importmap entry contains the path "vendor/some/package/assets/foo.js" 
but it does not appear to be in any of your asset paths.
```

`asset_mapper.yaml` içine ilgili yolu ekle:

```yaml
framework:
    asset_mapper:
        paths:
            - assets/
            - vendor/some/package/assets
```

---

## ⚙️ Yapılandırma (Configuration Options)

Tüm mevcut ayarları görmek için:

```bash
php bin/console config:dump framework asset_mapper
```

### 🔹 framework.asset_mapper.paths

Taranacak dizinleri tanımlar:

```yaml
framework:
    asset_mapper:
        paths:
            - assets/
            - vendor/some/package/assets
```

Namespace eklemek için:

```yaml
framework:
    asset_mapper:
        paths:
            assets/: ''
            vendor/some/package/assets/: 'some-package'
```

Böylece mantıksal yol `some-package/foo.js` şeklinde olur.

---

### 🔹 framework.asset_mapper.excluded_patterns

Belirli dosya türlerini hariç tutmak için:

```yaml
framework:
    asset_mapper:
        excluded_patterns:
            - '*/*.scss'
```

---

### 🔹 framework.asset_mapper.exclude_dotfiles

`.` ile başlayan dosyaları hariç tutar (ör. `.env`, `.gitignore`).

```yaml
framework:
    asset_mapper:
        exclude_dotfiles: true
```

> Bu ayar varsayılan olarak  **etkindir** .

---

✅ **Özetle:**

 **AssetMapper** , modern web teknolojilerini kullanarak dağıtımı kolay, güvenli ve performanslı hale getirir.

Varlıkları önceden derler, sıkıştırır, versiyonlar ve tarayıcıya en hızlı şekilde sunar.

---



# 🧩 AssetMapper Gelişmiş Yapılandırmalar ve Güvenlik Özellikleri



```markdown


## ⚙️ `framework.asset_mapper.importmap_polyfill`
Bu ayar, eski tarayıcılar için **polyfill (shim)** yapılandırmasını belirler.  
Varsayılan olarak AssetMapper, **ES Module Shim**’i bir **CDN üzerinden** yükler.

```yaml
framework:
    asset_mapper:
        # Shim'i tamamen devre dışı bırakmak istersen (eski tarayıcılar çalışmaz)
        importmap_polyfill: false

        # Kendi polyfill dosyanı tanımlamak için:
        # 1️⃣ importmap.php'ye ekle
        # 2️⃣ Bu ayarı o dosyanın anahtarına (key) ayarla
        # importmap_polyfill: 'custom_polyfill'
```

Eğer polyfill’i **yerel olarak yüklemek** istiyorsan, sadece şu komutu çalıştırman yeterlidir:

```bash
php bin/console importmap:require es-module-shims
```

---

## ⚙️ `framework.asset_mapper.importmap_script_attributes`

Bu ayar, `{{ importmap() }}` Twig fonksiyonu tarafından oluşturulan `<script>` etiketlerine eklenecek **HTML niteliklerini** tanımlar.

Örneğin:

```yaml
framework:
    asset_mapper:
        importmap_script_attributes:
            crossorigin: 'anonymous'
```

Sonuç olarak aşağıdaki gibi bir HTML etiketi oluşturulur:

```html
<script type="module" crossorigin="anonymous">...</script>
```

---

## 🧱 Sayfaya Özel CSS ve JavaScript Kullanımı

Bazen belirli CSS veya JavaScript dosyalarını yalnızca bazı sayfalarda dahil etmek isteyebilirsin.

### 1️⃣ Dinamik İçe Aktarım (Dynamic Import)

Koşullu olarak dosya yüklemek için:

```js
const someCondition = true;

if (someCondition) {
    import('./some-file.js');

    // async/await kullanımıyla:
    // const something = await import('./some-file.js');
}
```

### 2️⃣ Ayrı Bir Entrypoint Oluşturma

Örneğin, **checkout** sayfası için ayrı bir JS dosyası oluştur:

```js
// assets/checkout.js
import './checkout.css';

// ...
```

`importmap.php` içine ekle ve entrypoint olarak işaretle:

```php
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'checkout' => [
        'path' => './assets/checkout.js',
        'entrypoint' => true,
    ],
];
```

Ardından ilgili Twig şablonunda sadece bu entrypoint’leri yükle:

```twig
{# templates/products/checkout.html.twig #}
{% block importmap %}
    {{ importmap(['app', 'checkout']) }}
{% endblock %}
```

> ⚠️ `parent()` çağırma!
>
> Her sayfada yalnızca **bir adet importmap** bulunabilir.
>
> Eğer sadece `checkout.js` dosyasını çalıştırmak istiyorsan:
>
> ```twig
> {{ importmap('checkout') }}
> ```

Bu durumda importmap’in tamamı sayfaya dahil edilir, ancak yalnızca `checkout.js` yürütülür.

---

## 🔒 Content Security Policy (CSP) ile Kullanım

Bir **Content Security Policy** (CSP) kullanıyorsan, `{{ importmap() }}` fonksiyonu tarafından oluşturulan **inline `<script>` etiketleri** bu politikayı ihlal edebilir.

### 🧠 Çözüm: Nonce Kullanımı

Her istek için rastgele bir nonce değeri oluşturup, hem CSP başlığına hem de `<script>` etiketine ekleyebilirsin.

Örneğin, **NelmioSecurityBundle** bu işlemi otomatik yapar:

```twig
{{ importmap('app', {'nonce': csp_nonce('script')}) }}
```

Bu şekilde tarayıcı, sadece nonce değerini içeren güvenli script’leri çalıştırır.

---

### 🧩 CSP ve CSS Dosyaları

AssetMapper, CSS dosyalarını yüklemek için **data:application/javascript** hilesini kullanır.

Bu, bazı tarayıcılarda CSP ihlali olarak algılanabilir.

💡 Çözüm:

CSP başlığına şu yönergeyi ekle:

```text
Content-Security-Policy: script-src 'self' 'strict-dynamic' ...
```

Bu, importmap’in diğer kaynakları yüklemesine izin verir.

Ancak dikkat: `strict-dynamic` kullanıldığında, `self` veya `unsafe-inline` gibi diğer kaynaklar  **yoksayılır** ; bu nedenle diğer `<script>` etiketlerine de nonce eklenmelidir.

---

## 🧰 AssetMapper Önbellek Sistemi (dev Ortamında)

Geliştirme (debug) modundayken AssetMapper, her varlık dosyasının içeriğini  **önbelleğe alır** .

Bir dosya değiştiğinde otomatik olarak yeniden hesaplanır.

Ayrıca, bağımlılıkları da izler:

* Eğer `app.css`, `@import url('other.css')` içeriyorsa

  → `other.css` değiştiğinde `app.css` de yeniden hesaplanır.

Eğer bir dosya beklediğin gibi güncellenmiyorsa:

```bash
php bin/console cache:clear
```

komutunu çalıştırarak tüm varlıkların yeniden hesaplanmasını sağlayabilirsin.

---

## 🧑‍💻 Güvenlik Denetimleri (Dependency Audit)

AssetMapper, **npm benzeri güvenlik taramaları** yapabilir:

```bash
php bin/console importmap:audit
```

Örnek çıktı:

```
--------  ---------------------------------------------  ---------  -------  ----------  -----------------------------------------------------
Severity  Title                                          Package    Version  Patched in  More info
--------  ---------------------------------------------  ---------  -------  ----------  -----------------------------------------------------
Medium    jQuery Cross Site Scripting vulnerability      jquery     3.3.1    3.5.0       https://api.github.com/advisories/GHSA-257q-pV89-V3xv
High      Prototype Pollution in JSON5 via Parse Method  json5      1.0.0    1.0.2       https://api.github.com/advisories/GHSA-9c47-m6qq-7p4h
Critical  Prototype Pollution in minimist                minimist   1.1.3    1.2.6       https://api.github.com/advisories/GHSA-xvch-5gv4-984h
Medium    Bootstrap Vulnerable to Cross-Site Scripting   bootstrap  4.1.3    4.3.1       https://api.github.com/advisories/GHSA-9v3M-8fp8-mi99
--------  ---------------------------------------------  ---------  -------  ----------  -----------------------------------------------------
7 packages found: 7 audited / 0 skipped
6 vulnerabilities found: 1 Critical / 1 High / 4 Medium
```

Eğer güvenlik açığı yoksa komut `0` koduyla döner, varsa `1`.

Bu sayede bu komutu **CI sürecine entegre ederek** yeni bir güvenlik açığı bulunduğunda uyarı alabilirsin.

Çıktı biçimini değiştirmek için:

```bash
php bin/console importmap:audit --format=json
```

---

✅ **Özet:**

* `importmap_polyfill` eski tarayıcılar için shim sağlar.
* `importmap_script_attributes` ile `<script>` etiketlerine özel nitelikler ekleyebilirsin.
* CSP desteği nonce kullanımıyla güvenli hale gelir.
* Geliştirme modunda varlık değişiklikleri otomatik yeniden hesaplanır.
* `importmap:audit` komutuyla bağımlılık güvenliğini izleyebilirsin.
