<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookUser extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'book_user';

    /**
     * @var array
     */
    protected $fillable = [
        'book_id',
        'user_id',
        'start_page',
        'end_page',

    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
