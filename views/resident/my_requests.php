<!DOCTYPE html>
<html>
<head>
    <title>My Requests</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #667eea; color: white; }
        button { padding: 5px 10px; margin: 2px; cursor: pointer; }
        .cancel { background: orange; color: white; }
        .resubmit { background: blue; color: white; }
        .status-pending { color: orange; font-weight: bold; }
        .status-approved { color: green; font-weight: bold; }
        .status-completed { color: blue; font-weight: bold; }
        .status-cancelled, .status-rejected { color: red; font-weight: bold; }
        .filter { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>My Certificate Requests</h2>
    <div class="filter">
        <label>Filter by status:</label>
        <select id="statusFilter">
            <option value="all">All</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
            <option value="rejected">Rejected</option>
        </select>
        <button onclick="loadRequests()">Refresh</button>
    </div>
    <div id="requests">Loading...</div>
    <br><a href="/BeSCMS/views/resident/request_form.php">New Request</a> | <a href="/BeSCMS/views/resident/dashboard.php">Dashboard</a>
    <br><br><button onclick="logout()">Logout</button>

    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/BeSCMS/views/auth/login.php';

        async function loadRequests() {
            const status = document.getElementById('statusFilter').value;
            const url = status === 'all' ? '/BeSCMS/requests?action=my_requests' : `/BeSCMS/requests?action=my_requests&status=${status}`;
            const res = await fetch(url, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            if (!data.success) {
                document.getElementById('requests').innerHTML = '<p>Error loading requests.</p>';
                return;
            }
            if (data.data.length === 0) {
                document.getElementById('requests').innerHTML = '<p>No requests found.</p>';
                return;
            }
            let html = '<table><tr><th>ID</th><th>Type</th><th>Purpose</th><th>Qty</th><th>Status</th><th>Admin Notes</th><th>Actions</th></tr>';
            data.data.forEach(req => {
                let statusClass = `status-${req.status}`;
                let actions = '';
                if (req.status === 'pending') {
                    actions = `<button class="cancel" onclick="cancelRequest(${req.id})">Cancel</button>
                               <button class="resubmit" onclick="resubmitRequest(${req.id})">Resubmit</button>`;
                } else if (req.status === 'cancelled') {
                    actions = `<button class="resubmit" onclick="resubmitRequest(${req.id})">Resubmit</button>`;
                } else if (req.status === 'rejected') {
                    actions = `<span>Rejected - you may create a new request.</span>`;
                } else if (req.status === 'approved') {
                    actions = `<span>Waiting for barangay release</span>`;
                }
                html += `<tr>
                    <td>${req.id}</td>
                    <td>${req.certificate_type}</td>
                    <td>${req.purpose}</td>
                    <td>${req.quantity}</td>
                    <td class="${statusClass}">${req.status.toUpperCase()}</td>
                    <td>${req.admin_notes || ''}</td>
                    <td>${actions}</td>
                </tr>`;
            });
            html += '</table>';
            document.getElementById('requests').innerHTML = html;
        }

        async function cancelRequest(id) {
            if (!confirm('Cancel this request?')) return;
            const res = await fetch('/BeSCMS/requests?action=cancel', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                body: JSON.stringify({ request_id: id })
            });
            const data = await res.json();
            if (data.success) {
                alert('Request cancelled');
                loadRequests();
            } else {
                alert('Error: ' + data.error);
            }
        }

        async function resubmitRequest(id) {
            const newQty = prompt('Enter new quantity:', '1');
            if (!newQty || newQty < 1) return;
            const res = await fetch('/BeSCMS/requests?action=resubmit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                body: JSON.stringify({ request_id: id, quantity: parseInt(newQty) })
            });
            const data = await res.json();
            if (data.success) {
                alert('Request resubmitted');
                loadRequests();
            } else {
                alert('Error: ' + data.error);
            }
        }

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        loadRequests();
    </script>
</body>
</html>