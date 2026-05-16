<?php
// my_requests.php - User requests with status filters (No "All" tab)
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Requests | Barangay Lucero</title>
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
        <aside class="w-72 bg-white shadow-lg border-r fixed h-full z-10">
            <div class="p-6 border-b cursor-pointer hover:bg-gray-50 transition" id="sidebarUser" onclick="goToProfile()"></div>
            <nav class="mt-6 px-4 space-y-2">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                <a href="my_requests.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-blue-50 text-blue-700 font-medium"><i class="fas fa-file-alt"></i><span>My Request</span></a>
                <div id="adminLinkContainer"></div>
            </nav>
            <div class="p-6 border-t text-xs text-gray-400"><i class="fas fa-shield-alt"></i> Barangay Lucero</div>
        </aside>
        <div class="flex-1 ml-72">
            <header class="bg-white shadow-sm px-6 py-3 flex justify-between items-center border-b">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center"><i class="fas fa-landmark text-amber-700"></i></div><span class="font-semibold">Barangay Lucero, Bolinao, Pangasinan</span>
                </div><button id="logoutBtn"><i class="fas fa-sign-out-alt text-gray-500 text-xl"></i></button>
            </header>
            <div class="p-6">
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">📋 My Requests</h2>
                    <p class="text-gray-500 mb-6">Track the status of your barangay certificate requests</p>
                    <!-- Filters: No "All" button -->
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
            <div class="lg:w-80 w-full fixed bottom-4 right-4">
                <div class="rounded-2xl shadow-md p-4" style="background:#d4af37;"><i class="fas fa-phone-alt text-red-700"></i>
                    <h3 class="font-bold text-gray-900 mt-1 text-sm">EMERGENCY</h3>
                    <ul class="mt-2 text-xs">
                        <li>📞 Barangay: 0917 111 2222</li>
                        <li>🚔 Police: 117</li>
                        <li>🔥 Fire: 160</li>
                    </ul>
                </div>
            </div>
            <footer class="border-t bg-white py-4 text-center text-gray-400 text-sm mt-6">Copyright © Barangay Lucero 2021</footer>
        </div>
    </div>
    <!-- Cancel Modal (same) -->
    <div id="cancelModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-3">Cancel Request</h3>
            <p class="text-gray-600 mb-4">Are you sure you want to cancel this request?</p>
            <div class="flex gap-3"><button onclick="closeCancelModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">No</button><button id="confirmCancelBtn" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Yes, Cancel</button></div>
        </div>
    </div>
    <script>
        const token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('user') || '{}');
        if (!token || !user.id) window.location.href = '/BeSCMS/views/auth/login.php';
        let currentRequests = [],
            currentFilter = 'Pending',
            requestToCancel = null;

        function loadRequests() {
            const storageKey = `barangayRequests_${user.id}`;
            const stored = localStorage.getItem(storageKey);
            currentRequests = stored ? JSON.parse(stored) : [];
            renderRequests();
        }

        function renderRequests() {
            let filtered = currentRequests.filter(req => req.status === currentFilter);
            const container = document.getElementById('requestsContainer');
            if (filtered.length === 0) {
                container.innerHTML = `<div class="text-center py-12 bg-gray-50 rounded-xl"><i class="fas fa-inbox text-5xl text-gray-400 mb-3"></i><p class="text-gray-500">No ${currentFilter.toLowerCase()} requests</p><a href="dashboard.php" class="inline-block mt-4 text-blue-600 hover:underline">Make a request →</a></div>`;
                return;
            }
            container.innerHTML = filtered.map(req => `<div class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition bg-white"><div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3"><div class="flex-1"><div class="flex items-center gap-2 mb-2"><h3 class="font-bold text-lg text-gray-800">${escapeHtml(req.type)}</h3><span class="status-badge status-${req.status.toLowerCase()}">${req.status}</span></div><p class="text-gray-600 text-sm mb-1"><i class="fas fa-comment mr-1"></i> ${escapeHtml(req.purpose)}</p><p class="text-gray-400 text-xs"><i class="far fa-calendar-alt mr-1"></i> ${formatDate(req.date)}</p></div><div class="flex gap-2">${req.status === 'Pending' ? `<button onclick="cancelRequest(${req.id})" class="px-4 py-2 text-sm border border-red-300 text-red-600 rounded-lg hover:bg-red-50"><i class="fas fa-times mr-1"></i> Cancel</button>` : ''}${req.status === 'Completed' ? `<button onclick="viewCertificate(${req.id})" class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700"><i class="fas fa-download mr-1"></i> Download</button>` : ''}</div></div></div>`).join('');
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
        document.getElementById('confirmCancelBtn').onclick = function() {
            if (requestToCancel) {
                let idx = currentRequests.findIndex(r => r.id === requestToCancel);
                if (idx !== -1 && currentRequests[idx].status === 'Pending') {
                    currentRequests[idx].status = 'Cancelled';
                    localStorage.setItem(`barangayRequests_${user.id}`, JSON.stringify(currentRequests));
                    renderRequests();
                }
                closeCancelModal();
            }
        };

        function viewCertificate(id) {
            alert('📄 Certificate download demo.');
        }
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('filter-active');
                    b.classList.add('bg-gray-100', 'text-gray-700');
                });
                this.classList.add('filter-active');
                this.classList.remove('bg-gray-100');
                currentFilter = this.getAttribute('data-filter');
                renderRequests();
            });
        });
        // Set default active: Pending
        document.querySelector('.filter-btn[data-filter="Pending"]').classList.add('filter-active');

        function goToProfile() {
            window.location.href = 'request_form.php';
        }
        document.getElementById('sidebarUser').innerHTML = `<div class="flex items-center space-x-3"><div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">${(user.name?.charAt(0) || 'G').toUpperCase()}</div><div><h3 class="font-semibold">${user.name || 'Resident'}</h3><p class="text-xs text-gray-400">Resident</p></div></div><div class="mt-3 text-xs p-2 rounded-lg bg-yellow-100 text-yellow-800"><span class="font-semibold">Verification:</span> ${user.verification_status || 'Pending'}</div>`;
        // Admin link
        if (user.isAdmin === true) document.getElementById('adminLinkContainer').innerHTML = `<a href="admin_requests.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition"><i class="fas fa-user-shield w-5"></i><span>Admin Panel</span></a>`;
        document.getElementById('logoutBtn').addEventListener('click', () => {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        });
        loadRequests();
        window.onclick = function(e) {
            if (e.target === document.getElementById('cancelModal')) closeCancelModal();
        }
    </script>
</body>

</html>