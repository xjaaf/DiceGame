<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;



class GameTest extends TestCase
{
    use RefreshDatabase;
/**
     * Test playing the dice game.
     *
     * @return void
     */

     public function testPlayDiceGame()
     {
         // Crear un jugador de prueba
         $player = User::factory()->create();
 
         // Crear y asignar el rol de 'player' utilizando spatie/laravel-permission
         $playerRole = Role::create(['name' => 'player']);
         $player->assignRole($playerRole);
 
         // Simular la autenticación como jugador
         Auth::shouldReceive('guard->user')
             ->once()
             ->andReturn($player);
 
         // Simular una solicitud POST para jugar al juego de dados
         $response = $this->actingAs($player, 'api')
             ->postJson("/api/players/{$player->id}/games");
 
         // Verificar que la respuesta sea exitosa
         $response->assertStatus(200);
 
         // Verificar que el mensaje de respuesta sea correcto
         $response->assertJson([
             'welcome' => 'Welcome to the Dice Game!',
             'status' => 'success',
             'message' => 'Your game has been registered successfully!',
         ]);
 
         // Verificar que se devuelva la información del juego
         $response->assertJsonStructure([
             'welcome',
             'dice1',
             'dice2',
             'total',
             'result',
             'status',
             'message',
             'game' => [
                 'id',
                 'dice1',
                 'dice2',
                 'winner',
                 'user_id',
                 'created_at',
                 'updated_at',
             ],
         ]);
 
         // Verificar que el juego se haya guardado en la base de datos
         $this->assertDatabaseHas('games', [
             'user_id' => $player->id,
         ]);
     }

    /**
     * Test deleting all games of a player.
     *
     * @return void
     */

    public function test_DeleteAllGames()
    {
        // Crear un jugador de prueba
        $player = User::factory()->create();
        $player->assignRole('player');

        // Simular la autenticación como jugador
        Auth::shouldReceive('guard->user')
            ->once()
            ->andReturn($player);

        // Simular una solicitud DELETE para eliminar todos los juegos de un jugador
        $response = $this->actingAs($player, 'api')
            ->deleteJson("/api/players/{$player->id}/games");

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);

        // Verificar que el mensaje de respuesta sea correcto
        $response->assertJson(['message' => 'All games have been deleted successfully']);

        // Verificar que se hayan eliminado todos los juegos del jugador
        $this->assertDatabaseMissing('games', [
            'user_id' => $player->id,
        ]);
    }

    /**
     * Test getting all games of a player.
     *
     * @return void
     */ 

    public function test_GetAllGames(){
        // Crear un jugador de prueba
        $player = User::factory()->create();
        $player->assignRole('player');

        // Crear juegos de prueba para el jugador
        $games = Game::factory(3)->create(['user_id' => $player->id]);

        // Simular la autenticación como jugador
        Auth::shouldReceive('guard->user')
            ->once()
            ->andReturn($player);

        // Simular una solicitud GET para obtener todos los juegos de un jugador
        $response = $this->actingAs($player, 'api')
            ->getJson("/api/players/{$player->id}/games");

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);

        // Verificar que se devuelvan todos los juegos del jugador
        $response->assertJsonStructure([
            'total_games',
            'games',
        ]);

        // Verificar que se devuelvan los juegos correctos del jugador
        $response->assertJsonCount(3, 'games');
    }


}
