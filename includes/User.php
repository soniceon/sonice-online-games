<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Mailer.php';

class User {
    private $db;
    private $mailer;
    
    public function __construct() {
        try {
            $this->db = new Database();
            $this->mailer = new Mailer();
        } catch (Exception $e) {
            error_log("Initialization error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function register($username, $email, $password) {
        try {
            error_log("Starting registration for user: " . $username);
            
            // 验证输入
            if (empty($username) || empty($email) || empty($password)) {
                error_log("Empty input fields");
                return ['success' => false, 'message' => '所有字段都是必填的'];
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error_log("Invalid email format: " . $email);
                return ['success' => false, 'message' => '邮箱格式不正确'];
            }
            
            if (strlen($password) < 6) {
                error_log("Password too short");
                return ['success' => false, 'message' => '密码至少需要6个字符'];
            }
            
            // 检查用户名和邮箱是否已存在
            error_log("Checking for existing username/email");
            $stmt = $this->db->executeQuery(
                "SELECT 'username' as field FROM users WHERE username = ? 
                 UNION ALL 
                 SELECT 'email' as field FROM users WHERE email = ?",
                [$username, $email]
            );
            
            $results = $stmt->fetchAll();
            if (!empty($results)) {
                foreach ($results as $result) {
                    if ($result['field'] === 'username') {
                        error_log("Username already exists: " . $username);
                        return ['success' => false, 'message' => '用户名已被使用'];
                    }
                    if ($result['field'] === 'email') {
                        error_log("Email already exists: " . $email);
                        return ['success' => false, 'message' => '邮箱已被注册'];
                    }
                }
            }
            
            // 生成激活码
            $activationCode = bin2hex(random_bytes(32));
            error_log("Generated activation code for user: " . $username);
            
            // 生成密码哈希
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // 开始事务
            $this->db->beginTransaction();
            
            try {
                // 插入新用户
                error_log("Inserting new user: " . $username);
                $this->db->executeQuery(
                    "INSERT INTO users (username, email, password, activation_code, is_active, created_at) 
                    VALUES (?, ?, ?, ?, 0, NOW())",
                    [$username, $email, $passwordHash, $activationCode]
                );
                
                // 发送激活邮件
                error_log("Sending activation email to: " . $email);
                if ($this->mailer->sendActivationEmail($email, $activationCode)) {
                    $this->db->commit();
                    error_log("Registration successful for user: " . $username);
                    return [
                        'success' => true,
                        'message' => '注册成功！请检查您的邮箱完成激活。'
                    ];
                } else {
                    // 如果邮件发送失败，回滚事务
                    $this->db->rollBack();
                    error_log("Failed to send activation email to: " . $email);
                    return [
                        'success' => false,
                        'message' => '注册失败：无法发送激活邮件，请稍后重试'
                    ];
                }
            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '注册失败，请稍后重试'
            ];
        }
    }
    
    public function activate($activationCode) {
        try {
            $stmt = $this->db->executeQuery(
                "SELECT id FROM users WHERE activation_code = ? AND is_active = 0",
                [$activationCode]
            );
            $user = $stmt->fetch();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => '无效的激活码或账号已激活'
                ];
            }
            
            $this->db->executeQuery(
                "UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?",
                [$user['id']]
            );
            
            return [
                'success' => true,
                'message' => '账号激活成功！'
            ];
            
        } catch (Exception $e) {
            error_log("Activation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '激活失败，请稍后重试'
            ];
        }
    }
    
    public function login($email, $password) {
        try {
            $stmt = $this->db->executeQuery(
                "SELECT id, username, email, password, is_active FROM users WHERE email = ?",
                [$email]
            );
            $user = $stmt->fetch();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => '用户不存在'
                ];
            }
            
            if (!password_verify($password, $user['password'])) {
                return [
                    'success' => false,
                    'message' => '密码错误'
                ];
            }
            
            if (!$user['is_active']) {
                return [
                    'success' => false,
                    'message' => '账号未激活，请先查收邮件激活账号'
                ];
            }
            
            // 更新最后登录时间
            $this->db->executeQuery(
                "UPDATE users SET last_login = NOW() WHERE id = ?",
                [$user['id']]
            );
            
            // 设置会话
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            return [
                'success' => true,
                'message' => '登录成功',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '登录失败，请稍后重试'
            ];
        }
    }
    
    public function logout() {
        session_destroy();
        return [
            'success' => true,
            'message' => '已成功登出'
        ];
    }
    
    public function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            try {
                $stmt = $this->db->executeQuery(
                    "SELECT id, username, email FROM users WHERE id = ?",
                    [$_SESSION['user_id']]
                );
                return $stmt->fetch();
            } catch (Exception $e) {
                error_log("Get current user error: " . $e->getMessage());
                return null;
            }
        }
        return null;
    }
} 
 
 
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Mailer.php';

class User {
    private $db;
    private $mailer;
    
    public function __construct() {
        try {
            $this->db = new Database();
            $this->mailer = new Mailer();
        } catch (Exception $e) {
            error_log("Initialization error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function register($username, $email, $password) {
        try {
            error_log("Starting registration for user: " . $username);
            
            // 验证输入
            if (empty($username) || empty($email) || empty($password)) {
                error_log("Empty input fields");
                return ['success' => false, 'message' => '所有字段都是必填的'];
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error_log("Invalid email format: " . $email);
                return ['success' => false, 'message' => '邮箱格式不正确'];
            }
            
            if (strlen($password) < 6) {
                error_log("Password too short");
                return ['success' => false, 'message' => '密码至少需要6个字符'];
            }
            
            // 检查用户名和邮箱是否已存在
            error_log("Checking for existing username/email");
            $stmt = $this->db->executeQuery(
                "SELECT 'username' as field FROM users WHERE username = ? 
                 UNION ALL 
                 SELECT 'email' as field FROM users WHERE email = ?",
                [$username, $email]
            );
            
            $results = $stmt->fetchAll();
            if (!empty($results)) {
                foreach ($results as $result) {
                    if ($result['field'] === 'username') {
                        error_log("Username already exists: " . $username);
                        return ['success' => false, 'message' => '用户名已被使用'];
                    }
                    if ($result['field'] === 'email') {
                        error_log("Email already exists: " . $email);
                        return ['success' => false, 'message' => '邮箱已被注册'];
                    }
                }
            }
            
            // 生成激活码
            $activationCode = bin2hex(random_bytes(32));
            error_log("Generated activation code for user: " . $username);
            
            // 生成密码哈希
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // 开始事务
            $this->db->beginTransaction();
            
            try {
                // 插入新用户
                error_log("Inserting new user: " . $username);
                $this->db->executeQuery(
                    "INSERT INTO users (username, email, password, activation_code, is_active, created_at) 
                    VALUES (?, ?, ?, ?, 0, NOW())",
                    [$username, $email, $passwordHash, $activationCode]
                );
                
                // 发送激活邮件
                error_log("Sending activation email to: " . $email);
                if ($this->mailer->sendActivationEmail($email, $activationCode)) {
                    $this->db->commit();
                    error_log("Registration successful for user: " . $username);
                    return [
                        'success' => true,
                        'message' => '注册成功！请检查您的邮箱完成激活。'
                    ];
                } else {
                    // 如果邮件发送失败，回滚事务
                    $this->db->rollBack();
                    error_log("Failed to send activation email to: " . $email);
                    return [
                        'success' => false,
                        'message' => '注册失败：无法发送激活邮件，请稍后重试'
                    ];
                }
            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '注册失败，请稍后重试'
            ];
        }
    }
    
    public function activate($activationCode) {
        try {
            $stmt = $this->db->executeQuery(
                "SELECT id FROM users WHERE activation_code = ? AND is_active = 0",
                [$activationCode]
            );
            $user = $stmt->fetch();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => '无效的激活码或账号已激活'
                ];
            }
            
            $this->db->executeQuery(
                "UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?",
                [$user['id']]
            );
            
            return [
                'success' => true,
                'message' => '账号激活成功！'
            ];
            
        } catch (Exception $e) {
            error_log("Activation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '激活失败，请稍后重试'
            ];
        }
    }
    
    public function login($email, $password) {
        try {
            $stmt = $this->db->executeQuery(
                "SELECT id, username, email, password, is_active FROM users WHERE email = ?",
                [$email]
            );
            $user = $stmt->fetch();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => '用户不存在'
                ];
            }
            
            if (!password_verify($password, $user['password'])) {
                return [
                    'success' => false,
                    'message' => '密码错误'
                ];
            }
            
            if (!$user['is_active']) {
                return [
                    'success' => false,
                    'message' => '账号未激活，请先查收邮件激活账号'
                ];
            }
            
            // 更新最后登录时间
            $this->db->executeQuery(
                "UPDATE users SET last_login = NOW() WHERE id = ?",
                [$user['id']]
            );
            
            // 设置会话
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            return [
                'success' => true,
                'message' => '登录成功',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '登录失败，请稍后重试'
            ];
        }
    }
    
    public function logout() {
        session_destroy();
        return [
            'success' => true,
            'message' => '已成功登出'
        ];
    }
    
    public function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            try {
                $stmt = $this->db->executeQuery(
                    "SELECT id, username, email FROM users WHERE id = ?",
                    [$_SESSION['user_id']]
                );
                return $stmt->fetch();
            } catch (Exception $e) {
                error_log("Get current user error: " . $e->getMessage());
                return null;
            }
        }
        return null;
    }
} 
 
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Mailer.php';

class User {
    private $db;
    private $mailer;
    
    public function __construct() {
        try {
            $this->db = new Database();
            $this->mailer = new Mailer();
        } catch (Exception $e) {
            error_log("Initialization error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function register($username, $email, $password) {
        try {
            error_log("Starting registration for user: " . $username);
            
            // 验证输入
            if (empty($username) || empty($email) || empty($password)) {
                error_log("Empty input fields");
                return ['success' => false, 'message' => '所有字段都是必填的'];
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error_log("Invalid email format: " . $email);
                return ['success' => false, 'message' => '邮箱格式不正确'];
            }
            
            if (strlen($password) < 6) {
                error_log("Password too short");
                return ['success' => false, 'message' => '密码至少需要6个字符'];
            }
            
            // 检查用户名和邮箱是否已存在
            error_log("Checking for existing username/email");
            $stmt = $this->db->executeQuery(
                "SELECT 'username' as field FROM users WHERE username = ? 
                 UNION ALL 
                 SELECT 'email' as field FROM users WHERE email = ?",
                [$username, $email]
            );
            
            $results = $stmt->fetchAll();
            if (!empty($results)) {
                foreach ($results as $result) {
                    if ($result['field'] === 'username') {
                        error_log("Username already exists: " . $username);
                        return ['success' => false, 'message' => '用户名已被使用'];
                    }
                    if ($result['field'] === 'email') {
                        error_log("Email already exists: " . $email);
                        return ['success' => false, 'message' => '邮箱已被注册'];
                    }
                }
            }
            
            // 生成激活码
            $activationCode = bin2hex(random_bytes(32));
            error_log("Generated activation code for user: " . $username);
            
            // 生成密码哈希
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // 开始事务
            $this->db->beginTransaction();
            
            try {
                // 插入新用户
                error_log("Inserting new user: " . $username);
                $this->db->executeQuery(
                    "INSERT INTO users (username, email, password, activation_code, is_active, created_at) 
                    VALUES (?, ?, ?, ?, 0, NOW())",
                    [$username, $email, $passwordHash, $activationCode]
                );
                
                // 发送激活邮件
                error_log("Sending activation email to: " . $email);
                if ($this->mailer->sendActivationEmail($email, $activationCode)) {
                    $this->db->commit();
                    error_log("Registration successful for user: " . $username);
                    return [
                        'success' => true,
                        'message' => '注册成功！请检查您的邮箱完成激活。'
                    ];
                } else {
                    // 如果邮件发送失败，回滚事务
                    $this->db->rollBack();
                    error_log("Failed to send activation email to: " . $email);
                    return [
                        'success' => false,
                        'message' => '注册失败：无法发送激活邮件，请稍后重试'
                    ];
                }
            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '注册失败，请稍后重试'
            ];
        }
    }
    
    public function activate($activationCode) {
        try {
            $stmt = $this->db->executeQuery(
                "SELECT id FROM users WHERE activation_code = ? AND is_active = 0",
                [$activationCode]
            );
            $user = $stmt->fetch();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => '无效的激活码或账号已激活'
                ];
            }
            
            $this->db->executeQuery(
                "UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?",
                [$user['id']]
            );
            
            return [
                'success' => true,
                'message' => '账号激活成功！'
            ];
            
        } catch (Exception $e) {
            error_log("Activation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '激活失败，请稍后重试'
            ];
        }
    }
    
    public function login($email, $password) {
        try {
            $stmt = $this->db->executeQuery(
                "SELECT id, username, email, password, is_active FROM users WHERE email = ?",
                [$email]
            );
            $user = $stmt->fetch();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => '用户不存在'
                ];
            }
            
            if (!password_verify($password, $user['password'])) {
                return [
                    'success' => false,
                    'message' => '密码错误'
                ];
            }
            
            if (!$user['is_active']) {
                return [
                    'success' => false,
                    'message' => '账号未激活，请先查收邮件激活账号'
                ];
            }
            
            // 更新最后登录时间
            $this->db->executeQuery(
                "UPDATE users SET last_login = NOW() WHERE id = ?",
                [$user['id']]
            );
            
            // 设置会话
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            return [
                'success' => true,
                'message' => '登录成功',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '登录失败，请稍后重试'
            ];
        }
    }
    
    public function logout() {
        session_destroy();
        return [
            'success' => true,
            'message' => '已成功登出'
        ];
    }
    
    public function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            try {
                $stmt = $this->db->executeQuery(
                    "SELECT id, username, email FROM users WHERE id = ?",
                    [$_SESSION['user_id']]
                );
                return $stmt->fetch();
            } catch (Exception $e) {
                error_log("Get current user error: " . $e->getMessage());
                return null;
            }
        }
        return null;
    }
} 
 
 
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Mailer.php';

class User {
    private $db;
    private $mailer;
    
    public function __construct() {
        try {
            $this->db = new Database();
            $this->mailer = new Mailer();
        } catch (Exception $e) {
            error_log("Initialization error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function register($username, $email, $password) {
        try {
            error_log("Starting registration for user: " . $username);
            
            // 验证输入
            if (empty($username) || empty($email) || empty($password)) {
                error_log("Empty input fields");
                return ['success' => false, 'message' => '所有字段都是必填的'];
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error_log("Invalid email format: " . $email);
                return ['success' => false, 'message' => '邮箱格式不正确'];
            }
            
            if (strlen($password) < 6) {
                error_log("Password too short");
                return ['success' => false, 'message' => '密码至少需要6个字符'];
            }
            
            // 检查用户名和邮箱是否已存在
            error_log("Checking for existing username/email");
            $stmt = $this->db->executeQuery(
                "SELECT 'username' as field FROM users WHERE username = ? 
                 UNION ALL 
                 SELECT 'email' as field FROM users WHERE email = ?",
                [$username, $email]
            );
            
            $results = $stmt->fetchAll();
            if (!empty($results)) {
                foreach ($results as $result) {
                    if ($result['field'] === 'username') {
                        error_log("Username already exists: " . $username);
                        return ['success' => false, 'message' => '用户名已被使用'];
                    }
                    if ($result['field'] === 'email') {
                        error_log("Email already exists: " . $email);
                        return ['success' => false, 'message' => '邮箱已被注册'];
                    }
                }
            }
            
            // 生成激活码
            $activationCode = bin2hex(random_bytes(32));
            error_log("Generated activation code for user: " . $username);
            
            // 生成密码哈希
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // 开始事务
            $this->db->beginTransaction();
            
            try {
                // 插入新用户
                error_log("Inserting new user: " . $username);
                $this->db->executeQuery(
                    "INSERT INTO users (username, email, password, activation_code, is_active, created_at) 
                    VALUES (?, ?, ?, ?, 0, NOW())",
                    [$username, $email, $passwordHash, $activationCode]
                );
                
                // 发送激活邮件
                error_log("Sending activation email to: " . $email);
                if ($this->mailer->sendActivationEmail($email, $activationCode)) {
                    $this->db->commit();
                    error_log("Registration successful for user: " . $username);
                    return [
                        'success' => true,
                        'message' => '注册成功！请检查您的邮箱完成激活。'
                    ];
                } else {
                    // 如果邮件发送失败，回滚事务
                    $this->db->rollBack();
                    error_log("Failed to send activation email to: " . $email);
                    return [
                        'success' => false,
                        'message' => '注册失败：无法发送激活邮件，请稍后重试'
                    ];
                }
            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '注册失败，请稍后重试'
            ];
        }
    }
    
    public function activate($activationCode) {
        try {
            $stmt = $this->db->executeQuery(
                "SELECT id FROM users WHERE activation_code = ? AND is_active = 0",
                [$activationCode]
            );
            $user = $stmt->fetch();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => '无效的激活码或账号已激活'
                ];
            }
            
            $this->db->executeQuery(
                "UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?",
                [$user['id']]
            );
            
            return [
                'success' => true,
                'message' => '账号激活成功！'
            ];
            
        } catch (Exception $e) {
            error_log("Activation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '激活失败，请稍后重试'
            ];
        }
    }
    
    public function login($email, $password) {
        try {
            $stmt = $this->db->executeQuery(
                "SELECT id, username, email, password, is_active FROM users WHERE email = ?",
                [$email]
            );
            $user = $stmt->fetch();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => '用户不存在'
                ];
            }
            
            if (!password_verify($password, $user['password'])) {
                return [
                    'success' => false,
                    'message' => '密码错误'
                ];
            }
            
            if (!$user['is_active']) {
                return [
                    'success' => false,
                    'message' => '账号未激活，请先查收邮件激活账号'
                ];
            }
            
            // 更新最后登录时间
            $this->db->executeQuery(
                "UPDATE users SET last_login = NOW() WHERE id = ?",
                [$user['id']]
            );
            
            // 设置会话
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            return [
                'success' => true,
                'message' => '登录成功',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '登录失败，请稍后重试'
            ];
        }
    }
    
    public function logout() {
        session_destroy();
        return [
            'success' => true,
            'message' => '已成功登出'
        ];
    }
    
    public function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            try {
                $stmt = $this->db->executeQuery(
                    "SELECT id, username, email FROM users WHERE id = ?",
                    [$_SESSION['user_id']]
                );
                return $stmt->fetch();
            } catch (Exception $e) {
                error_log("Get current user error: " . $e->getMessage());
                return null;
            }
        }
        return null;
    }
} 
 