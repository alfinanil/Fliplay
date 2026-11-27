# FlipPlay REST API (Modul 4 - Pemrograman Web)

Laravel 12 • MySQL • Eloquent ORM • Form Request • API Resource

## Entitas & relasi
- `Media` (judul, jenis, genre, tahun_rilis, deskripsi)
- `Pengguna` (nama, email)
- Pivot `media_pengguna` -> relasi **belongsToMany** (koleksi media milik pengguna), unique(media_id, pengguna_id)

## Endpoint (prefix `/api`)
| Method | URL | Keterangan | Status |
|---|---|---|---|
| GET | /media?judul=&jenis=&genre= | daftar media + filter + pagination | 200 |
| GET | /media/{id} | detail media + pengguna pengoleksi | 200 / 404 |
| POST | /media | tambah media | 201 / 422 |
| PATCH | /media/{id} | ubah media | 200 / 422 / 404 |
| DELETE | /media/{id} | hapus media | 200 / 404 |
| GET/POST/PATCH/DELETE | /pengguna ... | CRUD pengguna | idem |
| GET | /pengguna/{id}/koleksi | lihat koleksi | 200 |
| POST | /pengguna/{id}/koleksi `{media_id}` | tambah ke koleksi | 201 / 409 / 422 |
| DELETE | /pengguna/{id}/koleksi/{media_id} | hapus dari koleksi | 200 / 404 |

## Pemetaan ke ketentuan demo
- Migration + pivot: `database/migrations/*`
- CRUD standar Media: `MediaController`
- Form Request: `StoreMediaRequest` (judul wajib & unik, beda aturan POST/PATCH), `StorePenggunaRequest`, `AddToCollectionRequest`
- API Resource + `whenLoaded`: `MediaResource`, `PenggunaResource`
- Custom action (attach/detach): `PenggunaController@addToCollection` / `@removeFromCollection`
- Query scope & mutator: `Media::scopeCari`, `Media::judul()`
- Race condition: `DB::transaction()` + `lockForUpdate()` di `addToCollection`
- Pengujian: import `postman/FlipPlay.postman_collection.json` ke Postman
