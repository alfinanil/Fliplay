<?php

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // POST: field wajib diisi. PATCH/PUT: hanya field yang dikirim yang divalidasi.
        $wajib = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'judul' => [
                $wajib, 'string', 'max:255',
                // saat update, abaikan baris media itu sendiri agar tidak dianggap duplikat
                Rule::unique('media', 'judul')->ignore($this->route('media')),
            ],
            'jenis' => [$wajib, Rule::in(Media::JENIS)],
            'genre' => [$wajib, 'string', 'max:100'],
            'tahun_rilis' => ['nullable', 'integer', 'between:1888,'.((int) date('Y') + 1)],
            'deskripsi' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul media wajib diisi',
            'judul.unique' => 'Judul media sudah terdaftar',
            'jenis.required' => 'Jenis media wajib diisi',
            'jenis.in' => 'Jenis media harus salah satu dari: '.implode(', ', Media::JENIS),
            'genre.required' => 'Genre wajib diisi',
            'tahun_rilis.integer' => 'Tahun rilis harus berupa angka',
            'tahun_rilis.between' => 'Tahun rilis tidak valid',
        ];
    }
}
