<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Artisan;

class PlayerTest extends TestCase
{

    public function setUp(): void{
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RoleSeeder']);
        Artisan::call('db:seed', ['--class' => 'AdminSeeder']);
        Artisan::call('db:seed', ['--class' => 'DatabaseSeeder']);
        Artisan::call('passport:install');
    }
    public function testRegister()
    {
        $faker = Faker::create();
        $response = $this->postJson('/api/players', [
            'name' => $faker->name,
            'email' => $faker->email,
            'password' => 'password',
            'nickname' => $faker->userName,
        ]);
        $response->assertStatus(201);
    }

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
    $nickname = 'nickname';

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

    $response = $this->getJson("/api/players/{$user->id}/games");

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

}