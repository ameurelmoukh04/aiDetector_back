<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    use HasFactory;
    protected $table = 'texts';
    protected $fillable = ['user_id', 'content', 'result'];
    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
