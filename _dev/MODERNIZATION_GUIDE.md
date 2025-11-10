# Symfony Express Pubs - Modernization Guide

## Overview

Your Symfony 7.3 application has been modernized with current best practices and modern PHP patterns. This guide explains the changes made and how to work with the updated codebase.

## Major Improvements Made

### 1. Dependency Management

**Changes:**
- Removed `symfony/webpack-encore-bundle` (conflicted with AssetMapper approach)
- Added PHPStan for static analysis
- Added PHP CS Fixer for code quality
- Added PHPUnit Bridge for better testing

**To install new dependencies:**
```bash
composer install
```

### 2. Entity Layer Modernization

**Improvements:**
- Added `declare(strict_types=1)` to all entity files
- Used `Doctrine\DBAL\Types\Types` constants for type safety
- Added comprehensive validation constraints using Symfony's `Assert` annotations
- Added timestamp tracking (createdAt, updatedAt) to User entity
- Fixed security issue: Users no longer default to `ROLE_ADMIN`
- Added `__toString()` method to Publisher for better form display

**Example (Title Entity):**
```php
#[ORM\Column(name: 'price', type: Types::DECIMAL, precision: 19, scale: 4, nullable: true)]
#[Assert\PositiveOrZero(message: 'Price must be zero or positive.')]
private ?string $price = null;
```

### 3. Security Enhancements

**New Features:**
- Remember Me functionality (1-week cookie)
- User impersonation for admins (`?_switch_user=email@example.com`)
- Role hierarchy (ROLE_ADMIN includes ROLE_USER)
- Rate limiting for login and registration
- Enhanced logout configuration
- Proper access control rules

**Configuration Files:**
- `/config/packages/security.yaml` - Enhanced security configuration
- `/config/packages/rate_limiter.yaml` - Rate limiting rules

**Rate Limits:**
- Login: 5 attempts per 15 minutes
- Registration: 3 attempts per hour
- API: 100 requests per minute (token bucket)

### 4. Controller Modernization

**Improvements:**
- Constructor dependency injection pattern
- `readonly` properties for immutability
- Added `declare(strict_types=1)`
- Used `#[IsGranted]` attributes for authorization
- Added flash messages for user feedback
- Proper type hints throughout

**Example:**
```php
final class TitleController extends AbstractController
{
    public function __construct(
        private readonly TitleRepository $titleRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }
}
```

### 5. Form Enhancements

**Improvements:**
- Explicit form field types (TextType, ChoiceType, MoneyType, etc.)
- Added labels, help text, and placeholders
- HTML5 validation attributes
- Proper choice fields with descriptive labels
- Field-specific configurations (min/max, patterns, etc.)

**Features:**
- Title ID field is disabled when editing (prevents changes to primary key)
- Publisher dropdown shows name and ID
- Money fields configured for USD
- Date fields use HTML5 date picker

### 6. Code Quality Tools

**PHPStan Configuration:**
- Level 8 (strictest) static analysis
- Doctrine and Symfony extensions
- Configured to work with SQL Server string IDs

**Run PHPStan:**
```bash
vendor/bin/phpstan analyse
```

**PHP CS Fixer Configuration:**
- PSR-12 and Symfony coding standards
- Strict types enforcement
- Automatic import ordering

**Run PHP CS Fixer:**
```bash
# Dry run (check only)
vendor/bin/php-cs-fixer fix --dry-run --diff

# Fix files
vendor/bin/php-cs-fixer fix
```

### 7. Services Configuration

**Improvements:**
- Excluded Entity and DependencyInjection directories
- Separated controller configuration
- Added repository service tags
- Added event subscriber configuration
- Configured parameter binding

### 8. Environment Configuration

**New File:**
- `.env.example` - Template with comprehensive documentation

**Features:**
- Better comments explaining each variable
- SQL Server connection examples
- Messenger and Mailer configuration examples

## Usage Instructions

### Running the Application

```bash
# Start Symfony development server
symfony serve

# Or use PHP built-in server
php -S localhost:8000 -t public
```

### Database Migrations

Since this project uses existing SQL Server tables, you need to add timestamp columns to the users table:

```sql
ALTER TABLE users
ADD created_at DATETIME2 NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME2 NOT NULL DEFAULT CURRENT_TIMESTAMP;
```

### Running Tests

```bash
# Run all tests
php vendor/bin/phpunit

# Run with coverage
php vendor/bin/phpunit --coverage-html coverage/
```

### Code Quality Checks

```bash
# Static analysis
vendor/bin/phpstan analyse

# Code style check
vendor/bin/php-cs-fixer fix --dry-run

# Code style fix
vendor/bin/php-cs-fixer fix
```

### Asset Management

```bash
# Build Tailwind CSS (watch mode)
php bin/console tailwind:build --watch

# Build for production
php bin/console tailwind:build --minify
```

## Best Practices Going Forward

### When Creating New Entities:

1. Always add `declare(strict_types=1)` at the top
2. Use `Types::*` constants instead of string types
3. Add validation constraints
4. Consider adding timestamps
5. Add `__toString()` method if entity will be used in forms

### When Creating New Controllers:

1. Use constructor injection for dependencies
2. Mark injected properties as `readonly`
3. Add `#[IsGranted]` for authorization
4. Add flash messages for user feedback
5. Use proper return types

### When Creating New Forms:

1. Use explicit field types
2. Add labels and help text
3. Configure HTML5 validation
4. Add placeholders for better UX
5. Use ChoiceType for predefined options

### When Adding Business Logic:

Create service classes in `src/Service/` directory:

```php
declare(strict_types=1);

namespace App\Service;

final class TitleService
{
    public function __construct(
        private readonly TitleRepository $titleRepository,
    ) {
    }

    public function calculateRoyalties(Title $title): float
    {
        // Business logic here
    }
}
```

## Security Notes

### Important Changes:

1. **Users no longer get ROLE_ADMIN by default** - They get ROLE_USER
2. **Access control is now enforced** - All routes except login/register require authentication
3. **Rate limiting is active** - Failed login attempts are limited

### Adding Admin Users:

You'll need to manually set ROLE_ADMIN in the database or create a console command:

```php
// Example console command to promote user to admin
$user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
$entityManager->flush();
```

### Using Remember Me:

Add checkbox to login form:
```twig
<input type="checkbox" name="_remember_me" id="remember_me">
<label for="remember_me">Remember me</label>
```

## Troubleshooting

### PHPStan Errors:

If PHPStan complains about the container XML file not being found:
```bash
php bin/console cache:clear
php bin/console cache:warmup
```

### Rate Limiter Issues:

Rate limiter uses cache. If you get locked out during development:
```bash
php bin/console cache:clear
```

### Webpack Encore Errors:

The webpack_encore bundle has been removed. Delete:
- `config/packages/webpack_encore.yaml`
- Any encore-related configuration

## Next Steps

Consider implementing:

1. **API Layer** - Add API endpoints with API Platform or custom controllers
2. **Admin Panel** - EasyAdmin bundle for quick admin interface
3. **Advanced Search** - Implement search functionality with Elasticsearch
4. **Background Jobs** - Use Messenger for asynchronous tasks
5. **Logging** - Enhance Monolog configuration for better debugging
6. **Testing** - Add comprehensive test coverage
7. **Docker** - Containerize the application for consistent environments

## Resources

- [Symfony Documentation](https://symfony.com/doc/current/index.html)
- [Symfony Best Practices](https://symfony.com/doc/current/best_practices.html)
- [PHPStan Documentation](https://phpstan.org/user-guide/getting-started)
- [PHP CS Fixer Documentation](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)
- [Doctrine ORM Documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/)

## Support

If you encounter any issues with the modernized codebase:

1. Check the Symfony logs: `var/log/dev.log`
2. Run `php bin/console debug:config` to verify configuration
3. Run `php bin/console debug:container` to check services
4. Use the Symfony Profiler toolbar for debugging

---

**Last Updated:** 2025-11-10
**Symfony Version:** 7.3
**PHP Version:** 8.2+
