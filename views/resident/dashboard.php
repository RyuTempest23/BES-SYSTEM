<?php
// dashboard.php - Main landing page after login
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Barangay Lucero</title>
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

        .card-hover-effect:hover {
            background-color: #2196F3 !important;
        }

        .card-hover-effect:hover .doc-title,
        .card-hover-effect:hover .doc-desc,
        .card-hover-effect:hover .select-btn {
            color: white !important;
        }

        .card-hover-effect:hover .select-btn {
            background-color: white !important;
            color: #2196F3 !important;
        }

        .modal {
            transition: opacity 0.3s ease;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="flex min-h-screen">
        <!-- SIDEBAR -->
        <aside class="w-72 bg-white shadow-lg border-r border-gray-200 fixed h-full z-10 flex flex-col">
            <div class="p-6 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition" id="userSidebarInfo" onclick="goToProfile()"></div>
            <nav class="flex-1 mt-6 px-4 space-y-2">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-blue-50 text-blue-700 font-medium">
                    <i class="fas fa-tachometer-alt w-5"></i><span>Dashboard</span>
                </a>
                <a href="my_requests.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-file-alt w-5"></i><span>My Request</span>
                </a>
                <!-- Admin link will be injected dynamically if user is admin -->
                <div id="adminLinkContainer"></div>
            </nav>
            <div class="p-6 border-t border-gray-100 text-xs text-gray-400">
                <i class="fas fa-shield-alt mr-1"></i> Barangay Lucero v2
            </div>
        </aside>

        <!-- MAIN CONTENT (same as before) -->
        <div class="flex-1 ml-72">
            <header class="bg-white shadow-sm sticky top-0 z-20 px-6 py-3 flex justify-between items-center border-b">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center text-amber-700">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <span class="font-semibold text-gray-700 text-sm md:text-base">Barangay Lucero, Bolinao, Pangasinan</span>
                </div>
                <button onclick="logout()" class="focus:outline-none flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-sign-out-alt text-gray-500"></i>
                    </div>
                </button>
            </header>

            <div class="flex flex-col lg:flex-row p-6 gap-6">
                <div class="flex-1 space-y-6">
                    <!-- Certification Request CARD (only shown if verified) -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100" id="requestCard">
                        <h2 class="text-xl font-bold text-gray-800">📄 Certification Request</h2>
                        <p class="text-gray-500 text-sm mt-1" id="requestMessage">Request official barangay certificates quickly online</p>
                        <div class="mt-5">
                            <button id="requestBtn" class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-sm transition">
                                <i class="fas fa-pen-alt mr-2"></i> Request
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-chalkboard-user mr-2 text-amber-500"></i> FIVE STEPS IN REQUESTING BARANGAY CERTIFICATES</h3>
                            <div class="flex gap-2">
                                <button id="stepLeftBtn" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition"><i class="fas fa-chevron-left"></i></button>
                                <button id="stepRightBtn" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>
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

                <div class="lg:w-80 w-full">
                    <div class="rounded-2xl shadow-md overflow-hidden sticky top-24" style="background-color: #d4af37;">
                        <div class="p-5">
                            <div class="flex items-center gap-2 text-white mb-3"><i class="fas fa-bell text-red-600 text-xl"></i>
                                <h3 class="font-bold text-lg text-gray-900">EMERGENCY HOTLINES</h3>
                            </div>
                            <ul class="space-y-3 text-gray-800">
                                <li class="flex justify-between border-b border-yellow-600/30 pb-2"><span><i class="fas fa-phone-alt mr-2"></i> Barangay Hall</span><span class="font-mono">(075) 123 4567</span></li>
                                <li class="flex justify-between border-b border-yellow-600/30 pb-2"><span><i class="fas fa-shield-alt mr-2"></i> PNP Bolinao</span><span class="font-mono">0998 765 4321</span></li>
                                <li class="flex justify-between border-b border-yellow-600/30 pb-2"><span><i class="fas fa-ambulance mr-2"></i> Emergency Rescue</span><span class="font-mono">117 / 911</span></li>
                                <li class="flex justify-between"><span><i class="fas fa-fire-extinguisher mr-2"></i> Fire Station</span><span class="font-mono">(075) 555 0199</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="border-t border-gray-200 bg-white py-4 text-center text-gray-400 text-sm mt-6">
                <div class="flex justify-between items-center px-6"><span>Copyright © Barangay Lucero 2021</span>
                    <div class="space-x-4"><a href="#" class="hover:text-gray-600">Privacy Policy</a><a href="#" class="hover:text-gray-600">Terms of Use</a></div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Document type modal & purpose modal (same as before) -->
    <div id="docTypeModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden modal">
        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full mx-4 relative"><button class="absolute top-4 right-5 text-gray-400 hover:text-gray-700 text-2xl" onclick="closeDocModal()">&times;</button>
            <div class="p-6 md:p-8">
                <h2 class="text-2xl font-bold text-gray-800 border-b pb-3 mb-5">📋 Select Document Type</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="rounded-xl border border-gray-200 p-5 shadow-sm transition-all card-hover-effect cursor-pointer" onclick="selectDocumentType('Barangay Indigency')">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-3"><i class="fas fa-hand-holding-heart text-blue-600 text-2xl"></i></div>
                            <h3 class="font-bold text-gray-800 doc-title text-lg">Barangay Indigency</h3>
                            <p class="text-xs text-gray-500 doc-desc">Certificate of Indigency</p><button class="select-btn mt-4 w-full py-2 rounded-lg bg-blue-600 text-white font-medium">Select</button>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition" onclick="selectDocumentType('Barangay Permit')">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-3"><i class="fas fa-clipboard-list text-purple-600 text-2xl"></i></div>
                            <h3 class="font-bold text-gray-800 text-lg">Barangay Permit</h3>
                            <p class="text-xs text-gray-500">Business / Special Permit</p><button class="mt-4 w-full py-2 rounded-lg bg-purple-600 text-white font-medium">Select</button>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition" onclick="selectDocumentType('Certificate of Residency')">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mb-3"><i class="fas fa-home text-pink-600 text-2xl"></i></div>
                            <h3 class="font-bold text-gray-800 text-lg">Barangay Residency</h3>
                            <p class="text-xs text-gray-500">Proof of Residency</p><button class="mt-4 w-full py-2 rounded-lg bg-pink-600 text-white font-medium">Select</button>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition" onclick="selectDocumentType('Barangay Clearance')">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-3"><i class="fas fa-stamp text-green-600 text-2xl"></i></div>
                            <h3 class="font-bold text-gray-800 text-lg">Barangay Clearance</h3>
                            <p class="text-xs text-gray-500">General clearance</p><button class="mt-4 w-full py-2 rounded-lg bg-green-600 text-white font-medium">Select</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="purposeModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden modal">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 relative">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Request Details</h3>
                <p class="text-gray-600 mb-2">Document Type: <span id="selectedDocType" class="font-semibold"></span></p><label class="block text-gray-700 mb-2">Purpose / Reason:</label><textarea id="requestPurpose" rows="4" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., For scholarship application, Business permit, etc."></textarea>
                <div class="flex gap-3 mt-5"><button onclick="closePurposeModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button><button onclick="submitRequest()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Submit</button></div>
            </div>
        </div>
    </div>

    <script>
        // Check authentication
        const token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('user') || '{}');
        if (!token || !user.id) window.location.href = '/BeSCMS/views/auth/login.php';

        // Fill sidebar & user info
        function updateSidebar() {
            const status = user.verification_status || 'Pending';
            let statusColor = 'bg-yellow-100 text-yellow-800';
            if (status === 'Verified') statusColor = 'bg-green-100 text-green-800';
            else if (status === 'Verifying') statusColor = 'bg-blue-100 text-blue-800';
            document.getElementById('userSidebarInfo').innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">${(user.name?.charAt(0) || 'G').toUpperCase()}</div>
                <div><h3 class="font-semibold text-gray-800">${user.name || 'Resident'}</h3><p class="text-xs text-gray-400">Resident</p></div>
            </div>
            <div class="mt-3 text-xs p-2 rounded-lg ${statusColor}"><span class="font-semibold">Verification:</span> ${status}</div>
        `;
        }

        // Handle request button visibility based on verification status
        function updateRequestAccess() {
            const status = user.verification_status;
            const requestBtn = document.getElementById('requestBtn');
            const msgSpan = document.getElementById('requestMessage');
            if (status !== 'Verified') {
                requestBtn.classList.add('opacity-50', 'pointer-events-none');
                requestBtn.href = '#';
                if (status === 'Verifying') {
                    msgSpan.innerHTML = '⚠️ Your account is being verified. Please wait for admin approval.';
                } else {
                    msgSpan.innerHTML = '🔒 Please verify your account first (go to Profile → Verify).';
                }
            } else {
                requestBtn.classList.remove('opacity-50', 'pointer-events-none');
                requestBtn.href = 'request.php';
                msgSpan.innerHTML = 'Request official barangay certificates quickly online';
            }
        }

        updateSidebar();
        updateRequestAccess();

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        let selectedDoc = '';

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
            if (type === 'Barangay Indigency' && user.verification_status !== 'Verified') {
                alert('⚠️ Account not verified! Please verify your account first to request a certificate.');
                closeDocModal();
                return;
            }
            if (user.verification_status !== 'Verified') {
                alert('⚠️ Your account is not verified. Please verify your account first.');
                closeDocModal();
                return;
            }
            selectedDoc = type;
            closeDocModal();
            document.getElementById('selectedDocType').innerText = type;
            document.getElementById('purposeModal').classList.remove('hidden');
        }

        function submitRequest() {
            const purpose = document.getElementById('requestPurpose').value.trim();
            if (!purpose) {
                alert('Please provide a purpose.');
                return;
            }
            const newRequest = {
                id: Date.now(),
                type: selectedDoc,
                purpose: purpose,
                status: 'Pending',
                date: new Date().toISOString(),
                userId: user.id,
                userName: user.name
            };
            const storageKey = `barangayRequests_${user.id}`;
            let userRequests = JSON.parse(localStorage.getItem(storageKey) || '[]');
            userRequests.unshift(newRequest);
            localStorage.setItem(storageKey, JSON.stringify(userRequests));
            alert('✅ Request submitted!');
            closePurposeModal();
            if (confirm('View your requests now?')) window.location.href = 'my_requests.php';
        }
        document.getElementById('requestBtn').addEventListener('click', openDocModal);
        updateSidebar();

        // Carousel
        const container = document.getElementById('stepCarousel');
        document.getElementById('stepLeftBtn')?.addEventListener('click', () => container.scrollBy({
            left: -220,
            behavior: 'smooth'
        }));
        document.getElementById('stepRightBtn')?.addEventListener('click', () => container.scrollBy({
            left: 220,
            behavior: 'smooth'
        }));
    </script>
</body>

</html>