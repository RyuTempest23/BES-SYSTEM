<?php
// dashboard.php - Main landing page after login with document selection modal
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Barangay Polo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .step-scroll {
            scroll-behavior: smooth;
            scrollbar-width: thin;
        }

        .step-scroll::-webkit-scrollbar {
            height: 6px;
        }

        .step-scroll::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 8px;
        }

        .step-scroll::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 8px;
        }

        /* Individual hover colors for each certificate type */
        .hover-indigency:hover {
            background-color: #2196F3 !important;
        }

        .hover-permit:hover {
            background-color: #9333ea !important;
        }

        .hover-residency:hover {
            background-color: #db2777 !important;
        }

        .hover-clearance:hover {
            background-color: #16a34a !important;
        }

        /* Make title, description, and button text white on hover */
        .hover-indigency:hover .doc-title,
        .hover-indigency:hover .doc-desc,
        .hover-indigency:hover .select-btn,
        .hover-permit:hover .doc-title,
        .hover-permit:hover .doc-desc,
        .hover-permit:hover .select-btn,
        .hover-residency:hover .doc-title,
        .hover-residency:hover .doc-desc,
        .hover-residency:hover .select-btn,
        .hover-clearance:hover .doc-title,
        .hover-clearance:hover .doc-desc,
        .hover-clearance:hover .select-btn {
            color: white !important;
        }

        /* Make the button background white and match the hover color for text */
        .hover-indigency:hover .select-btn {
            background-color: white !important;
            color: #2196F3 !important;
        }

        .hover-permit:hover .select-btn {
            background-color: white !important;
            color: #9333ea !important;
        }

        .hover-residency:hover .select-btn {
            background-color: white !important;
            color: #db2777 !important;
        }

        .hover-clearance:hover .select-btn {
            background-color: white !important;
            color: #16a34a !important;
        }

        .modal {
            transition: opacity 0.3s ease;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="flex min-h-screen">
        <!-- ========== LEFT SIDEBAR ========== -->
        <aside class="w-72 bg-white shadow-lg border-r border-gray-200 fixed h-full z-10 flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition" id="userSidebarInfo" onclick="goToProfile()">
                    <!-- Dynamically filled by JS -->
                </div>
            <nav class="flex-1 mt-6 px-4 space-y-2">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-blue-50 text-blue-700 font-medium">
                    <i class="fas fa-tachometer-alt w-5"></i><span>Dashboard</span>
                </a>
                <a href="my_requests.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-file-alt w-5"></i><span>My Request</span>
                </a>
                <a href="upload_id.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-id-card w-5"></i><span>Verify Account</span>
                </a>
            </nav>
            </div>
            <div class="p-6 border-t border-gray-100">
                <button onclick="logout()" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </div>
        </aside>

        <!-- ========== MAIN CONTENT ========== -->
        <div class="flex-1 ml-72">
            <!-- TOP NAVBAR -->
            <header class="bg-white shadow-sm sticky top-0 z-20 px-6 py-3 flex justify-between items-center border-b">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center text-amber-700">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <span class="font-semibold text-gray-700 text-sm md:text-base">Barangay Polo, Dapitan City, Zamboanga Del Norte</span>
                </div>
                <div class="relative"></div>
            </header>

            <!-- MAIN FLEX (center + right widget) -->
            <div class="flex flex-col lg:flex-row p-6 gap-6">
                <!-- CENTRAL CONTENT -->
                <div class="flex-1 space-y-6">
                    <!-- Certification Request CARD -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                        <h2 class="text-xl font-bold text-gray-800">📄 Certification Request</h2>
                        <p class="text-gray-500 text-sm mt-1" id="requestMessage">Request official barangay certificates quickly online</p>
                        <div class="mt-5">
                            <button id="requestBtn" class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-sm transition">
                                <i class="fas fa-pen-alt mr-2"></i> Request
                            </button>
                        </div>
                    </div>

                    <!-- FIVE STEPS TIMELINE -->
                    <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
                        <!-- <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-chalkboard-user mr-2 text-amber-500"></i> FIVE STEPS IN REQUESTING BARANGAY CERTIFICATES</h3>
                            <div class="flex gap-2">
                                <button id="stepLeftBtn" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition"><i class="fas fa-chevron-left"></i></button>
                                <button id="stepRightBtn" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        </div> -->
                        <div class="relative overflow-hidden">
                            <div id="stepCarousel" class="flex gap-5 overflow-x-auto step-scroll pb-3 snap-x snap-mandatory">
                                <div class="min-w-[180px] snap-start bg-gradient-to-br from-sky-50 to-blue-50 rounded-xl p-4 text-center shadow-sm">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-blue-500 text-white flex items-center justify-center font-bold text-xl mb-3">1</div>
                                    <h4 class="font-semibold">Fill up form</h4>
                                    <p class="text-xs text-gray-500">Online or walk-in</p>
                                </div>
                                <div class="min-w-[180px] snap-start bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-4 text-center shadow-sm">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold text-xl mb-3">2</div>
                                    <h4 class="font-semibold">Submit requirements</h4>
                                    <p class="text-xs text-gray-500">Valid ID / docs</p>
                                </div>
                                <div class="min-w-[180px] snap-start bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-4 text-center shadow-sm">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-xl mb-3">3</div>
                                    <h4 class="font-semibold">Barangay verification</h4>
                                    <p class="text-xs text-gray-500">Officer review</p>
                                </div>
                                <div class="min-w-[180px] snap-start bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-4 text-center shadow-sm">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xl mb-3">4</div>
                                    <h4 class="font-semibold">Payment (if any)</h4>
                                    <p class="text-xs text-gray-500">Minimal fee</p>
                                </div>
                                <div class="min-w-[180px] snap-start bg-gradient-to-br from-rose-50 to-pink-50 rounded-xl p-4 text-center shadow-sm">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-rose-500 text-white flex items-center justify-center font-bold text-xl mb-3">5</div>
                                    <h4 class="font-semibold">Release certificate</h4>
                                    <p class="text-xs text-gray-500">Claim or download</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            <!-- FOOTER -->
            <!-- <footer class="border-t border-gray-200 bg-white py-4 text-center text-gray-400 text-sm mt-6">
                <div class="flex justify-between items-center px-6">
                    <span>Copyright © Barangay Polo 2026</span>
                    <div class="space-x-4"><a href="#" class="hover:text-gray-600">Privacy Policy</a><a href="#" class="hover:text-gray-600">Terms of Use</a></div>
                </div>
            </footer> -->
        </div>
    </div>

    <!-- MODAL: Select Document Type -->
    <div id="docTypeModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden modal">
        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full mx-4 relative">
            <button class="absolute top-4 right-5 text-gray-400 hover:text-gray-700 text-2xl" onclick="closeDocModal()">&times;</button>
            <div class="p-6 md:p-8">
                <h2 class="text-2xl font-bold text-gray-800 border-b pb-3 mb-5">📋 Select Document Type</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Barangay Indigency (Blue) -->
                    <div class="rounded-xl border border-gray-200 p-5 shadow-sm transition-all hover-indigency cursor-pointer" onclick="selectDocumentType('Barangay Indigency')">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-3"><i class="fas fa-hand-holding-heart text-blue-600 text-2xl"></i></div>
                            <h3 class="font-bold text-gray-800 doc-title text-lg">Barangay Indigency</h3>
                            <p class="text-xs text-gray-500 doc-desc">Certificate of Indigency</p>
                            <button class="select-btn mt-4 w-full py-2 rounded-lg bg-blue-600 text-white font-medium">Select</button>
                        </div>
                    </div>
                    <!-- Barangay Permit (Purple) -->
                    <div class="rounded-xl border border-gray-200 p-5 shadow-sm transition-all hover-permit cursor-pointer" onclick="selectDocumentType('Barangay Permit')">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-3"><i class="fas fa-clipboard-list text-purple-600 text-2xl"></i></div>
                            <h3 class="font-bold text-gray-800 doc-title text-lg">Barangay Permit</h3>
                            <p class="text-xs text-gray-500 doc-desc">Business / Special Permit</p>
                            <button class="select-btn mt-4 w-full py-2 rounded-lg bg-purple-600 text-white font-medium">Select</button>
                        </div>
                    </div>
                    <!-- Certificate of Residency (Pink) -->
                    <div class="rounded-xl border border-gray-200 p-5 shadow-sm transition-all hover-residency cursor-pointer" onclick="selectDocumentType('Certificate of Residency')">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mb-3"><i class="fas fa-home text-pink-600 text-2xl"></i></div>
                            <h3 class="font-bold text-gray-800 doc-title text-lg">Barangay Residency</h3>
                            <p class="text-xs text-gray-500 doc-desc">Proof of Residency</p>
                            <button class="select-btn mt-4 w-full py-2 rounded-lg bg-pink-600 text-white font-medium">Select</button>
                        </div>
                    </div>
                    <!-- Barangay Clearance (Green) -->
                    <div class="rounded-xl border border-gray-200 p-5 shadow-sm transition-all hover-clearance cursor-pointer" onclick="selectDocumentType('Barangay Clearance')">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-3"><i class="fas fa-stamp text-green-600 text-2xl"></i></div>
                            <h3 class="font-bold text-gray-800 doc-title text-lg">Barangay Clearance</h3>
                            <p class="text-xs text-gray-500 doc-desc">General clearance</p>
                            <button class="select-btn mt-4 w-full py-2 rounded-lg bg-green-600 text-white font-medium">Select</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: Request Details (Purpose) -->
    <div id="purposeModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden modal">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 relative">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Request Details</h3>
                <p class="text-gray-600 mb-2">Document Type: <span id="selectedDocType" class="font-semibold"></span></p>
                <label class="block text-gray-700 mb-2">Purpose / Reason for request:</label>
                <textarea id="requestPurpose" rows="4" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., For scholarship application, Business permit, etc."></textarea>
                <label class="block text-gray-700 mb-2 mt-4">Quantity</label>
                <input id="requestQuantity" type="number" min="1" value="1" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <div class="flex gap-3 mt-5">
                    <button onclick="closePurposeModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button onclick="submitRequest()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Submit Request</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Check authentication
        const API_BASE = '/BeSCMS';
        const token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('user') || '{}');
        if (!token || !user.id) {
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        let selectedDoc = '';

        function normalizeVerificationStatus(status) {
            const normalized = String(status || 'pending').trim().toLowerCase();
            return normalized === 'verified' ? 'approved' : normalized;
        }

        function capitalizeStatus(status) {
            return status.charAt(0).toUpperCase() + status.slice(1);
        }

        // Fill sidebar & user info with checkmark for verified
        function updateSidebar() {
            const rawStatus = user.verification_status || 'pending';
            const status = normalizeVerificationStatus(rawStatus);
            const statusLabel = capitalizeStatus(status);
            let statusColor = 'bg-yellow-100 text-yellow-800';
            let checkIcon = '';
            if (status === 'approved') {
                statusColor = 'bg-green-100 text-green-800';
                checkIcon = '<i class="fas fa-check-circle text-green-600 ml-1"></i>';
            } else if (status === 'verifying') {
                statusColor = 'bg-blue-100 text-blue-800';
            }
            document.getElementById('userSidebarInfo').innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">${(user.name?.charAt(0) || 'G').toUpperCase()}</div>
                    <div><h3 class="font-semibold text-gray-800">${user.name || 'Resident'} ${checkIcon}</h3><p class="text-xs text-gray-400">Resident</p></div>
                </div>
                <div class="mt-3 text-xs p-2 rounded-lg ${statusColor}"><span class="font-semibold">Verification:</span> ${statusLabel}</div>
            `;

            // Add admin links if user is admin
            const nav = document.querySelector('nav');
            let existingAdminDiv = document.getElementById('adminNavLinks');
            if (existingAdminDiv) existingAdminDiv.remove();
            if (user.isAdmin === true) {
                const adminDiv = document.createElement('div');
                adminDiv.id = 'adminNavLinks';
                adminDiv.className = 'mt-6 pt-4 border-t border-gray-200 space-y-2';
                adminDiv.innerHTML = `
                    <a href="admin/verify_accounts.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition"><i class="fas fa-user-check w-5"></i><span>Verify Accounts</span></a>
                    <a href="admin/pending_request.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition"><i class="fas fa-clipboard-list w-5"></i><span>Pending Requests</span></a>
                `;
                nav.appendChild(adminDiv);
            }
        }

        function goToProfile() {
            window.location.href = 'request_form.php';
        }

        // Modal functions
        function openDocModal() {
            document.getElementById('docTypeModal').classList.remove('hidden');
        }

        function closeDocModal() {
            document.getElementById('docTypeModal').classList.add('hidden');
        }

        function closePurposeModal() {
            document.getElementById('purposeModal').classList.add('hidden');
            document.getElementById('requestPurpose').value = '';
        }

        function selectDocumentType(type) {
            const verificationStatus = normalizeVerificationStatus(user.verification_status);

            // Special check for Barangay Indigency
            if (type === 'Barangay Indigency' && verificationStatus !== 'approved') {
                alert('⚠️ Account not verified! Please verify your account first to request a certificate.');
                closeDocModal();
                return;
            }

            // General verification check for all document types
            if (verificationStatus !== 'approved') {
                alert('⚠️ Your account is not verified. Please verify your account first to request any certificate.\n\nGo to your profile to upload verification documents.');
                closeDocModal();
                return;
            }

            selectedDoc = type;
            closeDocModal();
            document.getElementById('selectedDocType').innerText = type;
            document.getElementById('purposeModal').classList.remove('hidden');
        }

        async function submitRequest() {
            const purpose = document.getElementById('requestPurpose').value.trim();
            const quantityInput = document.getElementById('requestQuantity');
            const quantity = parseInt(quantityInput.value, 10) || 1;
            if (!purpose) {
                alert('Please provide a purpose for your request.');
                return;
            }
            if (quantity < 1) {
                alert('Quantity must be at least 1.');
                quantityInput.focus();
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/index.php?route=requests&action=submit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({
                        certificate_type: selectedDoc,
                        purpose: purpose,
                        quantity: quantity
                    })
                });

                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Unable to submit your request.');
                }

                alert('✅ Your certificate request has been sent. Please wait for admin approval.');
                closePurposeModal();

                if (confirm('Would you like to view your requests now?')) {
                    window.location.href = 'my_requests.php';
                }
            } catch (error) {
                console.error('Request submission error:', error);
                alert(`⚠️ ${error.message}`);
            }
        }

        // Request button handler
        document.getElementById('requestBtn').addEventListener('click', openDocModal);

        updateSidebar();

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        // Carousel arrows
        const container = document.getElementById('stepCarousel');
        document.getElementById('stepLeftBtn')?.addEventListener('click', () => container.scrollBy({
            left: -220,
            behavior: 'smooth'
        }));
        document.getElementById('stepRightBtn')?.addEventListener('click', () => container.scrollBy({
            left: 220,
            behavior: 'smooth'
        }));

        // Close modals when clicking outside
        window.onclick = function(event) {
            const docModal = document.getElementById('docTypeModal');
            const purposeModal = document.getElementById('purposeModal');
            if (event.target === docModal) closeDocModal();
            if (event.target === purposeModal) closePurposeModal();
        }
    </script>
</body>

</html>