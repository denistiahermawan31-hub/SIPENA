<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_utama_mengarah_ke_login(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
        $this->followingRedirects()->get('/')
            ->assertSee('Masuk')
            ->assertSee('SIPEN');
    }

    public function test_gagal_login_dengan_username_salah(): void
    {
        $this->post('/login', ['username' => 'x1', 'password' => 'password'])
            ->assertSessionHasErrors('username');
    }

    public function test_perwakilan_bisa_login(): void
    {
        $class = ClassRoom::create(['name' => 'X.1', 'level' => 'X']);

        $user = User::create([
            'name' => 'Perwakilan X.1',
            'username' => 'x1',
            'password' => 'password',
            'class_id' => $class->id,
            'role' => 'user',
        ]);

        $response = $this->post('/login', ['username' => 'x1', 'password' => 'password']);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('X.1');
    }

    public function test_harus_login_untuk_membuka_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
