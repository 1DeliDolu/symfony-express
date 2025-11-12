# Symfony TypeScript Bundle Rehberi

## Genel Bakış

Bu bundle, TypeScript kodlarınızı derleyip Symfony'nin AssetMapper bileşeni ile kullanmanızı sağlar. **Node.js gerektirmez!**

**Özellikler:**

-   ✓ Doğru SWC binary'sini otomatik indirir
-   ✓ TypeScript dosyalarınızı derlemek için `typescript:build` komutu ekler
-   ✓ `asset-map:compile` komutu çalıştırıldığında TypeScript dosyalarını otomatik derler

## Kurulum

```bash
composer require sensiolabs/typescript-bundle
```

## Temel Kullanım

### 1. Kaynak Dizinini Ayarla

TypeScript dosyalarınızın konumunu belirtin:

```yaml
# config/packages/sensiolabs_typescript.yaml
sensiolabs_typescript:
    source_dir: ["%kernel.project_dir%/assets/typescript"]
```

**Örnek Proje Yapısı:**

```
assets/
  typescript/
    app.ts          # Ana entrypoint
    components/
      Button.ts
      Modal.ts
    utils/
      helpers.ts
```

### 2. Template'te TypeScript Dosyasını Yükle

```twig
{# templates/base.html.twig #}

{% block javascripts %}
    <script type="text/javascript" src="{{ asset('typescript/app.ts') }}"></script>
{% endblock %}
```

### 3. TypeScript'i Derle

**Development Modu (Watch ile):**

```bash
php bin/console typescript:build --watch
```

**Production için:**

```bash
php bin/console asset-map:compile
```

**İşte bu kadar! 🎉**

## Symfony CLI ile Kullanım

Symfony CLI kullanıyorsanız, `typescript:build` komutunu worker olarak ekleyin:

```yaml
# .symfony.local.yaml
workers:
    # ...
    typescript:
        cmd: ["symfony", "console", "typescript:build", "--watch"]
```

**Server'ı Başlat:**

```bash
symfony server:start
```

**Daemon Modunda Çalışıyorsa:**

```bash
# Log'ları takip et
symfony server:log
```

## Nasıl Çalışır?

### Adım Adım İşleyiş

1. **İlk Çalıştırma:**

    - TypeScript komutlarından birini ilk kez çalıştırdığınızda
    - Bundle, sisteminize uygun SWC binary'sini indirir
    - Binary, `var/` dizinine kaydedilir

2. **TypeScript Derleme:**

    - `typescript:build` komutu çalıştırıldığında
    - SWC binary kullanılarak TypeScript dosyaları derlenir
    - Derlenmiş dosyalar `var/typescript/` dizinine kaydedilir

3. **Asset Değişimi:**
    - `assets/typescript/app.ts` içeriği istendiğinde
    - Bundle, dosya içeriğini `var/typescript/` dizinindeki derlenmiş versiyon ile değiştirir

**Diyagram:**

```
assets/typescript/app.ts
         ↓
    [SWC Compiler]
         ↓
var/typescript/app.js
         ↓
    [Asset Mapper]
         ↓
  public/assets/app.js
```

## Yapılandırma

### Tam Yapılandırmayı Görüntüle

```bash
php bin/console config:dump sensiolabs_typescript
```

### Temel Yapılandırma

```yaml
# config/packages/sensiolabs_typescript.yaml
sensiolabs_typescript:
    # Kaynak dizinler (dizi)
    source_dir:
        - "%kernel.project_dir%/assets/typescript"
        - "%kernel.project_dir%/src/Resources/typescript"

    # SWC binary yolu (opsiyonel)
    swc_binary: "node_modules/.bin/swc"

    # SWC versiyonu (opsiyonel)
    swc_version: "v1.7.27-nightly-20240911.1"

    # SWC config dosyası (opsiyonel)
    swc_config_file: "%kernel.project_dir%/.swcrc"
```

### source_dir

**Varsayılan:** `['%kernel.project_dir%/assets']`

Derlenecek TypeScript dosyalarının bulunduğu dizinler.

**Örnekler:**

```yaml
# Tek dizin
sensiolabs_typescript:
    source_dir: ['%kernel.project_dir%/assets/typescript']

# Birden fazla dizin
sensiolabs_typescript:
    source_dir:
        - '%kernel.project_dir%/assets/typescript'
        - '%kernel.project_dir%/assets/admin/typescript'
        - '%kernel.project_dir%/assets/frontend/typescript'
```

### swc_binary

**Varsayılan:** Bundle tarafından otomatik indirilen binary

Zaten yüklü bir SWC binary'niz varsa kullanabilirsiniz:

```yaml
sensiolabs_typescript:
    swc_binary: "node_modules/.bin/swc"
```

**Kullanım Senaryoları:**

-   Global SWC kurulumu var
-   Node.js projesi içinde SWC kullanılıyor
-   Özel SWC versiyonu gerekli

### swc_version

**Varsayılan:** `v1.3.92`

Farklı bir SWC versiyonu kullanmak için:

```yaml
sensiolabs_typescript:
    swc_version: "v1.7.27-nightly-20240911.1"
```

⚠️ **Önemli:** Versiyon değiştirdikten sonra mevcut binary'yi silin:

```bash
# Windows (PowerShell)
Remove-Item -Path var/swc-* -Recurse -Force

# Linux/Mac
rm -rf var/swc-*
```

**Neden silmeli?**

-   İndirme sadece binary yoksa tetiklenir
-   Mevcut binary'yi silerseniz yeni versiyon indirilir

### swc_config_file

**Varsayılan:** Yok

SWC derleyicisini özelleştirmek için `.swcrc` dosyası belirtin:

```yaml
sensiolabs_typescript:
    swc_config_file: "%kernel.project_dir%/.swcrc"
```

**.swcrc Örneği:**

```json
{
    "$schema": "https://json.schemastore.org/swcrc",
    "jsc": {
        "parser": {
            "syntax": "typescript",
            "tsx": true,
            "decorators": true,
            "dynamicImport": true
        },
        "transform": {
            "react": {
                "runtime": "automatic"
            }
        },
        "target": "es2022",
        "loose": false,
        "externalHelpers": false,
        "keepClassNames": false
    },
    "module": {
        "type": "es6",
        "strict": false,
        "strictMode": true,
        "lazy": false,
        "noInterop": false
    },
    "minify": false,
    "isModule": true
}
```

## Komut Referansı

### typescript:build

TypeScript dosyalarını derler.

```bash
# Tek seferlik derleme
php bin/console typescript:build

# Watch modu (değişiklikleri izler)
php bin/console typescript:build --watch

# Verbose output
php bin/console typescript:build -v
```

**Seçenekler:**

-   `--watch`: Dosya değişikliklerini izler ve otomatik derler
-   `-v`, `--verbose`: Detaylı çıktı gösterir

### asset-map:compile

Asset'leri derler (TypeScript dahil).

```bash
php bin/console asset-map:compile
```

TypeScript bundle yüklüyse, bu komut otomatik olarak `typescript:build` komutunu da çalıştırır.

## Örnek Projeler

### Basit TypeScript Projesi

**Dizin Yapısı:**

```
assets/
  typescript/
    app.ts
    types/
      User.ts
    services/
      ApiService.ts
```

**app.ts:**

```typescript
import { User } from "./types/User";
import { ApiService } from "./services/ApiService";

const api = new ApiService();

async function loadUser(id: number): Promise<User> {
    return await api.get<User>(`/api/users/${id}`);
}

document.addEventListener("DOMContentLoaded", async () => {
    const user = await loadUser(1);
    console.log("User loaded:", user);
});
```

**types/User.ts:**

```typescript
export interface User {
    id: number;
    name: string;
    email: string;
}
```

**services/ApiService.ts:**

```typescript
export class ApiService {
    private baseUrl: string = "/api";

    async get<T>(endpoint: string): Promise<T> {
        const response = await fetch(`${this.baseUrl}${endpoint}`);
        return (await response.json()) as T;
    }
}
```

**Yapılandırma:**

```yaml
# config/packages/sensiolabs_typescript.yaml
sensiolabs_typescript:
    source_dir: ["%kernel.project_dir%/assets/typescript"]
```

**Template:**

```twig
{# templates/base.html.twig #}
{% block javascripts %}
    <script type="text/javascript" src="{{ asset('typescript/app.ts') }}"></script>
{% endblock %}
```

### React + TypeScript Projesi

**Dizin Yapısı:**

```
assets/
  typescript/
    app.tsx
    components/
      Button.tsx
      Modal.tsx
```

**.swcrc:**

```json
{
    "jsc": {
        "parser": {
            "syntax": "typescript",
            "tsx": true
        },
        "transform": {
            "react": {
                "runtime": "automatic"
            }
        },
        "target": "es2022"
    }
}
```

**Yapılandırma:**

```yaml
# config/packages/sensiolabs_typescript.yaml
sensiolabs_typescript:
    source_dir: ["%kernel.project_dir%/assets/typescript"]
    swc_config_file: "%kernel.project_dir%/.swcrc"
```

**app.tsx:**

```typescript
import React from "react";
import { createRoot } from "react-dom/client";
import { Button } from "./components/Button";

const App: React.FC = () => {
    return (
        <div>
            <h1>Hello TypeScript + React!</h1>
            <Button onClick={() => alert("Clicked!")}>Click Me</Button>
        </div>
    );
};

const root = createRoot(document.getElementById("root")!);
root.render(<App />);
```

### Multi-Directory Projesi

```yaml
# config/packages/sensiolabs_typescript.yaml
sensiolabs_typescript:
    source_dir:
        - "%kernel.project_dir%/assets/admin/typescript"
        - "%kernel.project_dir%/assets/frontend/typescript"
        - "%kernel.project_dir%/assets/shared/typescript"
```

**Dizin Yapısı:**

```
assets/
  admin/
    typescript/
      admin.ts
  frontend/
    typescript/
      app.ts
  shared/
    typescript/
      utils.ts
```

## Development Workflow

### 1. Local Development

```bash
# Terminal 1: Symfony server
symfony server:start

# Terminal 2: TypeScript watch
php bin/console typescript:build --watch

# Terminal 3: Asset watch (opsiyonel)
php bin/console asset-map:watch
```

**Veya Symfony CLI Workers ile:**

```yaml
# .symfony.local.yaml
workers:
    typescript:
        cmd: ["symfony", "console", "typescript:build", "--watch"]
```

```bash
# Tek komut ile hepsi
symfony server:start
```

### 2. Production Build

```bash
# 1. TypeScript derle
php bin/console typescript:build

# 2. Asset'leri compile et
php bin/console asset-map:compile

# 3. Cache temizle
php bin/console cache:clear --env=prod

# 4. Cache warmup
php bin/console cache:warmup --env=prod
```

## Sık Karşılaşılan Hatalar ve Çözümleri

### Hata: "SWC binary not found"

**Çözüm:**

```bash
# Binary'yi manuel indir
php bin/console typescript:build

# Veya cache temizle
rm -rf var/cache/*
php bin/console typescript:build
```

### Hata: "TypeScript file not found"

**Çözüm:**

```bash
# source_dir yapılandırmasını kontrol et
php bin/console config:dump sensiolabs_typescript

# Dosya yolunu kontrol et
ls assets/typescript/app.ts
```

### Hata: "Cannot read .swcrc file"

**Çözüm:**

```bash
# .swcrc dosyasının varlığını kontrol et
ls .swcrc

# JSON syntax kontrolü
cat .swcrc | jq .  # jq yüklü değilse, online JSON validator kullan
```

### Hata: "Module not found"

**Çözüm:**

```typescript
// Relative import kullan
import { User } from "./types/User"; // ✓ Doğru
import { User } from "types/User"; // ✗ Yanlış

// Dosya uzantısını ekle (.ts veya .tsx)
import { Button } from "./components/Button"; // ✓ Doğru
```

### Hata: "Watch mode not working"

**Çözüm:**

```bash
# İşlemi durdur (Ctrl+C)
# Cache temizle
php bin/console cache:clear

# Tekrar başlat
php bin/console typescript:build --watch
```

## En İyi Pratikler

### 1. Dizin Organizasyonu

```
assets/
  typescript/
    app.ts              # Entrypoint
    types/              # Type definitions
      User.ts
      Product.ts
    services/           # API, Storage vb.
      ApiService.ts
      StorageService.ts
    components/         # UI bileşenleri
      Button.ts
      Modal.ts
    utils/              # Yardımcı fonksiyonlar
      helpers.ts
      validators.ts
    constants/          # Sabitler
      config.ts
      routes.ts
```

### 2. Type Definitions

```typescript
// types/index.ts - Merkezi type export
export * from "./User";
export * from "./Product";
export * from "./Order";

// Kullanım
import { User, Product, Order } from "./types";
```

### 3. Environment Bazlı Config

```typescript
// constants/config.ts
const isDev = process.env.NODE_ENV === "development";

export const CONFIG = {
    apiUrl: isDev ? "http://localhost:8000/api" : "/api",
    debug: isDev,
};
```

### 4. TypeScript Strict Mode

```json
// tsconfig.json (SWC kullanmasanız bile IDE için)
{
    "compilerOptions": {
        "strict": true,
        "noImplicitAny": true,
        "strictNullChecks": true,
        "strictFunctionTypes": true,
        "noUnusedLocals": true,
        "noUnusedParameters": true
    }
}
```

## Performance İpuçları

### 1. Lazy Loading

```typescript
// Ağır modülleri lazy load edin
const loadChart = async () => {
    const Chart = await import("./components/Chart");
    return Chart.default;
};

document.getElementById("btn")?.addEventListener("click", async () => {
    const Chart = await loadChart();
    new Chart();
});
```

### 2. Code Splitting

```typescript
// app.ts - Ana bundle
import "./components/Header";
import "./components/Footer";

// admin.ts - Admin bundle (ayrı)
import "./components/AdminDashboard";
```

```twig
{# Frontend #}
<script src="{{ asset('typescript/app.ts') }}"></script>

{# Admin #}
<script src="{{ asset('typescript/admin.ts') }}"></script>
```

### 3. Tree Shaking

```typescript
// Sadece ihtiyacınız olanı import edin
import { debounce } from "./utils/helpers"; // ✓ İyi

// Tüm modülü import etmeyin
import * as helpers from "./utils/helpers"; // ✗ Kötü
```

## Kaynaklar

-   **Bundle GitHub:** https://github.com/sensiolabs/TypeScript-bundle
-   **SWC Resmi Site:** https://swc.rs/
-   **Symfony AssetMapper:** https://symfony.com/doc/current/frontend/asset_mapper.html
-   **TypeScript Dokümantasyon:** https://www.typescriptlang.org/docs/
-   **.swcrc Referansı:** https://swc.rs/docs/configuration/swcrc

## Notlar

-   ⚠️ Bundle, Node.js **gerektirmez** ancak isterseniz npm paketlerini de kullanabilirsiniz
-   ✓ SWC, TypeScript'i çok hızlı derler (Babel'den 20x daha hızlı)
-   ✓ Production için `asset-map:compile` kullanın, watch modunu kullanmayın
-   ✓ `.swcrc` dosyasını version control'e ekleyin
-   ⚠️ `var/typescript/` dizinini `.gitignore`'a ekleyin
