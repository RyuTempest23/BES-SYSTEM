<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Barangay eServices</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: url('/BeSCMS/assets/images/bg1.png');
            background-size: 100% auto;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 20px;
        }
        
        .container {
            background: white;
            margin-top: 20px;
            padding: 15px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
            display: block;
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
        
        .info-box {
            background: #e3f2fd;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2196f3;
            font-size: 14px;
            color: #1565c0;
        }
        
        .form-group {
            margin-bottom: 5px;
            flex-shrink: 10;
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
            background: #48bb78;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #38a169;
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
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="/BeSCMS/assets/images/polog1.png" alt="Barangay Logo">
            <h1>Create Account</h1>
            <p>Barangay e-Services</p>
        </div>
        
        <div class="info-box">
            <strong>Note:</strong> You can only sign up if your name is already registered 
            in the barangay master list. Visit the barangay hall first if you're not listed.
        </div>
        
        <div id="message" class="message"></div>
        
        <form id="signupForm">
            <div class="form-group">
                <label>Full Name (exactly as registered)</label>
                <input type="text" id="full_name" required placeholder="e.g., Juan Dela Cruz">
            </div>
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="email" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label>Password (min 6 characters)</label>
                <input type="password" id="password" required placeholder="Create a password">
            </div>
            
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" id="confirm_password" required placeholder="Confirm your password">
            </div>
            
            <button type="submit" id="submitBtn">Create Account</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
    
    <script>
        const API_BASE = '/BeSCMS';
        
        document.getElementById('signupForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const full_name = document.getElementById('full_name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password').value;
            const submitBtn = document.getElementById('submitBtn');
            const messageDiv = document.getElementById('message');
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating account...';
            messageDiv.style.display = 'none';
            
            try {
                const response = await fetch(`${API_BASE}/index.php?route=auth&action=signup`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ 
                        full_name, 
                        email, 
                        password, 
                        confirm_password 
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    messageDiv.className = 'message success';
                    messageDiv.textContent = data.message + ' Redirecting to login...';
                    messageDiv.style.display = 'block';
                    
                    // Clear form
                    document.getElementById('signupForm').reset();
                    
                    // Redirect to login page after 2 seconds
                    setTimeout(() => {
                        window.location.href = `${API_BASE}/views/auth/login.php`;
                    }, 2000);
                } else {
                    messageDiv.className = 'message error';
                    messageDiv.textContent = data.error || 'Signup failed';
                    messageDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Create Account';
                }
            } catch (error) {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Network error. Please try again.';
                messageDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Account';
            }
        });
        
        // Check if already logged in
        const token = localStorage.getItem('token');
        if (token) {
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                if (user && user.role === 'admin') {
                    window.location.href = `${API_BASE}/views/admin/dashboard.php`;
                } else if (user) {
                    window.location.href = `${API_BASE}/views/resident/dashboard.php`;
                }
            }
        }
    </script>
</body>
</html>