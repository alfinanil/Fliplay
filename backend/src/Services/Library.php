<?php

declare(strict_types=1);

namespace FlipPlay\Services;

use FlipPlay\Models\Media;
use InvalidArgumentException;

class Library
{
    // Semua media disimpan di sini (private), diakses lewat method
    private array $mediaList = [];

    public function addMedia(Media $media): void
    {
        if ($this->findById($media->getId()) !== null) {
            throw new InvalidArgumentException("Media dengan ID {$media->getId()} sudah ada di library");
        }

        $this->mediaList[] = $media;
    }

    public function count(): int
    {
        return count($this->mediaList);
    }

    public function getAll(): array
    {
        return $this->mediaList;
    }

    public function findById(string $id): ?Media
    {
        foreach ($this->mediaList as $media) {
            if ($media->getId() === $id) {
                return $media;
            }
        }

        return null;
    }

    // Cari berdasarkan judul (tidak peduli huruf besar/kecil, cukup sebagian kata)
    public function searchByTitle(string $keyword): array
    {
        $keyword = strtolower(trim($keyword));

        return array_values(array_filter(
            $this->mediaList,
            fn (Media $media): bool => str_contains(strtolower($media->getTitle()), $keyword)
        ));
    }

    public function filterByAura(string $aura): array
    {
        return array_values(array_filter(
            $this->mediaList,
            fn (Media $media): bool => $media->getAura() === $aura
        ));
    }

    public function listMedia(): void
    {
        if ($this->count() === 0) {
            echo "  (library masih kosong)" . PHP_EOL;
            return;
        }

        $no = 1;
        foreach ($this->mediaList as $media) {
            echo sprintf(
                "  %d. %-30s %-12s * %.1f  [%s]",
                $no,
                $media->getTitle(),
                $media->getType(),
                $media->getScore(),
                $media->getAura()
            ) . PHP_EOL;
            $no++;
        }
    }
}
