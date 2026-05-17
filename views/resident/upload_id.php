<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account - Barangay Polo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .message {
            transition: opacity 0.3s ease;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
        }

        .info {
            background: #e0e7ff;
            color: #1e3a8a;
        }

        .pending {
            background: #fef3c7;
            color: #d97706;
        }

        .verified {
            background: #d1fae5;
            color: #059669;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-72 bg-white shadow-lg border-r fixed h-full z-10">
            <div class="p-6 border-b cursor-pointer hover:bg-gray-50 transition" id="sidebarUser" onclick="goToProfile()"></div>
            <nav class="mt-6 px-4 space-y-2">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                <a href="my_requests.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><i class="fas fa-file-alt"></i><span>My Request</span></a>
                <a href="upload_id.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-blue-50 text-blue-700 font-medium"><i class="fas fa-id-card"></i><span>Verify Account</span></a>
            </nav>
            <div id="adminLinksContainer" class="px-4 mt-4"></div>
            <div class="p-6 border-t border-gray-100">
                <button onclick="logout()" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 ml-72">
            <header class="bg-white shadow-sm px-6 py-3 flex justify-between items-center border-b">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center"><i class="fas fa-landmark text-amber-700"></i></div>
                    <span class="font-semibold">Barangay Polo, Dapitan City, Zamboanga Del Norte</span>
                </div>
            </header>

            <div class="p-6">
                <div class="bg-white rounded-2xl shadow-md p-6 max-w-2xl">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2"><i class="fas fa-id-card mr-2 text-blue-600"></i>Account Verification</h2>
                    <p class="text-gray-500 mb-6">Upload your government ID for account verification. Admin will review and approve.</p>

                    <!-- Verification Status -->
                    <div id="statusContainer" class="mb-6 p-4 rounded-lg hidden">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-info-circle text-xl"></i>
                            <div>
                                <p id="statusText" class="font-semibold"></p>
                                <p id="statusDetail" class="text-sm mt-1"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <div id="uploadSection" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <form id="uploadForm" enctype="multipart/form-data" class="w-full">
                            <div class="mb-4">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600 font-semibold mb-2">Drag and drop your ID here</p>
                                <p class="text-gray-400 text-sm mb-4">or click to select a file</p>
                                <input type="file" id="verification_doc" name="verification_doc" accept=".jpg,.jpeg,.png,.pdf"
                                    class="hidden" required onchange="handleFileSelect()">
                                <label for="verification_doc" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg cursor-pointer hover:bg-blue-700 transition">
                                    <i class="fas fa-folder-open mr-2"></i>Select File
                                </label>
                            </div>
                            <p class="text-xs text-gray-400 mt-3">JPG, PNG, or PDF • Max 5MB</p>
                            <p id="selectedFile" class="text-sm text-gray-600 mt-3 hidden"><i class="fas fa-check-circle text-green-600 mr-2"></i><span id="fileName"></span></p>
                            <button type="submit" id="submitBtn" class="mt-6 w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold hidden">
                                <i class="fas fa-upload mr-2"></i>Upload ID
                            </button>
                        </form>
                    </div>

                    <!-- Messages -->
                    <div id="message" class="message mt-6 p-4 rounded-lg hidden"></div>

                    <hr class="my-6">
                    <p class="text-xs text-gray-400"><i class="fas fa-shield-alt mr-1"></i> Your document is securely stored and only accessible to authorized admins.</p>
                </div>
            </div>

            <!-- Footer -->
            <footer class="border-t border-gray-200 bg-white py-4 text-center text-gray-400 text-sm mt-6">
                <div class="flex justify-between items-center px-6">
                    <span>Copyright © Barangay Polo 2026</span>
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

        // Initialize
        updateSidebar();
        checkVerificationStatus();

        function updateSidebar() {
            const statusColor = user.verification_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
            document.getElementById('sidebarUser').innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">${(user.name?.charAt(0) || 'G').toUpperCase()}</div>
                    <div><h3 class="font-semibold text-gray-800">${user.name || 'Resident'}</h3><p class="text-xs text-gray-400">Resident</p></div>
                </div>
                <div class="mt-3 text-xs p-2 rounded-lg ${statusColor}"><span class="font-semibold">Verification:</span> ${user.verification_status.charAt(0).toUpperCase() + user.verification_status.slice(1) || 'Pending'}</div>
            `;

            // Add admin links if needed
            if (user.role === 'admin') {
                document.getElementById('adminLinksContainer').innerHTML = `
                    <div class="pt-4 border-t border-gray-200 space-y-2">
                        <a href="admin/verify_accounts.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><i class="fas fa-user-check"></i><span>Verify Accounts</span></a>
                    </div>
                `;
            }
        }

        async function checkVerificationStatus() {
            try {
                const response = await fetch(`${API_BASE}/index.php?route=auth&action=profile`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                const data = await response.json();

                if (data.success) {
                    const profile = data.profile;
                    const statusContainer = document.getElementById('statusContainer');

                    if (profile.verification_status === 'pending' && profile.verification_doc) {
                        statusContainer.className = 'mb-6 p-4 rounded-lg pending';
                        document.getElementById('statusText').textContent = 'Verification Pending';
                        document.getElementById('statusDetail').textContent = 'Your ID has been uploaded. Admin will review it shortly.';
                        statusContainer.classList.remove('hidden');
                        document.getElementById('uploadSection').classList.add('hidden');
                    } else if (profile.verification_status === 'approved') {
                        statusContainer.className = 'mb-6 p-4 rounded-lg verified';
                        document.getElementById('statusText').textContent = '✓ Account Verified';
                        document.getElementById('statusDetail').textContent = 'Your account has been verified successfully!';
                        statusContainer.classList.remove('hidden');
                        document.getElementById('uploadSection').classList.add('hidden');
                    } else if (profile.verification_status === 'rejected') {
                        statusContainer.className = 'mb-6 p-4 rounded-lg error';
                        document.getElementById('statusText').textContent = 'Verification Rejected';
                        document.getElementById('statusDetail').textContent = 'Your ID was rejected. Please upload again with a clearer image.';
                        statusContainer.classList.remove('hidden');
                    }
                }
            } catch (err) {
                console.error('Error checking status:', err);
            }
        }

        function handleFileSelect() {
            const fileInput = document.getElementById('verification_doc');
            const file = fileInput.files[0];
            if (file) {
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('selectedFile').classList.remove('hidden');
                document.getElementById('submitBtn').classList.remove('hidden');
            }
        }

        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const fileInput = document.getElementById('verification_doc');
            const file = fileInput.files[0];
            if (!file) {
                showMessage('Please select a file.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('verification_doc', file);

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';

            try {
                const response = await fetch(`${API_BASE}/index.php?route=auth&action=upload_id`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    },
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showMessage(data.message, 'success');
                    fileInput.value = '';
                    document.getElementById('selectedFile').classList.add('hidden');
                    document.getElementById('submitBtn').classList.add('hidden');
                    if (user && user.id) {
                        user.verification_status = 'pending';
                        localStorage.setItem('user', JSON.stringify(user));
                    }
                    setTimeout(() => checkVerificationStatus(), 1000);
                } else {
                    showMessage(data.error || 'Upload failed', 'error');
                }
            } catch (err) {
                showMessage('Network error. Please try again.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Upload ID';
            }
        });

        function showMessage(msg, type) {
            const msgDiv = document.getElementById('message');
            msgDiv.textContent = msg;
            msgDiv.className = `message ${type}`;
            msgDiv.classList.remove('hidden');
            setTimeout(() => msgDiv.classList.add('hidden'), 5000);
        }

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        function goToProfile() {
            window.location.href = 'dashboard.php';
        }
    </script>
</body>

</html>