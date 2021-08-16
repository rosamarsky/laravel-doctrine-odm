# Doctrine ODM for Laravel framework

## Installation
    composer require rosamarsky/laravel-doctrine-odm

## Configuration

1. Publish `doctrine-odm.php` config file;

2. Set .env variables for your mongodb connection:
   - `MONGO_HOST`
   - `MONGO_PORT`
   - `MONGO_DB`
   - `MONGO_USER`
   - `MONGO_PASS`

3. Make sure `DocumentManagerServiceProvider.php` is registered in your application.

## Notes
    Only annotation driver available

## Example to use

```php
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document(collection="users") */
class User {
    /** @ODM\Id() */
    private string $id;
    
    /** @ODM\Field(type="string") */
    private string $name;
    
    /** @ODM\Field(type="string") */
    private string $email;
    
    /** @ODM\Field(type="carbon") */
    private Carbon $createdAt;

    public function __construct(string $name, string $email) {
        $this->name = $name;
        $this->email = $email;
    }
}
```

```php
class UserController extends AbstractController {
    private $manager;
 
    public function __construct(\Doctrine\ODM\MongoDB\DocumentManager $manager) 
    {
        $this->manager = $manager;
    }
    
    public function store(Request $request): User
    {
        $user = new User('Roman Samarsky', 'rosamarsky@gmail.com');

        $this->manager->persist($user);
        $this->manager->flush();

        return $user;
    }
}
```
