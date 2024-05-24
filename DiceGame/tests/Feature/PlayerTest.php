<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class PlayerTest extends TestCase
{
    use RefreshDatabase, WithFaker;



    public function testRegister()
    {
       
        $userData = [
            'name' => 'John Doe',
            'nickname' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            
        ];

        $response = $this->postJson('/api/players', $userData);

        $response->assertJson(['message' => 'User registered successfully'])
                 ->assertStatus(201);


    }

    
/*
    public function testLogin()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/players/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'nickname',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'access_token',
            ]);
    }

    public function testUpdateNickname()
    {
        $user = User::factory()->create();
        $user->assignRole('player');
        $nickname = 'newnickname';

        $response = $this->actingAs($user, 'api')
                         ->putJson("/api/players/{$user->id}", [
                             'nickname' => $nickname,
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Player nickname updated successfully',
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nickname' => $nickname,
        ]);
    }

    public function testGetAllGames()
    {
        $user = User::factory()->create();
        $user->assignRole('player');
        $game1 = Game::factory()->create(['user_id' => $user->id]);
        $game2 = Game::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')
                         ->getJson("/api/players/{$user->id}/games");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'number_of_games',
                'number_of_wins',
                'games' => [
                    '*' => [
                        'id',
                        'dice1',
                        'dice2',
                        'winner',
                        'user_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(2, 'games');
    }


    */
}
