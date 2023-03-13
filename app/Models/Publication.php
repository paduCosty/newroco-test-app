<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'summary',
        'type',
        'file_path',
    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function article()
    {
        return $this->hasOne(Article::class);
    }

    public function clientReport()
    {
        return $this->hasOne(ClientReport::class);
    }

    public function monograph()
    {
        return $this->hasOne(Monograph::class);
    }
}
