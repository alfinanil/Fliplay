<?php

declare(strict_types=1);

namespace FlipPlay\Models;

class Drama extends Media
{
    public function __construct(
        string $id,
        string $title,
        array $genres,
        float $score,
        private int $episodes,
        private string $country,
    ) {
        parent::__construct($id, $title, $genres, $score);
    }

    public function getType(): string
    {
        return 'Drama';
    }

    public function consume(): string
    {
        return "Menonton drama \"{$this->getTitle()}\" dari {$this->country}";
    }

    public function getDetail(): string
    {
        return parent::getDetail() . PHP_EOL
            . "   Episode: {$this->episodes} | Negara: {$this->country}";
    }
}
