<!DOCTYPE html>
<html>

<head>
    <title>Verify Resident Accounts | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial;
            padding: 20px;
            background: #f3f4f6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border-bottom: 1px solid #e5e7eb;
            padding: 16px;
            text-align: left;
        }

        th {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        tbody tr {
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: #f9fafb;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        button.approve {
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            margin-right: 5px;
            font-weight: 600;
            transition: background 0.2s;
        }

        button.reject {
            background: #ef4444;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s;
        }

        button.view-doc {
            background: #3b82f6;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s;
        }

        button.approve:hover {
            background: #059669;
        }

        button.reject:hover {
            background: #dc2626;
        }

        button.view-doc:hover {
            background: #2563eb;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-8 rounded-lg mb-6 shadow-lg">
            <h1 class="text-3xl font-bold mb-2"><i class="fas fa-shield-alt mr-2"></i>Account Verification</h1>
            <p class="text-blue-100 mb-3">Review and verify resident identity documents for account authentication</p>
            <p class="text-blue-100 text-sm"><i class="fas fa-info-circle mr-1"></i>Click "View ID" to inspect the uploaded document, then approve or reject the verification request.</p>
        </div>

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">🔐 Account Verification</h2>
                <p class="text-gray-500 mt-1">Review and verify resident ID documents</p>
            </div>
            <div class="flex gap-2">
                <select id="statusFilter" class="bg-blue-50 text-gray-700 px-4 py-2 rounded border border-blue-200 font-medium" onchange="loadPendingVerifications()">
                    <option value="pending">⏳ Pending</option>
                    <option value="approved">✓ Approved</option>
                    <option value="rejected">✗ Rejected</option>
                </select>
                <a href="dashboard.php" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded transition">← Dashboard</a>
            </div>
        </div>
        <div id="users-list">Loading verifications...</div>
        <button onclick="logout()" class="mt-6 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition">Logout</button>
    </div>

    <!-- Modal to view document -->
    <div id="docModal" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-4xl h-5/6 flex flex-col">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-800"><i class="fas fa-file-alt mr-2 text-blue-600"></i>ID Document Preview</h3>
                <button onclick="closeDocModal()" class="text-gray-400 hover:text-gray-600 text-3xl transition">&times;</button>
            </div>
            <div id="docContent" class="flex-1 overflow-auto bg-gray-50 rounded flex items-center justify-center p-6">
                <img id="docImage" style="max-width: 100%; max-height: 100%; object-fit: contain;" />
                <iframe id="docPdf" style="width: 100%; height: 100%; border: none; display: none; border-radius: 8px;"></iframe>
                <div id="docLoading" style="display: none;" class="text-center">
                    <div class="inline-block animate-spin">
                        <i class="fas fa-spinner text-blue-600 text-4xl"></i>
                    </div>
                    <p class="text-gray-600 mt-4">Loading document...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '/BeSCMS';
        const token = localStorage.getItem('token');
        let adminUser = JSON.parse(localStorage.getItem('user') || '{}');

        console.log('Token:', token ? 'Present' : 'Missing');
        console.log('Admin User:', adminUser);
        console.log('Role:', adminUser.role);

        if (!token || !adminUser.id || adminUser.role !== 'admin') {
            console.error('Admin access denied. User role:', adminUser.role);
            alert('⛔ Admin access only. Your role: ' + (adminUser.role || 'none'));
            window.location.href = 'dashboard.php';
        }

        console.log('Admin authenticated. Loading verifications...');

        async function loadPendingVerifications() {
            const status = document.getElementById('statusFilter').value;
            const container = document.getElementById('users-list');
            container.innerHTML = '<p class="text-gray-500">Loading...</p>';

            try {
                const response = await fetch(`${API_BASE}/index.php?route=admin&action=pending_verifications&status=${status}`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                const data = await response.json();

                if (!data.success) {
                    container.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">Error: ${data.error || 'Failed to load verifications'}</div>`;
                    return;
                }

                if (!data.data || data.data.length === 0) {
                    const messages = {
                        pending: 'No pending verifications at this time.',
                        approved: 'No approved accounts.',
                        rejected: 'No rejected accounts.'
                    };
                    container.innerHTML = `<div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded"><i class="fas fa-info-circle mr-2"></i>${messages[status]}</div>`;
                    return;
                }

                let html = '<table><thead><tr><th>Name</th><th>Email</th><th>Address</th><th>Status</th><th>Uploaded Document</th><th>Action</th></tr></thead><tbody>';
                data.data.forEach(user => {
                    const docBtn = user.verification_doc ?
                        `<button class="view-doc" onclick="viewDocument('${user.verification_doc}')"><i class="fas fa-eye mr-1"></i>View ID</button>` :
                        '<span class="text-gray-400 text-sm">No file uploaded</span>';
                    const statusBadge = user.verification_status === 'pending' ?
                        '<span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-semibold">⏳ Pending</span>' :
                        user.verification_status === 'approved' ?
                        '<span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">✓ Approved</span>' :
                        '<span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">✗ Rejected</span>';

                    const actionBtns = user.verification_status === 'pending' ?
                        `<div class="flex gap-2">
                            <button class="approve" onclick="verifyUser(${user.id}, 'approve')"><i class="fas fa-check mr-1"></i>Approve</button>
                            <button class="reject" onclick="verifyUser(${user.id}, 'reject')"><i class="fas fa-times mr-1"></i>Reject</button>
                         </div>` :
                        '<span class="text-gray-400 text-sm">-</span>';

                    html += `
                    <tr>
                        <td><strong>${escapeHtml(user.full_name)}</strong></td>
                        <td>${escapeHtml(user.email)}</td>
                        <td>${escapeHtml(user.address || '-')}</td>
                        <td>${statusBadge}</td>
                        <td>${docBtn}</td>
                        <td>${actionBtns}</td>
                    </tr>
                    `;
                });
                html += '</tbody></table>';
                container.innerHTML = html;
            } catch (err) {
                console.error('Error loading verifications:', err);
                container.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded"><i class="fas fa-exclamation-circle mr-2"></i>Error loading verifications. Please check the console for details.</div>`;
            }
        }

        async function verifyUser(userId, decision) {
            const decisionText = decision === 'approve' ? 'APPROVE' : 'REJECT';
            if (!confirm(`Are you sure you want to ${decisionText} this account?`)) {
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/index.php?route=admin&action=verify_account`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        decision: decision
                    })
                });
                const data = await response.json();

                if (data.success) {
                    const message = decision === 'approve' ?
                        '✓ Account approved successfully!' :
                        '✗ Account rejected';
                    alert(message);
                    loadPendingVerifications();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                    console.error('Verification error:', data);
                }
            } catch (err) {
                console.error('Error verifying user:', err);
                alert('Error updating verification status. Please try again.');
            }
        }

        function viewDocument(filename) {
            const img = document.getElementById('docImage');
            const pdf = document.getElementById('docPdf');
            const ext = filename.split('.').pop().toLowerCase();

            // Fetch the file with proper Authorization header
            fetch(`${API_BASE}/serve_file.php?file=${encodeURIComponent(filename)}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.blob();
                })
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    if (ext === 'pdf') {
                        img.style.display = 'none';
                        pdf.style.display = 'block';
                        pdf.src = url;
                    } else {
                        pdf.style.display = 'none';
                        img.style.display = 'block';
                        img.src = url;
                    }
                    document.getElementById('docModal').classList.remove('hidden');
                })
                .catch(err => {
                    console.error('Error loading document:', err);
                    alert('Failed to load document. Please try again.');
                });
        }

        function closeDocModal() {
            document.getElementById('docModal').classList.add('hidden');
            document.getElementById('docImage').src = '';
            document.getElementById('docPdf').src = '';
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;'
            } [m]));
        }

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        // Initial load
        loadPendingVerifications();
    </script>
</body>

</html>