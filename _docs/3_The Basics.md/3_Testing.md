```markdown
# Test Etme (Testing)

Yeni bir kod satırı yazdığınızda, potansiyel olarak yeni hatalar da eklemiş olursunuz. Daha iyi ve güvenilir uygulamalar geliştirmek için kodunuzu **fonksiyonel ve birim testleri (unit tests)** ile test etmelisiniz.

Symfony, zengin bir test çerçevesi sunmak için bağımsız bir kütüphane olan **PHPUnit** ile entegre çalışır. Bu bölüm, Symfony testleri yazmak için ihtiyaç duyacağınız PHPUnit temellerini kapsar. PHPUnit ve özellikleri hakkında daha fazla bilgi için resmi PHPUnit belgelerine bakabilirsiniz.

---

## Test Türleri

Birçok otomatik test türü vardır ve tanımlar projeden projeye değişiklik gösterebilir. Symfony dokümantasyonunda aşağıdaki tanımlar kullanılır:

### 1. Unit Tests (Birim Testleri)
Belirli birimlerin (örneğin tek bir sınıfın) beklenen şekilde davrandığını doğrular.

### 2. Integration Tests (Entegrasyon Testleri)
Birden fazla sınıfın veya servisin birlikte çalışmasını test eder. Genellikle Symfony’nin **service container**’ı ile etkileşime girer.

### 3. Application Tests (Uygulama/Fonksiyonel Testler)
Tam uygulamanın davranışını test eder. Gerçek veya simüle edilmiş HTTP istekleri gönderir ve yanıtların beklendiği gibi olup olmadığını kontrol eder.

---

## Kurulum

İlk testinizi oluşturmadan önce test için gerekli paketleri yükleyin:

```bash
composer require --dev symfony/test-pack
```

Kurulumdan sonra PHPUnit’i çalıştırın:

```bash
php bin/phpunit
```

Bu komut, `tests/` dizininde bulunan tüm testleri çalıştırır. Her test sınıfının adı **Test** ile bitmelidir (örneğin: `BlogControllerTest`).

PHPUnit ayarları genellikle projenizin kök dizinindeki `phpunit.dist.xml` dosyasında bulunur. Symfony Flex tarafından varsayılan bir yapılandırma sağlanır. Gelişmiş özellikler (örneğin kod kapsamı veya çoklu test setleri) için PHPUnit belgelerine bakabilirsiniz.

Eğer bu dosyalar eksikse:

```bash
composer recipes:install phpunit/phpunit --force -v
```

komutunu çalıştırarak yeniden oluşturabilirsiniz.

---

## Unit Tests (Birim Testleri)

Birim testleri, tek bir sınıfın veya metodun doğru çalıştığını doğrular. Symfony’de birim testi yazmak, standart PHPUnit testi yazmakla aynıdır.

Varsayılan olarak, testler `tests/` dizininde tutulur ve bu dizin, uygulamanızın dizin yapısını yansıtmalıdır:

```
src/Form/UserType.php  →  tests/Form/UserTypeTest.php
```

Testleri çalıştırmak için:

```bash
php bin/phpunit
php bin/phpunit tests/Form
php bin/phpunit tests/Form/UserTypeTest.php
```

Büyük test yapılarında testleri alt klasörlere ayırabilirsiniz (örneğin `tests/Unit/`, `tests/Integration/`, `tests/Application/`).

---

## Integration Tests (Entegrasyon Testleri)

Entegrasyon testleri, birim testlerinden daha geniş bir kapsama sahiptir ve genellikle birden fazla servisin birlikte çalışmasını test eder.

Symfony, kernel’i başlatmayı kolaylaştıran **KernelTestCase** sınıfını sağlar:

```php
// tests/Service/NewsletterGeneratorTest.php
namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NewsletterGeneratorTest extends KernelTestCase
{
    public function testSomething(): void
    {
        self::bootKernel();

        // ...
    }
}
```

`KernelTestCase`, her testte kernel’in yeniden başlatılmasını sağlar. Böylece testler birbirinden bağımsız çalışır.

Kernel sınıfı genellikle `.env.test` dosyasında tanımlanır:

```env
# .env.test
KERNEL_CLASS=App\Kernel
```

Dilerseniz `getKernelClass()` veya `createKernel()` metodlarını da test içinde geçersiz kılabilirsiniz.

---

## Test Ortamını Ayarlama

Testler, **test environment** içinde çalışır. Bu sayede testlere özel yapılandırmaları `config/packages/test/` dizininde yapabilirsiniz.

Örneğin Twig paketi test ortamında daha katı çalışacak şekilde yapılandırılmıştır:

```php
// config/packages/test/twig.php
use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig): void {
    $twig->strictVariables(true);
};
```

Kernel’i farklı bir ortam veya debug ayarıyla başlatmak için:

```php
self::bootKernel([
    'environment' => 'my_test_env',
    'debug'       => false,
]);
```

CI sunucularında test performansını artırmak için `debug` modunu **false** olarak ayarlamanız önerilir. Bu durumda önbelleği elle temizlemeniz gerekebilir:

```php
(new \Symfony\Component\Filesystem\Filesystem())->remove(__DIR__.'/../var/cache/test');
```

---

## Ortam Değişkenlerini Özelleştirme

Veritabanı gibi özel yapılandırmaları test ortamı için değiştirebilirsiniz:

```env
# .env.test
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name_test?serverVersion=8.0.37"
```

Test ortamında şu dosyalar sırasıyla okunur:

1. `.env`
2. `.env.test`
3. `.env.test.local`

`.env.local`  **test ortamında kullanılmaz** .

---

## Testlerde Servisleri Erişmek

Entegrasyon testlerinde servisleri container’dan almak gerekebilir. Bunu şu şekilde yapabilirsiniz:

```php
// tests/Service/NewsletterGeneratorTest.php
namespace App\Tests\Service;

use App\Service\NewsletterGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NewsletterGeneratorTest extends KernelTestCase
{
    public function testSomething(): void
    {
        // (1) Kernel’i başlat
        self::bootKernel();

        // (2) Container’ı al
        $container = static::getContainer();

        // (3) Servisi çağır ve sonucu test et
        $newsletterGenerator = $container->get(NewsletterGenerator::class);
        $newsletter = $newsletterGenerator->generateMonthlyNews(/* ... */);

        $this->assertEquals('...', $newsletter->getContent());
    }
}
```

`static::getContainer()` metodu, **özel bir test container** döndürür. Bu container, hem public servisleri hem de silinmemiş private servisleri içerir.

Eğer silinmiş (hiç kullanılmayan) private servisleri test etmeniz gerekiyorsa, bu servisleri **config/services_test.yaml** dosyasında `public: true` olarak tanımlamanız gerekir.



```markdown
# Bağımlılıkları Taklit Etme (Mocking Dependencies)

Bazen test edilen bir servisin bağımlılığını taklit etmek (mock) faydalı olabilir. Önceki örnekteki `NewsletterGenerator` servisinin, özel bir `NewsRepository` servisine işaret eden özel bir alias olan `NewsRepositoryInterface` bağımlılığına sahip olduğunu varsayalım. Gerçek repository yerine taklit (mocked) bir versiyonunu kullanmak isteyebilirsiniz:

```php
use App\Contracts\Repository\NewsRepositoryInterface;

class NewsletterGeneratorTest extends KernelTestCase
{
    public function testSomething(): void
    {
        // ... önceki örnekteki kernel başlatma kodu

        $newsRepository = $this->createMock(NewsRepositoryInterface::class);
        $newsRepository->expects(self::once())
            ->method('findNewsFromLastMonth')
            ->willReturn([
                new News('some news'),
                new News('some other news'),
            ]);

        $container->set(NewsRepositoryInterface::class, $newsRepository);

        // mock repository enjekte edilecek
        $newsletterGenerator = $container->get(NewsletterGenerator::class);

        // ...
    }
}
```

Ek bir yapılandırmaya gerek yoktur; çünkü test servis konteyneri, özel servisler ve alias'larla etkileşime izin veren özel bir konteynerdir.

---

## Test Veritabanını Yapılandırma

Veritabanı ile etkileşime giren testlerin, diğer ortamları bozmamak için kendi ayrı veritabanlarını kullanması gerekir.

`.env.test.local` dosyasını proje kök dizinine ekleyin ve test veritabanı URL’sini tanımlayın:

```env
# .env.test.local
DATABASE_URL="mysql://USERNAME:PASSWORD@127.0.0.1:3306/DB_NAME?serverVersion=8.0.37"
```

Her geliştirici/makine farklı bir test veritabanı kullanıyorsa bu dosyayı tercih edin. Ancak aynı test kurulumunu paylaşmak istiyorsanız `.env.test` dosyasını kullanabilir ve repoya gönderebilirsiniz.

Test veritabanını ve tablolarını oluşturmak için:

```bash
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:schema:create
```

Genellikle test veritabanı adının sonuna `_test` eklemek yaygındır.

Örneğin üretim veritabanı `project_acme` ise test veritabanı `project_acme_test` olabilir.

---

## Her Test Öncesi Veritabanını Sıfırlamak

Testlerin birbirinden bağımsız olması gerekir. Örneğin bir testin veritabanını değiştirmesi, diğer testlerin sonucunu etkileyebilir.

Bu durumda **DAMA Doctrine Test Bundle** kullanılır. Bu paket her test öncesi bir veritabanı transaction başlatır ve test bitince geri alır.

Kurulumu:

```bash
composer require --dev dama/doctrine-test-bundle
```

Ve PHPUnit uzantısı olarak etkinleştirin:

```xml
<!-- phpunit.dist.xml -->
<phpunit>
    <extensions>
        <!-- PHPUnit 10 veya üstü -->
        <bootstrap class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension"/>
        <!-- Daha eski PHPUnit sürümleri -->
        <extension class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension"/>
    </extensions>
</phpunit>
```

Bu şekilde her testin sonunda yapılan değişiklikler geri alınır.

---

## Test Verisi (Fixture) Yüklemek

Gerçek veriler yerine sahte veya test verisi (fixture) kullanmak genellikle daha doğrudur. Doctrine bunun için bir kütüphane sağlar:

```bash
composer require --dev doctrine/doctrine-fixtures-bundle
```

Yeni bir fixture sınıfı oluşturun:

```bash
php bin/console make:fixtures
```

Örneğin `ProductFixture` sınıfı oluşturulduktan sonra:

```php
// src/DataFixtures/ProductFixture.php
namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setName('Priceless widget');
        $product->setPrice(14.50);
        $product->setDescription('Ok, I guess it *does* have a price');
        $manager->persist($product);

        // diğer ürünler eklenebilir

        $manager->flush();
    }
}
```

Test ortamında verileri yüklemek için:

```bash
php bin/console --env=test doctrine:fixtures:load
```

---

# Uygulama Testleri (Application Tests)

Uygulama testleri, uygulamanın tüm katmanlarının entegrasyonunu kontrol eder (routing’den görünümlere kadar).

PHPUnit açısından bunlar da bir testtir, ancak özel bir akışları vardır:

1. İstek yap
2. Sayfa ile etkileşime geç (örneğin form gönder veya linke tıkla)
3. Yanıtı test et
4. Gerekirse tekrarla

---

## İlk Uygulama Testinizi Yazın

`make:test` komutunu kullanarak bir test oluşturun:

```bash
php bin/console make:test
```

Seçenekleri şu şekilde doldurun:

```
Which test type would you like?:
 > WebTestCase

The name of the test class (e.g. BlogPostTest):
 > Controller\PostControllerTest
```

Bu, aşağıdaki sınıfı oluşturur:

```php
// tests/Controller/PostControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello World');
    }
}
```

Bu test, yanıtın başarılı olduğunu ve gövdesinde `<h1>Hello World</h1>` içerdiğini doğrular.

---

## İstek Yapma

```php
$crawler = $client->request('GET', '/post/hello-world');
```

`request()` metodu HTTP yöntemi ve URL alır, bir `Crawler` döndürür.

URL’leri router üzerinden değil doğrudan yazmak önerilir — böylece URL değişiklikleri tespit edilir.

---

## Birden Fazla İstek

Bir test içinde birden fazla istek yapılabilir.

Varsayılan olarak her istekte kernel yeniden başlatılır.

Kernel’in reset edilmesini sağlamak için:

```php
$client->disableReboot();
```

Güvenlik token’ı veya Doctrine bağlantılarının sıfırlanmasını önlemek için `CompilerPass` kullanabilirsiniz:

```php
// src/Kernel.php
if ('test' === $this->environment) {
    $container->getDefinition('security.token_storage')->clearTag('kernel.reset');
    $container->getDefinition('doctrine')->clearTag('kernel.reset');
}
```

---

## Siteyi Dolaşmak

```php
$client->back();
$client->forward();
$client->reload();
$client->restart(); // tüm çerezleri ve geçmişi siler
```

---

## Yönlendirmeleri (Redirect) Takip Etme

```php
$crawler = $client->followRedirect();
$client->followRedirects(true); // tüm yönlendirmeleri otomatik takip et
```

---

## Kullanıcı Girişi (Authentication)

Gerçek giriş adımlarını (form gönderme vb.) simüle etmek yerine `loginUser()` metodu kullanılır:

```php
$userRepository = static::getContainer()->get(UserRepository::class);
$testUser = $userRepository->findOneByEmail('john.doe@example.com');

$client->loginUser($testUser);
$client->request('GET', '/profile');

$this->assertResponseIsSuccessful();
$this->assertSelectorTextContains('h1', 'Hello John!');
```

Alternatif olarak, bellek içi kullanıcı da kullanılabilir:

```php
use Symfony\Component\Security\Core\User\InMemoryUser;
$testUser = new InMemoryUser('admin', 'password', ['ROLE_ADMIN']);
$client->loginUser($testUser);
```

Stateless firewall kullanıyorsanız, her isteğe uygun token/header eklemelisiniz.

---

## AJAX İstekleri

```php
$client->xmlHttpRequest('POST', '/submit', ['name' => 'Fabien']);
```

---

## Özel HTTP Başlıkları

```php
$client = static::createClient([], [
    'HTTP_HOST'       => 'en.example.com',
    'HTTP_USER_AGENT' => 'MySuperBrowser/1.0',
]);
```

---

## Hataları (Exception) Görüntülemek

Test sırasında hataların PHPUnit tarafından doğrudan gösterilmesi için:

```php
$client->catchExceptions(false);
```

---

## İç Nesnelere Erişim

```php
$response = $client->getResponse();
$request = $client->getRequest();
$crawler = $client->getCrawler();
```

---

## Profiler Verisine Erişim

```php
$client->enableProfiler();
$crawler = $client->request('GET', '/profiler');
$profile = $client->getProfile();
```

---

## Sayfa ile Etkileşim (Formlar ve Linkler)

### Linke Tıklamak

```php
$client->clickLink('Click here');
```

### Form Göndermek

```php
$crawler = $client->submitForm('Add comment', [
    'comment_form[content]' => '...',
]);
```

Form nesnesine doğrudan erişmek isterseniz:

```php
$buttonCrawlerNode = $crawler->selectButton('submit');
$form = $buttonCrawlerNode->form();

$form['my_form[name]'] = 'Fabien';
$form['my_form[subject]'] = 'Symfony rocks!';
$client->submit($form);
```

Form alanlarını doldurmak için:

```php
$form['my_form[country]']->select('France');
$form['my_form[like_symfony]']->tick();
$form['my_form[photo]']->upload('/path/to/lucas.jpg');
```

Form adı dinamik olarak alınabilir:

```php
$formName = $form->getName();
```

Form gönderirken özel HTTP başlıkları da eklenebilir:

```php
$client->submit($form, [], ['HTTP_ACCEPT_LANGUAGE' => 'es']);
```

---

Bu yöntemlerle Symfony’de hem servis seviyesinde hem de tam uygulama düzeyinde testleri güvenli, izole ve hızlı bir şekilde gerçekleştirebilirsiniz.




```markdown
# Yanıtı Test Etme (Testing the Response – Assertions)

Artık testler bir sayfayı ziyaret edip onunla etkileşime girdiğine göre (örneğin bir formu doldurduğunda), beklenen çıktının görünüp görünmediğini doğrulama zamanı geldi.

Tüm testler PHPUnit tabanlı olduğu için, Symfony testlerinde de **herhangi bir PHPUnit Assertion** kullanılabilir.  
Ancak Symfony, en sık kullanılan doğrulamalar (assertion) için bazı **yardımcı kısayol metodları** sağlar.

---

## 🧩 Response (Yanıt) Doğrulamaları

| Metot | Açıklama |
|--------|-----------|
| **assertResponseIsSuccessful()** | Yanıtın başarılı (HTTP 2xx) olduğunu doğrular. |
| **assertResponseStatusCodeSame(int $code)** | Yanıtın belirli bir HTTP durum koduna sahip olduğunu doğrular. |
| **assertResponseRedirects(?string $location = null, ?int $code = null)** | Yanıtın yönlendirme (redirect) içerdiğini doğrular. (İsteğe bağlı olarak hedef adres ve kod da kontrol edilir.) |
| **assertResponseHasHeader(string $header)** / **assertResponseNotHasHeader()** | Belirtilen header'ın yanıt içinde olup olmadığını kontrol eder. |
| **assertResponseHeaderSame(string $header, string $value)** / **assertResponseHeaderNotSame()** | Header değerinin beklenen değere eşit olup olmadığını kontrol eder. |
| **assertResponseHasCookie(string $name)** / **assertResponseNotHasCookie()** | Belirli bir cookie’nin yanıt içinde olup olmadığını test eder. |
| **assertResponseCookieValueSame(string $name, string $value)** | Cookie’nin beklenen değeri içerip içermediğini doğrular. |
| **assertResponseFormatSame(string $format)** | Yanıt formatının (`getFormat()`) beklenen formatla eşleştiğini doğrular. |
| **assertResponseIsUnprocessable()** | Yanıtın 422 (Unprocessable Entity) durum koduna sahip olduğunu doğrular. |

> 🆕 `assertResponseIsUnprocessable` ve `verbose` parametresi Symfony **7.1** ile eklenmiştir.

---

## 🧭 Request (İstek) Doğrulamaları

| Metot | Açıklama |
|--------|-----------|
| **assertRequestAttributeValueSame(string $name, string $value)** | İstek attribute’unun beklenen değere sahip olduğunu doğrular. |
| **assertRouteSame(string $route, array $params = [])** | İstek yapılan rotanın beklenen rota olduğunu kontrol eder. |

---

## 🍪 Browser (Tarayıcı) Doğrulamaları

| Metot | Açıklama |
|--------|-----------|
| **assertBrowserHasCookie() / assertBrowserNotHasCookie()** | Test client’ında belirli bir cookie’nin mevcut olup olmadığını doğrular. |
| **assertBrowserCookieValueSame()** | Tarayıcıda bulunan cookie’nin beklenen değeri içerdiğini doğrular. |
| **assertThatForClient(Constraint $constraint)** | Özel assertion'lar için kullanılır. Kendi özel constraint’lerinizi bu şekilde tanımlayabilirsiniz. |

Örnek:
```php
protected static function assertMyOwnCustomAssert(): void
{
    self::assertThatForClient(new SomeCustomConstraint());
}
```

---

## 🌿 Crawler (Sayfa İçeriği) Doğrulamaları

| Metot                                                                                                            | Açıklama                                                                                     |
| ---------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------- |
| **assertSelectorExists() / assertSelectorNotExists()**                                                     | Verilen CSS seçiciye uygun bir elementin var olup olmadığını kontrol eder.                |
| **assertSelectorCount(int $count, string $selector)**                                                    | Sayfada belirli sayıda element olup olmadığını kontrol eder.                              |
| **assertSelectorTextContains(string $selector, string $text)**/**assertSelectorTextNotContains()** | Seçilen elementin belirli bir metni içerip içermediğini kontrol eder.                      |
| **assertSelectorTextSame()**/**assertAnySelectorTextSame()**                                         | Seçilen elementin içeriğinin tam olarak beklenen metne eşit olup olmadığını doğrular. |
| **assertPageTitleSame(string $title)**/**assertPageTitleContains()**                                 | Sayfa başlığının beklenen değeri içerip içermediğini kontrol eder.                    |
| **assertInputValueSame() / assertInputValueNotSame()**                                                     | Bir form input’unun beklenen değere sahip olup olmadığını kontrol eder.                  |
| **assertCheckboxChecked() / assertCheckboxNotChecked()**                                                   | Checkbox’ın işaretli olup olmadığını test eder.                                         |
| **assertFormValue() / assertNoFormValue()**                                                                | Form alanının beklenen değere sahip olup olmadığını test eder.                          |

---

## 📧 Mailer (E-posta) Doğrulamaları

| Metot                                                                      | Açıklama                                                                                                                                          |
| -------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------- |
| **assertEmailCount(int $count)**                                     | Gönderilen e-posta sayısını kontrol eder.                                                                                                       |
| **assertQueuedEmailCount(int $count)**                               | Kuyruğa alınan e-posta sayısını doğrular.                                                                                                     |
| **assertEmailIsQueued() / assertEmailIsNotQueued()**                 | Belirli bir e-posta olayının kuyruğa alınıp alınmadığını test eder.                                                                       |
| **assertEmailAttachmentCount(RawMessage $email, int $count)**      | E-postadaki ek sayısını doğrular.                                                                                                               |
| **assertEmailTextBodyContains() / assertEmailTextBodyNotContains()** | E-postanın metin gövdesinin belirli bir metni içerip içermediğini test eder.                                                                   |
| **assertEmailHtmlBodyContains() / assertEmailHtmlBodyNotContains()** | E-postanın HTML gövdesinde beklenen içeriğin olup olmadığını kontrol eder.                                                                  |
| **assertEmailHasHeader() / assertEmailNotHasHeader()**               | E-postada belirli bir header’ın bulunup bulunmadığını kontrol eder.                                                                           |
| **assertEmailHeaderSame() / assertEmailHeaderNotSame()**             | Header’ın beklenen değere sahip olup olmadığını test eder.                                                                                   |
| **assertEmailAddressContains()**                                     | E-posta adresini normalize ederek (ör.*Jane[jane@example.com](mailto:jane@example.com)*→ *[jane@example.com](mailto:jane@example.com)* ) doğrular. |
| **assertEmailSubjectContains() / assertEmailSubjectNotContains()**   | E-posta konusunun beklenen metni içerip içermediğini doğrular.                                                                                  |

---

## 🔔 Notifier (Bildirim) Doğrulamaları

| Metot                                                                                    | Açıklama                                                                                     |
| ---------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------- |
| **assertNotificationCount(int $count)**                                            | Gönderilen bildirim sayısını doğrular.                                                    |
| **assertQueuedNotificationCount(int $count)**                                      | Kuyruğa alınan bildirim sayısını kontrol eder.                                            |
| **assertNotificationIsQueued() / assertNotificationIsNotQueued()**                 | Bildirimin kuyruğa alınıp alınmadığını doğrular.                                      |
| **assertNotificationSubjectContains() / assertNotificationSubjectNotContains()**   | Bildirim başlığının belirli bir metni içerip içermediğini kontrol eder.                |
| **assertNotificationTransportIsEqual() / assertNotificationTransportIsNotEqual()** | Bildirim için kullanılan transport’un beklenen isimle eşleşip eşleşmediğini test eder. |

---

## 🌐 HttpClient Doğrulamaları

> ⚠️ Aşağıdaki doğrulamaların çalışması için önce `$client->enableProfiler()` çağrılmalıdır.

| Metot                                                                       | Açıklama                                                                                                             |
| --------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------- |
| **assertHttpClientRequest(string $url, string $method = 'GET')**    | Belirtilen URL’nin belirtilen yöntemle (ve isteğe bağlı olarak body/header’larla) çağrıldığını doğrular. |
| **assertNotHttpClientRequest(string $url, string $method = 'GET')** | Belirtilen URL’nin belirtilen yöntemle çağrılmadığını test eder.                                              |
| **assertHttpClientRequestCount(int $count)**                          | Belirtilen sayıda HTTP isteği yapıldığını doğrular.                                                            |

---

## 🧪 Uçtan Uca Testler (End to End – E2E)

Uygulamayı JavaScript dahil tüm katmanlarıyla test etmek istiyorsanız, test client yerine **gerçek bir tarayıcı** kullanabilirsiniz.

Bu tür testlere **end-to-end testler (E2E)** denir.

Symfony, bunu gerçekleştirmek için **Panther** bileşenini sağlar.

Daha fazla bilgi için Symfony’nin Panther dökümantasyonuna bakabilirsiniz.
