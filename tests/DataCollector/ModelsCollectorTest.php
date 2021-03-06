<?php

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ModelsCollectorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_collects_retrieved_models()
    {
        $this->loadLaravelMigrations();

        $this->debugbar()->boot();

        /** @var \Barryvdh\Debugbar\DataCollector\ModelsCollector $collector */
        $collector = $this->debugbar()->getCollector('models');

        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertEquals(
            ['data' => [], 'count' => 0],
            $collector->collect()
        );

        User::first();

        $this->assertEquals(
            ['data' => [User::class => 1], 'count' => 1],
            $collector->collect()
        );

        Person::all();

        $this->assertEquals(
            ['data' => [User::class => 1, Person::class => 2], 'count' => 3],
            $collector->collect()
        );
    }
}

class User extends Model
{
    protected $table = 'users';
    protected $guarded = [];
}

class Person extends User
{
}
