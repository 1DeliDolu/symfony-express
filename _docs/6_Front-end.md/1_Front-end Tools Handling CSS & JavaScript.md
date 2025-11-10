

# 🎨 Front-end Araçları: CSS ve JavaScript Yönetimi




```markdown


Symfony, ön yüz (frontend) geliştirmede kullanacağın araçları seçme konusunda sana tam bir esneklik sunar.  
Genellikle iki farklı yaklaşım vardır:

1. HTML yapısını **PHP & Twig** ile oluşturmak  
2. Ön yüzü **JavaScript frameworkleri (React, Vue, Svelte vb.)** ile geliştirmek

Her iki yöntem de gayet başarılıdır — aşağıda detaylı olarak açıklanmıştır.

---

## 🧱 PHP & Twig Kullanımı

Symfony, modern ve hızlı bir frontend oluşturmanı kolaylaştıran iki güçlü araçla birlikte gelir:

| Özellik | 🧩 AssetMapper (Tavsiye Edilen) | ⚙️ Webpack Encore |
|----------|--------------------------------|------------------|
| Üretim Ortamına Uygun mu? | ✅ Evet | ✅ Evet |
| Kararlı mı? | ✅ Evet | ✅ Evet |
| Gereksinimler | 🚫 Yok | 🟢 Node.js |
| Build aşaması gerekiyor mu? | 🚫 Hayır | ✅ Evet |
| Tüm tarayıcılarda çalışır mı? | ✅ Evet | ✅ Evet |
| Stimulus / UX desteği | ✅ Evet | ✅ Evet |
| Sass / Tailwind desteği | ✅ Evet | ✅ Evet |
| React, Vue, Svelte desteği | ✅ Evet [1] | ✅ Evet |
| TypeScript desteği | ✅ Evet | ✅ Evet |
| JS yorumlarını kaldırır mı? | 🚫 Hayır [2] | ✅ Evet |
| CSS yorumlarını kaldırır mı? | 🚫 Hayır [2] | ✅ Evet [4] |
| Versiyonlu varlıklar | 🟢 Her zaman | 🟡 İsteğe bağlı |
| Üçüncü parti paket güncellemesi | ✅ Evet | 🚫 Hayır [3] |

---

### 🧾 Dipnotlar

1️⃣ AssetMapper ile JSX (React), Vue vb. kullanılabilir. Ancak kendi derleme araçlarını kullanman gerekir. Bazı özellikler (ör. Vue single-file component'ler) doğrudan tarayıcıda çalıştırılamaz.  
2️⃣ AssetMapper ile CSS/JS sıkıştırmak (ve yorumları kaldırmak) için **SensioLabs Minify Bundle** yükleyebilirsin.  
3️⃣ Eğer npm kullanıyorsan, **npm-check** gibi araçlarla güncellemeleri kontrol edebilirsin.  
4️⃣ CSS yorumları, Encore’daki **CssMinimizerPlugin** ile kaldırılabilir (Encore.configureCssMinimizerPlugin() üzerinden yapılandırılır).

---

## 🚀 AssetMapper (Tavsiye Edilen Yöntem)

**AssetMapper**, tümüyle PHP üzerinde çalışan, derleme adımı gerektirmeyen hafif bir sistemdir.  
Modern web standartlarını ve tarayıcıların desteklediği **importmap** özelliğini (gerekirse polyfill ile) kullanır.

📚 Ayrıntılar için: [AssetMapper Belgeleri](https://symfony.com/doc/current/frontend/asset_mapper.html)

🎥 Video eğitim serisi: **AssetMapper Screencast Series**

---

## ⚙️ Webpack Encore

**Webpack Encore**, Webpack'i Symfony'ye entegre etmenin basitleştirilmiş bir yoludur.  
JavaScript modüllerini birleştirme, CSS/JS ön işleme, derleme ve sıkıştırma işlemleri için güçlü bir API sağlar.

📚 Ayrıntılar için: [Encore Belgeleri](https://symfony.com/doc/current/frontend/encore/installation.html)

🎥 Video eğitim serisi: **Webpack Encore Screencast Series**

---

## 🔄 AssetMapper’dan Encore’a Geçiş

Yeni Symfony webapp projeleri varsayılan olarak **AssetMapper** kullanır.  
Ancak **Webpack Encore** kullanmak istiyorsan, aşağıdaki adımları takip et:

```bash
composer remove symfony/ux-turbo symfony/asset-mapper symfony/stimulus-bundle

composer require symfony/webpack-encore-bundle symfony/ux-turbo symfony/stimulus-bundle

npm install
npm run dev
```

---

## ⚡ Stimulus & Symfony UX Bileşenleri

AssetMapper veya Webpack Encore kurulumundan sonra, artık ön yüzünü geliştirmeye başlayabilirsin.

JavaScript kodunu dilediğin gibi yazabilirsin, ancak  **Stimulus** , **Turbo** ve **Symfony UX** araçlarını kullanman önerilir.

📚 Ayrıntılar için: [StimulusBundle Belgeleri](https://symfony.com/bundles/StimulusBundle/current/index.html)

---

## 🧠 Front-end Frameworkleri (React, Vue, Svelte, vb.)

React, Vue, Svelte, Next.js gibi modern JS frameworkleriyle çalışmak istiyorsan,

bu durumda Symfony’yi **saf bir API** olarak kullanman önerilir. Bunun için en iyi araçlardan biri  **API Platform** ’dur.

 **API Platform** :

* Symfony tabanlı güçlü bir API backend içerir
* Next.js veya diğer frameworklerle ön yüz oluşturmayı destekler
* React Admin arayüzüyle birlikte gelir
* Tamamen Dockerize edilmiştir ve kendi web sunucusuna sahiptir

🎥 Video eğitim serisi: **API Platform Screencast Series**

📚 Ayrıntılar: [API Platform Belgeleri](https://api-platform.com/)

---

## 🔗 Diğer Front-end Konuları

* [UX paketi oluşturma (Create a UX Bundle)](https://symfony.com/doc/current/frontend/ux.html)
* [Varlıklar için özel sürümleme stratejisi kullanma](https://symfony.com/doc/current/frontend/custom_version_strategy.html)
* [Twig’den JavaScript’e bilgi aktarma](https://symfony.com/doc/current/frontend/templating.html)
