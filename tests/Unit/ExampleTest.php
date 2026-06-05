<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Auth\User;
use Carbon\Carbon;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que verifica que un usuario puede ser creado.
     */
    public function test_user_can_be_created()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'email_verified' => 1,
            'status' => 1,
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    /**
     * Test que verifica que el PIN está bloqueado después de 5 intentos fallidos.
     */
    public function test_pin_is_locked_after_5_failed_attempts()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'pin_failed_attempts' => 0,
        ]);

        // Simular 5 intentos fallidos
        for ($i = 0; $i < 5; $i++) {
            $user->recordFailedPinAttempt();
        }

        $this->assertTrue($user->isPinLocked());
        $this->assertEquals(5, $user->pin_failed_attempts);
        $this->assertNotNull($user->pin_locked_at);
    }

    /**
     * Test que verifica que el PIN no está bloqueado con menos de 5 intentos.
     */
    public function test_pin_is_not_locked_with_less_than_5_attempts()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'pin_failed_attempts' => 0,
        ]);

        // Simular 3 intentos fallidos
        for ($i = 0; $i < 3; $i++) {
            $user->recordFailedPinAttempt();
        }

        $this->assertFalse($user->isPinLocked());
        $this->assertEquals(3, $user->pin_failed_attempts);
    }

    /**
     * Test que verifica que los intentos de PIN se pueden resetear.
     */
    public function test_pin_attempts_can_be_reset()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'pin_failed_attempts' => 5,
            'pin_locked_at' => Carbon::now(),
        ]);

        $user->resetPinAttempts();

        $this->assertEquals(0, $user->pin_failed_attempts);
        $this->assertNull($user->pin_locked_at);
    }

    /**
     * Test que verifica que el PIN se desbloquea después de 15 minutos.
     */
    public function test_pin_unlocks_after_15_minutes()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'pin_failed_attempts' => 5,
            'pin_locked_at' => Carbon::now()->subMinutes(16),
        ]);

        $this->assertFalse($user->isPinLocked());
    }

    /**
     * Test que verifica los atributos ocultos del usuario.
     */
    public function test_user_hidden_attributes()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret_password',
            'remember_token' => 'token123',
            'pin_hash' => 'pin123',
        ]);

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
        $this->assertArrayNotHasKey('pin_hash', $array);
    }

    /**
     * Test que verifica que must_change_pin se castea correctamente a boolean.
     */
    public function test_must_change_pin_cast_to_boolean()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'must_change_pin' => 1,
        ]);

        $this->assertTrue(is_bool($user->must_change_pin));
        $this->assertTrue($user->must_change_pin);
    }
}
