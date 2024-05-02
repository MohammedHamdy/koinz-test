<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Book extends BaseModel
{

    use HasFactory, Notifiable;

    /**
     * @var string
     */
    protected $table = 'books';

    /**
     * @var array
     */
    protected $fillable = [
        'book_name'
    ];

}
