<!DOCTYPE html>
<html>
<head>
    <title>Simple Register Test</title>
    <style>
        body { padding: 20px; font-family: Arial; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 200px; padding: 5px; }
        button { padding: 10px 20px; }
        #result { margin-top: 20px; padding: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h2>测试注册</h2>
    
    <div class="form-group">
        <label>用户名:</label>
        <input type="text" id="username">
    </div>
    
    <div class="form-group">
        <label>邮箱:</label>
        <input type="email" id="email">
    </div>
    
    <div class="form-group">
        <label>密码:</label>
        <input type="password" id="password">
    </div>
    
    <button onclick="testRegister()">注册</button>
    
    <div id="result"></div>

    <script>
        function testRegister() {
            // 获取输入值
            var username = document.getElementById('username').value;
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;
            
            // 显示收集到的数据
            alert('收集到的数据:\n' + 
                  '用户名: ' + username + '\n' + 
                  '邮箱: ' + email + '\n' + 
                  '密码: ' + password);
            
            // 更新结果显示
            document.getElementById('result').innerHTML = '正在提交...';
            
            // 发送到服务器
            fetch('/sonice-online-games-new/api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'username=' + encodeURIComponent(username) + 
                      '&email=' + encodeURIComponent(email) + 
                      '&password=' + encodeURIComponent(password)
            })
            .then(function(response) {
                return response.text();
            })
            .then(function(data) {
                alert('服务器响应:\n' + data);
                document.getElementById('result').innerHTML = '服务器响应: ' + data;
            })
            .catch(function(error) {
                alert('错误:\n' + error);
                document.getElementById('result').innerHTML = '错误: ' + error;
            });
        }
    </script>
</body>
</html> 
 
 
<html>
<head>
    <title>Simple Register Test</title>
    <style>
        body { padding: 20px; font-family: Arial; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 200px; padding: 5px; }
        button { padding: 10px 20px; }
        #result { margin-top: 20px; padding: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h2>测试注册</h2>
    
    <div class="form-group">
        <label>用户名:</label>
        <input type="text" id="username">
    </div>
    
    <div class="form-group">
        <label>邮箱:</label>
        <input type="email" id="email">
    </div>
    
    <div class="form-group">
        <label>密码:</label>
        <input type="password" id="password">
    </div>
    
    <button onclick="testRegister()">注册</button>
    
    <div id="result"></div>

    <script>
        function testRegister() {
            // 获取输入值
            var username = document.getElementById('username').value;
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;
            
            // 显示收集到的数据
            alert('收集到的数据:\n' + 
                  '用户名: ' + username + '\n' + 
                  '邮箱: ' + email + '\n' + 
                  '密码: ' + password);
            
            // 更新结果显示
            document.getElementById('result').innerHTML = '正在提交...';
            
            // 发送到服务器
            fetch('/sonice-online-games-new/api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'username=' + encodeURIComponent(username) + 
                      '&email=' + encodeURIComponent(email) + 
                      '&password=' + encodeURIComponent(password)
            })
            .then(function(response) {
                return response.text();
            })
            .then(function(data) {
                alert('服务器响应:\n' + data);
                document.getElementById('result').innerHTML = '服务器响应: ' + data;
            })
            .catch(function(error) {
                alert('错误:\n' + error);
                document.getElementById('result').innerHTML = '错误: ' + error;
            });
        }
    </script>
</body>
</html> 
 
<html>
<head>
    <title>Simple Register Test</title>
    <style>
        body { padding: 20px; font-family: Arial; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 200px; padding: 5px; }
        button { padding: 10px 20px; }
        #result { margin-top: 20px; padding: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h2>测试注册</h2>
    
    <div class="form-group">
        <label>用户名:</label>
        <input type="text" id="username">
    </div>
    
    <div class="form-group">
        <label>邮箱:</label>
        <input type="email" id="email">
    </div>
    
    <div class="form-group">
        <label>密码:</label>
        <input type="password" id="password">
    </div>
    
    <button onclick="testRegister()">注册</button>
    
    <div id="result"></div>

    <script>
        function testRegister() {
            // 获取输入值
            var username = document.getElementById('username').value;
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;
            
            // 显示收集到的数据
            alert('收集到的数据:\n' + 
                  '用户名: ' + username + '\n' + 
                  '邮箱: ' + email + '\n' + 
                  '密码: ' + password);
            
            // 更新结果显示
            document.getElementById('result').innerHTML = '正在提交...';
            
            // 发送到服务器
            fetch('/sonice-online-games-new/api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'username=' + encodeURIComponent(username) + 
                      '&email=' + encodeURIComponent(email) + 
                      '&password=' + encodeURIComponent(password)
            })
            .then(function(response) {
                return response.text();
            })
            .then(function(data) {
                alert('服务器响应:\n' + data);
                document.getElementById('result').innerHTML = '服务器响应: ' + data;
            })
            .catch(function(error) {
                alert('错误:\n' + error);
                document.getElementById('result').innerHTML = '错误: ' + error;
            });
        }
    </script>
</body>
</html> 
 
 
<html>
<head>
    <title>Simple Register Test</title>
    <style>
        body { padding: 20px; font-family: Arial; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 200px; padding: 5px; }
        button { padding: 10px 20px; }
        #result { margin-top: 20px; padding: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h2>测试注册</h2>
    
    <div class="form-group">
        <label>用户名:</label>
        <input type="text" id="username">
    </div>
    
    <div class="form-group">
        <label>邮箱:</label>
        <input type="email" id="email">
    </div>
    
    <div class="form-group">
        <label>密码:</label>
        <input type="password" id="password">
    </div>
    
    <button onclick="testRegister()">注册</button>
    
    <div id="result"></div>

    <script>
        function testRegister() {
            // 获取输入值
            var username = document.getElementById('username').value;
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;
            
            // 显示收集到的数据
            alert('收集到的数据:\n' + 
                  '用户名: ' + username + '\n' + 
                  '邮箱: ' + email + '\n' + 
                  '密码: ' + password);
            
            // 更新结果显示
            document.getElementById('result').innerHTML = '正在提交...';
            
            // 发送到服务器
            fetch('/sonice-online-games-new/api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'username=' + encodeURIComponent(username) + 
                      '&email=' + encodeURIComponent(email) + 
                      '&password=' + encodeURIComponent(password)
            })
            .then(function(response) {
                return response.text();
            })
            .then(function(data) {
                alert('服务器响应:\n' + data);
                document.getElementById('result').innerHTML = '服务器响应: ' + data;
            })
            .catch(function(error) {
                alert('错误:\n' + error);
                document.getElementById('result').innerHTML = '错误: ' + error;
            });
        }
    </script>
</body>
</html> 
 