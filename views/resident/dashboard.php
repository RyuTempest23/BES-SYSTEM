<!DOCTYPE html>
<html>
<head>
    <title>Resident Dashboard</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .header { background: #667eea; color: white; padding: 20px; border-radius: 5px; }
        button { background: #c33; color: white; padding: 10px 20px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="header">
        <h2 id="welcome"></h2>
        <p>Role: Resident</p>
        <p>Verification Status: <span id="verifyStatus"></span></p>
    </div>
    
    <div style="margin-top: 20px;">
        <h3>Quick Links</h3>
        <p><a href="/BeSCMS/views/resident/request_form.php">Request Certificate</a></p>
        <p><a href="/BeSCMS/views/resident/my_requests.php">My Requests</a></p>
    </div>
    
    <button onclick="logout()">Logout</button>
    
    <script>
        const token = localStorage.getItem('token');
        const user = JSON.parse(localStorage.getItem('user'));
        
        if (!token || !user) {
            window.location.href = '/BeSCMS/views/auth/login.php';
        }
        
        document.getElementById('welcome').innerHTML = `Welcome, ${user.name || 'Resident'}!`;
        document.getElementById('verifyStatus').innerHTML = user.verification_status;
        
        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }
    </script>
</body>
</html>