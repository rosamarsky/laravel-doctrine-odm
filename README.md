# Doctrine ODM for Laravel framework
___

## Installation & Configuration
1. Install via composer: `composer require rosamarsky/laravel-doctrine-odm`;

2. Make sure `ServiceProvider.php` is registered in your application.

3. Publish `doctrine-odm.php` config file via command `php artisan vendor:publish` or `cp ./vendor/rosamarsky/laravel-doctrine-odm/config/doctrine-odm.php ./config/doctrine-odm.php`;

4. Set .env variables for your mongodb connection:
   - `MONGO_HOST`
   - `MONGO_PORT`
   - `MONGO_DB`
   - `MONGO_USER`
   - `MONGO_PASS`

## Notes
    Only annotation driver is available

___

## Example

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
