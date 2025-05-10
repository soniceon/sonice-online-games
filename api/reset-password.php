<?php
require_once __DIR__ . '/db.php';
header('Content-Type: text/html; charset=utf-8');
$email = trim($_GET['email'] ?? '');
$token = trim($_GET['token'] ?? '');
$valid = false;
if ($email && $token) {
    $stmt = $conn->prepare('SELECT id FROM users WHERE email=? AND reset_token=? AND reset_expires > NOW()');
    $stmt->bind_param('ss', $email, $token);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $valid = true;
    }
}
// 处理AJAX POST请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $email = trim($_POST['email'] ?? '');
    $token = trim($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$email || !$token || !$password) {
        echo json_encode(['success'=>false, 'msg'=>'参数不完整']);
        exit;
    }
    if (strlen($password) < 6) {
        echo json_encode(['success'=>false, 'msg'=>'密码长度不能少于6位']);
        exit;
    }
    $stmt = $conn->prepare('SELECT id FROM users WHERE email=? AND reset_token=? AND reset_expires > NOW()');
    $stmt->bind_param('ss', $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if (!$user) {
        echo json_encode(['success'=>false, 'msg'=>'重置链接无效或已过期']);
        exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?');
    $stmt->bind_param('ssi', $hash, $user['id']);
    if ($stmt->execute()) {
        echo json_encode(['success'=>true, 'msg'=>'密码重置成功！你现在可以登录了。']);
    } else {
        echo json_encode(['success'=>false, 'msg'=>'重置失败，请重试']);
    }
    exit;
}
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>重置密码 - Sonice.Games</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    <style>
        body { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
<div class="bg-white/10 shadow-xl rounded-xl p-8 w-full max-w-md mx-auto mt-12 text-center text-white backdrop-blur">
    <h2 class="text-2xl font-bold mb-6">重置密码</h2>
    <?php if ($valid): ?>
        <form id="resetForm" class="space-y-4">
            <input type="hidden" name="email" value="<?=htmlspecialchars($email)?>">
            <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>">
            <div>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 rounded bg-gray-800 text-white" placeholder="请输入新密码（不少于6位）" required minlength="6">
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded">设置新密码</button>
        </form>
        <div id="msg" class="mt-4"></div>
        <script>
        document.getElementById('resetForm').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await fetch('', {method:'POST', body:formData});
            const data = await res.json();
            const msgDiv = document.getElementById('msg');
            msgDiv.textContent = data.msg;
            msgDiv.className = 'mt-4 ' + (data.success ? 'text-green-400' : 'text-red-400');
            if(data.success) {
                this.style.display = 'none';
            }
        }
        </script>
    <?php else: ?>
        <div class="text-red-400">重置链接无效或已过期，请重新申请找回密码。</div>
    <?php endif; ?>
</div>
</body>
</html> 
// GET请求，显示重置表单
if (!$email || !$token) {
    echo '重置链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id FROM users WHERE email=? AND reset_token=? AND reset_token_expire > NOW()');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo '重置链接无效或已过期。';
    exit;
}
echo '<form method="POST">
    <input type="hidden" name="email" value="'.htmlspecialchars($email).'">
    <input type="hidden" name="token" value="'.htmlspecialchars($token).'">
    新密码：<input type="password" name="password" required><br>
    <button type="submit">重置密码</button>
</form>'; 
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>重置密码 - Sonice.Games</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    <style>
        body { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
<div class="bg-white/10 shadow-xl rounded-xl p-8 w-full max-w-md mx-auto mt-12 text-center text-white backdrop-blur">
    <h2 class="text-2xl font-bold mb-6">重置密码</h2>
    <?php if ($valid): ?>
        <form id="resetForm" class="space-y-4">
            <input type="hidden" name="email" value="<?=htmlspecialchars($email)?>">
            <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>">
            <div>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 rounded bg-gray-800 text-white" placeholder="请输入新密码（不少于6位）" required minlength="6">
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded">设置新密码</button>
        </form>
        <div id="msg" class="mt-4"></div>
        <script>
        document.getElementById('resetForm').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await fetch('', {method:'POST', body:formData});
            const data = await res.json();
            const msgDiv = document.getElementById('msg');
            msgDiv.textContent = data.msg;
            msgDiv.className = 'mt-4 ' + (data.success ? 'text-green-400' : 'text-red-400');
            if(data.success) {
                this.style.display = 'none';
            }
        }
        </script>
    <?php else: ?>
        <div class="text-red-400">重置链接无效或已过期，请重新申请找回密码。</div>
    <?php endif; ?>
</div>
</body>
</html> 
// GET请求，显示重置表单
if (!$email || !$token) {
    echo '重置链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id FROM users WHERE email=? AND reset_token=? AND reset_token_expire > NOW()');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo '重置链接无效或已过期。';
    exit;
}
echo '<form method="POST">
    <input type="hidden" name="email" value="'.htmlspecialchars($email).'">
    <input type="hidden" name="token" value="'.htmlspecialchars($token).'">
    新密码：<input type="password" name="password" required><br>
    <button type="submit">重置密码</button>
</form>'; 
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>重置密码 - Sonice.Games</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    <style>
        body { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
<div class="bg-white/10 shadow-xl rounded-xl p-8 w-full max-w-md mx-auto mt-12 text-center text-white backdrop-blur">
    <h2 class="text-2xl font-bold mb-6">重置密码</h2>
    <?php if ($valid): ?>
        <form id="resetForm" class="space-y-4">
            <input type="hidden" name="email" value="<?=htmlspecialchars($email)?>">
            <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>">
            <div>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 rounded bg-gray-800 text-white" placeholder="请输入新密码（不少于6位）" required minlength="6">
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded">设置新密码</button>
        </form>
        <div id="msg" class="mt-4"></div>
        <script>
        document.getElementById('resetForm').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await fetch('', {method:'POST', body:formData});
            const data = await res.json();
            const msgDiv = document.getElementById('msg');
            msgDiv.textContent = data.msg;
            msgDiv.className = 'mt-4 ' + (data.success ? 'text-green-400' : 'text-red-400');
            if(data.success) {
                this.style.display = 'none';
            }
        }
        </script>
    <?php else: ?>
        <div class="text-red-400">重置链接无效或已过期，请重新申请找回密码。</div>
    <?php endif; ?>
</div>
</body>
</html> 
// GET请求，显示重置表单
if (!$email || !$token) {
    echo '重置链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id FROM users WHERE email=? AND reset_token=? AND reset_token_expire > NOW()');
$stmt->bind_param('ss', $email, $token);
    $stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo '重置链接无效或已过期。';
    exit;
}
echo '<form method="POST">
    <input type="hidden" name="email" value="'.htmlspecialchars($email).'">
    <input type="hidden" name="token" value="'.htmlspecialchars($token).'">
    新密码：<input type="password" name="password" required><br>
    <button type="submit">重置密码</button>
</form>'; 
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>重置密码 - Sonice.Games</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    <style>
        body { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
<div class="bg-white/10 shadow-xl rounded-xl p-8 w-full max-w-md mx-auto mt-12 text-center text-white backdrop-blur">
    <h2 class="text-2xl font-bold mb-6">重置密码</h2>
    <?php if ($valid): ?>
        <form id="resetForm" class="space-y-4">
            <input type="hidden" name="email" value="<?=htmlspecialchars($email)?>">
            <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>">
            <div>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 rounded bg-gray-800 text-white" placeholder="请输入新密码（不少于6位）" required minlength="6">
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded">设置新密码</button>
        </form>
        <div id="msg" class="mt-4"></div>
        <script>
        document.getElementById('resetForm').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await fetch('', {method:'POST', body:formData});
            const data = await res.json();
            const msgDiv = document.getElementById('msg');
            msgDiv.textContent = data.msg;
            msgDiv.className = 'mt-4 ' + (data.success ? 'text-green-400' : 'text-red-400');
            if(data.success) {
                this.style.display = 'none';
            }
        }
        </script>
    <?php else: ?>
        <div class="text-red-400">重置链接无效或已过期，请重新申请找回密码。</div>
    <?php endif; ?>
</div>
</body>
</html> 
// GET请求，显示重置表单
if (!$email || !$token) {
    echo '重置链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id FROM users WHERE email=? AND reset_token=? AND reset_token_expire > NOW()');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo '重置链接无效或已过期。';
    exit;
}
echo '<form method="POST">
    <input type="hidden" name="email" value="'.htmlspecialchars($email).'">
    <input type="hidden" name="token" value="'.htmlspecialchars($token).'">
    新密码：<input type="password" name="password" required><br>
    <button type="submit">重置密码</button>
</form>'; 
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>重置密码 - Sonice.Games</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    <style>
        body { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
<div class="bg-white/10 shadow-xl rounded-xl p-8 w-full max-w-md mx-auto mt-12 text-center text-white backdrop-blur">
    <h2 class="text-2xl font-bold mb-6">重置密码</h2>
    <?php if ($valid): ?>
        <form id="resetForm" class="space-y-4">
            <input type="hidden" name="email" value="<?=htmlspecialchars($email)?>">
            <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>">
            <div>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 rounded bg-gray-800 text-white" placeholder="请输入新密码（不少于6位）" required minlength="6">
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded">设置新密码</button>
        </form>
        <div id="msg" class="mt-4"></div>
        <script>
        document.getElementById('resetForm').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await fetch('', {method:'POST', body:formData});
            const data = await res.json();
            const msgDiv = document.getElementById('msg');
            msgDiv.textContent = data.msg;
            msgDiv.className = 'mt-4 ' + (data.success ? 'text-green-400' : 'text-red-400');
            if(data.success) {
                this.style.display = 'none';
            }
        }
        </script>
    <?php else: ?>
        <div class="text-red-400">重置链接无效或已过期，请重新申请找回密码。</div>
    <?php endif; ?>
</div>
</body>
</html> 
// GET请求，显示重置表单
if (!$email || !$token) {
    echo '重置链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id FROM users WHERE email=? AND reset_token=? AND reset_token_expire > NOW()');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo '重置链接无效或已过期。';
    exit;
}
echo '<form method="POST">
    <input type="hidden" name="email" value="'.htmlspecialchars($email).'">
    <input type="hidden" name="token" value="'.htmlspecialchars($token).'">
    新密码：<input type="password" name="password" required><br>
    <button type="submit">重置密码</button>
</form>'; 
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>重置密码 - Sonice.Games</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    <style>
        body { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
<div class="bg-white/10 shadow-xl rounded-xl p-8 w-full max-w-md mx-auto mt-12 text-center text-white backdrop-blur">
    <h2 class="text-2xl font-bold mb-6">重置密码</h2>
    <?php if ($valid): ?>
        <form id="resetForm" class="space-y-4">
            <input type="hidden" name="email" value="<?=htmlspecialchars($email)?>">
            <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>">
            <div>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 rounded bg-gray-800 text-white" placeholder="请输入新密码（不少于6位）" required minlength="6">
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded">设置新密码</button>
        </form>
        <div id="msg" class="mt-4"></div>
        <script>
        document.getElementById('resetForm').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await fetch('', {method:'POST', body:formData});
            const data = await res.json();
            const msgDiv = document.getElementById('msg');
            msgDiv.textContent = data.msg;
            msgDiv.className = 'mt-4 ' + (data.success ? 'text-green-400' : 'text-red-400');
            if(data.success) {
                this.style.display = 'none';
            }
        }
        </script>
    <?php else: ?>
        <div class="text-red-400">重置链接无效或已过期，请重新申请找回密码。</div>
    <?php endif; ?>
</div>
</body>
</html> 
// GET请求，显示重置表单
if (!$email || !$token) {
    echo '重置链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id FROM users WHERE email=? AND reset_token=? AND reset_token_expire > NOW()');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo '重置链接无效或已过期。';
    exit;
}
echo '<form method="POST">
    <input type="hidden" name="email" value="'.htmlspecialchars($email).'">
    <input type="hidden" name="token" value="'.htmlspecialchars($token).'">
    新密码：<input type="password" name="password" required><br>
    <button type="submit">重置密码</button>
</form>'; 
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>重置密码 - Sonice.Games</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    <style>
        body { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
<div class="bg-white/10 shadow-xl rounded-xl p-8 w-full max-w-md mx-auto mt-12 text-center text-white backdrop-blur">
    <h2 class="text-2xl font-bold mb-6">重置密码</h2>
    <?php if ($valid): ?>
        <form id="resetForm" class="space-y-4">
            <input type="hidden" name="email" value="<?=htmlspecialchars($email)?>">
            <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>">
            <div>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 rounded bg-gray-800 text-white" placeholder="请输入新密码（不少于6位）" required minlength="6">
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded">设置新密码</button>
        </form>
        <div id="msg" class="mt-4"></div>
        <script>
        document.getElementById('resetForm').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await fetch('', {method:'POST', body:formData});
            const data = await res.json();
            const msgDiv = document.getElementById('msg');
            msgDiv.textContent = data.msg;
            msgDiv.className = 'mt-4 ' + (data.success ? 'text-green-400' : 'text-red-400');
            if(data.success) {
                this.style.display = 'none';
            }
        }
        </script>
    <?php else: ?>
        <div class="text-red-400">重置链接无效或已过期，请重新申请找回密码。</div>
    <?php endif; ?>
</div>
</body>
</html> 
// GET请求，显示重置表单
if (!$email || !$token) {
    echo '重置链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id FROM users WHERE email=? AND reset_token=? AND reset_token_expire > NOW()');
$stmt->bind_param('ss', $email, $token);
    $stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo '重置链接无效或已过期。';
    exit;
}
echo '<form method="POST">
    <input type="hidden" name="email" value="'.htmlspecialchars($email).'">
    <input type="hidden" name="token" value="'.htmlspecialchars($token).'">
    新密码：<input type="password" name="password" required><br>
    <button type="submit">重置密码</button>
</form>'; 
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>重置密码 - Sonice.Games</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    <style>
        body { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
<div class="bg-white/10 shadow-xl rounded-xl p-8 w-full max-w-md mx-auto mt-12 text-center text-white backdrop-blur">
    <h2 class="text-2xl font-bold mb-6">重置密码</h2>
    <?php if ($valid): ?>
        <form id="resetForm" class="space-y-4">
            <input type="hidden" name="email" value="<?=htmlspecialchars($email)?>">
            <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>">
            <div>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 rounded bg-gray-800 text-white" placeholder="请输入新密码（不少于6位）" required minlength="6">
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded">设置新密码</button>
        </form>
        <div id="msg" class="mt-4"></div>
        <script>
        document.getElementById('resetForm').onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await fetch('', {method:'POST', body:formData});
            const data = await res.json();
            const msgDiv = document.getElementById('msg');
            msgDiv.textContent = data.msg;
            msgDiv.className = 'mt-4 ' + (data.success ? 'text-green-400' : 'text-red-400');
            if(data.success) {
                this.style.display = 'none';
            }
        }
        </script>
    <?php else: ?>
        <div class="text-red-400">重置链接无效或已过期，请重新申请找回密码。</div>
    <?php endif; ?>
</div>
</body>
</html> 
// GET请求，显示重置表单
if (!$email || !$token) {
    echo '重置链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id FROM users WHERE email=? AND reset_token=? AND reset_token_expire > NOW()');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo '重置链接无效或已过期。';
    exit;
}
echo '<form method="POST">
    <input type="hidden" name="email" value="'.htmlspecialchars($email).'">
    <input type="hidden" name="token" value="'.htmlspecialchars($token).'">
    新密码：<input type="password" name="password" required><br>
    <button type="submit">重置密码</button>
</form>'; 