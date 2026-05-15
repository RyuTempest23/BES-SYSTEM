<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .header { background: #764ba2; color: white; padding: 20px; border-radius: 5px; }
        button { background: #c33; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .links { margin-top: 20px; }
        .links a { display: block; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2 id="welcome"></h2>
        <p>Role: Administrator</p>
    </div>
    
    <div class="links">
        <h3>Admin Functions:</h3>
        <a href="/BeSCMS/views/admin/pending_requests.php">Pending Certificate Requests</a>
        <a href="/BeSCMS/views/admin/verify_accounts.php">Verify Resident Accounts</a>
        <a href="/BeSCMS/views/admin/reports.php">Generate Reports</a>
    </div>
    
    <button onclick="logout()">Logout</button>
    
    <script>
        const token = localStorage.getItem('token');
        const user = JSON.parse(localStorage.getItem('user'));
        
        if (!token || !user || user.role !== 'admin') {
            window.location.href = '/BeSCMS/views/auth/login.php';
        }
        
        document.getElementById('welcome').innerHTML = `Welcome, ${user.name || 'Admin'}!`;
        
        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }
    </script>
</body>
</html>