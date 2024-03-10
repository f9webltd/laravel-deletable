![](https://banners.beyondco.de/Laravel%20Deletable.png?theme=light&packageManager=composer+require&packageName=f9webltd%2Flaravel-deletable&pattern=architect&style=style_1&description=Gracefully+restrict+deletion+of+Laravel+Eloquent+models&md=1&showWatermark=0&fontSize=100px&images=trash)

[![Packagist Version](https://img.shields.io/packagist/v/f9webltd/laravel-deletable?style=flat-square)](https://packagist.org/packages/f9webltd/laravel-deletable)
[![run-tests-laravel-8](https://img.shields.io/github/workflow/status/f9webltd/laravel-deletable/run-tests-laravel-8?style=flat-square)](https://github.com/f9webltd/laravel-deletable/actions)
[![StyleCI Status](https://github.styleci.io/repos/278581318/shield)](https://github.styleci.io/repos/278581318)
[![Packagist License](https://img.shields.io/packagist/l/f9webltd/laravel-deletable?style=flat-square)](https://packagist.org/packages/f9webltd/laravel-deletable)
[![PHP ^8.2](https://img.shields.io/badge/php-%5E8.2-brightgreen)]()

# Laravel Deletable

Gracefully handle deletion restrictions on your [Eloquent models](https://laravel.com/docs/7.x/eloquent), as featured on [Laravel News](https://laravel-news.com/laravel-deletable-trait)

## Requirements

* PHP `^8.2`
* Laravel `^10.0` / `^11.0`.

For older versions of PHP / Laravel use version [1.0.6](https://github.com/f9webltd/laravel-deletable/tree/1.0.6)

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
    return $this->orders()->doesntExist();
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
