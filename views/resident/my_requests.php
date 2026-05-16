<?php
// request.php - Modal popup to choose certificate type
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Select Document | Barangay Lucero</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .card-hover-effect:hover {
            background-color: #2196F3 !important;
            transition: all 0.2s;
        }

        .card-hover-effect:hover .doc-title,
        .card-hover-effect:hover .doc-desc,
        .card-hover-effect:hover .select-btn {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.2) !important;
        }

        .card-hover-effect:hover .select-btn {
            background-color: white !important;
            color: #2196F3 !important;
        }
    </style>
</head>

<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <!-- SIDEBAR (same as dashboard) -->
        <aside class="w-72 bg-white shadow-lg border-r fixed h-full z-10">
            <div class="p-6 border-b" id="sidebarUser"></div>
            <nav class="mt-6 px-4 space-y-2">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                <a href="my_requests.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><i class="fas fa-file-alt"></i><span>My Request</span></a>
            </nav>
            <div class="p-6 border-t text-xs text-gray-400"><i class="fas fa-shield-alt"></i> Barangay Lucero</div>
        </aside>

        <div class="flex-1 ml-72">
            <!-- TOP NAV -->
            <header class="bg-white shadow-sm px-6 py-3 flex justify-between items-center border-b">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center"><i class="fas fa-landmark text-amber-700"></i></div><span class="font-semibold">Barangay Lucero, Bolinao, Pangasinan</span>
                </div>
                <button onclick="logout()"><i class="fas fa-sign-out-alt text-gray-500 text-xl"></i></button>
            </header>

            <div class="flex flex-col lg:flex-row p-6 gap-6">
                <div class="flex-1 relative">
                    <!-- MODAL OVERLAY (centered) -->
                    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full mx-4 relative">
                            <a href="dashboard.php" class="absolute top-4 right-5 text-gray-400 hover:text-gray-700 text-2xl">&times;</a>
                            <div class="p-6 md:p-8">
                                <h2 class="text-2xl font-bold text-gray-800 border-b pb-3 mb-5">📋 Select Document Type</h2>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <!-- Barangay Indigency (special hover) -->
                                    <div class="rounded-xl border border-gray-200 p-5 shadow-sm transition-all card-hover-effect cursor-pointer" onclick="selectDoc('Barangay Indigency')">
                                        <div class="flex flex-col items-center text-center">
                                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-3"><i class="fas fa-hand-holding-heart text-blue-600 text-2xl"></i></div>
                                            <h3 class="font-bold text-gray-800 doc-title text-lg">Barangay Indigency</h3>
                                            <p class="text-xs text-gray-500 doc-desc">Certificate of Indigency</p><button class="select-btn mt-4 w-full py-2 rounded-lg bg-blue-600 text-white font-medium">Select</button>
                                        </div>
                                    </div>
                                    <!-- Barangay Permit -->
                                    <div class="rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition" onclick="selectDoc('Barangay Permit')">
                                        <div class="flex flex-col items-center text-center">
                                            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-3"><i class="fas fa-clipboard-list text-purple-600 text-2xl"></i></div>
                                            <h3 class="font-bold text-gray-800 text-lg">Barangay Permit</h3>
                                            <p class="text-xs text-gray-500">Business / Special Permit</p><button class="mt-4 w-full py-2 rounded-lg bg-purple-600 text-white font-medium">Select</button>
                                        </div>
                                    </div>
                                    <!-- Barangay Residency -->
                                    <div class="rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition" onclick="selectDoc('Certificate of Residency')">
                                        <div class="flex flex-col items-center text-center">
                                            <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mb-3"><i class="fas fa-home text-pink-600 text-2xl"></i></div>
                                            <h3 class="font-bold text-gray-800 text-lg">Barangay Residency</h3>
                                            <p class="text-xs text-gray-500">Proof of Residency</p><button class="mt-4 w-full py-2 rounded-lg bg-pink-600 text-white font-medium">Select</button>
                                        </div>
                                    </div>
                                    <!-- Barangay Clearance -->
                                    <div class="rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition" onclick="selectDoc('Barangay Clearance')">
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
                </div>
                <!-- Right Emergency Widget -->
                <div class="lg:w-80 w-full">
                    <div class="rounded-2xl shadow-md p-5" style="background:#d4af37;"><i class="fas fa-phone-alt text-red-700"></i>
                        <h3 class="font-bold text-gray-900 mt-1">EMERGENCY</h3>
                        <ul class="mt-3 text-sm">
                            <li>📞 Barangay: 0917 111 2222</li>
                            <li>🚔 Police: 117</li>
                            <li>🔥 Fire: 160</li>
                        </ul>
                    </div>
                </div>
            </div>
            <footer class="border-t bg-white py-4 text-center text-gray-400 text-sm">Copyright © Barangay Lucero 2021</footer>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        if (!token || !user.id) window.location.href = '/BeSCMS/views/auth/login.php';

        document.getElementById('sidebarUser').innerHTML = `<div class="flex items-center space-x-3"><div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">${(user.name?.charAt(0) || 'G').toUpperCase()}</div><div><h3 class="font-semibold">${user.name || 'Resident'}</h3><p class="text-xs text-gray-400">Resident</p></div></div>`;

        function selectDoc(type) {
            // Redirect to existing request_form.php with the chosen type pre-selected
            window.location.href = `request_form.php?type=${encodeURIComponent(type)}`;
        }

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }
    </script>
</body>

</html>