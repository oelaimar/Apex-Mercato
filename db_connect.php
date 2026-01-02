<?php
class Database
{

    private static ?Database $instance = null;

    private ?PDO $pdo;
    private string $host = "db";
    private string $username = "apex";
    private string $password = "apex";
    private string $dbname = "apex_manager";

    private function __construct()
    {
        try{
            $dataSourceName = "mysql:host={$this->host};dbname={$this->dbname};setcar=utf8mb4";
            $this->pdo = new PDO($dataSourceName, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            die("Error connect to database");
            // die("Error connect to database : " . $e->getMessage());
        }
    }

    public static function getInstance(): Database
    {
        if (!self::$instance) {
            self::$instance = new Database;
        }
        return self::$instance;
    }
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}