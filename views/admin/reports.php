<!DOCTYPE html>
<html>
<head>
    <title>Yearly Reports</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #764ba2; color: white; }
        select, button { padding: 8px; margin-right: 10px; }
    </style>
</head>
<body>
    <h2>Completed Transactions Report</h2>
    <div>
        <label>Select Year:</label>
        <select id="year">
            <?php for ($y = 2023; $y <= date('Y'); $y++): ?>
                <option value="<?php echo $y; ?>" <?php echo $y == date('Y') ? 'selected' : ''; ?>><?php echo $y; ?></option>
            <?php endfor; ?>
        </select>
        <button onclick="loadReport()">Generate Report</button>
    </div>
    <div id="report">Select a year and click Generate.</div>
    <br><button onclick="logout()">Logout</button>

    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/BeSCMS/views/auth/login.php';

        async function loadReport() {
            const year = document.getElementById('year').value;
            const res = await fetch(`/BeSCMS/admin?action=yearly_report&year=${year}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            if (!data.success) {
                document.getElementById('report').innerHTML = '<p>Error loading report.</p>';
                return;
            }
            if (data.data.length === 0) {
                document.getElementById('report').innerHTML = `<p>No completed transactions for ${year}.</p>`;
                return;
            }
            let html = `<h3>Year: ${year}</h3><table><tr>
                <th>Resident Name</th><th>Certificate Type</th><th>Purpose</th>
                <th>Quantity</th><th>Completed Date</th><th>Admin Notes</th>
            </tr>`;
            data.data.forEach(row => {
                html += `<tr>
                    <td>${row.full_name}</td>
                    <td>${row.certificate_type}</td>
                    <td>${row.purpose}</td>
                    <td>${row.quantity}</td>
                    <td>${row.completed_at}</td>
                    <td>${row.admin_notes || ''}</td>
                </tr>`;
            });
            html += '</table>';
            document.getElementById('report').innerHTML = html;
        }

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }
    </script>
</body>
</html>