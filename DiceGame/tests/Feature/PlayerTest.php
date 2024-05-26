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

    public function test_wrong_login()
    {
        $response = $this->postJson('/api/players/login', [
            'email' => 'test@example.com',
            'password' => 'passworddd',


        ]);

        $response->assertStatus(401);
    }

    public function test_update()
    {
        $response = $this->postJson('/api/players/login', [
            'email' => 'test@example.com',
            'password' => 'password',


        ]);

        $response->assertStatus(200);

        $responseJson =  json_decode($response->getContent());


        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $responseJson->access_token
        ])->putJson('/api/players/' . $responseJson->user->id, [
            'name' => 'Updated Name',
            'nickname' => 'updatednickname',
        ]);


        $response->assertStatus(200);
    }
    public function test_wrong_update_id()
    {
        $response = $this->postJson('/api/players/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $responseJson = json_decode($response->getContent());

        $token = $responseJson->access_token;
        $userId = $responseJson->user->id;

        // Intenta actualizar con un ID incorrecto
        $updateResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/players/' . ($userId + 1), [
            'nickname' => 'new_nickname',
        ]);

        $updateResponse->assertStatus(403);
    }


    public function test_play()
    {
        $response = $this->postJson('/api/players/login', [
            'email' => 'test@example.com',
            'password' => 'password',

        ]);
        $response->assertStatus(200);

        $responseJson =  json_decode($response->getContent());

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $responseJson->access_token
        ])->postJson('/api/players/' . $responseJson->user->id  .  '/games/', []);


        $response->assertStatus(200);
    }

    public function test_getAllGames()
    {
        $response = $this->postJson('/api/players/login', [
            'email' => 'test@example.com',
            'password' => 'password',

        ]);
        $response->assertStatus(200);

        $responseJson =  json_decode($response->getContent());

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $responseJson->access_token
        ])->getJson('/api/players/' . $responseJson->user->id  .  '/games', []);

        $response->assertStatus(200);
    }



    public function test_destroy()
    {
        $response = $this->postJson('/api/players/login', [
            'email' => 'test@example.com',
            'password' => 'password',

        ]);
        $response->assertStatus(200);

        $responseJson =  json_decode($response->getContent());

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $responseJson->access_token
        ])->deleteJson('/api/players/' . $responseJson->user->id  .  '/games', []);

        $response->assertStatus(200);
    }
    public function test_wrong_destroy_id()
    {
        $response = $this->postJson('/api/players/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $responseJson = json_decode($response->getContent());

        $token = $responseJson->access_token;
        $userId = $responseJson->user->id;

        $destroyResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/players/' . ($userId + 1) . '/games');

        $destroyResponse->assertStatus(403);
    }


    public function test_ranking()
    {
        $response = $this->postJson('/api/players/login', [
            'email' => 'test@example.com',
            'password' => 'password',

        ]);
        $response->assertStatus(200);

        $responseJson =  json_decode($response->getContent());

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $responseJson->access_token
        ])->getJson('/api/players/ranking', []);

        $response->assertStatus(200);
    }

    public function test_loserRanking()
    {
        $response = $this->postJson('/api/players/login', [
            'email' => 'test@example.com',
            'password' => 'password',

        ]);
        $response->assertStatus(200);

        $responseJson =  json_decode($response->getContent());

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $responseJson->access_token
        ])->getJson('/api/players/ranking/loser', []);

        $response->assertStatus(200);
    }

    public function test_winnerRanking()
    {
        $response = $this->postJson('/api/players/login', [
            'email' => 'test@example.com',
            'password' => 'password',

        ]);
        $response->assertStatus(200);

        $responseJson =  json_decode($response->getContent());

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $responseJson->access_token
        ])->getJson('/api/players/ranking/winner', []);

        $response->assertStatus(200);
    }

    public function test_averageSuccessRate()
    {
        $response = $this->postJson('/api/players/login', [
            'email' => 'jose@mail.com',
            'password' => 'password',

        ]);
        $response->assertStatus(200);

        $responseJson =  json_decode($response->getContent());

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $responseJson->access_token
        ])->getJson('/api/players', []);

        $response->assertStatus(200);
    }
}
