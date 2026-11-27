<?php

declare(strict_types=1);

namespace FlipPlay\Models;

class Anime extends Media
{
    // Constructor Property Promotion (PHP 8): property langsung dideklarasikan di parameter
    public function __construct(
        string $id,
        string $title,
        array $genres,
        float $score,
        private int $episodes,
    ) {
        parent::__construct($id, $title, $genres, $score);
    }

    public function getEpisodes(): int
    {
        return $this->episodes;
    }

    public function getType(): string
    {
        return 'Anime';
    }

    // Override: anime panjang (>= 24 episode) masuk Binge Mode
    public function getAura(): string
    {
        if ($this->episodes >= 24) {
            return 'Binge Mode';
        }

        return parent::getAura();
    }

    public function consume(): string
    {
        return "Menonton anime \"{$this->getTitle()}\" ({$this->episodes} episode)";
    }

    public function getDetail(): string
    {
        return parent::getDetail() . PHP_EOL . "   Episode: {$this->episodes}";
    }
}
