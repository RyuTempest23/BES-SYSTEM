<!DOCTYPE html>
<html>

<head>
    <title>Pending Requests - Admin</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #764ba2;
            color: white;
        }

        button {
            padding: 5px 10px;
            margin: 2px;
            cursor: pointer;
        }

        .approve {
            background: green;
            color: white;
        }

        .reject {
            background: red;
            color: white;
        }

        .completed {
            background: #1d4ed8;
            color: white;
        }

        .notes {
            width: 200px;
        }
    </style>
</head>

<body>
    <h2>Pending Certificate Requests</h2>
    <div id="requests-list">Loading...</div>
    <h2 style="margin-top:30px">Approved Certificates</h2>
    <div id="approved-list">Loading approved requests...</div>
    <br>
    <a href="dashboard.php" style="display:inline-block;padding:8px 14px;background:#64748b;color:#fff;border-radius:6px;text-decoration:none;margin-top:10px;">← Back to Dashboard</a>

    <script>
        const API_BASE = '/BeSCMS';
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/BeSCMS/views/auth/login.php';

        async function loadRequests() {
            const res = await fetch(`${API_BASE}/index.php?route=admin&action=pending_requests`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            const data = await res.json();
            // Always load approved list even if there are no pending requests
            loadApprovedRequests();
            if (!data.success) return document.getElementById('requests-list').innerHTML = '<p>Error loading requests</p>';
            if (data.data.length === 0) {
                document.getElementById('requests-list').innerHTML = '<p>No pending requests.</p>';
                return;
            }
            let html = '<table><tr><th>ID</th><th>Resident</th><th>Certificate Type</th><th>Purpose</th><th>Qty</th><th>Admin Notes</th><th>Actions</th></tr>';
            data.data.forEach(req => {
                html += `<tr>
                            <td>${req.id}</td>
                            <td>${req.full_name}</td>
                            <td>${req.certificate_type}</td>
                            <td>${req.purpose}</td>
                            <td>${req.quantity}</td>
                            <td><input type="text" id="notes_${req.id}" placeholder="Optional notes" class="notes"></td>
                            <td>
                                <button class="approve" onclick="approve(${req.id})">Approve & Print</button>
                                <button class="reject" onclick="reject(${req.id})">Reject</button>
                            </td>
                        </tr>`;
            });
            html += '</table>';
            document.getElementById('requests-list').innerHTML = html;
            // After pending loaded, also load approved requests
            loadApprovedRequests();
        }

        async function loadApprovedRequests() {
            const res = await fetch(`${API_BASE}/index.php?route=admin&action=approved_requests`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            if (!data.success) return document.getElementById('approved-list').innerHTML = '<p>Error loading approved requests</p>';
            if (data.data.length === 0) {
                document.getElementById('approved-list').innerHTML = '<p>No approved requests.</p>';
                return;
            }
            let html = '<table><tr><th>ID</th><th>Resident</th><th>Certificate Type</th><th>Purpose</th><th>Qty</th><th>Admin Notes</th><th>Actions</th></tr>';
            data.data.forEach(req => {
                html += `<tr>
                            <td>${req.id}</td>
                            <td>${req.full_name}</td>
                            <td>${req.certificate_type}</td>
                            <td>${req.purpose}</td>
                            <td>${req.quantity}</td>
                            <td>${req.admin_notes ?? ''}</td>
                            <td>
                                            <button class="completed" onclick="markCompleted(${req.id})">Completed</button>
                                        </td>
                        </tr>`;
            });
            html += '</table>';
            document.getElementById('approved-list').innerHTML = html;
        }

        async function approve(id) {
            const notes = document.getElementById(`notes_${id}`).value;
            const res = await fetch(`${API_BASE}/index.php?route=admin&action=approve_request`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    request_id: id,
                    admin_notes: notes
                })
            });
            const data = await res.json();
            if (data.success) {
                alert('Approved! You can now print the certificate.');
                window.open(`${API_BASE}/views/admin/print_certificate.php?id=${id}`, '_blank');
                loadRequests();
            } else {
                alert('Error: ' + data.error);
            }
        }

        async function reject(id) {
            const notes = document.getElementById(`notes_${id}`).value;
            if (!confirm('Reject this request?')) return;
            const res = await fetch(`${API_BASE}/index.php?route=admin&action=reject_request`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    request_id: id,
                    admin_notes: notes
                })
            });
            const data = await res.json();
            if (data.success) {
                alert('Request rejected');
                    loadRequests();
            } else {
                alert('Error: ' + data.error);
            }
        }


        async function markCompleted(id) {
            if (!confirm('Mark this approved request as completed?')) return;
            const res = await fetch(`${API_BASE}/index.php?route=admin&action=mark_completed`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ request_id: id })
            });
            const data = await res.json();
            if (data.success) {
                alert('Request marked as completed');
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