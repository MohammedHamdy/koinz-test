<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;

class BookTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_books(): void
    {
        $books = Book::factory(10)->create();
        $this->addToAssertionCount(10);
    }
}
