<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRedirectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_page()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_is_redirected_from_login_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertStatus(302);
        $response->assertRedirect('/home');
    }
    
    public function test_authenticated_user_is_redirected_from_register_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/registrar');

        $response->assertStatus(302);
        $response->assertRedirect('/home');
    }
}
