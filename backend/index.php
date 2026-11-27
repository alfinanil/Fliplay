<?php

declare(strict_types=1);

/**
 * FlipPlay - Prototype Backend Engine (PHP OOP)
 * Entry point simulasi. Jalankan lewat terminal:  php index.php
 */

require_once 'src/Models/Media.php';
require_once 'src/Models/Anime.php';
require_once 'src/Models/Drama.php';
require_once 'src/Models/Manga.php';
require_once 'src/Models/LightNovel.php';
require_once 'src/Models/User.php';
require_once 'src/Services/Library.php';

use FlipPlay\Models\Anime;
use FlipPlay\Models\Drama;
use FlipPlay\Models\LightNovel;
use FlipPlay\Models\Manga;
use FlipPlay\Models\Media;
use FlipPlay\Models\User;
use FlipPlay\Services\Library;

function printSection(string $title): void
{
    echo PHP_EOL . str_repeat('=', 60) . PHP_EOL;
    echo $title . PHP_EOL;
    echo str_repeat('=', 60) . PHP_EOL;
}

function printResults(array $results): void
{
    if (count($results) === 0) {
        echo "  Tidak ada media yang cocok." . PHP_EOL;
        return;
    }

    foreach ($results as $media) {
        echo "  - {$media->getTitle()} [{$media->getType()}]" . PHP_EOL;
    }
}

// ---------------------------------------------------------------
// 1. Menambahkan media ke dalam library
// ---------------------------------------------------------------
printSection('1. MENAMBAHKAN MEDIA KE LIBRARY');

$library = new Library();

$library->addMedia(new Anime('A01', 'One Piece', ['Action', 'Adventure'], 8.7, 1100));
$library->addMedia(new Anime('A02', 'Violet Evergarden', ['Drama', 'Fantasy'], 8.6, 13));
$library->addMedia(new Anime('A03', 'Haikyuu!!', ['Sports', 'Comedy'], 8.5, 25));
$library->addMedia(new Drama('D01', 'Crash Landing on You', ['Romance', 'Comedy'], 8.7, 16, 'Korea Selatan'));
$library->addMedia(new Drama('D02', 'Reply 1988', ['Drama', 'Family'], 9.0, 20, 'Korea Selatan'));
$library->addMedia(new Manga('M01', 'Vagabond', ['Action', 'Drama'], 9.2, 327));
$library->addMedia(new Manga('M02', 'Yotsuba&!', ['Comedy', 'Slice of Life'], 8.9, 110));
$library->addMedia(new LightNovel('L01', 'Sword Art Online', ['Action', 'Adventure'], 7.5, 27));
$library->addMedia(new LightNovel('L02', 'Kimi no Na wa.', ['Romance', 'Drama'], 8.4, 3));

echo "Berhasil menambahkan {$library->count()} media ke library FlipPlay." . PHP_EOL;

// Library menolak ID yang sama (validasi + exception)
try {
    $library->addMedia(new Manga('M01', 'Duplikat', ['Action'], 5.0, 1));
} catch (InvalidArgumentException $e) {
    echo "Gagal menambahkan: {$e->getMessage()}" . PHP_EOL;
}

// ---------------------------------------------------------------
// 2. Menampilkan daftar media yang tersedia
// ---------------------------------------------------------------
printSection('2. DAFTAR MEDIA YANG TERSEDIA');
$library->listMedia();

// ---------------------------------------------------------------
// 3. Mencari media berdasarkan judul
// ---------------------------------------------------------------
printSection('3. CARI MEDIA BERDASARKAN JUDUL');

$keyword = 'o';
echo "Kata kunci \"{$keyword}\":" . PHP_EOL;
printResults($library->searchByTitle($keyword));

$keyword = 'violet';
echo PHP_EOL . "Kata kunci \"{$keyword}\":" . PHP_EOL;
printResults($library->searchByTitle($keyword));

$keyword = 'naruto';
echo PHP_EOL . "Kata kunci \"{$keyword}\":" . PHP_EOL;
printResults($library->searchByTitle($keyword));

// ---------------------------------------------------------------
// 4. Menampilkan informasi detail suatu media (polymorphism)
// ---------------------------------------------------------------
printSection('4. INFORMASI DETAIL MEDIA');

foreach (['A01', 'D02', 'M01', 'L01'] as $id) {
    $media = $library->findById($id);

    if ($media instanceof Media) {
        // getDetail() dipanggil sama, hasilnya berbeda tiap jenis media
        echo $media->getDetail() . PHP_EOL . PHP_EOL;
    }
}

// ---------------------------------------------------------------
// 5. Koleksi media milik pengguna
// ---------------------------------------------------------------
printSection('5. KOLEKSI MEDIA MILIK PENGGUNA');

$user = new User('U001', 'Jessi');

$user->addToCollection($library->findById('A02'));
$user->addToCollection($library->findById('D01'));
$user->addToCollection($library->findById('M01'));
$user->addToCollection($library->findById('L02'));

// Menambahkan media yang sama dua kali ditolak
$added = $user->addToCollection($library->findById('A02'));
echo "Tambah 'Violet Evergarden' lagi: " . ($added ? 'berhasil' : 'ditolak (sudah ada di koleksi)') . PHP_EOL . PHP_EOL;

$user->showCollection();

echo PHP_EOL . "Mulai menikmati koleksi:" . PHP_EOL;
foreach ($user->getCollection() as $media) {
    echo "  > " . $media->consume() . PHP_EOL;
}

// ---------------------------------------------------------------
// Bonus: filter berdasarkan Aura (lanjutan "The Vibe Grid" Modul 2)
// ---------------------------------------------------------------
printSection('BONUS. FILTER BERDASARKAN AURA');

foreach (['High Energy', 'Melancholy', 'Binge Mode', 'Chill'] as $aura) {
    echo "Aura \"{$aura}\":" . PHP_EOL;
    printResults($library->filterByAura($aura));
    echo PHP_EOL;
}

// ---------------------------------------------------------------
// Bonus: bukti encapsulation & type hinting
// ---------------------------------------------------------------
printSection('BONUS. ENCAPSULATION & TYPE HINTING');

$anime = $library->findById('A01');

// 1) Validasi lewat setter
try {
    $anime->setScore(15.0);
} catch (InvalidArgumentException $e) {
    echo "Setter menolak nilai tidak valid -> {$e->getMessage()}" . PHP_EOL;
}

// 2) Property private tidak bisa diakses langsung dari luar class
$propertyName = 'collection';
try {
    echo $user->$propertyName;
} catch (Error $e) {
    echo "Akses langsung ditolak -> {$e->getMessage()}" . PHP_EOL;
}

// 3) Type hinting: addMedia() hanya menerima object Media
$wrongValue = json_decode('"bukan object Media"'); // sengaja bertipe string
try {
    $library->addMedia($wrongValue);
} catch (TypeError $e) {
    echo "Type hinting menolak tipe salah -> TypeError" . PHP_EOL;
}

echo PHP_EOL . "Simulasi FlipPlay selesai." . PHP_EOL;
