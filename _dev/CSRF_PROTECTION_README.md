# Symfony CSRF Koruması Rehberi

## CSRF Nedir?

**CSRF (Cross-Site Request Forgery)**, bir saldırganın kullanıcıyı bilgisi dışında bir web uygulamasında işlem yaptırmaya zorladığı bir saldırı türüdür.

### Gerçek Hayat Örneği

Kötü niyetli bir saldırgan şu gibi bir website oluşturabilir:

```html
<html>
    <body>
        <form action="https://example.com/settings/update-email" method="POST">
            <input
                type="hidden"
                name="email"
                value="malicious-actor-address@some-domain.com"
            />
        </form>
        <script>
            document.forms[0].submit();
        </script>

        <!-- Kullanıcıyı meşgul edecek içerik -->
    </body>
</html>
```

**Senaryo:**

1. Kullanıcı `https://example.com` sitesine zaten giriş yapmış
2. Bir e-posta linki veya sosyal medya postu ile bu kötü niyetli siteye yönlendiriliyor
3. Sayfa açılır açılmaz form otomatik gönderiliyor
4. Kullanıcının email adresi değiştirilmiş oluyor (hesap ele geçirilmiş)
5. Kullanıcı bunun farkında bile değil

### Çözüm: Anti-CSRF Token'ları

CSRF saldırılarını önlemenin etkili yolu **anti-CSRF token'ları** kullanmaktır:

-   Form'lara gizli alan olarak eklenen benzersiz token'lar
-   Sunucu bu token'ları doğrular
-   İstek beklenen kaynaktan gelmiş mi kontrol edilir

**İki Yaklaşım:**

1. **Stateful (Durumlu):** Token'lar session'da saklanır, kullanıcı ve işlem bazında benzersiz
2. **Stateless (Durumsuz):** Token'lar client-side'da generate edilir, session gerektirmez

## Kurulum

```bash
composer require symfony/security-csrf
```

### CSRF Korumasını Etkinleştir

```php
// config/packages/framework.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->csrfProtection()
        ->enabled(true)
    ;
};
```

⚠️ **Önemli:** Varsayılan olarak CSRF token'ları **session'da saklanır**. Bu yüzden CSRF korumalı bir form render edildiğinde otomatik olarak session başlatılır.

## Symfony Form'larında CSRF Koruması

**Harika Haber:** Symfony Form'ları varsayılan olarak CSRF token'larını içerir ve otomatik kontrol eder. **Hiçbir şey yapmanıza gerek yok!** 🎉

### Varsayılan Davranış

```php
// Form oluştururken otomatik CSRF koruması eklenir
$form = $this->createForm(TaskType::class, $task);

// Form submit edildiğinde otomatik kontrol edilir
$form->handleRequest($request);
if ($form->isSubmitted() && $form->isValid()) {
    // Token otomatik kontrol edildi, güvenli!
}
```

**Varsayılan Token Alan Adı:** `_csrf_token` (özelleştirilebilir)

## Global CSRF Yapılandırması

Tüm formlar için CSRF ayarlarını özelleştirin:

```php
// config/packages/framework.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework) {
    $framework->form()->csrfProtection()
        ->enabled(true)
        ->fieldName('custom_token_name')  // Gizli alan adı
    ;
};
```

## Form Bazında CSRF Yapılandırması

Her form için ayrı ayrı özelleştirin:

```php
// src/Form/TaskType.php
namespace App\Form;

use App\Entity\Task;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class TaskType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,

            // Bu form için CSRF korumasını etkinleştir/devre dışı bırak
            'csrf_protection' => true,

            // Token'ı saklayan gizli HTML alanının adı
            'csrf_field_name' => '_token',

            // Token değerini generate etmek için kullanılan benzersiz string
            // Her form için farklı string kullanmak güvenliği artırır
            'csrf_token_id' => 'task_item',
        ]);
    }
}
```

### CSRF Alan Render'ını Özelleştirme

Form theme oluşturarak CSRF alanını özelleştirebilirsiniz:

```twig
{# templates/form/csrf_token_widget.html.twig #}
{% block csrf_token_widget %}
    <input type="hidden"
           id="{{ id }}"
           name="{{ full_name }}"
           value="{{ value }}"
           data-csrf-token="true"
    />
{% endblock %}
```

## Login Form ve Logout için CSRF Koruması

### Login Form'unda CSRF

```yaml
# config/packages/security.yaml
security:
    firewalls:
        main:
            form_login:
                enable_csrf: true
```

```twig
{# templates/security/login.html.twig #}
<form method="post">
    <input type="text" name="_username" />
    <input type="password" name="_password" />

    {# CSRF token otomatik eklenir #}
    <input type="hidden" name="_csrf_token" value="{{ csrf_token('authenticate') }}" />

    <button type="submit">Login</button>
</form>
```

### Logout için CSRF

```yaml
# config/packages/security.yaml
security:
    firewalls:
        main:
            logout:
                enable_csrf: true
```

```twig
{# Logout linki #}
<a href="{{ path('app_logout', {_csrf_token: csrf_token('logout')}) }}">
    Çıkış Yap
</a>
```

## Manuel CSRF Token Oluşturma ve Kontrol

Symfony Form kullanmadan düz HTML form'larında CSRF koruması.

### Template'te Token Oluştur

```twig
{# templates/admin/post/delete.html.twig #}
<form action="{{ url('admin_post_delete', { id: post.id }) }}" method="post">
    {# csrf_token() fonksiyonunun argümanı token ID'dir #}
    <input type="hidden" name="token" value="{{ csrf_token('delete-item') }}">

    <button type="submit">Delete item</button>
</form>
```

### Controller'da Token Kontrol Et

```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

public function delete(Request $request): Response
{
    $submittedToken = $request->getPayload()->get('token');

    // 'delete-item' template'te kullanılan aynı ID
    if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
        // Token geçerli, işlemi yap
        // ... objeyi sil

        return $this->redirectToRoute('admin_post_list');
    }

    throw $this->createAccessDeniedException('Invalid CSRF token.');
}
```

## IsCsrfTokenValid Attribute (Symfony 7.1+)

### Temel Kullanım

```php
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

#[IsCsrfTokenValid('delete-item', tokenKey: 'token')]
public function delete(): Response
{
    // Token otomatik kontrol edildi
    // Geçersizse 403 hatası döner

    // ... objeyi sil
    return $this->redirectToRoute('admin_post_list');
}
```

### Dinamik Token ID

Her item için farklı token:

**Template:**

```twig
<form action="{{ url('admin_post_delete', { id: post.id }) }}" method="post">
    {# Dinamik token ID #}
    <input type="hidden" name="token" value="{{ csrf_token('delete-item-' ~ post.id) }}">

    <button type="submit">Delete</button>
</form>
```

**Controller:**

```php
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

#[IsCsrfTokenValid(
    new Expression('"delete-item-" ~ args["post"].getId()'),
    tokenKey: 'token'
)]
public function delete(Post $post): Response
{
    // ... objeyi sil
    return $this->redirectToRoute('admin_post_list');
}
```

### Controller Class'ına Uygulama

Tüm action'lara CSRF koruması:

```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

#[IsCsrfTokenValid('admin-actions')]
final class AdminController extends AbstractController
{
    // Bu controller'daki TÜM action'lar CSRF korumalı

    public function create(): Response { /* ... */ }
    public function update(): Response { /* ... */ }
    public function delete(): Response { /* ... */ }
}
```

### HTTP Method Kısıtlaması (Symfony 7.3+)

CSRF kontrolünü sadece belirli HTTP metodlarına uygula:

```php
#[IsCsrfTokenValid('delete-item', tokenKey: 'token', methods: ['DELETE', 'POST'])]
public function delete(Post $post): Response
{
    // Sadece DELETE ve POST metodlarında CSRF kontrol edilir
    // GET isteğinde attribute göz ardı edilir
}
```

## Stateless CSRF Token'ları (Symfony 7.2+)

### Nedir?

**Stateful (Geleneksel):** Token'lar session'da saklanır
**Stateless (Yeni):** Token'lar session gerektirmez, header bazlı kontrol

**Avantajları:**

-   ✓ Session'a ihtiyaç yok
-   ✓ Sayfaları tamamen cache'leyebilirsiniz
-   ✓ Yine de CSRF koruması var

### Yapılandırma

```php
// config/packages/framework.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->csrfProtection()
        ->statelessTokenIds(['submit', 'authenticate', 'logout'])
    ;
};
```

**Açıklama:**

-   `submit`: Form'lar için varsayılan
-   `authenticate`: Login için
-   `logout`: Logout için

### Nasıl Çalışır?

Stateless token'lar şu HTTP header'ları kontrol eder:

-   `Origin` header
-   `Referer` header

Bu header'lar uygulamanın kendi domain'i ile eşleşiyorsa token geçerli kabul edilir.

⚠️ **Önemli:** Reverse proxy arkasındaysanız, proxy yapılandırmasının doğru olduğundan emin olun.

### Default Token ID Ayarlama

Tüm form'lar için varsayılan stateless token:

```php
// config/packages/framework.php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    // Stateless token ID'leri tanımla
    $framework->csrfProtection()
        ->statelessTokenIds(['submit', 'authenticate', 'logout'])
    ;

    // Form'lar için varsayılan token ID
    $framework->form()
        ->csrfProtection()
            ->tokenId('submit')
    ;
};
```

Bu yapılandırma ile autoconfiguration kullanan tüm form'lar otomatik olarak `submit` token ID'sini kullanır ve stateless koruma elde eder.

## JavaScript ile CSRF Token Oluşturma

### "Double-Submit" Koruması

Stateless CSRF koruması, ek bir savunma katmanı olarak **cookie ve header** kontrolü de yapabilir.

**Nasıl Çalışır:**

1. JavaScript, form submit edildiğinde kriptografik olarak güvenli bir random token oluşturur
2. Token'ı form'un gizli CSRF alanına ekler
3. Aynı token'ı hem cookie hem de request header'ında gönderir
4. Sunucu, cookie ve header'daki değerleri karşılaştırır

**Güvenlik Özellikleri:**

-   Her submit için yeni token (cookie fixation'ı önler)
-   `samesite=strict` cookie attribute
-   `__Host-` cookie prefix (HTTPS zorunlu, domain'e sınırlı)

### JavaScript Snippet Beklentisi

Symfony JavaScript snippet'i şu koşullardan birini bekler:

**Opsyon 1: Alan Adı**

```html
<input type="hidden" name="_csrf_token" value="..." />
```

**Opsyon 2: Data Attribute**

```html
<input
    type="hidden"
    name="token"
    value="..."
    data-controller="csrf-protection"
/>
```

### Örnek JavaScript Implementasyonu

```javascript
// CSRF token generator
document.addEventListener("submit", function (e) {
    const form = e.target;
    const csrfField = form.querySelector(
        '[name="_csrf_token"], [data-controller="csrf-protection"]'
    );

    if (csrfField) {
        // Kriptografik olarak güvenli random token
        const token = generateSecureToken();

        // Form'a ekle
        csrfField.value = token;

        // Cookie'ye kaydet
        document.cookie = `csrf-token=${token}; path=/; samesite=strict; secure`;

        // Header olarak gönder (fetch API kullanıyorsanız)
        // Aksi halde meta tag kullanabilirsiniz
    }
});

function generateSecureToken() {
    const array = new Uint8Array(32);
    crypto.getRandomValues(array);
    return Array.from(array, (byte) => byte.toString(16).padStart(2, "0")).join(
        ""
    );
}
```

### Davranışsal Kontrol

**Önemli Özellik:**

-   Session zaten varsa ve "double-submit" başarılı olmuşsa
-   Bu doğrulama gelecek istekler için **zorunlu hale gelir**
-   Bu, JavaScript etkin olduğunda ekstra güvenlik sağlar
-   JavaScript devre dışıysa Origin/Referer kontrolüne geri döner

⚠️ **Tavsiye Edilmeyen:** Tüm isteklerde "double-submit" zorunlu tutmak kullanıcı deneyimini bozabilir. Yukarıdaki opportunistic yaklaşım önerilir.

## Caching Stratejileri

CSRF korumalı formları cache'lemek için stratejiler:

### 1. ESI Fragment (Edge Side Includes)

```twig
{# Cache'li sayfa #}
{% cache 'product_page' ttl(3600) %}
    <h1>{{ product.name }}</h1>
    <p>{{ product.description }}</p>
{% endcache %}

{# Cache'siz CSRF korumalı form #}
{{ render_esi(controller('App\\Controller\\ProductController::buyForm', {
    id: product.id
})) }}
```

### 2. AJAX ile Form Yükleme

```javascript
// Sayfa cache'li, form AJAX ile yükleniyor
fetch("/product/123/buy-form")
    .then((response) => response.text())
    .then((html) => {
        document.getElementById("buy-form-container").innerHTML = html;
    });
```

### 3. Stateless Token Kullan (En İyi)

```php
// Stateless token ile tam sayfa cache mümkün
$framework->csrfProtection()
    ->statelessTokenIds(['submit'])
;
```

## CSRF ve Compression Saldırıları

### BREACH ve CRIME

Bu saldırılar, HTTPS kullanan sitelerde HTTP compression'dan sızan bilgileri kullanarak plaintext'i kurtarmaya çalışır.

**Symfony'nin Koruması:**

-   Token'lara random bir mask eklenir
-   Bu mask token'ı scramble etmek için kullanılır
-   Saldırganın token'ı tahmin etmesi önlenir

**Teknik Detay:**

```
Original Token: abc123def456
Random Mask:    xyz789uvw012
Result Token:   [masked_value]
```

## Gerçek Dünya Örnekleri

### Örnek 1: Blog Post Silme

**Template:**

```twig
{# templates/blog/post/show.html.twig #}
<article>
    <h1>{{ post.title }}</h1>
    <p>{{ post.content }}</p>

    <form action="{{ path('blog_post_delete', {id: post.id}) }}" method="post"
          onsubmit="return confirm('Are you sure?')">
        <input type="hidden" name="_token" value="{{ csrf_token('delete-post-' ~ post.id) }}">
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>
</article>
```

**Controller:**

```php
namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\ExpressionLanguage\Expression;

class BlogController extends AbstractController
{
    #[IsCsrfTokenValid(
        new Expression('"delete-post-" ~ args["post"].getId()'),
        tokenKey: '_token'
    )]
    public function delete(Post $post): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'Post deleted successfully!');

        return $this->redirectToRoute('blog_post_index');
    }
}
```

### Örnek 2: AJAX Form Submit

**Template:**

```twig
<form id="comment-form" data-url="{{ path('blog_comment_add', {postId: post.id}) }}">
    <textarea name="content" required></textarea>
    <input type="hidden" name="_csrf_token" value="{{ csrf_token('add-comment') }}">
    <button type="submit">Add Comment</button>
</form>
```

**JavaScript:**

```javascript
document
    .getElementById("comment-form")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        try {
            const response = await fetch(form.dataset.url, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (response.ok) {
                const data = await response.json();
                alert("Comment added!");
                form.reset();
            } else {
                const error = await response.json();
                alert("Error: " + error.message);
            }
        } catch (error) {
            alert("Network error");
        }
    });
```

**Controller:**

```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

#[IsCsrfTokenValid('add-comment', tokenKey: '_csrf_token')]
public function addComment(Request $request, int $postId): JsonResponse
{
    $content = $request->request->get('content');

    // Comment'i kaydet
    // ...

    return new JsonResponse([
        'success' => true,
        'message' => 'Comment added successfully'
    ]);
}
```

### Örnek 3: Stateless CSRF ile Cached Form

**Yapılandırma:**

```php
// config/packages/framework.php
$framework->csrfProtection()
    ->statelessTokenIds(['newsletter-signup'])
;
```

**Template (Tamamen Cache'lenebilir):**

```twig
{% cache 'homepage' ttl(3600) %}
<form action="{{ path('newsletter_signup') }}" method="post">
    <input type="email" name="email" required>
    <input type="hidden" name="_token" value="{{ csrf_token('newsletter-signup') }}">
    <button type="submit">Subscribe</button>
</form>
{% endcache %}
```

**Controller:**

```php
#[IsCsrfTokenValid('newsletter-signup', tokenKey: '_token')]
public function newsletterSignup(Request $request): Response
{
    $email = $request->request->get('email');

    // Newsletter kaydı yap
    // ...

    return $this->redirectToRoute('newsletter_success');
}
```

## En İyi Pratikler

### 1. Her Zaman CSRF Koruması Kullanın

```php
// ✓ İyi
public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'csrf_protection' => true,  // Varsayılan zaten true
    ]);
}

// ✗ Kötü (sadece çok özel durumlar için)
$resolver->setDefaults([
    'csrf_protection' => false,
]);
```

### 2. Unique Token ID'ler Kullanın

```php
// ✓ İyi - Her form için benzersiz
'csrf_token_id' => 'delete-post-' . $post->getId(),

// ✗ Kötü - Genel token
'csrf_token_id' => 'delete',
```

### 3. HTTP Method'ları Doğru Kullanın

```php
// ✓ İyi - Değişiklik yapan işlemler POST/DELETE
#[Route('/post/{id}/delete', methods: ['POST', 'DELETE'])]
public function delete(Post $post): Response

// ✗ Kötü - GET ile silme
#[Route('/post/{id}/delete', methods: ['GET'])]
public function delete(Post $post): Response
```

### 4. Stateless Token'ları Cache İçin Kullanın

```php
// Yoğun trafik alan, sık değişmeyen formlar için
$framework->csrfProtection()
    ->statelessTokenIds(['contact-form', 'newsletter', 'search'])
;
```

### 5. SameSite Cookie Attribute

```yaml
# config/packages/framework.yaml
framework:
    session:
        cookie_samesite: "lax" # veya 'strict'
```

## Hata Ayıklama

### CSRF Token Geçersiz Hatası

**Kontrol Listesi:**

1. Token ID'ler eşleşiyor mu?

    ```php
    // Template
    csrf_token('delete-item')

    // Controller
    $this->isCsrfTokenValid('delete-item', $token)
    ```

2. Session çalışıyor mu?

    ```bash
    php bin/console debug:container session
    ```

3. Cache temiz mi?
    ```bash
    php bin/console cache:clear
    ```

### Debug Modu

```php
public function delete(Request $request): Response
{
    $submittedToken = $request->request->get('_token');

    // Debug
    dump([
        'submitted' => $submittedToken,
        'expected_id' => 'delete-item',
        'valid' => $this->isCsrfTokenValid('delete-item', $submittedToken)
    ]);

    // ...
}
```

## Kaynaklar

-   **Symfony CSRF Dokümantasyon:** https://symfony.com/doc/current/security/csrf.html
-   **OWASP CSRF:** https://owasp.org/www-community/attacks/csrf
-   **SameSite Cookies:** https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie/SameSite
-   **Symfony Security:** https://symfony.com/doc/current/security.html

## Özet

| Özellik             | Stateful    | Stateless    |
| ------------------- | ----------- | ------------ |
| **Session gerekli** | ✓ Evet      | ✗ Hayır      |
| **Cache'lenebilir** | ✗ Zor       | ✓ Kolay      |
| **Güvenlik**        | ✓ Yüksek    | ✓ Yüksek     |
| **Kullanım**        | Form submit | Cached pages |
| **Varsayılan**      | ✓ Evet      | ✗ Manuel     |

**Hızlı Başlangıç:**

```bash
# 1. Yükle
composer require symfony/security-csrf

# 2. Form oluştur (otomatik CSRF korumalı)
php bin/console make:form TaskType

# 3. Kullan
# Symfony Form kullanıyorsanız başka bir şey yapmanıza gerek yok! ✨
```
