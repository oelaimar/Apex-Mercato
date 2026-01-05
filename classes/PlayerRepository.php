<?php
require_once __DIR__ . "/../db_connect.php";
require_once "RepositoryInterface.php";
require_once "player.php";

class PlayerRepository implements RepositoryInterface
{

    private PDO $pdo = Database::getInstance()->getConnection();

    public function save(object $entity): bool
    {
        if (!$entity instanceof Player) {
            throw new InvalidArgumentException("the entity must be instance of Player");
        }
        $sql = "INSERT INTO persons (type, name, email, nationality) VALUES (?, ?, ?, ?);";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$entity->getType(), $entity->getName(), $entity->getEmail(), $entity->getNationality()]);
        $persons_id = $this->pdo->lastInsertId();

        $sql = "INSERT INTO players (persons_id, nickname, role, market_value) VALUES (?, ?, ?, ?);";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([$persons_id, $entity->getNickname(), $entity->getRole(), $entity->getMarketValue()]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM players WHERE persons_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function findById(int $id): ?object
    {
        $sql = "SELECT * FROM players p JOIN persons P ON p.persons_id = P.id WHERE p.persons_id = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        return new Player($data['name'], $data['email'], $data['nationality'], $data['nickname'], $data['role'], $data['market_value']);
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM players JOIN persons ON players.persons_id = persons.id;";
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Player) {
            throw new InvalidArgumentException("the entity must be instance of Player");
        }
        $$stmt = $this->pdo->prepare(
            "UPDATE persons
             SET type = ?, name = ?, email = ?, nationality = ?
             WHERE id = ?;"
        );
        $stmt->execute([
            $entity->getType(),
            $entity->getName(),
            $entity->getEmail(),
            $entity->getNationality(),
            $entity->getId()
        ]);

        $stmt = $this->pdo->prepare(
            "UPDATE players
             SET nickname = ?, role = ?, market_value = ?
             WHERE persons_id = ?;"
        );
        $stmt->execute([
            $entity->getNickname(),
            $entity->getRole(),
            $entity->getMarketValue(),
            $entity->getId()
        ]);
        return $this->pdo->commit();
    }
}
