<?php
session_start();
header('Content-Type: application/json');

// Database connection
require_once __DIR__ . '/../config/database.php';

// Response helper function
function sendResponse($success, $message = '', $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Get the action from POST or GET
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            sendResponse(false, 'Email and password are required');
        }
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                if (!$user['is_active']) {
                    sendResponse(false, 'Please activate your account first');
                }
                
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Return user data
                sendResponse(true, 'Login successful', [
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'avatar' => $user['avatar'] ?? 'https://api.dicebear.com/7.x/miniavs/svg?seed=' . $user['username']
                    ]
                ]);
            } else {
                sendResponse(false, 'Invalid email or password');
            }
        } catch (PDOException $e) {
            sendResponse(false, 'Login failed');
        }
        break;
        
    case 'register':
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($email) || empty($password)) {
            sendResponse(false, 'All fields are required');
        }
        
        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                sendResponse(false, 'Email already exists');
            }
            
            // Generate activation token
            $activationToken = bin2hex(random_bytes(32));
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, activation_token, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$username, $email, $hashedPassword, $activationToken]);
            
            // Send activation email
            $activationLink = "http://localhost:8081/sonice-online-games-new/activate.php?token=" . $activationToken;
            // TODO: Implement email sending
            
            sendResponse(true, 'Registration successful. Please check your email to activate your account.');
        } catch (PDOException $e) {
            sendResponse(false, 'Registration failed');
        }
        break;
        
    case 'logout':
        session_destroy();
        sendResponse(true, 'Logout successful');
        break;
        
    case 'check_session':
        if (isset($_SESSION['user_id'])) {
            try {
                $stmt = $pdo->prepare("SELECT id, username, email, avatar FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();
                
                if ($user) {
                    sendResponse(true, '', [
                        'user' => [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'email' => $user['email'],
                            'avatar' => $user['avatar'] ?? 'https://api.dicebear.com/7.x/miniavs/svg?seed=' . $user['username']
                        ]
                    ]);
                }
            } catch (PDOException $e) {
                sendResponse(false, 'Session check failed');
            }
        }
        sendResponse(false, 'No active session');
        break;
        
    default:
        sendResponse(false, 'Invalid action');
} 
session_start();
header('Content-Type: application/json');

// Database connection
require_once __DIR__ . '/../config/database.php';

// Response helper function
function sendResponse($success, $message = '', $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Get the action from POST or GET
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            sendResponse(false, 'Email and password are required');
        }
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                if (!$user['is_active']) {
                    sendResponse(false, 'Please activate your account first');
                }
                
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Return user data
                sendResponse(true, 'Login successful', [
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'avatar' => $user['avatar'] ?? 'https://api.dicebear.com/7.x/miniavs/svg?seed=' . $user['username']
                    ]
                ]);
            } else {
                sendResponse(false, 'Invalid email or password');
            }
        } catch (PDOException $e) {
            sendResponse(false, 'Login failed');
        }
        break;
        
    case 'register':
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($email) || empty($password)) {
            sendResponse(false, 'All fields are required');
        }
        
        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                sendResponse(false, 'Email already exists');
            }
            
            // Generate activation token
            $activationToken = bin2hex(random_bytes(32));
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, activation_token, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$username, $email, $hashedPassword, $activationToken]);
            
            // Send activation email
            $activationLink = "http://localhost:8081/sonice-online-games-new/activate.php?token=" . $activationToken;
            // TODO: Implement email sending
            
            sendResponse(true, 'Registration successful. Please check your email to activate your account.');
        } catch (PDOException $e) {
            sendResponse(false, 'Registration failed');
        }
        break;
        
    case 'logout':
        session_destroy();
        sendResponse(true, 'Logout successful');
        break;
        
    case 'check_session':
        if (isset($_SESSION['user_id'])) {
            try {
                $stmt = $pdo->prepare("SELECT id, username, email, avatar FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();
                
                if ($user) {
                    sendResponse(true, '', [
                        'user' => [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'email' => $user['email'],
                            'avatar' => $user['avatar'] ?? 'https://api.dicebear.com/7.x/miniavs/svg?seed=' . $user['username']
                        ]
                    ]);
                }
            } catch (PDOException $e) {
                sendResponse(false, 'Session check failed');
            }
        }
        sendResponse(false, 'No active session');
        break;
        
    default:
        sendResponse(false, 'Invalid action');
} 