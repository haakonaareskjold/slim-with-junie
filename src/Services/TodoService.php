<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Todo;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

final class TodoService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->initializeSchema();
    }

    private function initializeSchema(): void
    {
        $tool = new SchemaTool($this->entityManager);
        $classes = [
            $this->entityManager->getClassMetadata(Todo::class),
        ];

        // In a real application, you'd use migrations.
        // For this refactor, we keep the auto-initialization behavior.
        // updateSchema is safe to call if schema is already up to date.
        $tool->updateSchema($classes, true);
    }

    /** @return list<array<string, mixed>> */
    public function all(): array
    {
        $repository = $this->entityManager->getRepository(Todo::class);
        $todos = $repository->findBy([], ['id' => 'ASC']);

        return array_map(fn(Todo $todo) => $todo->toArray(), $todos);
    }

    public function find(int $id): ?array
    {
        $todo = $this->entityManager->find(Todo::class, $id);
        return $todo?->toArray();
    }

    public function create(string $title, bool $done = false): array
    {
        $todo = new Todo($title, $done);
        $this->entityManager->persist($todo);
        $this->entityManager->flush();

        return $todo->toArray();
    }

    public function update(int $id, ?string $title, ?bool $done): ?array
    {
        $todo = $this->entityManager->find(Todo::class, $id);
        if ($todo === null) {
            return null;
        }

        if ($title !== null) {
            $todo->setTitle($title);
        }
        if ($done !== null) {
            $todo->setDone($done);
        }

        $this->entityManager->flush();

        return $todo->toArray();
    }

    public function delete(int $id): bool
    {
        $todo = $this->entityManager->find(Todo::class, $id);
        if ($todo === null) {
            return false;
        }

        $this->entityManager->remove($todo);
        $this->entityManager->flush();

        return true;
    }
}
