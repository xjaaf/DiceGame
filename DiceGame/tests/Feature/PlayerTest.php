<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\GameController;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Game;
use Mockery\Generator\StringManipulation\Pass\Pass;

class PlayerTest extends TestCase
{


    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->artisan('passport:install');
    }
    
    public function test_register()
    {
        
        $response = $this->postJson('/api/players', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'nickname' => 'testuser'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'name',
                'email',
                'nickname',
            ],
            'access_token',
        ]);
    }

}
