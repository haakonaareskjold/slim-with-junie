<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Todo;
use PDO;

final class TodoService
{
    public function __construct(private PDO $pdo)
    {
        $this->initializeSchema();
    }

    private function initializeSchema(): void
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS todos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            done INTEGER NOT NULL DEFAULT 0
        )');
    }

    /** @return list<array<string, mixed>> */
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id, title, done FROM todos ORDER BY id');
        $rows = $stmt->fetchAll();
        return array_map(fn(array $r) => [
            'id' => (int)$r['id'],
            'title' => (string)$r['title'],
            'done' => (bool)$r['done'],
        ], $rows);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, title, done FROM todos WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }
        return [
            'id' => (int)$row['id'],
            'title' => (string)$row['title'],
            'done' => (bool)$row['done'],
        ];
    }

    public function create(string $title, bool $done = false): array
    {
        $stmt = $this->pdo->prepare('INSERT INTO todos (title, done) VALUES (:title, :done)');
        $stmt->execute([':title' => $title, ':done' => $done ? 1 : 0]);
        $id = (int)$this->pdo->lastInsertId();
        return ['id' => $id, 'title' => $title, 'done' => $done];
    }

    public function update(int $id, ?string $title, ?bool $done): ?array
    {
        $existing = $this->find($id);
        if ($existing === null) {
            return null;
        }
        $newTitle = $title !== null ? $title : $existing['title'];
        $newDone = $done !== null ? $done : $existing['done'];
        $stmt = $this->pdo->prepare('UPDATE todos SET title = :title, done = :done WHERE id = :id');
        $stmt->execute([':title' => $newTitle, ':done' => $newDone ? 1 : 0, ':id' => $id]);
        return ['id' => $id, 'title' => $newTitle, 'done' => $newDone];
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM todos WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
