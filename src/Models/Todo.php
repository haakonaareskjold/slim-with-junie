<?php
declare(strict_types=1);

namespace App\Models;

final class Todo
{
    public function __construct(
        public int $id,
        public string $title,
        public bool $done = false,
    ) {}

    public function toArray(): array
    {
        return ['id' => $this->id, 'title' => $this->title, 'done' => $this->done];
    }
}
