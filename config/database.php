<?php
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $host     = getenv('DB_HOST') ?: 'postgres-5lyq.railway.internal';
        $port     = getenv('DB_PORT') ?: '5432';
        $dbname   = getenv('DB_NAME') ?: 'railway';
        $user     = getenv('DB_USER') ?: 'postgres';
        $password = getenv('DB_PASS') ?: 'lkSXQdgqDcIJKhdhwUBnvRlcZchjEtDk';
        
        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
        $this->pdo = new PDO($dsn, $user, $password, [
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
