<?php
class Database extends PDO {
    public function __construct() {
        $host = 'localhost';
        $port = '3306';
        $dbname = 'sonice_online_games';
        $username = 'root';
        $password = '';
        
        try {
            parent::__construct(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception('数据库连接失败，请稍后重试');
        }
    }
    
    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage() . "\nSQL: " . $sql . "\nParams: " . print_r($params, true));
            throw new Exception("数据库操作失败：" . $e->getMessage());
        }
    }
    
    public function fetch($sql, $params = []) {
        return $this->executeQuery($sql, $params)->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->executeQuery($sql, $params)->fetchAll();
    }
    
    public function beginTransaction() {
        if (!$this->inTransaction()) {
            return parent::beginTransaction();
        }
        return false;
    }
    
    public function commit() {
        if ($this->inTransaction()) {
            return parent::commit();
        }
        return false;
    }
    
    public function rollBack() {
        if ($this->inTransaction()) {
            return parent::rollBack();
        }
        return false;
    }
} 
 
 
class Database extends PDO {
    public function __construct() {
        $host = 'localhost';
        $port = '3306';
        $dbname = 'sonice_online_games';
        $username = 'root';
        $password = '';
        
        try {
            parent::__construct(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception('数据库连接失败，请稍后重试');
        }
    }
    
    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage() . "\nSQL: " . $sql . "\nParams: " . print_r($params, true));
            throw new Exception("数据库操作失败：" . $e->getMessage());
        }
    }
    
    public function fetch($sql, $params = []) {
        return $this->executeQuery($sql, $params)->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->executeQuery($sql, $params)->fetchAll();
    }
    
    public function beginTransaction() {
        if (!$this->inTransaction()) {
            return parent::beginTransaction();
        }
        return false;
    }
    
    public function commit() {
        if ($this->inTransaction()) {
            return parent::commit();
        }
        return false;
    }
    
    public function rollBack() {
        if ($this->inTransaction()) {
            return parent::rollBack();
        }
        return false;
    }
} 
 
class Database extends PDO {
    public function __construct() {
        $host = 'localhost';
        $port = '3306';
        $dbname = 'sonice_online_games';
        $username = 'root';
        $password = '';
        
        try {
            parent::__construct(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception('数据库连接失败，请稍后重试');
        }
    }
    
    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage() . "\nSQL: " . $sql . "\nParams: " . print_r($params, true));
            throw new Exception("数据库操作失败：" . $e->getMessage());
        }
    }
    
    public function fetch($sql, $params = []) {
        return $this->executeQuery($sql, $params)->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->executeQuery($sql, $params)->fetchAll();
    }
    
    public function beginTransaction() {
        if (!$this->inTransaction()) {
            return parent::beginTransaction();
        }
        return false;
    }
    
    public function commit() {
        if ($this->inTransaction()) {
            return parent::commit();
        }
        return false;
    }
    
    public function rollBack() {
        if ($this->inTransaction()) {
            return parent::rollBack();
        }
        return false;
    }
} 
 
 
class Database extends PDO {
    public function __construct() {
        $host = 'localhost';
        $port = '3306';
        $dbname = 'sonice_online_games';
        $username = 'root';
        $password = '';
        
        try {
            parent::__construct(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception('数据库连接失败，请稍后重试');
        }
    }
    
    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage() . "\nSQL: " . $sql . "\nParams: " . print_r($params, true));
            throw new Exception("数据库操作失败：" . $e->getMessage());
        }
    }
    
    public function fetch($sql, $params = []) {
        return $this->executeQuery($sql, $params)->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->executeQuery($sql, $params)->fetchAll();
    }
    
    public function beginTransaction() {
        if (!$this->inTransaction()) {
            return parent::beginTransaction();
        }
        return false;
    }
    
    public function commit() {
        if ($this->inTransaction()) {
            return parent::commit();
        }
        return false;
    }
    
    public function rollBack() {
        if ($this->inTransaction()) {
            return parent::rollBack();
        }
        return false;
    }
} 
 