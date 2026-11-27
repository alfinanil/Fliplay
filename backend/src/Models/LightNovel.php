<?php

declare(strict_types=1);

namespace FlipPlay\Models;

class LightNovel extends Media
{
    public function __construct(
        string $id,
        string $title,
        array $genres,
        float $score,
        private int $volumes,
    ) {
        parent::__construct($id, $title, $genres, $score);
    }

    public function getType(): string
    {
        return 'Light Novel';
    }

    public function consume(): string
    {
        return "Membaca light novel \"{$this->getTitle()}\" ({$this->volumes} volume)";
    }

    public function getDetail(): string
    {
        return parent::getDetail() . PHP_EOL . "   Volume : {$this->volumes}";
    }
}
