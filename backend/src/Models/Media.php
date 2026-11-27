<?php

declare(strict_types=1);

namespace FlipPlay\Models;

use InvalidArgumentException;

/**
 * Media = parent class (abstract) untuk semua konten FlipPlay.
 * Anime, Drama, Manga, dan LightNovel mewarisi class ini (inheritance).
 */
abstract class Media
{
    // Encapsulation: property dibuat private, diakses lewat getter/setter
    private string $id;
    private string $title;
    private array $genres;
    private float $score;

    // Constructor biasa (assign satu per satu)
    public function __construct(string $id, string $title, array $genres, float $score)
    {
        $this->id     = $id;
        $this->title  = $title;
        $this->genres = $genres;
        $this->setScore($score); // lewat setter supaya nilai divalidasi
    }

    // ===== Getter =====
    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    // ===== Setter dengan validasi (encapsulation) =====
    public function setScore(float $score): void
    {
        if ($score < 0 || $score > 10) {
            throw new InvalidArgumentException("Skor harus di antara 0 dan 10, diberikan: {$score}");
        }
        $this->score = $score;
    }

    // ===== Method abstract: WAJIB di-implementasi oleh child class =====
    abstract public function getType(): string;

    abstract public function consume(): string;

    // ===== Method biasa yang bisa di-override child class =====

    // Aura dipetakan dari genre (sama seperti The Vibe Grid di Modul 2)
    public function getAura(): string
    {
        $genres = array_map('strtolower', $this->genres);

        if (array_intersect($genres, ['action', 'adventure', 'sports'])) {
            return 'High Energy';
        }

        if (array_intersect($genres, ['drama', 'romance', 'tragedy', 'psychological'])) {
            return 'Melancholy';
        }

        return 'Chill';
    }

    public function getDetail(): string
    {
        $genres = implode(', ', $this->genres);

        return "[{$this->getType()}] {$this->title}" . PHP_EOL
            . "   Genre  : {$genres}" . PHP_EOL
            . "   Skor   : " . number_format($this->score, 1) . PHP_EOL
            . "   Aura   : {$this->getAura()}";
    }
}
