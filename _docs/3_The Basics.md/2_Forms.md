### **Formlar (Forms)**

HTML formlarını oluşturmak ve işlemek zordur ve tekrarlayan işlemler içerir.

Form alanlarını render etmek, gönderilen verileri doğrulamak, form verilerini nesnelere aktarmak gibi birçok detayla uğraşmanız gerekir.

Symfony, tüm bu işlemleri kolaylaştıran güçlü bir **Form** bileşeni sağlar.

---

### 🧩 **Kurulum (Installation)**

Symfony Flex kullanan projelerde, form özelliğini etkinleştirmek için şu komutu çalıştırın:

```bash
composer require symfony/form
```

---

### ⚙️ **Kullanım (Usage)**

Symfony form’larıyla çalışırken önerilen iş akışı şu şekildedir:

1. **Formu oluşturun** (Controller içinde veya özel bir Form sınıfında)
2. **Formu şablonda render edin** (kullanıcı düzenleyip gönderebilsin)
3. **Formu işleyin** (verileri doğrulayın, PHP nesnelerine dönüştürün ve gerekirse veritabanına kaydedin)

---

### 🗂 **Örnek Uygulama: Task (Görev) Nesnesi**

Kullanıcıların görev oluşturup düzenleyebildiği bir “Yapılacaklar Listesi” uygulaması düşünelim.

Her görev aşağıdaki `Task` sınıfı ile temsil edilir:

```php
// src/Entity/Task.php
namespace App\Entity;

class Task
{
    protected string $task;
    protected ?\DateTimeInterface $dueDate;

    public function getTask(): string
    {
        return $this->task;
    }

    public function setTask(string $task): void
    {
        $this->task = $task;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeInterface $dueDate): void
    {
        $this->dueDate = $dueDate;
    }
}
```

Bu sınıf Symfony’ye özel değildir — tamamen bağımsız bir  **PHP nesnesidir (POPO)** .

Ancak Doctrine Entity’leri de aynı şekilde form aracılığıyla düzenlenebilir.

---

### 🧱 **Form Tipleri (Form Types)**

Symfony’de tüm form elemanları “form tipi” olarak adlandırılır.

Yani:

* Tek bir `<input type="text">` → `TextType`
* Birden fazla alan içeren bir adres grubu → `PostalAddressType`
* Tüm kullanıcı profili formu → `UserProfileType`

Symfony’de form ve alanlar arasında ayrım yoktur — hepsi **Form Type** olarak geçer.

Bu yapı, formların iç içe geçmesini ve yeniden kullanılabilir olmasını kolaylaştırır.

Tüm kullanılabilir form tiplerini listelemek için:

```bash
php bin/console debug:form
```

Belirli bir form tipi hakkında bilgi almak için:

```bash
php bin/console debug:form BirthdayType
php bin/console debug:form BirthdayType label_attr
```

---

### 🧩 **Form Oluşturma (Building Forms)**

Symfony, “form builder” adında bir nesne sağlar.

Bu nesne sayesinde alanları akıcı (fluent) bir şekilde tanımlarsınız.

#### 📄 **Controller içinde form oluşturma**

Eğer controller sınıfınız `AbstractController`’dan türemişse:

```php
// src/Controller/TaskController.php
namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends AbstractController
{
    public function new(Request $request): Response
    {
        $task = new Task();
        $task->setTask('Write a blog post');
        $task->setDueDate(new \DateTimeImmutable('tomorrow'));

        $form = $this->createFormBuilder($task)
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();

        // ...
    }
}
```

Eğer controller `AbstractController`’dan türememişse, `form.factory` servisini manuel olarak çağırmanız gerekir.

---

### 🧾 **Form Sınıfı Oluşturma (Creating Form Classes)**

Controller içinde fazla kod bulundurmak yerine, formu özel bir sınıfa taşımak  **en iyi uygulamadır** .

Bu sayede form tekrar kullanılabilir hale gelir.

Form sınıfları `FormTypeInterface`’i uygular. Ancak genellikle `AbstractType` sınıfından genişletilir:

```php
// src/Form/Type/TaskType.php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('save', SubmitType::class);
    }
}
```

Form sınıfını otomatik oluşturmak için şu komutu kullanabilirsiniz:

```bash
php bin/console make:form
```

---

### 🧩 **Controller’da Formu Kullanma**

```php
// src/Controller/TaskController.php
namespace App\Controller;

use App\Entity\Task;
use App\Form\Type\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends AbstractController
{
    public function new(): Response
    {
        $task = new Task();
        $task->setTask('Write a blog post');
        $task->setDueDate(new \DateTimeImmutable('tomorrow'));

        $form = $this->createForm(TaskType::class, $task);

        // ...
    }
}
```

---

### ⚙️ **Veri Sınıfını Belirtmek (data_class)**

Form, hangi sınıfla ilişkilendirileceğini bilmelidir.

Bu genellikle `createForm()`’a verdiğiniz nesneden otomatik olarak çıkarılır.

Ancak iç içe formlar oluşturduğunuzda bu bilgi açıkça belirtilmelidir.

```php
// src/Form/Type/TaskType.php
namespace App\Form\Type;

use App\Entity\Task;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
```

---

✅ **Özetle:**

* `composer require symfony/form` → kurulumu yapar
* `createForm()` veya `createFormBuilder()` → form nesnesini oluşturur
* `AbstractType` → form sınıflarının temelidir
* `data_class` → formun hangi entity ile eşleneceğini belirtir

---

### 🧱 **Formların Görüntülenmesi (Rendering Forms)**

Form oluşturulduktan sonra bir sonraki adım onu **görselleştirmek (render etmek)** olacaktır.

---

#### 📄 **Controller’da Formu Şablona Göndermek**

```php
// src/Controller/TaskController.php
namespace App\Controller;

use App\Entity\Task;
use App\Form\Type\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends AbstractController
{
    public function new(Request $request): Response
    {
        $task = new Task();
        // ...

        $form = $this->createForm(TaskType::class, $task);

        return $this->render('task/new.html.twig', [
            'form' => $form,
        ]);
    }
}
```

`render()` metodu aslında içerde `$form->createView()` metodunu çağırır ve formu **FormView nesnesine** dönüştürür.

---

#### 🧩 **Twig Şablonunda Formu Render Etme**

```twig
{# templates/task/new.html.twig #}
{{ form(form) }}
```

Bu kadar basit!

`form()` fonksiyonu:

* `<form>` etiketinin **başlangıcını ve bitişini** oluşturur,
* **tüm form alanlarını** otomatik olarak render eder.

Varsayılan olarak formun `method="POST"` ve hedef URL’si de aynı sayfadır (ancak değiştirilebilir).

---

#### 🧠 **Form Değerlerinin Otomatik Doldurulması**

Form render edildiğinde, `Task` nesnesindeki veriler otomatik olarak alanlara yansıtılır.

Örneğin `$task->setTask('Write a blog post');` tanımlaması yapılmışsa, formdaki `task` input alanı bu değeri gösterecektir.

Symfony form sistemi, korumalı (`protected`) özelliklere `getTask()` ve `setTask()` metodları aracılığıyla erişir.

Eğer bir özellik **boolean** ise, `isPublished()` veya `hasReminder()` gibi “isser/hasser” metotları da kullanılabilir.

---

### 🎨 **Form Görünümünü Özelleştirme**

Varsayılan render yöntemi hızlıdır ama esnek değildir.

Genellikle Bootstrap gibi CSS framework’leriyle entegre çalışmak istersiniz.

Örneğin, Bootstrap 5 uyumlu bir görünüm elde etmek için Twig yapılandırmasına şu satırı ekleyebilirsiniz:

```php
// config/packages/twig.php
use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig): void {
    $twig->formThemes(['bootstrap_5_layout.html.twig']);
};
```

Symfony’nin hazır form temaları:

* Bootstrap 3, 4 ve 5
* Foundation 5 ve 6
* Tailwind 2

Ayrıca **kendi özel form temanızı** da oluşturabilirsiniz.

Form alanlarını parça parça (örneğin yalnızca label, error veya help metinleri) render etmek için Symfony, birçok yardımcı Twig fonksiyonu sunar:

* `form_start(form)`
* `form_end(form)`
* `form_row(form.field)`
* `form_widget(form.field)`
* `form_label(form.field)`
* `form_errors(form.field)`

---

## ⚙️ **Formların İşlenmesi (Processing Forms)**

Formları işlemek için en iyi yöntem, **aynı action’da** hem formu göstermek hem de gönderimini ele almaktır.

Bu yaklaşım kodu sadeleştirir ve bakımı kolaylaştırır.

---

#### 📄 **Form İşleme Örneği**

```php
// src/Controller/TaskController.php
namespace App\Controller;

use App\Entity\Task;
use App\Form\Type\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends AbstractController
{
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            // ... örneğin veritabanına kaydedilebilir

            return $this->redirectToRoute('task_success');
        }

        return $this->render('task/new.html.twig', [
            'form' => $form,
        ]);
    }
}
```

---

#### 🔄 **Bu Controller’ın 3 Akışı:**

1. **Sayfa ilk kez yüklendiğinde:**

   Form henüz gönderilmemiştir (`isSubmitted()` = false).

   → Form sadece oluşturulup gösterilir.
2. **Kullanıcı formu gönderdiğinde (veri hatalıysa):**

   `handleRequest()` metodu formu doldurur ve verileri `$task` nesnesine yazar.

   Ardından Symfony **doğrulama (validation)** yapar.

   Geçersizse `isValid()` false döner ve form hata mesajlarıyla tekrar render edilir.

   > Not: `$form` doğrudan `render()` metoduna gönderilirse, Symfony yanıt kodunu otomatik olarak **422 Unprocessable Content** olarak ayarlar.
   >
   > Bu, HTTP standardına uyum ve Symfony UX Turbo gibi araçlarla uyumluluk sağlar.
   >
3. **Kullanıcı geçerli verilerle gönderdiğinde:**

   `isValid()` true döner.

   Artık `$task` nesnesini kullanarak (örneğin veritabanına kaydedip) kullanıcıyı yönlendirebilirsiniz.
   🚫 Yeniden gönderim hatalarını önlemek için, başarılı gönderimden sonra **redirect** kullanmak en iyi uygulamadır.

---

### 🧰 **Formu Manuel Olarak Göndermek (submit())**

Bazı durumlarda formun ne zaman gönderileceğini veya hangi verilerin kullanılacağını daha hassas kontrol etmek isterseniz,

`$form->submit($data)` metodunu doğrudan kullanabilirsiniz.

---

## ✅ **Form Doğrulama (Validating Forms)**

Symfony’de formun “geçerli” olup olmaması, aslında altındaki nesnenin (örneğin `$task`) geçerli olup olmamasına bağlıdır.

`$form->isValid()` çağrısı, aslında `$task` nesnesine sorar: “veriler geçerli mi?”.

---

#### 🧩 **Kurulum**

```bash
composer require symfony/validator
```

---

#### 📄 **Doğrulama Kurallarını (Constraints) Eklemek**

Doğrulama kuralları, sınıfa eklenen **constraint’ler** ile tanımlanır.

İlk yaklaşım: Kuralları doğrudan Entity sınıfına eklemek.

```php
// src/Entity/Task.php
namespace App\Entity;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Task
{
    // ...

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('task', new NotBlank());
        $metadata->addPropertyConstraint('dueDate', new NotBlank());
        $metadata->addPropertyConstraint(
            'dueDate',
            new Type(\DateTimeInterface::class)
        );
    }
}
```

Bu kadar!

Artık formu geçersiz verilerle gönderdiğinizde, form hata mesajlarını otomatik olarak gösterecektir.

---

#### 🔄 **Alternatif Yöntem: Constraint’leri Form Üzerinde Tanımlamak**

Doğrulama kurallarını doğrudan **Form Type** içinde de tanımlayabilirsiniz.

Her iki yöntem de birlikte kullanılabilir.

---

### 💡 **Özet**

| Adım                           | Açıklama                                           |
| ------------------------------- | ---------------------------------------------------- |
| `render()`                    | Formu Twig şablonuna aktarır                       |
| `{{ form(form) }}`            | Tüm formu tek satırda render eder                  |
| `handleRequest()`             | Form gönderimini dinler ve veriyi işler            |
| `isSubmitted()`/`isValid()` | Gönderim ve doğrulama kontrolü yapar              |
| `redirectToRoute()`           | Başarılı gönderimden sonra yeniden yönlendirir  |
| `Validation`                  | Form verilerinin kurallara uygunluğunu kontrol eder |

---




### ⚙️ **Diğer Yaygın Form Özellikleri (Other Common Form Features)**

Symfony formları oldukça güçlüdür ve birçok gelişmiş özelliği destekler.

Bu bölümde, formlarla çalışırken sıkça kullanılan özellikleri ayrıntılı şekilde inceleyeceğiz.

---

## 🧩 **Formlara Özel Seçenekler (Passing Options to Forms)**

Formları sınıf olarak oluşturduğunuzda, `createForm()` metoduna üçüncü parametre olarak özel seçenekler (options) geçebilirsiniz:

```php
// src/Controller/TaskController.php
namespace App\Controller;

use App\Entity\Task;
use App\Form\Type\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends AbstractController
{
    public function new(): Response
    {
        $task = new Task();
        $dueDateIsRequired = true; // Örnek karar

        $form = $this->createForm(TaskType::class, $task, [
            'require_due_date' => $dueDateIsRequired,
        ]);

        // ...
    }
}
```

Eğer bu formu hemen kullanırsanız şu hatayı görürsünüz:

> `The option "require_due_date" does not exist.`

Çünkü form sınıfı, kabul ettiği tüm seçenekleri `configureOptions()` içinde tanımlamalıdır:

```php
// src/Form/Type/TaskType.php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'require_due_date' => false,
        ]);

        $resolver->setAllowedTypes('require_due_date', 'bool');
    }
}
```

Artık bu seçeneği `buildForm()` içinde kullanabilirsiniz:

```php
// src/Form/Type/TaskType.php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dueDate', DateType::class, [
                'required' => $options['require_due_date'],
            ]);
    }
}
```

---

## 🧱 **Form Alanı Seçenekleri (Form Type Options)**

Symfony’de her form alanı birçok seçeneği destekler. En yaygın olanları:

### 🔹 **required (zorunlu alan)**

Varsayılan değeri `true`’dur.

Tarayıcı, bu alan doldurulmadan formun gönderilmesine izin vermez.

```php
->add('dueDate', DateType::class, [
    'required' => false,
])
```

> ⚠️ `required` yalnızca **istemci tarafında (HTML5)** doğrulama yapar.
>
> Sunucu tarafında da kontrol istiyorsanız `@NotBlank` veya `@NotNull` constraint’lerini ekleyin.

---

### 🔹 **label (etiket)**

Alan etiketini manuel olarak belirlemek için kullanılır:

```php
->add('dueDate', DateType::class, [
    'label' => 'Tamamlanma Tarihi',
])
```

Eğer etiketi hiç göstermek istemiyorsanız:

```php
'label' => false
```

Zorunlu alanların `<label>` etiketleri, `required` CSS sınıfı ile işaretlenir.

Bu sayede CSS üzerinden yıldız eklenebilir:

```css
label.required:before {
    content: "*";
}
```

---

## 🌐 **Formun Action ve Method Özelliklerini Değiştirme**

Varsayılan olarak form `<form method="post">` şeklinde oluşturulur ve **aynı URL’ye** gönderilir.

Bunu değiştirmek için `setAction()` ve `setMethod()` kullanılabilir:

```php
$form = $this->createFormBuilder($task)
    ->setAction($this->generateUrl('target_route'))
    ->setMethod('GET')
    ->getForm();
```

Eğer formu bir sınıfta oluşturuyorsanız:

```php
$form = $this->createForm(TaskType::class, $task, [
    'action' => $this->generateUrl('target_route'),
    'method' => 'GET',
]);
```

Twig şablonunda da değiştirebilirsiniz:

```twig
{{ form_start(form, {'action': path('target_route'), 'method': 'GET'}) }}
```

> 🔸 Eğer method `PUT`, `PATCH` veya `DELETE` ise, Symfony formun içine gizli bir `_method` alanı ekler.
>
> Tarayıcı formu POST olarak gönderir ama Symfony bu gizli alan sayesinde isteği doğru HTTP metoduna çevirir.
>
> Bunun çalışması için `http_method_override` özelliği etkin olmalıdır.

---

## 🧾 **Formun İsmini Değiştirme (Changing the Form Name)**

Form ismi ve alan adları varsayılan olarak sınıf adına göre oluşturulur:

örneğin `<form name="task">`.

Eğer bunu değiştirmek isterseniz:

```php
use Symfony\Component\Form\FormFactoryInterface;

$form = $formFactory->createNamed('my_name', TaskType::class, $task);
```

Form adını tamamen kaldırmak için boş string verebilirsiniz:

```php
$form = $formFactory->createNamed('', TaskType::class, $task);
```

---

## ✅ **İstemci Tarafı HTML Doğrulaması (Client-Side Validation)**

HTML5 ile birçok tarayıcı bazı doğrulamaları doğal olarak destekler.

Symfony bu özellikten yararlanır ve gerekli `required` gibi HTML niteliklerini otomatik ekler.

Eğer test amacıyla tarayıcı doğrulamasını devre dışı bırakmak isterseniz:

```twig
{{ form_start(form, {'attr': {'novalidate': 'novalidate'}}) }}
    {{ form_widget(form) }}
{{ form_end(form) }}
```

---

## 🔍 **Form Tipi Tahmini (Form Type Guessing)**

Eğer Entity sınıfınızda doğrulama kuralları (constraints) varsa, Symfony bu kurallardan form alan türünü tahmin edebilir.

```php
$builder
    ->add('task') // Tipi otomatik olarak TextType olarak algılar
    ->add('dueDate', null, ['required' => false])
    ->add('save', SubmitType::class);
```

> Örneğin `dueDate` alanına `Type(\DateTimeInterface::class)` kısıtlaması eklenmişse, Symfony bu alanın bir `DateType` olduğunu tahmin eder.

Ayrıca şu seçenekleri de otomatik tahmin eder:

* **required** → Doğrulama kurallarına veya Doctrine metadata’sına göre
* **maxlength** → `Length` constraint veya veritabanı kolon uzunluğuna göre

Bu tahminleri ezmek isterseniz:

```php
->add('task', null, ['attr' => ['maxlength' => 4]])
```

---

## 🧮 **Nesneyle Eşleşmeyen Alanlar (Unmapped Fields)**

Formdaki tüm alanlar varsayılan olarak Entity’deki özelliklerle eşleştirilir.

Eğer Entity’de olmayan bir alan eklemeye çalışırsanız hata alırsınız.

Ancak “anlaşma şartlarını kabul ediyorum” gibi fazladan alanlar eklemek isterseniz, `mapped` seçeneğini **false** yapabilirsiniz:

```php
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

$builder
    ->add('task')
    ->add('dueDate')
    ->add('agreeTerms', CheckboxType::class, [
        'mapped' => false,
    ])
    ->add('save', SubmitType::class);
```

Bu alanın değerini controller’da şöyle alabilirsiniz:

```php
$form->get('agreeTerms')->getData();
```

veya manuel olarak ayarlayabilirsiniz:

```php
$form->get('agreeTerms')->setData(true);
```

> Form gönderiminde eksik alanlar varsa, Symfony bu alanlara otomatik olarak `null` değeri atar.

---

## 💡 **Genel Bakış (Summary)**

| Özellik                        | Açıklama                                                             |
| ------------------------------- | ---------------------------------------------------------------------- |
| `configureOptions()`          | Formun kabul ettiği özel seçenekleri tanımlar                      |
| `required`                    | Alanın zorunlu olup olmadığını belirler                           |
| `label`                       | Alan etiketini özelleştirir                                          |
| `setAction()`/`setMethod()` | Formun gönderim hedefini ve HTTP metodunu belirler                    |
| `createNamed()`               | Form ismini özelleştirir                                             |
| `novalidate`                  | Tarayıcı doğrulamasını devre dışı bırakır                    |
| `mapped => false`             | Nesneyle eşleşmeyen ek alanlar oluşturur                            |
| Otomatik Tip Tahmini            | Doğrulama veya Doctrine metadata’sına göre form türünü belirler |

---
