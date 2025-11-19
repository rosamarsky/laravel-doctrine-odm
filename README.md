# Laravel Doctrine ODM

A simple **Doctrine ODM adapter** for Laravel 8+ / 9+ that supports **attribute** and **XML** mapping for MongoDB.

## Features
- Supports PHP 8 attributes and XML mappings.
- Works with Doctrine ODM 3.0+.
- Laravel service provider for easy integration.
- Registers custom Carbon type automatically.

---

## Installation

Install via Composer:

```bash
composer require rosamarsky/laravel-doctrine-odm
```

The package auto-registers the service provider, so no manual registration is required.

---

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="Rosamarsky\LaravelDoctrineOdm\ServiceProvider" --tag=config
```

This will create config/doctrine-odm.php.

Set your MongoDB credentials in .env or directly in the config:

```dotenv
MONGO_HOST=127.0.0.1
MONGO_PORT=27017
MONGO_DB=your_database
MONGO_USER=
MONGO_PASS=
```

---

## Usage

### Define Documents
```php
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use Carbon\Carbon;

#[Document(collection: "users")]
class User
{
    #[Id]
    private string $id;

    #[Field(type: "string")]
    private string $name;

    #[Field(type: "string")]
    private string $email;

    #[Field(type: "carbon")]
    private Carbon $createdAt;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = new Carbon();
    }
}
```

XML mapping is also supported if you configure driver => 'xml' and put your XML files in metadata_dir.

### Using DocumentManager

```php
use \Doctrine\ODM\MongoDB\DocumentManager;

class UserController extends AbstractController
{
    public function __construct(private readonly DocumentManager $manager) {}

    public function store(Request $request): User
    {
        $user = new User('Roman Samarsky', 'rosamarsky@gmail.com');

        $this->manager->persist($user);
        $this->manager->flush();

        return $user;
    }
}
```

---
## License 
MIT © Roman Samarsky
