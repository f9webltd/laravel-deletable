# Laravel Deletable

Gracefully handle deletion restrictions on your [Eloquent models](https://laravel.com/docs/7.x/eloquent).

## Requirements

PHP >= 7.2, Laravel >= 5.8.

## Installation

``` bash
composer require f9webltd/laravel-deletable
```

The package will automatically register itself.

Optionally publish the configuration file by running: `php artisan vendor:publish` and selecting the appropriate package.

## Documentation

 ### Usage
 
 Within an Eloquent model use the `RestrictsDeletion` trait:

 ```php
namespace App;

use F9Web\LaravelDeletable\Traits\RestrictsDeletion;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use RestrictsDeletion;
}
```

The trait overrides calls to Eloquent's `delete()` method.

Implement the `isDeletable()` method within the model in question. 

This method should return `true` to allow deletion and `false` to deny deletion:

```php
namespace App;

use F9Web\LaravelDeletable\Traits\RestrictsDeletion;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
  use RestrictsDeletion;
  
  public function isDeletable() : bool
  {
    return $this->orders->isEmpty();
  }  
}
```

The above denies deletion of users with orders.

None deletable models throw an exception when the `isDeletable()` method returns `false`:

```php
namespace App\Controllers;

use F9Web\LaravelDeletable\Exceptions\NoneDeletableModel;
use App\User;

class UsersController
{
  public function destroy(User $user) : bool
  {
    try {
      $user->delete();
    } catch (NoneDeletableModel $e) {
      // dd($ex->getMessage());
    }
  }  
}
```

#### Eloquent Base Model

As the default `isDeletable()` method returns `true`, a base Eloquent model can be optionally defined from which all models extend. Each model can then optionally implement the `isDeletable()` method as needed.

### Customising Messages

The default exception message is defined within the config `f9web-laravel-deletable.messages.default` and is simply `The model cannot be deleted`.

By setting `f9web-laravel-deletable.messages.default` to `null` a more detailed message is automatically generated i.e. `Restricted deletion: App\User - 1 is not deletable`.

Custom messages can be set within the `isDeletable()` method:

```php
namespace App;

use F9Web\LaravelDeletable\Traits\RestrictsDeletion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class User extends Model
{
  use RestrictsDeletion;
  
  public function isDeletable() : bool
  {
    if (Str::endsWith($this->email, 'f9web.co.uk')) {
        return $this->denyDeletionReason('Users with f9web.co.uk company email addresses cannot be deleted');
    }

    return true;
  }  
}
```

The `denyDeletionReason()` method can be used to specify the exception message. 

In the above case, the exception message is `Users with f9web.co.uk company email addresses cannot be deleted`.

### Multiple Checks

Multiple checks can be performed within `isDeletable()` if necessary, each of which returning a different exception message: 

```php
namespace App;

use F9Web\LaravelDeletable\Traits\RestrictsDeletion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class User extends Model
{
  use RestrictsDeletion;
  
  public function isDeletable() : bool
  {
    if (Str::endsWith($this->email, 'f9web.co.uk')) {
       return $this->denyDeletionReason('Users with f9web.co.uk company email addresses cannot be deleted');
    }
    
    if ($this->orders->isNotEmpty()) {
       return false;
    }
    
    if ($this->purchaseOrders->isNotEmpty()) {
       return $this->denyDeletionReason('This user has active purchase orders and cannot be deleted');
    }
    
    if ($this->overdueInvoices->isNotEmpty()) {
       return $this->denyDeletionReason('Users with overdue invoices cannot be deleted');
    }

    return true;
  }  
}
```

## Contribution

Any ideas are welcome. Feel free to submit any issues or pull requests.

## Testing

``` bash
composer test
```

## Security

If you discover any security related issues, please email rob@f9web.co.uk instead of using the issue tracker.

## Credits

- [Rob Allport](https://github.com/ultrono) for [F9 Web Ltd.](https://www.f9web.co.uk)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
