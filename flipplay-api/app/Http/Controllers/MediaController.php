<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /** GET /api/media?judul=&jenis=&genre= */
    public function index(Request $request)
    {
        $media = Media::query()
            ->withCount('pengguna')
            ->cari($request->query('judul'))
            ->jenis($request->query('jenis'))
            ->genre($request->query('genre'))
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return MediaResource::collection($media);
    }

    /** GET /api/media/{media} — detail beserta pengguna yang mengoleksinya */
    public function show(Media $media)
    {
        return new MediaResource($media->load('pengguna'));
    }

    /** POST /api/media */
    public function store(StoreMediaRequest $request): JsonResponse
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))->response()->setStatusCode(201);
    }

    /** PATCH /api/media/{media} */
    public function update(StoreMediaRequest $request, Media $media)
    {
        $media->update($request->validated());

        return new MediaResource($media);
    }

    /** DELETE /api/media/{media} — baris pivot ikut terhapus (cascadeOnDelete di migration) */
    public function destroy(Media $media): JsonResponse
    {
        $media->delete();

        return response()->json(['message' => 'Media berhasil dihapus']);
    }
}
