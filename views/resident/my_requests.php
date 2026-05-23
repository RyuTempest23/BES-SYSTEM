<?php
// my_requests.php - User requests listing with status filters (No "All" tab)
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Requests | Barangay Polo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-approved {
            background: #d1fae5;
            color: #059669;
        }

        .status-rejected {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-completed {
            background: #dbeafe;
            color: #2563eb;
        }

        .status-cancelled {
            background: #f3f4f6;
            color: #6b7280;
        }

        .filter-active {
            background-color: #2563eb !important;
            color: white !important;
        }
    </style>
</head>

<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <aside class="w-72 bg-white shadow-lg border-r border-gray-200 fixed h-full z-10 flex flex-col justify-between">
            <div>
                <div class="p-6 border-b cursor-pointer hover:bg-gray-50 transition" id="sidebarUser" onclick="goToProfile()"></div>
                <nav class="mt-6 px-4 space-y-2">
                    <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                    <a href="my_requests.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-blue-50 text-blue-700 font-medium"><i class="fas fa-file-alt"></i><span>My Request</span></a>
                    <a href="upload_id.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><i class="fas fa-id-card"></i><span>Verify Account</span></a>
                </nav>
                <div id="adminLinksContainer" class="px-4 mt-4"></div>
            </div>
            <div>
                <div class="p-6 border-t border-gray-100">
                    <button onclick="logout()" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </div>
        </aside>

        <div class="flex-1 ml-72">
            <header class="bg-white shadow-sm px-6 py-3 flex justify-between items-center border-b">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center"><i class="fas fa-landmark text-amber-700"></i></div>
                    <span class="font-semibold">Barangay Polo, Dapitan City, Zamboanga Del Norte</span>
                </div>
            </header>

            <div class="p-6">
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">📋 My Requests</h2>
                    <p class="text-gray-500 mb-6">Track the status of your barangay certificate requests</p>

                    <!-- Filter Tabs (No "All" button) -->
                    <div class="flex flex-wrap gap-2 mb-6 border-b pb-3">
                        <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition bg-gray-100 text-gray-700 hover:bg-gray-200" data-filter="Pending">Pending</button>
                        <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition bg-gray-100 text-gray-700 hover:bg-gray-200" data-filter="Approved">Approved</button>
                        <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition bg-gray-100 text-gray-700 hover:bg-gray-200" data-filter="Rejected">Rejected</button>
                        <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition bg-gray-100 text-gray-700 hover:bg-gray-200" data-filter="Completed">Completed</button>
                        <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition bg-gray-100 text-gray-700 hover:bg-gray-200" data-filter="Cancelled">Cancelled</button>
                    </div>

                    <div id="requestsContainer" class="space-y-4">
                        <div class="text-center py-8 text-gray-500">Loading requests...</div>
                    </div>
                </div>
            </div>
            <footer class="border-t bg-white py-4 text-center text-gray-400 text-sm mt-6">
                Copyright © Barangay Polo 2026
            </footer>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-3">Cancel Request</h3>
            <p class="text-gray-600 mb-4">Are you sure you want to cancel this request? This action cannot be undone.</p>
            <div class="flex gap-3">
                <button onclick="closeCancelModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">No, Keep</button>
                <button id="confirmCancelBtn" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Yes, Cancel</button>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '/BeSCMS';
        const token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('user') || '{}');
        if (!token || !user.id) {
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        let currentRequests = [];
        // Default filter is 'Pending' (not 'all')
        let currentFilter = 'Pending';
        let requestToCancel = null;

        async function loadRequests() {
            try {
                const statusParam = currentFilter.toLowerCase();
                const response = await fetch(`${API_BASE}/index.php?route=requests&action=my_requests&status=${encodeURIComponent(statusParam)}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                const data = await response.json();
                if (response.ok && data.success) {
                    currentRequests = Array.isArray(data.data) ? data.data : [];
                } else {
                    currentRequests = [];
                    console.error('Failed to load requests:', data.error || response.statusText);
                }
            } catch (error) {
                currentRequests = [];
                console.error('Error loading requests:', error);
            }
            renderRequests();
        }

        function renderRequests() {
            const container = document.getElementById('requestsContainer');
            if (!Array.isArray(currentRequests) || currentRequests.length === 0) {
                container.innerHTML = `<div class="text-center py-12 bg-gray-50 rounded-xl"><i class="fas fa-inbox text-5xl text-gray-400 mb-3"></i><p class="text-gray-500">No ${currentFilter.toLowerCase()} requests.</p><a href="dashboard.php" class="inline-block mt-4 text-blue-600 hover:underline">Make a request →</a></div>`;
                return;
            }

            container.innerHTML = currentRequests.map(req => {
                const statusText = capitalizeStatus(String(req.status || ''));
                const statusClass = `status-${String(req.status || '').toLowerCase()}`;
                const requestDate = req.requested_at || req.date || '';
                const quantityText = req.quantity && req.quantity > 1 ? `${req.quantity} copies` : '1 copy';
                const adminNotes = req.admin_notes ? `<div class="mt-3 text-sm text-gray-600"><span class="font-semibold">Admin note:</span> ${escapeHtml(req.admin_notes)}</div>` : '';
                return `
                    <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition bg-white">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="font-bold text-lg text-gray-800">${escapeHtml(req.certificate_type || req.type || '')}</h3>
                                    <span class="status-badge ${statusClass}">${statusText}</span>
                                </div>
                                <p class="text-gray-600 text-sm mb-1"><i class="fas fa-comment mr-1"></i> ${escapeHtml(req.purpose)}</p>
                                <p class="text-gray-600 text-sm mb-1"><i class="fas fa-layer-group mr-1"></i> ${quantityText}</p>
                                <p class="text-gray-400 text-xs"><i class="far fa-calendar-alt mr-1"></i> ${formatDate(requestDate)}</p>
                                ${adminNotes}
                            </div>
                            <div class="flex gap-2">
                                ${String(req.status).toLowerCase() === 'pending' ? `<button onclick="cancelRequest(${req.id})" class="px-4 py-2 text-sm border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition"><i class="fas fa-times mr-1"></i> Cancel</button>` : ''}
                                ${String(req.status).toLowerCase() === 'completed' ? `<button onclick="viewCertificate(${req.id})" class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition"><i class="fas fa-download mr-1"></i> Download</button>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;'
            } [m]));
        }

        function formatDate(d) {
            return new Date(d).toLocaleDateString('en-PH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function cancelRequest(id) {
            requestToCancel = id;
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
            requestToCancel = null;
        }

        document.getElementById('confirmCancelBtn').addEventListener('click', async function() {
            if (!requestToCancel) {
                closeCancelModal();
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/index.php?route=requests&action=cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ request_id: requestToCancel })
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Unable to cancel request');
                }
                await loadRequests();
            } catch (error) {
                console.error('Cancel request error:', error);
                alert(`⚠️ ${error.message}`);
            } finally {
                closeCancelModal();
            }
        });

        function viewCertificate(id) {
            alert('📄 Certificate download would start here. (Demo)');
        }

        // Filter functionality – highlight active button
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('filter-active');
                    b.classList.add('bg-gray-100', 'text-gray-700');
                });
                this.classList.add('filter-active');
                this.classList.remove('bg-gray-100');
                currentFilter = this.getAttribute('data-filter');
                loadRequests();
            });
        });

        // Set default active filter to Pending
        const defaultBtn = document.querySelector('.filter-btn[data-filter="Pending"]');
        if (defaultBtn) {
            defaultBtn.classList.add('filter-active');
            defaultBtn.classList.remove('bg-gray-100');
        }

        function normalizeVerificationStatus(status) {
            const normalized = String(status || 'pending').trim().toLowerCase();
            return normalized === 'verified' ? 'approved' : normalized;
        }

        function capitalizeStatus(status) {
            return status.charAt(0).toUpperCase() + status.slice(1);
        }

        function goToProfile() {
            window.location.href = 'request_form.php';
        }

        // Sidebar with checkmark and admin links
        const rawStatus = user.verification_status || 'pending';
        const status = normalizeVerificationStatus(rawStatus);
        const statusLabel = capitalizeStatus(status);
        const verifiedBadge = status === 'approved' ? '<i class="fas fa-check-circle text-green-600 ml-1"></i>' : '';
        document.getElementById('sidebarUser').innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">${(user.name?.charAt(0) || 'G').toUpperCase()}</div>
                <div><h3 class="font-semibold">${user.name || 'Resident'} ${verifiedBadge}</h3><p class="text-xs text-gray-400">Resident</p></div>
            </div>
            <div class="mt-3 text-xs p-2 rounded-lg ${status === 'approved' ? 'bg-green-100 text-green-800' : (status === 'verifying' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')}">
                <span class="font-semibold">Verification:</span> ${statusLabel}
            </div>
        `;

        // Add admin links if user is admin
        if (user.isAdmin === true) {
            const adminContainer = document.getElementById('adminLinksContainer');
            adminContainer.innerHTML = `
                <div class="space-y-2 border-t border-gray-200 pt-4 mt-2">
                    <a href="admin/verify_accounts.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition"><i class="fas fa-user-check w-5"></i><span>Verify Accounts</span></a>
                    <a href="admin/pending_request.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition"><i class="fas fa-clipboard-list w-5"></i><span>Pending Requests</span></a>
                </div>
            `;
        }

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        loadRequests();

        window.onclick = (e) => {
            if (e.target === document.getElementById('cancelModal')) closeCancelModal();
        };
    </script>
</body>

</html>