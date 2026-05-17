<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Barangay Polo</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        .sidebar {
            width: 260px;
            background-color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e0e0e0;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            z-index: 1000;
        }
        .brand {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .brand-text {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            line-height: 1.2;
        }
        .menu-section {
            padding: 20px 0;
        }
        .menu-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #999;
            padding: 0 20px 10px;
            font-weight: 600;
        }
        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #555;
            text-decoration: none;
            transition: 0.2s;
            font-size: 14px;
        }
        .menu-item:hover, .menu-item.active {
            background-color: #f0f0f0;
            color: #000;
            border-left: 3px solid #004080;
        }
        .menu-icon { margin-right: 12px; font-size: 16px; width: 20px; text-align: center; }
        .menu-arrow { margin-left: auto; font-size: 12px; }
        .sidebar-bottom {
            margin-top: auto;
            padding: 20px;
            border-top: 1px solid #eee;
        }
        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 20px 30px;
            overflow-y: auto;
            background: #f4f6f9;
        }
        .topbar {
            margin-bottom: 30px;
        }
        .topbar h1 { font-size: 26px; color: #333; }
        .topbar p { color: #888; margin-top: 4px; }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            border: 1px solid #eee;
        }
        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }
        .chart-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .pie-chart-wrapper {
            display: flex;
            align-items: center;
            width: 100%;
            gap: 40px;
            flex-wrap: wrap;
            justify-content: center;
        }
        canvas {
            width: 200px;
            height: 200px;
        }
        .chart-legend {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #555;
        }
        .legend-color {
            width: 16px;
            height: 12px;
            display: inline-block;
        }
        .status-card {
            background: white;
            padding: 15px 20px;
            border-radius: 12px;
            border: 1px solid #eee;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .status-card-title { font-size: 16px; color: #333; font-weight: 500; }
        .status-card-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-status {
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-blue { background-color: #4a9eff; }
        .btn-yellow { background-color: #ffc107; color: #333; }
        .btn-pink { background-color: #d63384; }
        .status-number {
            font-size: 28px;
            font-weight: 600;
            color: #333;
        }
        .content-bottom {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #eee;
        }
        .content-bottom-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #333;
        }
        .bottom-date {
            margin-top: 30px;
            color: #888;
            font-size: 14px;
        }
        @media (max-width: 992px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .pie-chart-wrapper { flex-direction: column; align-items: center; }
        }
        @media (max-width: 768px) {
            .sidebar { width: 0; overflow: hidden; transition: 0.3s; }
            .main-content { margin-left: 0; width: 100%; padding: 15px; }
            .sidebar.open { width: 260px; }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR (logout button remains at bottom) -->
    <nav class="sidebar" id="sidebar">
        <div class="brand">
            <img src="/BeSCMS/assets/images/polog1.png" alt="Polo Logo">
            <div class="brand-text">Barangay Polo<br></div>
        </div>
        <div class="menu-section">
            <div class="menu-label">General</div>
            <a href="#" class="menu-item active"><span class="menu-icon">🏠</span> Dashboard</a>
            <a href="#" class="menu-item"><span class="menu-icon">👤</span> Barangay Officials</a>
            <a href="/BeSCMS/views/admin/add_residents.php" class="menu-item"><span class="menu-icon">👥</span> Residents</a>
            <a href="/BeSCMS/views/admin/pending_requests.php" class="menu-item"><span class="menu-icon">📄</span> Certification</a>
            <a href="/BeSCMS/views/admin/pending_requests.php" class="menu-item"><span class="menu-icon">🌐</span> Online Request</a>
            <a href="/BeSCMS/views/admin/verify_accounts.php" class="menu-item"><span class="menu-icon">🔑</span> Accounts</a>
        </div>
        <div class="menu-section">
            <div class="menu-label">Settings</div>
            <a href="#" class="menu-item"><span class="menu-icon">⚙️</span> Barangay Details</a>
        </div>
        <div class="sidebar-bottom">
            <button onclick="logout()" style="width:100%; background:#dc2626; color:white; border:none; padding:10px; border-radius:6px; cursor:pointer;">🚪 Logout</button>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="topbar">
            <div>
                <h1>Dashboard</h1>
                <p>Dashboard</p>
            </div>
            <!-- Removed user-profile (Admin text and avatar) -->
        </div>

        <div class="dashboard-grid">
            <!-- LEFT COLUMN: Pie Chart for Registered Voters -->
            <div class="card">
                <div class="card-header">
                    <span>📊</span> Registered Voters
                </div>
                <div class="chart-container">
                    <div class="pie-chart-wrapper">
                        <canvas id="voterPieChart" width="200" height="200"></canvas>
                        <div id="voterLegend" class="chart-legend"></div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Status Cards -->
            <div>
                <div class="status-card">
                    <div class="status-card-title">Pending request</div>
                    <div class="status-card-content">
                        <a href="/BeSCMS/views/admin/pending_requests.php" class="btn-status btn-blue">View</a>
                        <div class="status-number" id="pendingCount">0</div>
                    </div>
                </div>
                <div class="status-card">
                    <div class="status-card-title">To verify accounts</div>
                    <div class="status-card-content">
                        <a href="/BeSCMS/views/admin/verify_accounts.php" class="btn-status btn-yellow">View</a>
                        <div class="status-number" id="verifyCount">0</div>
                    </div>
                </div>
                <div class="status-card">
                    <div class="status-card-title">Total residents</div>
                    <div class="status-card-content">
                        <a href="/BeSCMS/views/admin/add_residents.php" class="btn-status btn-pink">View</a>
                        <div class="status-number" id="residentCount">0</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-bottom">
            <div class="content-bottom-header">
                <span>📢</span> Recent Announcements
            </div>
            <p style="margin-top: 10px; color: #666;">No new announcements.</p>
        </div>

        <div class="bottom-date">
            Today is <?php echo date('l Y/m/d'); ?>
        </div>
    </main>

    <script>
        const API_BASE = '/BeSCMS';
        const token = localStorage.getItem('token');
        const user = JSON.parse(localStorage.getItem('user'));

        if (!token || !user || user.role !== 'admin') {
            window.location.href = '/BeSCMS/views/auth/login.php';
        }
        // Removed welcomeText display since the element is gone

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        // Draw pie chart on canvas
        function drawPieChart(ctx, centerX, centerY, radius, segments) {
            let startAngle = -Math.PI / 2;
            for (let i = 0; i < segments.length; i++) {
                const segment = segments[i];
                const endAngle = startAngle + (segment.value * Math.PI * 2);
                ctx.beginPath();
                ctx.fillStyle = segment.color;
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                ctx.fill();
                startAngle = endAngle;
            }
        }

        async function loadVoterStats() {
            try {
                const res = await fetch(`${API_BASE}/index.php?route=residents&action=list&limit=1000`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                if (!data.success) throw new Error('Failed to fetch residents');

                const residents = data.data;
                let yesCount = 0, noCount = 0;
                for (const r of residents) {
                    if (r.registered_voter === 'yes') yesCount++;
                    else noCount++;
                }
                const total = yesCount + noCount;
                if (total === 0) {
                    document.getElementById('voterLegend').innerHTML = '<div>No resident data yet.</div>';
                    return;
                }

                const yesPercent = yesCount / total;
                const noPercent = noCount / total;

                const canvas = document.getElementById('voterPieChart');
                const ctx = canvas.getContext('2d');
                const size = canvas.width;
                const center = size / 2;
                const radius = size / 2 - 2;

                ctx.clearRect(0, 0, size, size);
                drawPieChart(ctx, center, center, radius, [
                    { value: yesPercent, color: '#198754' },
                    { value: noPercent, color: '#dc3545' }
                ]);

                document.getElementById('voterLegend').innerHTML = `
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: #198754;"></span> Registered Voters (YES) — ${yesCount}
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: #dc3545;"></span> Not Registered (NO) — ${noCount}
                    </div>
                `;
            } catch (err) {
                console.error(err);
                document.getElementById('voterLegend').innerHTML = '<div>Error loading data.</div>';
            }
        }

        async function loadCounts() {
            try {
                let res = await fetch(`${API_BASE}/index.php?route=admin&action=pending_requests`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                let data = await res.json();
                if (data.success) document.getElementById('pendingCount').innerText = data.count || 0;

                res = await fetch(`${API_BASE}/index.php?route=admin&action=pending_verifications`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                data = await res.json();
                if (data.success) document.getElementById('verifyCount').innerText = typeof data.count === 'number' ? data.count : (data.data ? data.data.length : 0);

                res = await fetch(`${API_BASE}/index.php?route=residents&action=list&limit=1`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                data = await res.json();
                if (data.success) document.getElementById('residentCount').innerText = data.pagination?.total || 0;
            } catch (error) {
                console.error(error);
            }
        }

        loadVoterStats();
        loadCounts();
    </script>
</body>
</html>