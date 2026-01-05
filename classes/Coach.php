<?php
require_once "person.php";

class Coach extends Person
{
    private int $persons_id, $year_of_experience;
    private string $style_coaching;
    private float $salary;

    public function __construct(
        string $name,
        string $email,
        string $nationality,
        string $style_coaching,
        int $year_of_experience,
        float $salary = 0.0,
        ?int $contractId = null
    ) {
        parent::__construct($name, $email, $nationality, $contractId);

        $this->style_coaching = $style_coaching;
        $this->setYearsExperience($year_of_experience);
        $this->setSalary($salary);
        $this->setType();
    }

    // ===== GETTERS =====

    public function getAnnualCost(): float
    {
        return $this->salary * 12;
    }

    public function getById(int $id)
    {
        $sql = "SELECT * FROM coachs JOIN players ON players.persons_id = persons.id WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getCoachingStyle(): string
    {
        return $this->style_coaching;
    }

    public function getYearsExperience(): int
    {
        return $this->year_of_experience;
    }

    public function getSalary(): float
    {
        return $this->salary;
    }
    public function getType(): string
    {
        return $this->type;
    }

    // ===== SETTERS =====

    public function setCoachingStyle(string $style_coaching): void
    {
        if (empty(trim($style_coaching))) {
            throw new InvalidArgumentException("the coaching style should not be empty");
        }
        $this->style_coaching = $style_coaching;
    }

    public function setYearsExperience(int $year_of_experience): void
    {
        if ($year_of_experience < 0) {
            throw new InvalidArgumentException("experience should be positive");
        }
        $this->year_of_experience = $year_of_experience;
    }

    public function setSalary(float $salary): void
    {
        if ($salary < 0) {
            throw new InvalidArgumentException("the salary should be positive");
        }
        $this->salary = $salary;
    }

     public function setType(){
        $this->type = 'coach';
    }
}
