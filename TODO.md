Symfony’de **e-posta (Mailer)** ve **bildirimleri (Notifier)** yönetmek için aşağıdaki adımları izleyebilirsin. Kısa ve net şekilde anlatıyorum:

---

# ✅ 1. Mailer Paketini Kur

Symfony 5+ ile birlikte mailer zaten var ama yoksa şu komutu çalıştır:

```bash
composer require symfony/mailer
```

---

# ✅ 2. .env Dosyasına Mail Ayarını Yap

Örnek (SMTP kullanıyorsan):

```env
MAILER_DSN=smtp://kullanici:sifre@smtp.sunucu.com:587
```

Gmail için:

```env
MAILER_DSN=gmail://KULLANICI_ADI:APP_PASSWORD@default
```

---

# ✅ 3. Mail Gönderme Örneği

```php
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

public function sendEmail(MailerInterface $mailer)
{
    $email = (new Email())
        ->from('sender@example.com')
        ->to('user@example.com')
        ->subject('Örnek Konu')
        ->text('Bu bir test mesajıdır.');

    $mailer->send($email);
}
```

---

# 📣 Bildirim Sistemi (Notifier) Kullanımı

## 4. Notifier Paketini Kur

```bash
composer require symfony/notifier
```

---

# ✅ 5. Bildirim Kanalını Seç

Notifier pek çok kanal destekliyor:

* Email
* SMS (Twilio, Nexmo, …)
* Slack
* Telegram
* Browser Notifications (Mercure)
* Chat apps

Örnek: Slack bildirimi için

```bash
composer require symfony/slack-notifier
```

`.env` ayarı:

```env
SLACK_DSN=slack://TOKEN@default
```

---

# 💬 6. Basit Bir Bildirim Gönderme Örneği

```php
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;

public function notify(NotifierInterface $notifier)
{
    $notification = (new Notification('Yeni bir olay var!', ['email', 'slack']));
    $notification->content('Sistemde yeni bir işlem gerçekleşti.');

    $notifier->send($notification);
}
```

---

# 📌 Ekstra: Kullanıcıya Özel Bildirimler (Notifiable Trait)

Eğer kullanıcı modeline bildirim göndereceksen:

```php
use Symfony\Component\Notifier\Recipient\Recipient;

$recipient = new Recipient('user@example.com');
$notifier->send($notification, $recipient);
```

---

# 🎯 Özetle Ne Yapmalısın?

1. **Mailer** paketini kur → `.env` dosyasını yapılandır.
2. E-posta gönderme kodunu yaz.
3. **Notifier** paketini kur → Slack, SMS, Telegram vb. entegrasyonları ekle.
4. Bildirim kanallarını tanımla ve gönder.

---
