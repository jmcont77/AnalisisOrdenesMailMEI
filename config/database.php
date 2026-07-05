<?php
class Database {
    private static $instance = null;
    
    private $host     = 'postgres-5lyq.railway.internal';
    private $port     = '5432';
    private $dbname   = 'railway';
    private $user     = 'postgres';
    private $password = 'lkSXQdgqDcIJKhdhwUBnvRlcZchjEtDk';
    
    private $pdo;
    
    private function __construct() {
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
        $this->pdo = new PDO($dsn, $this->user, $this->password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getPdo(): PDO {
        return $this->pdo;
    }
}
