<?php
require_once __DIR__ . '/../autoload.php';

class CoachRepository implements RepositoryInterface
{

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function save(object $entity): bool
    {
        if (!$entity instanceof Coach) {
            throw new InvalidArgumentException("the entity must be instance of Coach");
        }

        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO persons (type, name, email, nationality) VALUES (?, ?, ?, ?);";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$entity->getType(), $entity->getName(), $entity->getEmail(), $entity->getNationality()]);
            $persons_id = $this->pdo->lastInsertId();

            $sql = "INSERT INTO coaches (persons_id, coaching_style, years_of_experience) VALUES (?, ?, ?);";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$persons_id, $entity->getCoachingStyle(), $entity->getYearsExperience()]);

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
        $sql = "SELECT * FROM coaches c JOIN persons ps ON c.persons_id = ps.id WHERE c.persons_id = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }

        $coach = new Coach($data['name'], $data['email'], $data['nationality'], $data['coaching_style'], $data['years_of_experience']);
        $coach->setId($data['id']);
        return $coach;
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM coaches c JOIN persons ps ON c.persons_id = ps.id;";
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Coach) {
            throw new InvalidArgumentException("the entity must be instance of Coach");
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
                "UPDATE coaches
                 SET coaching_style = ?, years_of_experience = ?
                 WHERE persons_id = ?;"
            );
            $stmt->execute([
                $entity->getCoachingStyle(),
                $entity->getYearsExperience(),
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
