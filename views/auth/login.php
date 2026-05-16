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
            background: url('/BeSCMS/assets/images/bg1.png');
            background-size: 100% auto;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* MODIFIED: form on the right side — justify content to space-between, removed gap, added flex-wrap for responsiveness */
        .container {
            display: flex;
            width: 85%;
            max-width: 1100px;
            justify-content: space-between;
            /* pushes logo left, form right */
            align-items: center;
            gap: 0;
            /* removed original gap, space-between handles spacing */
            flex-wrap: wrap;
            /* ensures wrapping on narrow screens */
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-shrink: 0;
            /* prevents logo from shrinking */
        }

        .logo img {
            width: 150px;
            height: auto;
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

        /* New form card and text group styles to ensure form-group rules apply */
        .form-card {
            background: #fff;
            padding: 28px 32px;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            width: 100%;
            max-width: 420px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            flex-shrink: 0;
            /* ensures form doesn't shrink on smaller screens */
        }

        .text-group h1 {
            color: #1877f2;
            font-size: 2.4rem;
            margin: 0 0 6px 0;
        }

        .text-group p {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
            color: #333;
        }

        .logo {
            text-align: center;
            margin-bottom: 0;
        }

        .logo img {
            max-width: 140px;
            height: auto;
        }

        .forgot-password {
            display: block;
            margin: 15px 0;
            color: #1877f2;
            text-decoration: none;
            font-size: 0.9rem;
        }

        /* optional small-screen adjustment: form will be centered when wrapped, but on large screens it stays on the right */
        @media (max-width: 850px) {
            .container {
                justify-content: center;
                gap: 30px;
            }

            .form-card {
                margin-left: auto;
                margin-right: auto;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="/BeSCMS/assets/images/polog1.png" alt="Barangay Logo"> <!-- onerror="this.style.display='none' -->
            <div class="text-group">
                <h1>Brgy. Polo</h1>
                <p>Dapitan, Zamboanga del Norte</p>
            </div>
        </div>

        <form id="loginForm" class="form-card">
            <div id="message" class="message"></div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="email" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" required placeholder="Enter your password">
            </div>

            <button type="submit" id="submitBtn">Login</button>
            <a class="forgot-password" href="#">Forgot password?</a>
            <hr>
            <a class="register-link" href="signup.php">Create new account</a>
        </form>
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
                    body: JSON.stringify({
                        email,
                        password
                    })
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