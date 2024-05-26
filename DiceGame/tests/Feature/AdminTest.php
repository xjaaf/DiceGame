<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminTest extends TestCase
{
    public function test_CalculateAverageSuccessRate()
{
    // Crear un usuario administrador
    $admin = User::factory()->create();
    $admin->assignRole('admin', 'api');  // Asignar el rol 'admin' con el guardia 'api'

    // Verificar que el rol se ha asignado correctamente
    $this->assertTrue($admin->hasRole('admin', 'api'));

    // Simular la autenticación como administrador
    Passport::actingAs($admin);

    // Simular una solicitud GET para calcular la tasa de éxito promedio
    $response = $this->actingAs($admin, 'api')
        ->getJson('/api/players');

    // Verificar que la respuesta sea exitosa
    $response->assertStatus(200);

    // Verificar que el mensaje de respuesta sea correcto
    $response->assertJson(['message' => 'Average success rate calculated successfully']);

    // Verificar que se devuelva el total de jugadores
    $response->assertJsonStructure([
        'total_players',
        'total_average_success_rate',
        'players',
    ]);

    // Verificar que el usuario administrador tiene acceso autorizado
    $response->assertJsonMissing(['message' => 'Unauthorized']);
}

    
}