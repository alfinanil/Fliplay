<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pengguna extends Model
{
    use HasFactory;

    protected $table = 'pengguna';

    protected $fillable = ['nama', 'email'];

    /** Relasi many-to-many: satu pengguna bisa mengoleksi banyak media. */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_pengguna', 'pengguna_id', 'media_id')
            ->withTimestamps();
    }
}
