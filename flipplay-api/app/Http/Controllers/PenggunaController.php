<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCollectionRequest;
use App\Http\Requests\StorePenggunaRequest;
use App\Http\Resources\PenggunaResource;
use App\Models\Media;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PenggunaController extends Controller
{
    /** GET /api/pengguna */
    public function index()
    {
        return PenggunaResource::collection(
            Pengguna::withCount('media')->orderBy('id')->paginate(10)
        );
    }

    /** GET /api/pengguna/{pengguna} */
    public function show(Pengguna $pengguna)
    {
        return new PenggunaResource($pengguna->loadCount('media'));
    }

    /** POST /api/pengguna */
    public function store(StorePenggunaRequest $request): JsonResponse
    {
        $pengguna = Pengguna::create($request->validated());

        return (new PenggunaResource($pengguna))->response()->setStatusCode(201);
    }

    /** PATCH /api/pengguna/{pengguna} */
    public function update(StorePenggunaRequest $request, Pengguna $pengguna)
    {
        $pengguna->update($request->validated());

        return new PenggunaResource($pengguna);
    }

    /** DELETE /api/pengguna/{pengguna} */
    public function destroy(Pengguna $pengguna): JsonResponse
    {
        $pengguna->delete();

        return response()->json(['message' => 'Pengguna berhasil dihapus']);
    }

    // ------------------------------------------------------------------
    // Custom action di luar CRUD standar (mengubah relasi many-to-many)
    // ------------------------------------------------------------------

    /** GET /api/pengguna/{pengguna}/koleksi — daftar media milik pengguna */
    public function koleksi(Pengguna $pengguna)
    {
        return new PenggunaResource($pengguna->load('media')->loadCount('media'));
    }

    /**
     * POST /api/pengguna/{pengguna}/koleksi  body: { "media_id": 1 }
     *
     * Pola "baca kondisi -> validasi -> ubah" sehingga dibungkus DB::transaction()
     * + lockForUpdate() agar dua request bersamaan tidak sama-sama lolos cek duplikat
     * (lihat Modul 4 bagian 4.4). Unique index pada pivot menjadi pengaman terakhir.
     */
    public function addToCollection(AddToCollectionRequest $request, Pengguna $pengguna)
    {
        return DB::transaction(function () use ($request, $pengguna) {
            $terkunci = Pengguna::whereKey($pengguna->getKey())->lockForUpdate()->firstOrFail();

            $sudahAda = $terkunci->media()
                ->wherePivot('media_id', $request->media_id)
                ->exists();

            if ($sudahAda) {
                return response()->json(['message' => 'Media sudah ada di koleksi'], 409);
            }

            $terkunci->media()->attach($request->media_id);

            return (new PenggunaResource($terkunci->load('media')->loadCount('media')))
                ->additional(['message' => 'Media berhasil ditambahkan ke koleksi'])
                ->response()
                ->setStatusCode(201);
        });
    }

    /** DELETE /api/pengguna/{pengguna}/koleksi/{media} */
    public function removeFromCollection(Pengguna $pengguna, Media $media): JsonResponse
    {
        $adaDiKoleksi = $pengguna->media()->wherePivot('media_id', $media->id)->exists();

        if (! $adaDiKoleksi) {
            return response()->json(['message' => 'Media tidak ada di koleksi pengguna'], 404);
        }

        $pengguna->media()->detach($media->id);

        return response()->json(['message' => 'Media berhasil dihapus dari koleksi']);
    }
}
