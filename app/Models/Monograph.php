<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monograph extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'number_of_pages',
    ];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }
}
