<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\GameController;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use App\Models\Game;
use Mockery\Generator\StringManipulation\Pass\Pass;

class PlayerTest extends TestCase
{

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

    public function test_login()
    {
        $response = $this->postJson('/api/players/login', [
            'email' => 'test@example.com',
            'password' => 'password',
            
        
        ]);

        $response->assertStatus(200);
        
    }

    public function test_update()
    {
        $response = $this->postJson('/api/players/login', [
            'email' => 'test@example.com',
            'password' => 'password',
            
        ]);

        $response = $this->putJson('/api/players/' . $response['user']['id'], [
            'Bearer ' => $response['access_token'],
            'name' => 'Updated Name',
            'nickname' => 'updatednickname',
        ]);

        $response->assertStatus(200);

    }
}
