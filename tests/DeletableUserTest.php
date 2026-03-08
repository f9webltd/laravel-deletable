<?php

declare(strict_types=1);

namespace F9Web\LaravelDeletable\Tests;

use F9Web\LaravelDeletable\Tests\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeletableUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws \Exception
     */
    public function test_it_allows_deletion()
    {
        $user = User::query()->create($record = ['email' => 'user@domain.co.uk']);

        $user->delete();

        $this->assertDatabaseMissing('users', $record);
    }
}
