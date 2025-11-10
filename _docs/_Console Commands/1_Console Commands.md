
---

## 🧩 Komutları Çalıştırmak

Her Symfony uygulaması birçok hazır komutla gelir.

Tüm mevcut komutları görmek için aşağıdaki komutu çalıştırabilirsin:

```bash
php bin/console list
```

**Çıktı örneği:**

```
Available commands:
  about             Display information about the current project
  completion        Dump the shell completion script
  help              Display help for a command
  list              List commands
 assets
  assets:install    Install bundle's web assets under a public directory
 cache
  cache:clear       Clear the cache
...
```

> `list` varsayılan komuttur, yani sadece `php bin/console` yazmak aynı sonucu verir.

İstediğin bir komutun yardım bilgilerini görmek için `--help` seçeneğini kullanabilirsin:

```bash
php bin/console assets:install --help
```

`--help`, Console bileşenine ait **global seçeneklerden biridir** ve tüm komutlarda geçerlidir.

Bu seçenekler hakkında daha fazla bilgi almak için ilgili bölümü inceleyebilirsin.

---

## ⚙️ APP_ENV & APP_DEBUG

Konsol komutları, `.env` dosyasındaki **APP_ENV** değişkeninde belirtilen ortamda (varsayılan: `dev`) çalışır.

Ayrıca, **APP_DEBUG** değişkenini de okur (varsayılan olarak `1`, yani "debug modu açık").

Komutu farklı bir ortamda veya debug modunda çalıştırmak istersen:

```bash
APP_ENV=prod php bin/console cache:clear
```

---

## 💡 Otomatik Tamamlama (Console Completion)

Eğer Bash, Zsh veya Fish kabuklarını kullanıyorsan, Symfony’nin **komut tamamlama betiğini (completion script)** yükleyerek terminalde komut isimleri, seçenekler ve bazı değerler için **otomatik tamamlama** kullanabilirsin.

Kurulum talimatlarını görmek için şu komutu çalıştır:

```bash
php bin/console completion --help
```

> Bash kullanıyorsan, sisteminde `bash-completion` paketinin yüklü olduğundan emin olmalısın.

Kurulumdan ve terminali yeniden başlattıktan sonra, artık `Tab` tuşuyla otomatik tamamlama özelliğini kullanabilirsin.

Symfony Console bileşenini kullanan diğer PHP araçları (ör.  **Composer** ,  **PHPStan** ,  **Behat** ) da 5.4 veya üzeri sürümlerindeyse kendi completion betiklerini destekler:

```bash
php vendor/bin/phpstan completion --help
composer completion --help
```

---

## 🧱 Komut Oluşturma

Komutlar sınıflar olarak tanımlanır ve `#[AsCommand]` özelliği (attribute) kullanılarak  **otomatik olarak kaydedilir** .

Örneğin, bir kullanıcı oluşturma komutu şöyle tanımlanabilir:

```php
// src/Command/CreateUserCommand.php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'app:create-user')]
class CreateUserCommand
{
    public function __invoke(): int
    {
        // Kullanıcı oluşturma işlemleri buraya gelecek

        // Başarılı sonuç (int 0)
        return Command::SUCCESS;

        // Hata durumu (int 1)
        // return Command::FAILURE;

        // Yanlış kullanım (int 2)
        // return Command::INVALID;
    }
}
```

Eğer PHP attribute kullanamıyorsan, komutu bir servis olarak kaydedip `console.command` etiketiyle işaretlemen yeterlidir.

Varsayılan `services.yaml` yapılandırmasında bu işlem zaten **autoconfiguration** sayesinde otomatik yapılır.

---

## 📝 Açıklama ve Yardım Metni Ekleme

`#[AsCommand]` özelliği, komut açıklaması ve yardım metni tanımlamayı da destekler:

```php
#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user.',
    help: 'This command allows you to create a user...'
)]
class CreateUserCommand
{
    public function __invoke(): int
    {
        // ...
    }
}
```

---

## 🔁 Gelişmiş Özellikler (Lifecycle Hooks)

Daha gelişmiş işlemler için `Command` sınıfını **genişleterek** yaşam döngüsü metotlarını kullanabilirsin:

```php
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-user')]
class CreateUserCommand extends Command
{
    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        // Komut çalışmadan önce
    }

    public function interact(InputInterface $input, OutputInterface $output): void
    {
        // Kullanıcıyla etkileşim (örneğin input sormak)
    }

    public function __invoke(): int
    {
        // Ana işlem
        return Command::SUCCESS;
    }
}
```

---

✅ **Özetle:**

Symfony’nin Console bileşeni, hem yerleşik komutları yönetmek hem de **kendi CLI araçlarını** kolayca oluşturmak için güçlü bir altyapı sunar.

Bu sistem sayesinde uygulama yönetimini, veri işlemlerini ve bakım görevlerini otomatikleştirebilirsin.


## 🏃‍♂️ Komutu Çalıştırmak

Komutu yapılandırıp kaydettikten sonra terminalde aşağıdaki şekilde çalıştırabilirsin:

```bash
php bin/console app:create-user
```

Bu komut, henüz bir işlem mantığı (logic) yazmadığın için hiçbir şey yapmaz.

İşlem mantığını `__invoke()` metodunun içine eklemelisin.

---

## 💬 Konsol Çıktısı (Console Output)

`__invoke()` metodu, konsola mesaj yazdırmak için bir **output akışına (output stream)** erişebilir:

```php
// ...
public function __invoke(OutputInterface $output): int
{
    // Birden fazla satırı yazdırır (her satırın sonuna "\n" ekler)
    $output->writeln([
        'User Creator',
        '============',
        '',
    ]);

    // someMethod() metodu bir iterator döndürebilir (ör. yield ile)
    $output->writeln($this->someMethod());

    // Satır sonuna "\n" ekleyerek çıktı verir
    $output->writeln('Whoa!');

    // Satır sonuna "\n" eklemeden çıktı verir
    $output->write('You are about to ');
    $output->write('create a user.');

    return Command::SUCCESS;
}
```

**Çalıştırma çıktısı:**

```bash
php bin/console app:create-user
User Creator
============

Whoa!
You are about to create a user.
```

---

## 🧱 Çıktı Bölümleri (Output Sections)

Konsol çıktısı, birbirinden bağımsız alanlara (bölümlere) ayrılabilir.

Bu alanlara **“output section”** denir ve **ConsoleOutput::section()** metodu ile oluşturulur.

Bölümler, çıktı içeriğini temizleyip yeniden yazmak (ör. canlı ilerleme çubukları, dinamik tablolar) için kullanılır.

```php
use Symfony\Component\Console\Output\ConsoleOutputInterface;

#[AsCommand(name: 'app:my-command')]
class MyCommand
{
    public function __invoke(OutputInterface $output): int
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new \LogicException('This command accepts only an instance of "ConsoleOutputInterface".');
        }

        $section1 = $output->section();
        $section2 = $output->section();

        $section1->writeln('Hello');
        $section2->writeln('World!');
        sleep(1);
        // Çıktı: "Hello\nWorld!\n"

        // overwrite() — mevcut içeriği tamamen değiştirir
        $section1->overwrite('Goodbye');
        sleep(1);
        // Çıktı: "Goodbye\nWorld!\n"

        // clear() — tüm içeriği siler
        $section2->clear();
        sleep(1);
        // Çıktı: "Goodbye\n"

        // clear(2) — son 2 satırı siler
        $section1->clear(2);
        sleep(1);
        // Çıktı artık tamamen boş!

        // setMaxHeight(2) — sadece 2 satır gösterir, yeniler eskilerin yerini alır
        $section1->setMaxHeight(2);
        $section1->writeln('Line1');
        $section1->writeln('Line2');
        $section1->writeln('Line3');

        return Command::SUCCESS;
    }
}
```

> 🔎 Her bilgi yazdırıldığında yeni satır otomatik olarak eklenir.
>
> Bu özellik,  **ilerleme çubukları** , **dinamik tablolar** gibi gelişmiş terminal çıktıları oluşturmak için oldukça faydalıdır.

---

## 🎛️ Konsol Girdisi (Console Input)

Komutlara bilgi geçirmek için **argümanlar** veya **seçenekler (options)** kullanılır:

```php
use Symfony\Component\Console\Attribute\Argument;

// #[Argument] attribute, $username parametresini zorunlu bir argüman olarak tanımlar
public function __invoke(#[Argument('The username of the user.')] string $username, OutputInterface $output): int
{
    $output->writeln([
        'User Creator',
        '============',
        '',
    ]);

    $output->writeln('Username: '.$username);

    return Command::SUCCESS;
}
```

**Çalıştırma örneği:**

```bash
php bin/console app:create-user Wouter
```

**Çıktı:**

```
User Creator
============

Username: Wouter
```

> 📘 Daha fazla bilgi için: *“Read Console Input (Arguments & Options)”* bölümüne bakabilirsin.

---

## 🧩 Servis Container’dan Servis Erişimi

Gerçek bir kullanıcı oluşturmak için komutun bazı servislere erişmesi gerekir.

Komutlar zaten **servis olarak kaydedildiği** için **dependency injection** (bağımlılık enjeksiyonu) normal şekilde çalışır.

Örneğin, bir `App\Service\UserManager` servisini kullanmak istiyorsan:

```php
use App\Service\UserManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-user')]
class CreateUserCommand
{
    public function __construct(
        private UserManager $userManager
    ) {
    }

    public function __invoke(#[Argument] string $username, OutputInterface $output): int
    {
        // Kullanıcı oluşturma işlemi
        $this->userManager->create($username);

        $output->writeln('User successfully generated!');

        return Command::SUCCESS;
    }
}
```

---

✅ **Özet:**

* Komutlar terminalden `php bin/console` ile çalıştırılır.
* `OutputInterface` ile mesaj yazdırabilir, `ConsoleOutput::section()` ile dinamik çıktı alanları yönetebilirsin.
* `#[Argument]` ile argüman tanımlayabilir, `#[Option]` ile seçenekler ekleyebilirsin.
* Servis container aracılığıyla uygulama servislerine doğrudan erişebilirsin.



## ⚙️ Komut Yaşam Döngüsü (Command Lifecycle)

Bir Symfony komutu çalıştırıldığında üç ana yaşam döngüsü metodu çağrılır:

---

### 🧩 **initialize()** (isteğe bağlı)

Bu metod, `interact()` ve `execute()` (veya `__invoke()`) metotlarından **önce** çalıştırılır.

Amacı, komutun geri kalanında kullanılacak değişkenleri veya yapılandırmaları  **başlatmaktır** .

---

### 💬 **interact()** (isteğe bağlı)

Bu metod, `initialize()` metodundan **sonra** ve `execute()` metodundan **önce** çalıştırılır.

Amacı, eksik olan **argümanları veya seçenekleri (options)** kullanıcıdan  **etkileşimli olarak sormaktır** .

Bu, kullanıcıdan girdi almak için son fırsattır.

> 🛑 Not: `--no-interaction` seçeneği kullanılırsa bu metod çağrılmaz.

---

### 🚀 **__invoke()** (veya execute()) (zorunlu)

Bu metod, `interact()` ve `initialize()` metotlarından sonra çalışır.

Komutun asıl mantığını içerir ve bir **integer** döndürmelidir.

Bu sayı, **komutun çıkış durumunu (exit status)** belirtir.

---

## 🧪 Komutların Test Edilmesi (Testing Commands)

Symfony, komutlarını test etmeni kolaylaştıran araçlar sunar.

En yaygın kullanılan sınıf  **`CommandTester`** ’dır.

Bu sınıf, gerçek bir konsol gerekmeden özel giriş/çıkış sınıflarıyla test yapmanı sağlar.

```php
// tests/Command/CreateUserCommandTest.php
namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:create-user');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            // Argümanları ilet
            'username' => 'Wouter',

            // Seçenekleri (options) "--" ile başlat
            // Örnek: '--some-option' => 'value',
            // Dizi değerleri test etmek için: '--some-option' => ['value'],
        ]);

        $commandTester->assertCommandIsSuccessful();

        // Komut çıktısını al
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Username: Wouter', $output);
    }
}
```

> 🎯 **Tek komutluk (single-command) uygulamalarda** test sonucu almak için `setAutoExit(false)` çağırmalısın.

---

### 🧩 ApplicationTester Kullanımı

Birden fazla komut içeren tam bir uygulamayı test etmek istiyorsan **`ApplicationTester`** kullanabilirsin.

```php
$application = new Application();
$application->setAutoExit(false);

$tester = new ApplicationTester($application);
```

> ⚠️ Komutları `CommandTester` ile test ederken **konsol olayları (console events)** tetiklenmez.
>
> Olayları test etmek istiyorsan **`ApplicationTester`** kullanmalısın.

---

### 🧷 VALUE_NONE Seçeneklerini Test Etmek

`InputOption::VALUE_NONE` türündeki seçenekleri test ederken değeri `true` olarak geçmelisin:

```php
$commandTester = new CommandTester($command);
$commandTester->execute(['--some-option' => true]);
```

---

### 🧰 Bağımsız Projelerde Test Etmek

Console bileşenini bağımsız bir projede (Symfony Framework olmadan) kullanıyorsan:

`Application` sınıfını kullan ve normal `\PHPUnit\Framework\TestCase`’den kalıtım al.

---

## 🖥️ Terminal Bilgileri (Terminal Class)

Komutlarını farklı terminal ayarlarında test etmek istiyorsan `Terminal` sınıfını kullanabilirsin:

```php
use Symfony\Component\Console\Terminal;

$terminal = new Terminal();

// Terminaldeki satır sayısı
$height = $terminal->getHeight();

// Terminaldeki sütun sayısı
$width = $terminal->getWidth();

// Renk modu (ör. 8-bit, 24-bit)
$colorMode = $terminal->getColorMode();

// Renk modunu değiştirme
$terminal->setColorMode(AnsiColorMode::Ansi24);
```

Bu bilgiler, komutunun **farklı ekran genişliklerinde ve renk ayarlarında nasıl davrandığını** test etmede faydalıdır.

---

## 🪵 Komut Hatalarını Günlükleme (Logging Command Errors)

Komut çalışırken bir **istisna (exception)** fırlatılırsa, Symfony bu olayı  **log dosyasına yazar** .

Log mesajı, hatanın oluştuğu komutu ve hata detaylarını içerir.

Ayrıca, Symfony **ConsoleEvents::TERMINATE** olayını dinleyerek **0 dışındaki çıkış durumlarını (exit code)** da kaydeder.

---

## ⚡ Olaylar ve Sinyallerin Yönetimi (Using Events and Signals)

Bir komut çalışırken, Symfony çeşitli **konsol olaylarını (events)** tetikler.

Bu olaylardan biri de **sinyallere (signals)** tepki vermeni sağlar (ör. `SIGTERM`, `SIGINT`).

Bu sayede uzun süren işlemleri güvenli şekilde durdurabilir veya temizleme işlemleri yapabilirsin.

---

## 🧭 Komutların Profillenmesi (Profiling Commands)

Symfony, tüm komutların çalışmasını profillemeni sağlar.

Bunun için:

1. **Debug modu** ve **profiler** etkin olmalıdır.
2. Komutu `--profile` seçeneğiyle çalıştır:

```bash
php bin/console --profile app:my-command
```

Symfony, bu komutun çalışmasıyla ilgili verileri toplar.

Bu veriler, **web profiler** üzerinden incelenebilir.

> 🔗 Eğer terminalin destekliyorsa `-v` (verbose) modunda profil bağlantısı tıklanabilir olarak gösterilir.
>
> `-vvv` (debug) modunda ayrıca **süre ve bellek kullanımı** da gösterilir.

📦 **Messenger bileşeni** ile `messenger:consume` komutunu profillemek istiyorsan:

`--no-reset` seçeneğini ekle ve `--limit` kullanarak yalnızca birkaç mesaj işle (profilin okunabilirliği için).

---

## 📚 Daha Fazla Bilgi

* Başka Komutları Çağırma
* Konsol Çıktısını Renklendirme ve Biçimlendirme
* Controller İçinden Komut Çalıştırma
* Komutları Servis Olarak Tanımlama
* Komutları Gizleme
* Konsol Girdisi (Argümanlar & Seçenekler)
* Komutları Lazy Yükleme
* Aynı Komutun Aynı Anda Çalışmasını Engelleme
* Konsol Komutlarını Biçimlendirme
* Ayrıntı Düzeyleri (Verbosity Levels)

---

## 🧰 Yardımcı Araçlar (Console Helpers)

Symfony’nin Console bileşeni, komut yazarken sık kullanılan işleri kolaylaştıran **“helper”** sınıflarını da içerir:

| Yardımcı                       | Açıklama                                                     |
| -------------------------------- | -------------------------------------------------------------- |
| **Question Helper**        | Kullanıcıdan etkileşimli bilgi almak                        |
| **Formatter Helper**       | Çıktının renk ve biçimini özelleştirmek                 |
| **Progress Bar**           | İlerleme çubuğu göstermek                                  |
| **Progress Indicator**     | Dönen gösterge (spinner) göstermek                          |
| **Table Helper**           | Tablo şeklinde veri göstermek                                |
| **Debug Formatter Helper** | Harici programlar için hata ayıklama çıktısı oluşturmak |
| **Process Helper**         | Harici süreçleri (process) çalıştırmak                   |
| **Cursor Helper**          | Terminal imlecini yönetmek                                    |
| **Tree Helper**            | Ağaç (tree) yapısında veri göstermek                      |

---

✅ **Özetle:**

Symfony Console bileşeni, komutların  **yaşam döngüsünü yönetme** ,  **test etme** ,  **profilleme** ,  **hata kaydı** , ve **gelişmiş terminal araçları** ile tam kontrol sunar.
