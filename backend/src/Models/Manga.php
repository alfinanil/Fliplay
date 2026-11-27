<?php

declare(strict_types=1);

namespace FlipPlay\Models;

class Manga extends Media
{
    public function __construct(
        string $id,
        string $title,
        array $genres,
        float $score,
        private int $chapters,
    ) {
        parent::__construct($id, $title, $genres, $score);
    }

    public function getType(): string
    {
        return 'Manga';
    }

    public function consume(): string
    {
        return "Membaca manga \"{$this->getTitle()}\" ({$this->chapters} bab)";
    }

    public function getDetail(): string
    {
        return parent::getDetail() . PHP_EOL . "   Bab    : {$this->chapters}";
    }
}
