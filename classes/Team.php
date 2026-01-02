<?php
class Team {
    private int $id;
    private string $name;
    private float $budget;
    private ?string $managerName;


    public function __construct(
        string $name,
        float $budget = 0.0,
        ?string $managerName = null
    ) {
        $this->setName($name);
        $this->setBudget($budget);
        $this->managerName = $managerName;
    }

    public function canAffordTransfer(float $amount): bool {
        return $this->budget >= $amount;
    }
    
    public function debitBudget(float $amount): void {
        if (!$this->canAffordTransfer($amount)) {
            throw new Exception("you can transfer with your budget");
        }
        $this->budget -= $amount;
    }

    public function creditBudget(float $amount): void {
        $this->budget += $amount;
    }
    
    public function displayInfo(): string {
        $formattedBudget = number_format($this->budget, 2, ',', ' ') . ' €';
        $manager = $this->managerName ?? 'Non défini';
        
        return "
            <div class='team-card'>
                <h2>{$this->name}</h2>
                <p><strong>Budget:</strong> {$formattedBudget}</p>
                <p><strong>Manager:</strong> {$manager}</p>
            </div>
        ";
    }
    
    // ===== GETTERS =====
    
    public function getId(): int {
        return $this->id;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getBudget(): float {
        return $this->budget;
    }
    
    public function getManagerName(): ?string {
        return $this->managerName;
    }
    
    // ===== SETTERS =====
    
    public function setId(int $id): void {
        $this->id = $id;
    }
    
    public function setName(string $name): void {
        if (empty(trim($name))) {
            throw new InvalidArgumentException("Le nom de l'équipe ne peut pas être vide");
        }
        $this->name = $name;
    }
    
    public function setBudget(float $budget): void {
        if ($budget < 0) {
            throw new InvalidArgumentException("Le budget ne peut pas être négatif");
        }
        $this->budget = $budget;
    }
    
    public function setManagerName(?string $managerName): void {
        $this->managerName = $managerName;
    }
}