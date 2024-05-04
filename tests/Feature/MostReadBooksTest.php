<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MostReadBooksTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_most_read_books(): void
    {
        $response = $this->getJson('/api/book-user/most-read-books');
        $response->assertStatus(200);
    }
}
