<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'jenis' => $this->jenis,
            'genre' => $this->genre,
            'tahun_rilis' => $this->tahun_rilis,
            'deskripsi' => $this->deskripsi,
            // hanya muncul jika dipanggil dengan withCount('pengguna')
            'jumlah_pengguna' => $this->whenCounted('pengguna'),
            // hanya muncul jika relasi sudah di-load (eager loading)
            'pengguna' => PenggunaResource::collection($this->whenLoaded('pengguna')),
            // hanya muncul saat media diambil lewat koleksi pengguna
            'ditambahkan_pada' => $this->whenPivotLoaded('media_pengguna', fn () => (string) $this->pivot->created_at),
            'dibuat_pada' => (string) $this->created_at,
        ];
    }
}
