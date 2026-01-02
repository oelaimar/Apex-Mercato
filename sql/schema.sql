-- =========================
-- PERSONS
-- =========================
CREATE TABLE
    persons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type ENUM ('player', 'coach') NOT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        nationality VARCHAR(80) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE = InnoDB;

-- =========================
-- PLAYERS (1–1 with persons)
-- =========================
CREATE TABLE
    players (
        persons_id INT PRIMARY KEY,
        nickname VARCHAR(50) NOT NULL,
        role VARCHAR(50) NOT NULL,
        market_value DECIMAL(12, 2) CHECK (market_value >= 0),
        CONSTRAINT fk_players_person FOREIGN KEY (persons_id) REFERENCES persons (id) ON DELETE CASCADE
    ) ENGINE = InnoDB;

-- =========================
-- COACHES (1–1 with persons)
-- =========================
CREATE TABLE
    coaches (
        persons_id INT PRIMARY KEY,
        coaching_style VARCHAR(100),
        years_of_experience INT CHECK (years_of_experience >= 0),
        CONSTRAINT fk_coaches_person FOREIGN KEY (persons_id) REFERENCES persons (id) ON DELETE CASCADE
    ) ENGINE = InnoDB;

-- =========================
-- TEAMES
-- =========================
CREATE TABLE
    teames (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL,
        budget DECIMAL(14, 2) CHECK (budget >= 0),
        manager VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE = InnoDB;

-- =========================
-- CONTRACTS
-- =========================
CREATE TABLE
    contracts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uuid CHAR(36) NOT NULL UNIQUE,
        persons_id INT NOT NULL,
        team_id INT NOT NULL,
        salary DECIMAL(12, 2) CHECK (salary >= 0),
        buyout DECIMAL(12, 2) CHECK (buyout >= 0),
        end_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_contract_person FOREIGN KEY (persons_id) REFERENCES persons (id) ON DELETE CASCADE,
        CONSTRAINT fk_contract_team FOREIGN KEY (team_id) REFERENCES teames (id) ON DELETE CASCADE
    ) ENGINE = InnoDB;

-- =========================
-- TRANSFERS
-- =========================
CREATE TABLE
    transfers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        reference VARCHAR(30) UNIQUE NOT NULL,
        persons_id INT NOT NULL,
        departure_team_id INT,
        arrival_team_id INT,
        amount DECIMAL(14, 2) CHECK (amount >= 0),
        status ENUM ('pending', 'completed', 'canceled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_transfer_person FOREIGN KEY (persons_id) REFERENCES persons (id) ON DELETE CASCADE,
        CONSTRAINT fk_transfer_departure FOREIGN KEY (departure_team_id) REFERENCES teames (id),
        CONSTRAINT fk_transfer_arrival FOREIGN KEY (arrival_team_id) REFERENCES teames (id)
    ) ENGINE = InnoDB;