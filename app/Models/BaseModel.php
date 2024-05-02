<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function getCreatedAtAttribute(): string
    {
        return date('Y-m-d h:i A', strtotime($this->attributes['created_at']));
    }

    public function getUpdatedAtAttribute(): string
    {
        return date('Y-m-d h:i A', strtotime($this->attributes['updated_at']));
    }
}
