<!DOCTYPE html>
<html>
<head>
    <title>Verify Resident Accounts</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #764ba2; color: white; }
        button.approve { background: green; color: white; padding: 5px 10px; cursor: pointer; }
        button.reject { background: red; color: white; padding: 5px 10px; cursor: pointer; }
        img { max-width: 100px; max-height: 100px; }
    </style>
</head>
<body>
    <h2>Pending Account Verifications</h2>
    <div id="users-list">Loading...</div>
    <br><button onclick="logout()">Logout</button>

    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/BeSCMS/views/auth/login.php';

        async function loadPending() {
            const res = await fetch('/BeSCMS/admin?action=pending_verifications', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            if (!data.success) {
                document.getElementById('users-list').innerHTML = '<p>Error loading data.</p>';
                return;
            }
            if (data.data.length === 0) {
                document.getElementById('users-list').innerHTML = '<p>No pending verifications.</p>';
                return;
            }
            let html = '<table><tr><th>Name</th><th>Email</th><th>Uploaded ID</th><th>Action</th></tr>';
            data.data.forEach(user => {
                const idPath = user.verification_doc ? `/BeSCMS/uploads/${user.verification_doc}` : '';
                html += `<tr>
                    <td>${user.full_name}</td>
                    <td>${user.email}</td>
                    <td>${idPath ? `<a href="${idPath}" target="_blank">View ID</a>` : 'No ID'}</td>
                    <td>
                        <button class="approve" onclick="verify(${user.id}, 'approve')">Approve</button>
                        <button class="reject" onclick="verify(${user.id}, 'reject')">Reject</button>
                    </td>
                </tr>`;
            });
            html += '</table>';
            document.getElementById('users-list').innerHTML = html;
        }

        async function verify(userId, decision) {
            const res = await fetch('/BeSCMS/admin?action=verify_account', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                body: JSON.stringify({ user_id: userId, decision: decision })
            });
            const data = await res.json();
            if (data.success) {
                alert(`Account ${decision}d.`);
                loadPending();
            } else {
                alert('Error: ' + data.error);
            }
        }

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        loadPending();
    </script>
</body>
</html>