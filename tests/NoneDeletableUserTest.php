<?php

declare(strict_types=1);

namespace F9Web\LaravelDeletable\Tests;

use F9Web\LaravelDeletable\Exceptions\NoneDeletableModel;
use F9Web\LaravelDeletable\Tests\Models\NoneDeletableUser;
use F9Web\LaravelDeletable\Tests\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function get_class;

class NoneDeletableUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @throws \Exception
     */
    public function it_denies_deletion()
    {
        $this->expectException(NoneDeletableModel::class);
        $this->expectExceptionMessage('The model cannot be deleted');

        $user = $this->getModel()::query()->create($record = ['email' => 'rob@f9web.co.uk']);
        $user->delete();

        $this->assertDatabaseHas('users', $record);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_renders_the_fallback_exception_message()
    {
        $this->app['config']->set(['f9web-laravel-deletable.messages.default' => null]);

        $user = $this->getModel()::query()->create($record = ['id' => 23, 'email' => 'rob@f9web.co.uk']);

        $class = get_class($this->getModel());

        $this->expectException(NoneDeletableModel::class);
        $this->expectExceptionMessage("Restricted deletion: {$class} - 23 is not deletable");

        $user->delete();

        $this->assertDatabaseHas('users', $record);
    }

    private function getModel()
    {
        // the user with the email 'rob@f9web.co.uk' is not deletable
        return (new class() extends User {
            public function isDeletable(): bool
            {
                return $this->email !== 'rob@f9web.co.uk';
            }
        });
    }
}
