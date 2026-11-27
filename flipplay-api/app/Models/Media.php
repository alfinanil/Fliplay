<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HasFactory;

    public const JENIS = ['film', 'series', 'musik', 'game', 'podcast'];

    // Dituliskan eksplisit karena Laravel akan menebak nama tabel dari bentuk jamak "Media"
    protected $table = 'media';

    protected $fillable = ['judul', 'jenis', 'genre', 'tahun_rilis', 'deskripsi'];

    protected $casts = [
        'tahun_rilis' => 'integer',
    ];

    /** Mutator: judul selalu disimpan tanpa spasi berlebih di awal/akhir. */
    protected function judul(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => trim($value),
        );
    }

    /** Relasi many-to-many: satu media bisa dikoleksi banyak pengguna. */
    public function pengguna(): BelongsToMany
    {
        return $this->belongsToMany(Pengguna::class, 'media_pengguna', 'media_id', 'pengguna_id')
            ->withTimestamps();
    }

    // ----- Query scope (dipakai untuk filter di endpoint index) -----
    public function scopeCari(Builder $query, ?string $judul): void
    {
        if ($judul) {
            $query->where('judul', 'like', '%'.$judul.'%');
        }
    }

    public function scopeJenis(Builder $query, ?string $jenis): void
    {
        if ($jenis) {
            $query->where('jenis', $jenis);
        }
    }

    public function scopeGenre(Builder $query, ?string $genre): void
    {
        if ($genre) {
            $query->where('genre', $genre);
        }
    }
}
