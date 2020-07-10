<?php

namespace F9Web\LaravelDeletable\Tests;

use F9Web\LaravelDeletable\LaravelDeletableServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Illuminate\Database\Schema\Blueprint;

abstract class TestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    public function tearDown(): void
    {
        $this->app['config']->set(
            [
                'f9web-laravel-deletable' => [
                    'messages' => [
                        'default' => 'The model cannot be deleted',
                    ],
                ],
            ]
        );

        parent::tearDown();
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array|string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDeletableServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
        });
    }
}
