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
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="flex min-h-screen">
        <!-- ========== LEFT SIDEBAR ========== -->
        <aside class="w-72 bg-white shadow-lg border-r border-gray-200 fixed h-full z-10 flex flex-col">
            <div class="p-6 border-b border-gray-100" id="userSidebarInfo">
                <!-- Dynamically filled by JS -->
            </div>
            <nav class="flex-1 mt-6 px-4 space-y-2">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-blue-50 text-blue-700 font-medium">
                    <i class="fas fa-tachometer-alt w-5"></i><span>Dashboard</span>
                </a>
                <a href="my_requests.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-file-alt w-5"></i><span>My Request</span>
                </a>
            </nav>
            <div class="p-6 border-t border-gray-100 text-xs text-gray-400">
                <i class="fas fa-shield-alt mr-1"></i> Barangay Lucero v2
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
                    <span class="font-semibold text-gray-700 text-sm md:text-base">Barangay Lucero, Bolinao, Pangasinan</span>
                </div>
                <div class="relative">
                    <button onclick="logout()" class="focus:outline-none flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-sign-out-alt text-gray-500"></i>
                        </div>
                    </button>
                </div>
            </header>

            <!-- MAIN FLEX (center + right widget) -->
            <div class="flex flex-col lg:flex-row p-6 gap-6">
                <!-- CENTRAL CONTENT -->
                <div class="flex-1 space-y-6">
                    <!-- Verification Status & Upload Button -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100" id="verificationCard">
                        <h2 class="text-xl font-bold text-gray-800">🔐 Account Verification</h2>
                        <div id="verificationStatus" class="mt-2 text-sm"></div>
                        <div class="mt-4">
                            <a href="upload_id.php" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl shadow-sm transition">
                                <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Government ID
                            </a>
                        </div>
                    </div>

                    <!-- Certification Request CARD (only shown if verified) -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100" id="requestCard">
                        <h2 class="text-xl font-bold text-gray-800">📄 Certification Request</h2>
                        <p class="text-gray-500 text-sm mt-1" id="requestMessage">Request official barangay certificates quickly online</p>
                        <div class="mt-5">
                            <a href="request.php" id="requestBtn" class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-sm transition">
                                <i class="fas fa-pen-alt mr-2"></i> Request
                            </a>
                        </div>
                    </div>

                    <!-- FIVE STEPS TIMELINE -->
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

                <!-- RIGHT WIDGET: EMERGENCY HOTLINES -->
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

            <!-- FOOTER -->
            <footer class="border-t border-gray-200 bg-white py-4 text-center text-gray-400 text-sm mt-6">
                <div class="flex justify-between items-center px-6">
                    <span>Copyright © Barangay Lucero 2021</span>
                    <div class="space-x-4"><a href="#" class="hover:text-gray-600">Privacy Policy</a><a href="#" class="hover:text-gray-600">Terms of Use</a></div>
                </div>
            </footer>
        </div>
    </div>

    <script>
        const API_BASE = '/BeSCMS';
        const token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('user') || '{}');
        if (!token || !user.id) {
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        // Map backend verification_status to display text
        let statusText = 'Pending';
        let statusColor = 'bg-yellow-100 text-yellow-800';
        if (user.verification_status === 'approved') {
            statusText = 'Verified';
            statusColor = 'bg-green-100 text-green-800';
        } else if (user.verification_status === 'pending') {
            statusText = 'Pending';
            statusColor = 'bg-yellow-100 text-yellow-800';
        } else if (user.verification_status === 'rejected') {
            statusText = 'Rejected';
            statusColor = 'bg-red-100 text-red-800';
        }

        // Update sidebar
        document.getElementById('userSidebarInfo').innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">${(user.name?.charAt(0) || 'G').toUpperCase()}</div>
                <div><h3 class="font-semibold text-gray-800">${user.name || 'Resident'}</h3><p class="text-xs text-gray-400">Resident</p></div>
            </div>
            <div class="mt-3 text-xs p-2 rounded-lg ${statusColor}"><span class="font-semibold">Verification:</span> ${statusText}</div>
        `;

        // Update verification card
        const verificationDiv = document.getElementById('verificationStatus');
        if (user.verification_status === 'approved') {
            verificationDiv.innerHTML = '<i class="fas fa-check-circle text-green-600 mr-1"></i> Your account is verified. You can now request certificates.';
        } else if (user.verification_status === 'pending') {
            verificationDiv.innerHTML = '<i class="fas fa-clock text-yellow-600 mr-1"></i> Your ID is pending verification. Please wait for admin approval.';
        } else if (user.verification_status === 'rejected') {
            verificationDiv.innerHTML = '<i class="fas fa-times-circle text-red-600 mr-1"></i> Your ID was rejected. Please upload a new, clear copy.';
        }

        // Handle request button based on verification
        const requestBtn = document.getElementById('requestBtn');
        const requestMsg = document.getElementById('requestMessage');
        if (user.verification_status !== 'approved') {
            requestBtn.classList.add('opacity-50', 'pointer-events-none');
            requestBtn.href = '#';
            if (user.verification_status === 'pending') {
                requestMsg.innerHTML = '⏳ Your account is being verified. You can request certificates once approved.';
            } else if (user.verification_status === 'rejected') {
                requestMsg.innerHTML = '❌ Your ID was rejected. Please upload a new ID above.';
            } else {
                requestMsg.innerHTML = '🔒 Please verify your account by uploading a valid ID above.';
            }
        } else {
            requestBtn.classList.remove('opacity-50', 'pointer-events-none');
            requestBtn.href = 'request.php';
            requestMsg.innerHTML = 'Request official barangay certificates quickly online.';
        }

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        // Carousel arrows
        const container = document.getElementById('stepCarousel');
        document.getElementById('stepLeftBtn')?.addEventListener('click', () => container.scrollBy({ left: -220, behavior: 'smooth' }));
        document.getElementById('stepRightBtn')?.addEventListener('click', () => container.scrollBy({ left: 220, behavior: 'smooth' }));
    </script>
</body>
</html>