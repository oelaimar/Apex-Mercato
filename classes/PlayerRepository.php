<?php
require_once __DIR__ . '/../autoload.php';

class PlayerRepository implements RepositoryInterface
{

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function save(object $entity): bool
    {
        if (!$entity instanceof Player) {
            throw new InvalidArgumentException("the entity must be instance of Player");
        }

        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO persons (type, name, email, nationality) VALUES (?, ?, ?, ?);";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$entity->getType(), $entity->getName(), $entity->getEmail(), $entity->getNationality()]);
            $persons_id = $this->pdo->lastInsertId();

            $sql = "INSERT INTO players (persons_id, nickname, role, market_value) VALUES (?, ?, ?, ?);";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$persons_id, $entity->getPseudo(), $entity->getRole(), $entity->getMarketValue()]);

            return $this->pdo->commit();
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM persons WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function findById(int $id): ?object
    {
        $sql = "SELECT * FROM players p JOIN persons ps ON p.persons_id = ps.id WHERE p.persons_id = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        $player = new Player(
            $data['name'], 
            $data['email'], 
            $data['nationality'], 
            $data['nickname'], 
            $data['role'], 
            (float)$data['market_value']
        );
        $player->setId($data['id']);
        return $player;
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM players p JOIN persons ps ON p.persons_id = ps.id;";
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Player) {
            throw new InvalidArgumentException("the entity must be instance of Player");
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
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
                $entity->getPseudo(),
                $entity->getRole(),
                $entity->getMarketValue(),
                $entity->getId()
            ]);

            return $this->pdo->commit();
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
