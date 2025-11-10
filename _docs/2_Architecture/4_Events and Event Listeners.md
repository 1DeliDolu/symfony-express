### ⚡ Symfony'de Olaylar (Events) ve Olay Dinleyicileri (Event Listeners)

Symfony uygulaması çalışırken birçok **olay (event)** tetiklenir.

Uygulamanız bu olayları **dinleyebilir** ve onlara tepki olarak özel kodlar çalıştırabilir.

Symfony, **HTTP isteği** işlenirken çekirdek (`kernel`) ile ilgili çeşitli olaylar üretir.

Ayrıca üçüncü taraf paketler de kendi olaylarını yayınlayabilir ve siz de kendi özel olaylarınızı oluşturabilirsiniz.

Aşağıdaki örneklerde hep aynı olay (`KernelEvents::EXCEPTION`) kullanılmıştır,

ama siz istediğiniz olayları dinleyebilir ve karıştırabilirsiniz.

---

### 🧠 1. Bir Event Listener (Olay Dinleyici) Oluşturmak

Olayları dinlemenin en yaygın yolu bir **listener** sınıfı oluşturmaktır:

```php
// src/EventListener/ExceptionListener.php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $message = sprintf(
            'Hata mesajı: %s (kod: %s)',
            $exception->getMessage(),
            $exception->getCode()
        );

        $response = new Response();
        $response->setContent($message);
        $response->headers->set('Content-Type', 'text/plain; charset=utf-8');

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}
```

Bu sınıfı bir servis olarak kaydedip Symfony’ye bunun bir **event listener** olduğunu bildirmemiz gerekir:

```php
// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\EventListener\ExceptionListener;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(ExceptionListener::class)
        ->tag('kernel.event_listener');
};
```

---

### ⚙️ Listener Metodunun Belirlenmesi

Symfony aşağıdaki mantıkla hangi metodun çağrılacağını belirler:

1. `kernel.event_listener` etiketi bir `method` tanımladıysa, **o metod** çağrılır.
2. Tanımlanmadıysa, varsa `__invoke()` metodu çağrılır.
3. `__invoke()` da yoksa bir  **hata fırlatılır** .

Ayrıca `priority` parametresiyle çalıştırılma sırasını belirleyebilirsiniz:

```php
->tag('kernel.event_listener', ['priority' => 100])
```

* **Yüksek sayı** = daha **erken** çalışır
* **Düşük sayı** = daha **geç** çalışır
* Symfony’nin dahili listener’ları genellikle `-256` ile `256` arasındadır.

---

### 🧩 `event` Özelliği

Eğer listener içindeki `$event` parametresi **tip belirtilmeden** tanımlanmışsa,

`event` özelliğini belirterek hangi event türünün dinleneceğini belirtebilirsiniz:

```php
->tag('kernel.event_listener', ['event' => 'kernel.exception', 'method' => 'onKernelException'])
```

Symfony şu sırayla metod arar:

1. `method` tanımlanmışsa onu çağırır.
2. Yoksa `on` + `PascalCase event adı` (`onKernelException`) metodunu arar.
3. O da yoksa `__invoke()` metodunu dener.
4. Hiçbiri yoksa hata verir.

---

### 🧱 2. PHP Attribute ile Event Listener Tanımlamak

Yeni Symfony sürümlerinde, listener'ı PHP attribute olarak doğrudan sınıf üzerinde tanımlayabilirsiniz:

```php
namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class MyListener
{
    public function __invoke(CustomEvent $event): void
    {
        // ...
    }
}
```

Bir sınıf birden fazla event’i dinleyebilir:

```php
namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: CustomEvent::class, method: 'onCustomEvent')]
#[AsEventListener(event: 'foo', priority: 42)]
#[AsEventListener(event: 'bar', method: 'onBarEvent')]
final class MyMultiListener
{
    public function onCustomEvent(CustomEvent $event): void {}
    public function onFoo(): void {}
    public function onBarEvent(): void {}
}
```

Ayrıca `#[AsEventListener]` doğrudan metodlara da uygulanabilir:

```php
namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class MyMultiListener
{
    #[AsEventListener]
    public function onCustomEvent(CustomEvent $event): void {}

    #[AsEventListener(event: 'foo', priority: 42)]
    public function onFoo(): void {}

    #[AsEventListener(event: 'bar')]
    public function onBarEvent(): void {}
}
```

> Not: Eğer metod parametresi (örneğin `$event`) tip ipucu içeriyorsa (`CustomEvent` gibi),
>
> `event` parametresini belirtmek zorunda değilsiniz.

---

### 🧠 3. Event Subscriber (Olay Abonesi) Kullanmak

Event’leri dinlemenin başka bir yolu da **subscriber** (abone) tanımlamaktır.

Bu sınıf, hangi event’leri dinleyeceğini kendisi bildirir.

```php
// src/EventSubscriber/ExceptionSubscriber.php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => [
                ['processException', 10],
                ['logException', 0],
                ['notifyException', -10],
            ],
        ];
    }

    public function processException(ExceptionEvent $event): void
    {
        // ...
    }

    public function logException(ExceptionEvent $event): void
    {
        // ...
    }

    public function notifyException(ExceptionEvent $event): void
    {
        // ...
    }
}
```

Symfony, `EventSubscriberInterface`’i otomatik tanır.

Eğer `services.php` veya `services.yaml` dosyanız `EventSubscriber` klasörünü otomatik yüklüyorsa

ve `autoconfigure: true` aktifse, ekstra yapılandırmaya gerek yoktur.

> Eğer metodlarınız çalışmıyorsa,
>
> `EventSubscriber` klasörünün yüklendiğinden ve `autoconfigure`’ün etkin olduğundan emin olun.
>
> Alternatif olarak, servise manuel olarak `kernel.event_subscriber` etiketi ekleyebilirsiniz.

---

### 🧭 Özet

| Özellik                            | Açıklama                                                                  |
| ----------------------------------- | --------------------------------------------------------------------------- |
| **Event Listener**            | Tek bir event’i dinler;`kernel.event_listener`etiketiyle tanımlanır    |
| **Event Subscriber**          | Birden fazla event’i dinleyebilir;`EventSubscriberInterface`uygular      |
| **Priority**                  | Pozitif = erken, negatif = geç çalışır                                 |
| **AsEventListener Attribute** | Listener’ları doğrudan sınıf veya metod üzerine tanımlamanı sağlar |
| **Autoconfigure**             | Subscriber’ları ve listener’ları otomatik olarak tanımlar              |

---

### 🧩 Kısa Örnek

Bir olay (örneğin `kernel.request`) dinlemek için minimal örnek:

```php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'kernel.request', priority: 100)]
class RequestLoggerListener
{
    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();
        dump('İstek geldi: '.$request->getPathInfo());
    }
}
```

Bu sayede Symfony uygulamanızdaki olay akışına müdahale edebilir,


### ⚙️ Symfony'de Request Events (İstek Olayları) ve Olay Türlerini Kontrol Etmek

Symfony’de bir sayfa yüklenirken **birden fazla istek (request)** oluşabilir:

* **Ana istek (main request)**
* **Alt istekler (sub-requests)** – genellikle bir Twig şablonunda controller gömüldüğünde (`{{ render(controller(...)) }}`) meydana gelir.

Bu nedenle, bir olay (örneğin `kernel.request`) dinlenirken, bazen yalnızca **ana istekte** çalışmanız gerekir.

---

### 🧠 Ana ve Alt İstekleri Ayırmak

```php
// src/EventListener/RequestListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            // ana istek değilse bir şey yapma
            return;
        }

        // yalnızca ana isteklerde çalışacak kod buraya
    }
}
```

Bazı işlemler — örneğin kimlik doğrulama veya günlükleme (logging) — yalnızca **ana isteklerde** yapılmalıdır.

Alt isteklerde (örneğin bir “embed controller” çağrısı) bunların tekrarlanması gereksizdir.

---

### 🔁 Listener mı Subscriber mı?

Hem **event listener** hem **event subscriber** aynı işlevi görebilir.

Ancak bazı farklar vardır:

| Tür                 | Avantaj                                                                                                                                                                             |
| -------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Subscriber** | Yeniden kullanılabilirliği yüksektir, çünkü hangi olayları dinlediğini**sınıfın kendisi**belirtir. Symfony çekirdeğinde genellikle subscriber’lar kullanılır. |
| **Listener**   | Daha esnektir, çünkü bir “bundle” veya ayara göre**etkinleştirilebilir / devre dışı bırakılabilir** .                                                             |

Genellikle, küçük projelerde  *listener* , karmaşık veya çoklu olaylarda *subscriber* tercih edilir.

---

### 🧩 Event Alias’ları (Olay Takma Adları)

Symfony’nin çekirdek olaylarını tanımlarken iki yöntem kullanılabilir:

* Olay ismi (örneğin `'kernel.request'`)
* Veya olayın sınıfı (örneğin `RequestEvent::class`)

İki tanım da  **aynı anlama gelir** .

```php
// src/EventSubscriber/RequestSubscriber.php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // ...
    }
}
```

Symfony, derleme (compile) sırasında sınıf isimlerini olay adlarıyla eşleştirir.

Yani `RequestEvent::class`, aslında `kernel.request` ile aynıdır.

Bu eşleme sistemine **kendi özel event’lerinizi** de ekleyebilirsiniz:

```php
// src/Kernel.php
namespace App;

use App\Event\MyCustomEvent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\AddEventAliasesPass;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AddEventAliasesPass([
            MyCustomEvent::class => 'my_custom_event',
        ]));
    }
}
```

Bu sayede `my_custom_event` ve `MyCustomEvent::class` aynı olayı temsil eder.


---



### ⚙️ **After Filters – `kernel.response` Olayı ile Controller Sonrası İşlemler**

Symfony’de yalnızca controller çalışmadan **önce** değil,

controller çalıştıktan **sonra** da işlem yapabilirsiniz.

Bu tür işlemler, `kernel.response` olayı (yani `KernelEvents::RESPONSE`) üzerinden dinlenir.

Bu olay, her istekte **controller bir `Response` nesnesi döndürdükten sonra** tetiklenir.

---

### 🔐 Örnek: Token Doğrulama Sonrası SHA1 Hash Ekleme

Önceki örneğimizde (TokenSubscriber) **controller öncesi** işlemler yapmıştık.

Şimdi ise, controller’dan sonra gelen cevaba özel bir **header** ekleyeceğiz.

#### 1️⃣ Önce, TokenSubscriber’da kimliği doğrulanan istekleri işaretleyelim

Controller öncesinde (`onKernelController` içinde) istek objesine bir flag ekliyoruz:

```php
public function onKernelController(ControllerEvent $event): void
{
    $controller = $event->getController();

    if (is_array($controller)) {
        $controller = $controller[0];
    }

    if ($controller instanceof TokenAuthenticatedController) {
        $token = $event->getRequest()->query->get('token');
        if (!in_array($token, $this->tokens)) {
            throw new AccessDeniedHttpException('Bu işlem için geçerli bir token gerekli!');
        }

        // Bu isteğin token doğrulamasından geçtiğini işaretle
        $event->getRequest()->attributes->set('auth_token', $token);
    }
}
```

Bu sayede, istek doğrulandıysa `auth_token` bilgisi request’e eklenir.

---

#### 2️⃣ Sonra, `onKernelResponse()` ile Response’a header ekleyelim

Controller çalıştıktan sonra tetiklenecek bir `ResponseEvent` dinleyicisi ekliyoruz:

```php
use Symfony\Component\HttpKernel\Event\ResponseEvent;

public function onKernelResponse(ResponseEvent $event): void
{
    // TokenSubscriber'da işaretlenen auth_token değerini al
    if (!$token = $event->getRequest()->attributes->get('auth_token')) {
        return; // Eğer token doğrulaması yoksa hiçbir şey yapma
    }

    $response = $event->getResponse();

    // Yanıt içeriğinden ve token'dan SHA1 hash oluştur
    $hash = sha1($response->getContent() . $token);

    // Header olarak ekle
    $response->headers->set('X-CONTENT-HASH', $hash);
}
```

Ve son olarak, bu iki olayı aynı subscriber içinde dinleyelim:

```php
public static function getSubscribedEvents(): array
{
    return [
        KernelEvents::CONTROLLER => 'onKernelController',
        KernelEvents::RESPONSE   => 'onKernelResponse',
    ];
}
```

---

### ✅ Artık Ne Oldu?

`TokenSubscriber` artık hem:

* **Controller çalışmadan önce** (`onKernelController`) token doğrulaması yapıyor,
* **Controller döndükten sonra** (`onKernelResponse`) yanıtın üzerine SHA1 hash ekliyor.

İstek `TokenAuthenticatedController` arayüzünü implemente eden bir controller’a aitse,

bu filtreler otomatik olarak devreye giriyor.

Bu yapı, Symfony’de “before” ve “after” filtre mantığını esnek şekilde gerçekleştirmenin en iyi yoludur.

---

### 🧩 **Kalıtım (Inheritance) Kullanmadan Metot Davranışını Özelleştirmek**

Bazen bir sınıfın davranışını, **kalıtım olmadan** genişletmek istersiniz.

Bunun için, bir metodun **öncesinde** veya **sonrasında** özel bir event yayınlayabilirsiniz.

---

#### 📨 Örnek: E-posta Gönderiminden Önce/Sonra Event Tetiklemek

```php
class CustomMailer
{
    public function __construct(
        private \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $dispatcher
    ) {}

    public function send(string $subject, string $message): mixed
    {
        // 1️⃣ Metoddan önce event yayınla
        $event = new BeforeSendMailEvent($subject, $message);
        $this->dispatcher->dispatch($event, 'mailer.pre_send');

        // Event dinleyicileri tarafından değiştirilmiş olabilir
        $subject = $event->getSubject();
        $message = $event->getMessage();

        // 2️⃣ Asıl işlem burada
        $returnValue = 'Mail sent: ' . $subject;

        // 3️⃣ Metoddan sonra event yayınla
        $event = new AfterSendMailEvent($returnValue);
        $this->dispatcher->dispatch($event, 'mailer.post_send');

        // Event sonucu değiştirilmiş olabilir
        return $event->getReturnValue();
    }
}
```

Burada iki özel olay yayınlanır:

* `mailer.pre_send` → metoddan **önce**
* `mailer.post_send` → metoddan **sonra**

---

#### 📦 **BeforeSendMailEvent** Sınıfı

```php
// src/Event/BeforeSendMailEvent.php
namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class BeforeSendMailEvent extends Event
{
    public function __construct(
        private string $subject,
        private string $message,
    ) {}

    public function getSubject(): string { return $this->subject; }
    public function setSubject(string $subject): void { $this->subject = $subject; }

    public function getMessage(): string { return $this->message; }
    public function setMessage(string $message): void { $this->message = $message; }
}
```

---

#### 📦 **AfterSendMailEvent** Sınıfı

```php
// src/Event/AfterSendMailEvent.php
namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AfterSendMailEvent extends Event
{
    public function __construct(private mixed $returnValue) {}

    public function getReturnValue(): mixed { return $this->returnValue; }
    public function setReturnValue(mixed $returnValue): void { $this->returnValue = $returnValue; }
}
```

Her iki event de ilgili bilgiyi hem **okumaya** hem de **değiştirmeye** izin verir.

---

#### 🧠 Event Subscriber ile Metod Sonrası Davranışını Değiştirme

```php
// src/EventSubscriber/MailPostSendSubscriber.php
namespace App\EventSubscriber;

use App\Event\AfterSendMailEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MailPostSendSubscriber implements EventSubscriberInterface
{
    public function onMailerPostSend(AfterSendMailEvent $event): void
    {
        $value = $event->getReturnValue();
        // Örneğin, sonucu logla veya manipüle et
        $event->setReturnValue($value . ' ✅ (Post-processed)');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'mailer.post_send' => 'onMailerPostSend',
        ];
    }
}
```

Bu subscriber, `mailer.post_send` olayını dinler ve gönderim sonrası sonucu değiştirir.

---

### 🧾 **Özet**

| Konu                         | Açıklama                                                                                    |
| ---------------------------- | --------------------------------------------------------------------------------------------- |
| **kernel.controller**  | Controller çalışmadan önce tetiklenir (“before filter”).                                |
| **kernel.response**    | Controller döndükten sonra tetiklenir (“after filter”).                                   |
| **Request Attributes** | Controller öncesinde işaretlenen bilgiler (ör.`auth_token`) sonrasında kullanılabilir. |
| **Event Dispatching**  | Metot içinde “öncesi/sonrası” olayları tetikleyerek davranış genişletilebilir.       |
| **Subscriber**         | Event’leri merkezi biçimde yönetmek için önerilen yöntemdir.                            |

---

### 🚀 Sonuç

Symfony’nin **EventDispatcher** sistemi sayesinde:

* Controller öncesinde ve sonrasında filtreler oluşturabilir,
* Metot davranışlarını inheritance olmadan özelleştirebilir,
* Modüler, yeniden kullanılabilir, esnek iş akışları geliştirebilirsiniz.



---

### 🪲 Event Listener’ları Hata Ayıklamak

Kayıtlı listener’ları görmek için:

```bash
php bin/console debug:event-dispatcher
```

Belirli bir olayın listener’larını görmek için:

```bash
php bin/console debug:event-dispatcher kernel.exception
```

Kısmi eşleşme ile arama:

```bash
php bin/console debug:event-dispatcher kernel
```

Security sistemi her “firewall” için ayrı bir event dispatcher kullanır.

Belli bir dispatcher’ı kontrol etmek isterseniz:

```bash
php bin/console debug:event-dispatcher --dispatcher=security.event_dispatcher.main
```

---

### 🧩 “Before” ve “After” Filtreleri Kurmak

Symfony’de bazı işlemleri bir controller’dan **önce** veya **sonra** çalıştırmak isteyebilirsiniz.

Symfony’de `preExecute()` gibi yöntemler yoktur; bunun yerine **EventDispatcher** kullanılır.

---

### 🔐 Örnek: Token Doğrulama (Before Filter)

#### 1️⃣ Token Parametrelerini Tanımlayın

```php
// config/services.php
$container->setParameter('tokens', [
    'client1' => 'pass1',
    'client2' => 'pass2',
]);
```

#### 2️⃣ Kontrol Gerektiren Controller’ları İşaretleyin

```php
// src/Controller/TokenAuthenticatedController.php
namespace App\Controller;

interface TokenAuthenticatedController
{
    // Bu interface’i implement eden controller’lar token gerektirir.
}
```

```php
// src/Controller/FooController.php
namespace App\Controller;

use App\Controller\TokenAuthenticatedController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FooController extends AbstractController implements TokenAuthenticatedController
{
    public function bar(): Response
    {
        return new Response('Private content!');
    }
}
```

#### 3️⃣ Token Doğrulayan Subscriber Yazın

```php
// src/EventSubscriber/TokenSubscriber.php
namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class TokenSubscriber implements EventSubscriberInterface
{
    public function __construct(private array $tokens) {}

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // Controller bir dizi olarak dönerse (örn. [instance, 'method'])
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof TokenAuthenticatedController) {
            $token = $event->getRequest()->query->get('token');

            if (!in_array($token, $this->tokens)) {
                throw new AccessDeniedHttpException('Bu işlem için geçerli bir token gerekli!');
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
```

Bu subscriber, her istekte `KernelEvents::CONTROLLER` olayını dinler.

Eğer çağrılacak controller `TokenAuthenticatedController` arayüzünü implemente ediyorsa,

 **token doğrulaması yapılır** .

Token geçersizse `403 Access Denied` hatası fırlatılır.

---

### 🧩 Özet

| Konu                                        | Açıklama                                                               |
| ------------------------------------------- | ------------------------------------------------------------------------ |
| **isMainRequest()**                   | Ana isteği kontrol etmek için kullanılır.                            |
| **Event Alias**                       | Olay adlarını FQCN (ör.`RequestEvent::class`) ile eşleştirir.     |
| **Listener vs Subscriber**            | Subscriber’lar daha modüler, Listener’lar daha esnektir.              |
| **debug:event-dispatcher**            | Olay ve listener listesini gösterir.                                    |
| **Before Filter (kernel.controller)** | Controller çalışmadan önce özel mantık eklemek için kullanılır. |

---

### ✅ Sonuç

Symfony’nin **EventDispatcher** bileşeni, istek-yanıt döngüsüne güçlü şekilde müdahale etmenizi sağlar:

* Öncesinde (`kernel.request`, `kernel.controller`) kontroller yapabilir,
* Sonrasında (`kernel.response`, `kernel.terminate`) işlemler başlatabilirsiniz.
