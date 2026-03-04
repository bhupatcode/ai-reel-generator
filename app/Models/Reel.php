<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'topic',
        'mood',
        'duration',
        'script',
        'scenes',
        'captions',
        'music',
        'raw_response',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'script' => 'array',
            'scenes' => 'array',
            'captions' => 'array',
            'duration' => 'integer',
        ];
    }
}
