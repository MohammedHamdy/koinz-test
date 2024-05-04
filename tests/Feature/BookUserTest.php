<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BookUser;

class BookUserTest extends TestCase
{

    public function testIndex(): void
    {
        $response = $this->getJson('/api/book-user');
        $response->assertStatus(200);
    }

    public function testCreate(): void
    {
        $bookUser = [
            "book_id" => 1,
            "user_id" => 1,
            "start_page" => 1,
            "end_page" => 50
        ];
        $response = $this->post('/api/book-user',$bookUser);
        $response->assertStatus(200);
    }    
}
