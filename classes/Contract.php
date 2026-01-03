<?php
class Contract {
    private int $id;
    public readonly string $uuid;
    public readonly string $startDate;
    private int $teamId;
    private float $salary;
    private ?float $buyoutClause;
    private string $endDate;

    
    public function __construct(
        string $uuid,
        int $teamId,
        float $salary,
        string $startDate,
        string $endDate,
        ?float $buyoutClause = null
    ) {
        $this->uuid = $uuid;
        $this->startDate = $startDate;
        $this->teamId = $teamId;
        $this->setSalary($salary);
        $this->setEndDate($endDate);
        $this->buyoutClause = $buyoutClause;
    }

    public function isActive(): bool {
        $today = new DateTime();
        $end = new DateTime($this->endDate);
        
        return $today <= $end;
    }
    

    public function getDaysRemaining(): int {
        $today = new DateTime();
        $end = new DateTime($this->endDate);
        $interval = $today->diff($end);
        
        return $interval->invert ? -$interval->days : $interval->days;
    }
    
    public function getTotalContractValue(): float {
        $start = new DateTime($this->startDate);
        $end = new DateTime($this->endDate);
        $interval = $start->diff($end);
        
        $months = ($interval->y * 12) + $interval->m;
        
        return $this->salary * $months;
    }
    

    public function displayDetails(bool $showSensitive = false): string {
        $status = $this->isActive() ? 
            "<span class='badge badge-success'>Actif</span>" : 
            "<span class='badge badge-danger'>Expiré</span>";
        
        $html = "
            <div class='contract-details'>
                <p><strong>UUID:</strong> {$this->uuid}</p>
                <p><strong>Statut:</strong> {$status}</p>
                <p><strong>Début:</strong> {$this->startDate}</p>
                <p><strong>Fin:</strong> {$this->endDate}</p>
        ";
        
        if ($showSensitive) {
            $formattedSalary = number_format($this->salary, 2, ',', ' ') . ' €';
            $html .= "<p><strong>Salaire mensuel:</strong> {$formattedSalary}</p>";
            
            if ($this->buyoutClause !== null) {
                $formattedClause = number_format($this->buyoutClause, 2, ',', ' ') . ' €';
                $html .= "<p><strong>Clause de rachat:</strong> {$formattedClause}</p>";
            }
        }
        
        $html .= "</div>";
        
        return $html;
    }
    
    // ===== GETTERS =====
    
    public function getId(): int {
        return $this->id;
    }
    
    public function getTeamId(): int {
        return $this->teamId;
    }
    
    public function getSalary(): float {
        return $this->salary;
    }
    
    public function getEndDate(): string {
        return $this->endDate;
    }
    
    public function getBuyoutClause(): ?float {
        return $this->buyoutClause;
    }
    
    // ===== SETTERS (sauf pour readonly properties) =====
    
    public function setId(int $id): void {
        $this->id = $id;
    }
    
    public function setTeamId(int $teamId): void {
        $this->teamId = $teamId;
    }
    
    public function setSalary(float $salary): void {
        if ($salary <= 0) {
            throw new InvalidArgumentException("Le salaire doit être positif");
        }
        $this->salary = $salary;
    }
    
    public function setEndDate(string $endDate): void {
        // Validation de la date
        $date = DateTime::createFromFormat('Y-m-d', $endDate);
        if (!$date || $date->format('Y-m-d') !== $endDate) {
            throw new InvalidArgumentException("Format de date invalide (attendu: YYYY-MM-DD)");
        }
        
        $this->endDate = $endDate;
    }
    
    public function setBuyoutClause(?float $buyoutClause): void {
        if ($buyoutClause !== null && $buyoutClause < 0) {
            throw new InvalidArgumentException("La clause de rachat ne peut pas être négative");
        }
        $this->buyoutClause = $buyoutClause;
    }

}