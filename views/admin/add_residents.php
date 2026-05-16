<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Residents - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            padding: 30px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 { margin-bottom: 20px; color: #1e3c72; }
        .form-card {
            background: #f9fafb;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #334155;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
        }
        button {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-right: 10px;
        }
        button.danger { background: #ef4444; }
        button.warning { background: #f59e0b; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #eef2ff;
            color: #1e3a8a;
        }
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        .message.success { background: #dcfce7; color: #166534; display: block; }
        .message.error { background: #fee2e2; color: #b91c1c; display: block; }
        .actions button { margin: 2px; padding: 5px 10px; font-size: 12px; }
        .edit-form { background: #fef9e3; padding: 15px; margin-top: 10px; border-radius: 8px; display: none; }
    </style>
</head>
<body>
<div class="container">
    <h2>🏘️ Resident Management</h2>
    <div id="message" class="message"></div>

    <!-- Add Resident Form -->
    <div class="form-card">
        <h3>➕ Add New Resident</h3>
        <form id="addResidentForm">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" id="full_name" required>
            </div>
            <div class="form-group">
                <label>Birthdate</label>
                <input type="date" id="birthdate">
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" id="address">
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" id="contact_number">
            </div>
            <div class="form-group">
                <label>Registered Voter</label>
                <select id="registered_voter">
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                </select>
            </div>
            <button type="submit">Add Resident</button>
        </form>
    </div>

    <!-- Residents List -->
    <h3>📋 Residents List</h3>
    <div id="residentsList">Loading...</div>
</div>

<script>
    const API_BASE = '/BeSCMS';
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = `${API_BASE}/views/auth/login.php`;
    }

    // Helper to show message
    function showMessage(msg, type = 'error') {
        const msgDiv = document.getElementById('message');
        msgDiv.textContent = msg;
        msgDiv.className = `message ${type}`;
        setTimeout(() => {
            msgDiv.style.display = 'none';
            msgDiv.className = 'message';
        }, 4000);
    }

    // Fetch and display residents
    async function loadResidents() {
        try {
            const res = await fetch(`${API_BASE}/index.php?route=residents&action=list`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            if (data.success) {
                renderResidents(data.data);
            } else {
                document.getElementById('residentsList').innerHTML = '<p>Error loading residents.</p>';
                showMessage(data.error || 'Failed to load residents', 'error');
            }
        } catch (err) {
            console.error(err);
            document.getElementById('residentsList').innerHTML = '<p>Network error.</p>';
        }
    }

    function renderResidents(residents) {
        if (!residents.length) {
            document.getElementById('residentsList').innerHTML = '<p>No residents found.</p>';
            return;
        }
        let html = `<table>
            <thead>
                <tr><th>ID</th><th>Full Name</th><th>Birthdate</th><th>Address</th><th>Contact</th><th>Voter</th><th>Actions</th></tr>
            </thead>
            <tbody>`;
        residents.forEach(r => {
            html += `<tr id="row-${r.id}">
                        <td>${r.id}</td>
                        <td>${escapeHtml(r.full_name)}</td>
                        <td>${r.birthdate || '-'}</td>
                        <td>${escapeHtml(r.address) || '-'}</td>
                        <td>${r.contact_number || '-'}</td>
                        <td>${r.registered_voter}</td>
                        <td class="actions">
                            <button class="warning" onclick="showEditForm(${r.id}, '${escapeHtml(r.full_name)}', '${r.birthdate || ''}', '${escapeHtml(r.address) || ''}', '${r.contact_number || ''}', '${r.registered_voter}')">Edit</button>
                            <button class="danger" onclick="deleteResident(${r.id})">Delete</button>
                        </td>
                     </tr>
                     <tr id="edit-row-${r.id}" style="display:none;">
                        <td colspan="7">
                            <div class="edit-form" id="edit-form-${r.id}">
                                <h4>Edit Resident</h4>
                                <input type="text" id="edit-name-${r.id}" value="${escapeHtml(r.full_name)}" placeholder="Full Name" required>
                                <input type="date" id="edit-birthdate-${r.id}" value="${r.birthdate || ''}">
                                <input type="text" id="edit-address-${r.id}" value="${escapeHtml(r.address) || ''}" placeholder="Address">
                                <input type="text" id="edit-contact-${r.id}" value="${r.contact_number || ''}" placeholder="Contact">
                                <select id="edit-voter-${r.id}">
                                    <option value="no" ${r.registered_voter === 'no' ? 'selected' : ''}>No</option>
                                    <option value="yes" ${r.registered_voter === 'yes' ? 'selected' : ''}>Yes</option>
                                </select>
                                <button onclick="updateResident(${r.id})">Save</button>
                                <button onclick="cancelEdit(${r.id})">Cancel</button>
                            </div>
                        </td>
                     </tr>`;
        });
        html += `</tbody></table>`;
        document.getElementById('residentsList').innerHTML = html;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // Add resident
    document.getElementById('addResidentForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const payload = {
            full_name: document.getElementById('full_name').value.trim(),
            birthdate: document.getElementById('birthdate').value,
            address: document.getElementById('address').value,
            contact_number: document.getElementById('contact_number').value,
            registered_voter: document.getElementById('registered_voter').value
        };
        if (!payload.full_name) {
            showMessage('Full name is required', 'error');
            return;
        }
        try {
            const res = await fetch(`${API_BASE}/index.php?route=residents&action=add`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                showMessage('Resident added successfully', 'success');
                document.getElementById('addResidentForm').reset();
                loadResidents();
            } else {
                showMessage(data.error || 'Add failed', 'error');
            }
        } catch (err) {
            showMessage('Network error', 'error');
        }
    });

    // Show edit form inline
    window.showEditForm = function(id, name, birthdate, address, contact, voter) {
        const editRow = document.getElementById(`edit-row-${id}`);
        if (editRow.style.display === 'none' || !editRow.style.display) {
            editRow.style.display = 'table-row';
        } else {
            editRow.style.display = 'none';
        }
    };

    window.cancelEdit = function(id) {
        document.getElementById(`edit-row-${id}`).style.display = 'none';
    };

    window.updateResident = async function(id) {
        const payload = {
            id: id,
            full_name: document.getElementById(`edit-name-${id}`).value.trim(),
            birthdate: document.getElementById(`edit-birthdate-${id}`).value,
            address: document.getElementById(`edit-address-${id}`).value,
            contact_number: document.getElementById(`edit-contact-${id}`).value,
            registered_voter: document.getElementById(`edit-voter-${id}`).value
        };
        if (!payload.full_name) {
            showMessage('Name cannot be empty', 'error');
            return;
        }
        try {
            const res = await fetch(`${API_BASE}/index.php?route=residents&action=edit`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                showMessage('Resident updated', 'success');
                document.getElementById(`edit-row-${id}`).style.display = 'none';
                loadResidents();
            } else {
                showMessage(data.error || 'Update failed', 'error');
            }
        } catch (err) {
            showMessage('Network error', 'error');
        }
    };

    window.deleteResident = async function(id) {
        if (!confirm('Are you sure you want to delete this resident? This will only work if no user account is linked.')) return;
        try {
            const res = await fetch(`${API_BASE}/index.php?route=residents&action=delete&id=${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            if (data.success) {
                showMessage('Resident deleted', 'success');
                loadResidents();
            } else {
                showMessage(data.error || 'Delete failed', 'error');
            }
        } catch (err) {
            showMessage('Network error', 'error');
        }
    };

    // Initial load
    loadResidents();
</script>
</body>
</html>