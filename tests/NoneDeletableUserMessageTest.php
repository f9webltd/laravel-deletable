<?php

declare(strict_types=1);

namespace F9Web\LaravelDeletable\Tests;

use F9Web\LaravelDeletable\Exceptions\NoneDeletableModel;
use F9Web\LaravelDeletable\Tests\Models\User;

use function get_class;

class NoneDeletableUserMessageTest extends TestCase
{
    /**
     * @test
     * @throws \Exception
     */
    public function it_sets_custom_exception_messages()
    {
        $model = (new class () extends User {
            public function isDeletable(): bool
            {
                if ($this->email === 'rob@f9web.co.uk') {
                    return $this->denyDeletionReason('The core user with the email rob@f9web.co.uk cannot be deleted');
                }

                return true;
            }
        });

        $this->expectException(NoneDeletableModel::class);
        $this->expectExceptionMessage('The core user with the email rob@f9web.co.uk cannot be deleted');

        $user = $model::query()->create($record = ['email' => 'rob@f9web.co.uk']);
        $user->delete();

        $this->assertDatabaseHas('users', $record);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_sets_a_custom_message_for_core_models()
    {
        $this->expectException(NoneDeletableModel::class);

        $model = (new class () extends User {
            public function isDeletable(): bool
            {
                // the user with the email 'rob@f9web.co.uk' is a
                // core record and therefore not deletable
                if ($this->email === 'rob1@f9web.co.uk') {
                    return $this->isCoreEntity();
                }

                return true;
            }
        });

        $class = get_class($model);

        $user = $model::query()->create($record = ['email' => 'ro1b@f9web.co.uk']);

        $this->expectExceptionMessage(
            "[{$class} #{$user->getKey()}] is a core record and therefore not deletable. This indicates " .
            "the record is used programmatically within the system."
        );

        $user->delete();

        $this->assertDatabaseHas('users', $record);
    }
}
