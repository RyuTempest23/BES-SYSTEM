<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Barangay eServices</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo img {
            max-width: 80px;
            height: auto;
        }
        
        .logo h1 {
            color: #333;
            font-size: 24px;
            margin-top: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #5a67d8;
        }
        
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .message {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
        
        .message.error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
            display: block;
        }
        
        .message.success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
            display: block;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .register-link a {
            color: #667eea;
            text-decoration: none;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="assets/images/polog1.png" alt="Barangay Logo" onerror="this.style.display='none'">
            <h1>BeSCMS</h1>
            <p>Barangay e-Services</p>
        </div>
        
        <div id="message" class="message"></div>
        
        <form id="loginForm">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="email" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" required placeholder="Enter your password">
            </div>
            
            <button type="submit" id="submitBtn">Login</button>
        </form>
        
        <div class="register-link">
            Don't have an account? <a href="signup.php">Register here</a>
        </div>
    </div>
    
    <script>
        const API_BASE = '/BeSCMS';
        
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const submitBtn = document.getElementById('submitBtn');
            const messageDiv = document.getElementById('message');
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Logging in...';
            messageDiv.style.display = 'none';
            
            try {
                const response = await fetch(`${API_BASE}/index.php?route=auth&action=login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                let data = null;
                try {
                    data = await response.json();
                } catch (jsonError) {
                    console.error('Invalid JSON response from login API:', jsonError);
                }
                
                if (!response.ok) {
                    messageDiv.className = 'message error';
                    messageDiv.textContent = (data && data.error) ? data.error : `Login failed (${response.status})`;
                    messageDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Login';
                    return;
                }
                
                if (data && data.success) {
                    // Store token and user data
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('user', JSON.stringify(data.user));
                    
                    // Show success message
                    messageDiv.className = 'message success';
                    messageDiv.textContent = 'Login successful! Redirecting...';
                    messageDiv.style.display = 'block';
                    
                    // Redirect based on role
                    setTimeout(() => {
                        if (data.user.role === 'admin') {
                            window.location.href = `${API_BASE}/views/admin/dashboard.php`;
                        } else {
                            window.location.href = `${API_BASE}/views/resident/dashboard.php`;
                        }
                    }, 1000);
                } else {
                    messageDiv.className = 'message error';
                    messageDiv.textContent = (data && data.error) ? data.error : 'Login failed';
                    messageDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Login';
                }
            } catch (error) {
                console.error('Fetch login error:', error);
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Network error. Please try again.';
                messageDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Login';
            }
        });
        
        // Check if already logged in
        const token = localStorage.getItem('token');
        if (token) {
            const user = JSON.parse(localStorage.getItem('user'));
            if (user && user.role === 'admin') {
                window.location.href = `${API_BASE}/views/admin/dashboard.php`;
            } else if (user) {
                window.location.href = `${API_BASE}/views/resident/dashboard.php`;
            }
        }
    </script>
</body>
</html>