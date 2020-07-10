<?php

namespace F9Web\LaravelDeletable\Tests\Models;

use F9Web\LaravelDeletable\Traits\RestrictsDeletion;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use RestrictsDeletion;

    /** @var bool */
    public $timestamps = false;

    /** @var array */
    protected $guarded = [];

    /** @var string */
    protected $table = 'users';
}
