<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'project_name',
    ];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }
}
