<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenggunaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'email' => $this->email,
            'jumlah_koleksi' => $this->whenCounted('media'),
            'koleksi' => MediaResource::collection($this->whenLoaded('media')),
            // hanya muncul saat pengguna diambil lewat relasi media->pengguna
            'ditambahkan_pada' => $this->whenPivotLoaded('media_pengguna', fn () => (string) $this->pivot->created_at),
        ];
    }
}
