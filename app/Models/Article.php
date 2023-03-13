<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'magazine',
        'start_page',
        'end_page',
    ];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }
}
