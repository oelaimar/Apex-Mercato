<?php
require_once "person.php";

class Player extends Person
{
    private int $persons_id;
    private string $nickname, $role;
    private float $market_value;
    private float $salary;

    public function __construct(
        string $name,
        string $email,
        string $nationality,
        string $nickname,
        string $role,
        float $market_value,
        float $salary = 0.0,
        ?int $contractId = null
    ) {
        parent::__construct($name, $email, $nationality, $contractId);
        
        $this->nickname = $nickname;
        $this->role = $role;
        $this->setMarketValue($market_value);
        $this->setSalary($salary);
    }

    // ===== GETTERS =====
    
    public function getAnnualCost(): float {
        return $this->salary * 12;
    }

    public function getById(int $id)
    {
        $sql = "SELECT * FROM persons JOIN players ON players.persons_id = persons.id WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getNickname(): string {
        return $this->nickname;
    }
    
    public function getRole(): string {
        return $this->role;
    }
    
    public function getMarketValue(): float {
        return $this->market_value;
    }
    
    public function getSalary(): float {
        return $this->salary;
    }

    // ===== SETTERS =====

    public function setPseudo(string $nickname): void {
        if (strlen($nickname) < 3) {
            throw new InvalidArgumentException("the nickname should be more than 3 characrters");
        }
        $this->nickname = $nickname;
    }
    
    public function setRole(string $role): void {      
        $this->role = $role;
    }
    
    public function setMarketValue(float $market_value): void {
        if ($market_value < 0) {
            throw new InvalidArgumentException("the market value should be positive");
        }
        $this->market_value = $market_value;
    }
    
    public function setSalary(float $salary): void {
        if ($salary < 0) {
            throw new InvalidArgumentException("the salary should be positive");
        }
        $this->salary = $salary;
    }



    public function savePlayer(string $name, string $email, string $nationality, string $nickname, string $role, float $market_value): bool
    {
        $this->name = $name;
        $this->email = $email;
        $this->nationality = $nationality;

        $this->persons_id = $this->savePerson($this->type = "player", $this->name, $this->email, $this->nationality);
        $this->nickname = $nickname;
        $this->role = $role;
        $this->market_value = $market_value;

        $sql = "INSERT INTO players (persons_id, nickname, role, market_value) VALUES (?, ?, ?, ?);";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$this->persons_id, $this->nickname, $this->role, $this->market_value]);
    }
}