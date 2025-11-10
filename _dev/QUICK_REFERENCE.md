# Quick Reference - Modernized Symfony Project

## Installation & Setup

```bash
# 1. Install dependencies
composer install

# 2. Run database migration
# Execute MIGRATION.sql in SQL Server Management Studio

# 3. Clear cache
php bin/console cache:clear

# 4. Start development server
symfony serve
# OR
php -S localhost:8000 -t public
```

## Daily Development Commands

### Symfony Commands
```bash
# Start server
symfony serve

# Clear cache
php bin/console cache:clear

# List all routes
php bin/console debug:router

# Check environment variables
php bin/console debug:container --env-vars

# Generate new entity
php bin/console make:entity

# Generate CRUD controller
php bin/console make:crud
```

### Asset Management
```bash
# Build Tailwind CSS (watch mode)
php bin/console tailwind:build --watch

# Build for production
php bin/console tailwind:build --minify
```

### Code Quality
```bash
# Run static analysis (Level 8)
vendor/bin/phpstan analyse

# Check code style
vendor/bin/php-cs-fixer fix --dry-run --diff

# Fix code style
vendor/bin/php-cs-fixer fix

# Run all quality checks
vendor/bin/phpstan analyse && vendor/bin/php-cs-fixer fix --dry-run
```

### Testing
```bash
# Run all tests
php vendor/bin/phpunit

# Run specific test
php vendor/bin/phpunit tests/Controller/TitleControllerTest.php

# Run with coverage
php vendor/bin/phpunit --coverage-html coverage/
```

## Common Patterns

### Creating a New Entity

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\YourEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: YourEntityRepository::class)]
#[ORM\Table(name: 'your_table')]
class YourEntity
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Assert\NotBlank]
    private string $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    // Getters and setters...
}
```

### Creating a Modern Controller

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\YourEntity;
use App\Form\YourEntityType;
use App\Repository\YourEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/your-entity')]
#[IsGranted('ROLE_USER')]
final class YourEntityController extends AbstractController
{
    public function __construct(
        private readonly YourEntityRepository $repository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(name: 'app_your_entity_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('your_entity/index.html.twig', [
            'entities' => $this->repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_your_entity_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $entity = new YourEntity();
        $form = $this->createForm(YourEntityType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $this->addFlash('success', 'Created successfully.');

            return $this->redirectToRoute('app_your_entity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('your_entity/new.html.twig', [
            'entity' => $entity,
            'form' => $form,
        ]);
    }
}
```

### Creating a Modern Form

```php
<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\YourEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class YourEntityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'help' => 'Enter the name',
                'attr' => [
                    'placeholder' => 'Name',
                    'maxlength' => 255,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => YourEntity::class,
        ]);
    }
}
```

### Creating a Service

```php
<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\YourEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

final class YourService
{
    public function __construct(
        private readonly YourEntityRepository $repository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function doSomething(): void
    {
        // Business logic here
    }
}
```

## Security Quick Reference

### Access Control Annotations
```php
// Require ROLE_USER for entire controller
#[IsGranted('ROLE_USER')]
class YourController extends AbstractController

// Require ROLE_ADMIN for specific action
#[IsGranted('ROLE_ADMIN')]
public function adminAction(): Response

// Check in template
{% if is_granted('ROLE_ADMIN') %}
    <a href="#">Admin Link</a>
{% endif %}
```

### Flash Messages
```php
// In controller
$this->addFlash('success', 'Operation successful!');
$this->addFlash('error', 'An error occurred.');
$this->addFlash('warning', 'Warning message.');
$this->addFlash('info', 'Information message.');
```

### Rate Limiting
```php
// In controller action
public function register(Request $request, RateLimiterFactory $registrationLimiter): Response
{
    $limiter = $registrationLimiter->create($request->getClientIp());
    if (false === $limiter->consume(1)->isAccepted()) {
        $this->addFlash('error', 'Too many attempts. Please try again later.');
        return $this->redirectToRoute('app_login');
    }
    // ... rest of code
}
```

## Database Quick Reference

### Common SQL Server Commands

```sql
-- Check users table structure
SELECT * FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'users';

-- View all users
SELECT id, email, roles, created_at FROM users;

-- Promote user to admin
UPDATE users
SET roles = '["ROLE_ADMIN", "ROLE_USER"]'
WHERE email = 'user@example.com';

-- Reset user role to user only
UPDATE users
SET roles = '["ROLE_USER"]'
WHERE email = 'user@example.com';

-- Count users by role
SELECT roles, COUNT(*) as count
FROM users
GROUP BY roles;
```

## Validation Constraints Reference

```php
use Symfony\Component\Validator\Constraints as Assert;

// Common constraints
#[Assert\NotBlank]                                    // Must not be empty
#[Assert\Length(min: 5, max: 255)]                   // String length
#[Assert\Email]                                       // Valid email
#[Assert\Regex(pattern: '/^[A-Z]{2}\d{4}$/')]       // Pattern matching
#[Assert\Choice(choices: ['option1', 'option2'])]   // Must be one of choices
#[Assert\Range(min: 0, max: 100)]                   // Numeric range
#[Assert\PositiveOrZero]                            // Zero or positive
#[Assert\NotNull]                                    // Not null
#[Assert\Type('string')]                            // Type checking
#[Assert\Url]                                        // Valid URL
#[Assert\Unique]                                     // Unique in database
```

## Form Field Types Reference

```php
use Symfony\Component\Form\Extension\Core\Type\*;

TextType::class         // Single line text
TextareaType::class     // Multi-line text
EmailType::class        // Email input
PasswordType::class     // Password input
IntegerType::class      // Integer number
MoneyType::class        // Money amount
ChoiceType::class       // Select dropdown
EntityType::class       // Select from entity
DateType::class         // Date picker
DateTimeType::class     // Date and time picker
CheckboxType::class     // Checkbox
FileType::class         // File upload
HiddenType::class       // Hidden field
```

## Troubleshooting

### PHPStan Issues
```bash
# Regenerate container cache
php bin/console cache:clear
php bin/console cache:warmup

# Then run PHPStan
vendor/bin/phpstan analyse
```

### Rate Limiter Lockout
```bash
# Clear cache to reset rate limiters
php bin/console cache:clear
```

### Database Connection Issues
```bash
# Check connection
php bin/console doctrine:schema:validate

# Debug database configuration
php bin/console debug:config doctrine
```

### Tailwind Not Building
```bash
# Rebuild Tailwind
php bin/console tailwind:build

# Check for errors
php bin/console tailwind:build -vvv
```

## Environment Variables

```env
# .env.local (create this file, never commit)
APP_ENV=dev
APP_SECRET=your-secret-key-here

# SQL Server connection
DATABASE_URL="sqlsrv://@localhost\SQLEXPRESS/pubs?TrustServerCertificate=true&MultipleActiveResultSets=true&charset=UTF-8"

# Mailer (for development)
MAILER_DSN=null://null
```

## Useful URLs

- **Web Profiler**: `http://localhost:8000/_profiler`
- **Routes List**: `http://localhost:8000/_profiler/open/latest?panel=router`
- **Your App**: `http://localhost:8000/`
- **Login**: `http://localhost:8000/login`
- **Register**: `http://localhost:8000/register`

## Git Commands

```bash
# Stage all changes
git add .

# Commit changes
git commit -m "Applied Symfony modernization"

# View status
git status

# View changes
git diff

# Create new branch
git checkout -b feature/your-feature
```

---

**For detailed information, see:**
- `MODERNIZATION_GUIDE.md` - Complete guide
- `MODERNIZATION_SUMMARY.md` - All changes made
- `CLAUDE.md` - Project context
