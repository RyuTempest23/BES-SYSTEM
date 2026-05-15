<!DOCTYPE html>
<html>
<head>
    <title>Pending Requests - Admin</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #764ba2; color: white; }
        button { padding: 5px 10px; margin: 2px; cursor: pointer; }
        .approve { background: green; color: white; }
        .reject { background: red; color: white; }
        .notes { width: 200px; }
    </style>
</head>
<body>
    <h2>Pending Certificate Requests</h2>
    <div id="requests-list">Loading...</div>
    <br>
    <button onclick="logout()">Logout</button>

    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/BeSCMS/views/auth/login.php';

        async function loadRequests() {
            const res = await fetch('/BeSCMS/admin?action=pending_requests', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
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
        }

        async function approve(id) {
            const notes = document.getElementById(`notes_${id}`).value;
            const res = await fetch('/BeSCMS/admin?action=approve_request', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                body: JSON.stringify({ request_id: id, admin_notes: notes })
            });
            const data = await res.json();
            if (data.success) {
                alert('Approved! You can now print the certificate.');
                window.open(`/BeSCMS/views/admin/print_certificate.php?id=${id}`, '_blank');
                loadRequests();
            } else {
                alert('Error: ' + data.error);
            }
        }

        async function reject(id) {
            const notes = document.getElementById(`notes_${id}`).value;
            if (!confirm('Reject this request?')) return;
            const res = await fetch('/BeSCMS/admin?action=reject_request', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                body: JSON.stringify({ request_id: id, admin_notes: notes })
            });
            const data = await res.json();
            if (data.success) {
                alert('Request rejected');
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