<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Barangay eServices</title>
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
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 600px;
        }
        
        .logo img {
            max-width: 100px;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        
        .buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-secondary {
            background: #48bb78;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
        }
        
        .btn-secondary:hover {
            background: #38a169;
        }
        
        .features {
            margin-top: 40px;
            text-align: left;
            border-top: 1px solid #eee;
            padding-top: 30px;
        }
        
        .features h3 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .features ul {
            list-style: none;
            padding-left: 0;
        }
        
        .features li {
            padding: 8px 0;
            color: #555;
        }
        
        .features li:before {
            content: "✓ ";
            color: #48bb78;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="assets/images/polog1.png" alt="Barangay Logo" onerror="this.style.display='none'">
        </div>
        
        <h1>Barangay e-Services</h1>
        <h1>and Certificate Management System</h1>
        <p class="subtitle">BeSCMS</p>
        
        <div class="buttons">
            <a href="/BeSCMS/views/auth/login.php" class="btn btn-primary">Login</a>
            <a href="/BeSCMS/views/auth/signup.php" class="btn btn-secondary">Sign Up</a>
        </div>
        
        <div class="features">
            <h3>Available Services:</h3>
            <ul>
                <li>Request Barangay Certificates Online</li>
                <li>Barangay Indigency, Clearance, Residency</li>
                <li>Track Request Status</li>
                <li>Upload Verification Documents</li>
            </ul>
        </div>
    </div>
    
    <script>
        // Check if already logged in
        const token = localStorage.getItem('token');
        if (token) {
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                if (user.role === 'admin') {
                    window.location.href = '/BeSCMS/views/admin/dashboard.php';
                } else {
                    window.location.href = '/BeSCMS/views/resident/dashboard.php';
                }
            }
        }
    </script>
</body>
</html>