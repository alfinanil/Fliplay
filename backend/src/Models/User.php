<?php

declare(strict_types=1);

namespace FlipPlay\Models;

class User
{
    // Koleksi pribadi pengguna; private supaya hanya bisa diubah lewat method
    private array $collection = [];

    public function __construct(
        private string $userId,
        private string $name,
    ) {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    // Menambah media ke koleksi. Return false jika media sudah ada.
    public function addToCollection(Media $media): bool
    {
        if (isset($this->collection[$media->getId()])) {
            return false;
        }

        $this->collection[$media->getId()] = $media;
        return true;
    }

    public function removeFromCollection(string $mediaId): bool
    {
        if (!isset($this->collection[$mediaId])) {
            return false;
        }

        unset($this->collection[$mediaId]);
        return true;
    }

    public function getCollection(): array
    {
        return array_values($this->collection);
    }

    public function showCollection(): void
    {
        echo "Koleksi milik {$this->name}:" . PHP_EOL;

        if (count($this->collection) === 0) {
            echo "  (koleksi masih kosong)" . PHP_EOL;
            return;
        }

        $no = 1;
        foreach ($this->collection as $media) {
            echo "  {$no}. {$media->getTitle()} [{$media->getType()}]" . PHP_EOL;
            $no++;
        }
    }
}
