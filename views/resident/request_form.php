<?php
// request_form.php - User profile with separate edit details and verification upload
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile | Barangay Polo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-72 bg-white shadow-lg border-r fixed h-full z-10">
            <div class="p-6 border-b cursor-pointer hover:bg-gray-50 transition" id="sidebarUser" onclick="goToDashboard()"></div>
            <nav class="mt-6 px-4 space-y-2">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                <a href="my_requests.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><i class="fas fa-file-alt"></i><span>My Request</span></a>
            </nav>
            <div class="p-6 border-t border-gray-100">
                <button onclick="logout()" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </aside>

        <div class="flex-1 ml-72">
            <header class="bg-white shadow-sm px-6 py-3 flex justify-between items-center border-b">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center"><i class="fas fa-landmark text-amber-700"></i></div>
                    <span class="font-semibold">Barangay Polo, Dapitan City, Zamboanga Del Norte</span>
                </div>
            </header>

            <div class="flex flex-col lg:flex-row p-6 gap-6">
                <!-- Profile Main Section -->
                <div class="flex-1 max-w-2xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-md p-8 text-center">
                        <div class="w-32 h-32 mx-auto bg-gray-200 rounded-full flex items-center justify-center text-gray-400 text-5xl">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h2 class="text-2xl font-bold mt-4 text-gray-800" id="profileName">Loading...</h2>
                        <div class="inline-flex items-center text-sm px-3 py-1 rounded-full mt-2 gap-1" id="verifyBadge">
                            <i class="fas fa-info-circle"></i> Status: <span id="verifyStatusText">Pending</span>
                            <i id="verifiedCheck" class="fas fa-check-circle text-green-600 ml-1 hidden"></i>
                        </div>

                        <div class="mt-6 bg-gray-50 rounded-xl p-4 text-left space-y-3" id="userDetails"></div>

                        <div class="mt-4 flex justify-center gap-2 flex-wrap">
                            <button id="editDetailsBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow">
                                <i class="fas fa-edit mr-2"></i> Edit Details
                            </button>
                            <button id="verifyAccountBtn" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow">
                                <i class="fas fa-id-card mr-2"></i> Verify Account
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Right Emergency Widget -->
                <div class="lg:w-80 w-full">
                    <div class="rounded-2xl shadow-md p-5" style="background:#d4af37;">
                        <i class="fas fa-phone-alt text-red-700"></i>
                        <h3 class="font-bold text-gray-900 mt-1">EMERGENCY</h3>
                        <ul class="mt-3 text-sm">
                            <li>📞 Barangay: 0917 111 2222</li>
                            <li>🚔 Police: 911</li>
                            <li>🔥 Fire: 160</li>
                        </ul>
                    </div>
                </div>
            </div>
            <footer class="border-t bg-white py-4 text-center text-gray-400 text-sm">Copyright © Barangay Polo 2026</footer>
        </div>
    </div>

    <!-- MODAL: Edit Profile Details -->
    <div id="editDetailsModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 hidden transition-all">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
            <div class="flex justify-between items-center border-b p-5">
                <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-user-edit mr-2"></i> Edit Profile Details</h3>
                <button id="closeEditModalBtn" class="text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div class="p-6 space-y-4">
                <div><label class="block text-gray-700 font-medium mb-1">Full Name</label><input type="text" id="editFullName" class="w-full border border-gray-300 rounded-lg p-2"></div>
                <div><label class="block text-gray-700 font-medium mb-1">Username</label><input type="text" id="editUsername" class="w-full border border-gray-300 rounded-lg p-2"></div>
                <div><label class="block text-gray-700 font-medium mb-1">Email</label><input type="email" id="editEmail" class="w-full border border-gray-300 rounded-lg p-2"></div>
                <div><label class="block text-gray-700 font-medium mb-1">Barangay</label><input type="text" id="editBarangay" class="w-full border border-gray-300 rounded-lg p-2"></div>
                <div class="flex gap-3 mt-5">
                    <button id="saveDetailsBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">Save Changes</button>
                    <button id="cancelEditBtn" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: Upload Verification Document -->
    <div id="uploadModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 hidden transition-all">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 relative">
            <div class="flex justify-between items-center border-b p-5">
                <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-upload mr-2"></i> Upload Verification Document</h3>
                <button id="closeModalBtn" class="text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 bg-blue-50 p-2 rounded-lg mb-3">
                    <i class="fas fa-info-circle mr-1"></i> Upload any valid ID, Birth Certificate, Marriage Contract, or Barangay Clearance for verification.
                </p>
                <div class="mt-2 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 p-4 text-center">
                    <div id="imagePreviewArea" class="flex flex-col items-center justify-center min-h-[160px]">
                        <i class="fas fa-file-image text-5xl text-gray-400"></i>
                        <p class="text-gray-500 text-sm mt-2" id="previewFileName">No file selected</p>
                    </div>
                </div>
                <input type="file" id="docUploadInput" class="hidden" accept="image/*,application/pdf">
                <button id="triggerUploadBtn" class="mt-5 w-full bg-gray-700 hover:bg-gray-800 text-white py-2.5 rounded-xl flex items-center justify-center gap-2 transition">
                    <i class="fas fa-folder-open"></i> Choose File
                </button>
                <button id="submitDocsBtn" class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl transition">
                    Submit Verification
                </button>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('user') || '{}');
        if (!token || !user.id) {
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        function refreshProfileDisplay() {
            const rawStatus = user.verification_status || 'pending';
            const status = normalizeVerificationStatus(rawStatus);
            const statusLabel = capitalizeStatus(status);

            document.getElementById('profileName').innerText = user.name || 'Resident';
            document.getElementById('verifyStatusText').innerText = statusLabel;

            const badge = document.getElementById('verifyBadge');
            const checkIcon = document.getElementById('verifiedCheck');
            const verifyBtn = document.getElementById('verifyAccountBtn');

            if (status === 'approved') {
                badge.className = 'inline-flex items-center bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full mt-2 gap-1';
                checkIcon.classList.remove('hidden');
                verifyBtn.style.display = 'none';
            } else {
                badge.className = 'inline-flex items-center bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full mt-2 gap-1';
                checkIcon.classList.add('hidden');
                verifyBtn.style.display = 'inline-flex';
            }

            document.getElementById('userDetails').innerHTML = `
                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Full Name:</span><span class="font-medium">${escapeHtml(user.name || '')}</span></div>
                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Username:</span><span class="font-medium">${escapeHtml(user.username || user.email || '')}</span></div>
                <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Email:</span><span class="font-medium">${escapeHtml(user.email || '')}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Barangay:</span><span class="font-medium">${escapeHtml(user.barangay || 'Polo, Dapitan City')}</span></div>
            `;

            // Sidebar with checkmark
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
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;'
            } [m]));
        }

        function normalizeVerificationStatus(status) {
            const normalized = String(status || 'pending').trim().toLowerCase();
            if (normalized === 'verified') return 'approved';
            return normalized;
        }

        function capitalizeStatus(status) {
            return status.charAt(0).toUpperCase() + status.slice(1);
        }

        function goToDashboard() {
            window.location.href = 'dashboard.php';
        }

        // Edit Details Modal
        const editModal = document.getElementById('editDetailsModal');
        const editBtn = document.getElementById('editDetailsBtn');
        const closeEditModal = document.getElementById('closeEditModalBtn');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        const saveDetailsBtn = document.getElementById('saveDetailsBtn');

        editBtn.onclick = () => {
            document.getElementById('editFullName').value = user.name || '';
            document.getElementById('editUsername').value = user.username || '';
            document.getElementById('editEmail').value = user.email || '';
            document.getElementById('editBarangay').value = user.barangay || 'Polo, Dapitan City';
            editModal.classList.remove('hidden');
        };

        function closeEditModalFunc() {
            editModal.classList.add('hidden');
        }
        closeEditModal.onclick = closeEditModalFunc;
        cancelEditBtn.onclick = closeEditModalFunc;

        saveDetailsBtn.onclick = () => {
            const newName = document.getElementById('editFullName').value.trim();
            const newUsername = document.getElementById('editUsername').value.trim();
            const newEmail = document.getElementById('editEmail').value.trim();
            const newBarangay = document.getElementById('editBarangay').value.trim();
            if (newName) user.name = newName;
            if (newUsername) user.username = newUsername;
            if (newEmail) user.email = newEmail;
            if (newBarangay) user.barangay = newBarangay;
            localStorage.setItem('user', JSON.stringify(user));
            refreshProfileDisplay();
            closeEditModalFunc();
            alert('Profile details updated successfully!');
        };

        // Verification Upload Modal
        const uploadModal = document.getElementById('uploadModal');
        const verifyBtn = document.getElementById('verifyAccountBtn');
        const closeUploadModal = document.getElementById('closeModalBtn');
        const fileInput = document.getElementById('docUploadInput');
        const uploadTrigger = document.getElementById('triggerUploadBtn');
        const previewFileNameSpan = document.getElementById('previewFileName');
        const submitBtn = document.getElementById('submitDocsBtn');
        const imagePreviewArea = document.getElementById('imagePreviewArea');

        function openUploadModal() {
            uploadModal.classList.remove('hidden');
            fileInput.value = '';
            previewFileNameSpan.innerText = 'No file selected';
            imagePreviewArea.innerHTML = `<i class="fas fa-file-image text-5xl text-gray-400"></i><p class="text-gray-500 text-sm mt-2">No file selected</p>`;
        }
        verifyBtn.onclick = openUploadModal;
        closeUploadModal.onclick = () => uploadModal.classList.add('hidden');
        window.onclick = (e) => {
            if (e.target === uploadModal) uploadModal.classList.add('hidden');
            if (e.target === editModal) closeEditModalFunc();
        };
        uploadTrigger.onclick = () => fileInput.click();
        fileInput.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                previewFileNameSpan.innerText = file.name;
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = ev => {
                        imagePreviewArea.innerHTML = `<img src="${ev.target.result}" class="max-h-32 rounded shadow object-contain"><p class="text-xs text-gray-500 mt-2">${file.name}</p>`;
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreviewArea.innerHTML = `<i class="fas fa-file-pdf text-5xl text-red-500"></i><p class="text-gray-600 text-sm mt-2">${file.name}</p>`;
                }
            } else {
                previewFileNameSpan.innerText = "No file selected";
                imagePreviewArea.innerHTML = `<i class="fas fa-file-image text-5xl text-gray-400"></i><p class="text-gray-500 text-sm mt-2">No file selected</p>`;
            }
        };
        submitBtn.onclick = async () => {
            if (!fileInput.files.length) {
                alert("⚠️ Please upload a valid document for verification.");
                return;
            }

            const file = fileInput.files[0];
            const formData = new FormData();
            formData.append('verification_doc', file);

            submitBtn.disabled = true;
            submitBtn.innerText = 'Uploading...';

            try {
                const response = await fetch('/BeSCMS/index.php?route=auth&action=upload_id', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    },
                    body: formData
                });

                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Upload failed.');
                }

                user.verification_status = 'pending';
                localStorage.setItem('user', JSON.stringify(user));
                refreshProfileDisplay();
                uploadModal.classList.add('hidden');
                alert('✅ Document submitted! Your account is now under review.');
            } catch (err) {
                console.error('Upload error:', err);
                alert('⚠️ ' + (err.message || 'Failed to upload document.'));
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Submit Verification';
            }
        };

        refreshProfileDisplay();

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }
    </script>
</body>

</html>