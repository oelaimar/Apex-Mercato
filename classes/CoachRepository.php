<?php
require_once __DIR__ . "/../db_connect.php";
require_once "RepositoryInterface.php";
require_once "Coach.php";

class CoachRepository implements RepositoryInterface
{

    private PDO $pdo = Database::getInstance()->getConnection();

    public function save(object $entity): bool
    {
        if (!$entity instanceof Coach) {
            throw new InvalidArgumentException("the entity must be instance of Coach");
        }
        $sql = "INSERT INTO couches (type, name, email, nationality) VALUES (?, ?, ?, ?);";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$entity->getType(), $entity->getName(), $entity->getEmail(), $entity->getNationality()]);
        $persons_id = $this->pdo->lastInsertId();

        $sql = "INSERT INTO coaches (persons_id, coaching_style, year_of_experience) VALUES (?, ?, ?);";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([$persons_id, $entity->getCoachingStyle(), $entity->getYearsExperience()]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM coaches WHERE persons_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function findById(int $id): ?object
    {
        $sql = "SELECT * FROM coaches c JOIN persons P ON c.persons_id = P.id WHERE c.persons_id = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        return new Coach($data['name'], $data['email'], $data['nationality'], $data['style_coaching'], $data['year_of_experience']);
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM coaches JOIN persons ON coaches.persons_id = persons.id;";
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Coach) {
            throw new InvalidArgumentException("the entity must be instance of Coach");
        }
        $stmt = $this->pdo->prepare(
            "UPDATE coaches
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
             SET style_coaching = ?, year_of_experience = ?
             WHERE persons_id = ?;"
        );
        $stmt->execute([
            $entity->getCoachingStyle(),
            $entity->getYearsExperience(),
            $entity->getId()
        ]);
        return $this->pdo->commit();
    }
}
