<?php


abstract class Person
{
    protected $pdo = Database::getInstance()->getConnection();

    protected int $id;
    protected string $type, $name, $email, $nationality, $create_at;
    private ?int $contractId;

    public function __construct(
        string $name,
        string $email,
        string $nationality,
        ?int $contractId = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->nationality = $nationality;
        $this->contractId = $contractId;
    }
    
    abstract public function getAnnualCost(): float;
    abstract public function getById(int $id);

    // ===== GETTERS =====
    
    public function getId(): int {
        return $this->id;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getEmail(): string {
        return $this->email;
    }
    
    public function getNationality(): string {
        return $this->nationality;
    }
    
    public function getContractId(): ?int {
        return $this->contractId;
    }

    // ===== SETTERS =====
    
    public function setId(int $id): void {
        $this->id = $id;
    }
    
    public function setName(string $name): void {
        if (empty(trim($name))) {
            throw new InvalidArgumentException("the name should not be empty");
        }
        $this->name = $name;
    }
    
    public function setEmail(string $email): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email invalide");
        }
        $this->email = $email;
    }
    
    public function setNationality(string $nationality): void {
        $this->nationality = $nationality;
    }
    
    public function setContractId(?int $contractId): void {
        $this->contractId = $contractId;
    }
}