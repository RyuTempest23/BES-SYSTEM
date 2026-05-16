<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            padding: 30px;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header h2 {
            font-size: 28px;
            margin-bottom: 8px;
        }
        .links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: #333;
            display: block;
            border: 1px solid #e0e7ff;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border-color: #764ba2;
        }
        .card h3 {
            color: #764ba2;
            margin-bottom: 10px;
        }
        .card p {
            color: #666;
            font-size: 14px;
        }
        .logout-btn {
            background: #dc2626;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: background 0.2s;
        }
        .logout-btn:hover {
            background: #b91c1c;
        }
        @media (max-width: 600px) {
            body { padding: 15px; }
            .header h2 { font-size: 22px; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2 id="welcome"></h2>
        <p>Role: Administrator</p>
    </div>

    <div class="links">
        <a href="/BeSCMS/views/admin/pending_requests.php" class="card">
            <h3>📄 Pending Certificate Requests</h3>
            <p>Approve, print, or reject requests</p>
        </a>
        <a href="/BeSCMS/views/admin/verify_accounts.php" class="card">
            <h3>✅ Verify Resident Accounts</h3>
            <p>Review uploaded IDs and activate accounts</p>
        </a>
        <a href="/BeSCMS/views/admin/reports.php" class="card">
            <h3>📊 Generate Reports</h3>
            <p>Yearly completed transactions report</p>
        </a>
        <a href="/BeSCMS/views/admin/add_residents.php" class="card">
            <h3>👥 Manage Residents</h3>
            <p>Add, edit, delete residents from master list</p>
        </a>
    </div>

    <button onclick="logout()" class="logout-btn">🚪 Logout</button>
</div>

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