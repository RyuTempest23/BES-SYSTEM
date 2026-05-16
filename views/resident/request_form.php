<?php
// myrequest_form.php - User profile with document upload for verification
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile | Barangay Lucero</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <!-- Sidebar (same as dashboard) -->
        <aside class="w-72 bg-white shadow-lg border-r fixed h-full z-10">
            <div class="p-6 border-b" id="sidebarUser"></div>
            <nav class="mt-6 px-4 space-y-2">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                <a href="my_requests.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-blue-50 text-blue-700 font-medium"><i class="fas fa-file-alt"></i><span>My Request</span></a>
            </nav>
            <div class="p-6 border-t text-xs text-gray-400"><i class="fas fa-id-card"></i> Account management</div>
        </aside>

        <div class="flex-1 ml-72">
            <header class="bg-white shadow-sm px-6 py-3 flex justify-between items-center border-b">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center"><i class="fas fa-landmark text-amber-700"></i></div><span class="font-semibold">Barangay Lucero, Bolinao, Pangasinan</span>
                </div>
                <button onclick="logout()"><i class="fas fa-sign-out-alt text-gray-500 text-xl"></i></button>
            </header>

            <div class="flex flex-col lg:flex-row p-6 gap-6">
                <!-- Profile Main Section -->
                <div class="flex-1 max-w-2xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-md p-8 text-center">
                        <div class="w-32 h-32 mx-auto bg-gray-200 rounded-full flex items-center justify-center text-gray-400 text-5xl"><i class="fas fa-user-circle"></i></div>
                        <h2 class="text-2xl font-bold mt-4 text-gray-800" id="profileName">Loading...</h2>
                        <div class="inline-flex items-center text-sm px-3 py-1 rounded-full mt-2 gap-1" id="verifyBadge">
                            <i class="fas fa-info-circle"></i>
                            <span id="verifyStatusText">Pending</span>
                        </div>

                        <div class="mt-6 bg-gray-50 rounded-xl p-4 text-left space-y-3" id="userDetails"></div>

                        <!-- Verify button (opens upload modal) -->
                        <button id="verifyBtn" class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow transition w-full sm:w-auto">
                            <i class="fas fa-check-circle mr-2"></i> Verify
                        </button>
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

    <!-- MODAL: Upload supporting document for verification -->
    <div id="uploadModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 hidden transition-all">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 relative">
            <div class="flex justify-between items-center border-b p-5">
                <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-upload mr-2"></i>Upload document for verification</h3><button id="closeModalBtn" class="text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 bg-blue-50 p-2 rounded-lg"><i class="fas fa-info-circle mr-1"></i> Upload any supporting document: Any valid ID, Birth Certificate, Marriage Contract, Business Permit</p>
                <!-- Preview zone -->
                <div class="mt-5 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 p-4 text-center">
                    <div id="imagePreviewArea" class="flex flex-col items-center justify-center min-h-[160px]"><i class="fas fa-file-image text-5xl text-gray-400"></i>
                        <p class="text-gray-500 text-sm mt-2" id="previewFileName">No file selected</p>
                    </div>
                </div>
                <input type="file" id="docUploadInput" class="hidden" accept="image/*,application/pdf">
                <button id="triggerUploadBtn" class="mt-5 w-full bg-gray-700 hover:bg-gray-800 text-white py-2.5 rounded-xl flex items-center justify-center gap-2 transition"><i class="fas fa-folder-open"></i> Upload document</button>
                <button id="submitDocsBtn" class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl transition">Submit verification request</button>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('user') || '{}');
        if (!token || !user.id) window.location.href = '/BeSCMS/views/auth/login.php';

        // Helper to update UI with current user data
        function refreshProfileUI() {
            document.getElementById('profileName').innerText = user.name || 'Resident';
            const status = user.verification_status || 'Pending';
            const badge = document.getElementById('verifyBadge');
            if (status === 'Verified') {
                badge.className = 'inline-flex items-center bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full mt-2 gap-1';
                document.getElementById('verifyBtn').disabled = true;
                document.getElementById('verifyBtn').classList.add('opacity-50', 'cursor-not-allowed');
            } else if (status === 'Verifying') {
                badge.className = 'inline-flex items-center bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full mt-2 gap-1';
                document.getElementById('verifyBtn').disabled = true;
                document.getElementById('verifyBtn').classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                badge.className = 'inline-flex items-center bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full mt-2 gap-1';
                document.getElementById('verifyBtn').disabled = false;
                document.getElementById('verifyBtn').classList.remove('opacity-50', 'cursor-not-allowed');
            }
            document.getElementById('verifyStatusText').innerText = status;

            document.getElementById('userDetails').innerHTML = `
            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Username:</span><span class="font-medium">${user.username || user.email || 'user'}</span></div>
            <div class="flex justify-between border-b pb-2"><span class="text-gray-500">Email:</span><span class="font-medium">${user.email || 'resident@lucero.ph'}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Barangay:</span><span class="font-medium">Lucero, Bolinao</span></div>
        `;

            document.getElementById('sidebarUser').innerHTML = `<div class="flex items-center space-x-3"><div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">${(user.name?.charAt(0) || 'G').toUpperCase()}</div><div><h3 class="font-semibold">${user.name || 'Resident'}</h3><p class="text-xs text-gray-400">Resident</p></div></div>`;
        }

        refreshProfileUI();

        // Modal logic
        const modal = document.getElementById('uploadModal');
        const verifyBtn = document.getElementById('verifyBtn');
        const closeModal = document.getElementById('closeModalBtn');
        const fileInput = document.getElementById('docUploadInput');
        const uploadTrigger = document.getElementById('triggerUploadBtn');
        const previewArea = document.getElementById('imagePreviewArea');
        const previewFileNameSpan = document.getElementById('previewFileName');
        const submitBtn = document.getElementById('submitDocsBtn');

        verifyBtn.onclick = () => {
            if (user.verification_status === 'Verified') {
                alert('Your account is already verified.');
            } else if (user.verification_status === 'Verifying') {
                alert('Your verification request is already pending. Please wait for admin approval.');
            } else {
                modal.classList.remove('hidden');
            }
        };
        closeModal.onclick = () => modal.classList.add('hidden');
        window.onclick = (e) => {
            if (e.target === modal) modal.classList.add('hidden');
        };

        uploadTrigger.onclick = () => fileInput.click();
        fileInput.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                previewFileNameSpan.innerText = file.name;
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = ev => previewArea.innerHTML = `<img src="${ev.target.result}" class="max-h-32 rounded shadow object-contain"><p class="text-xs text-gray-500 mt-2">${file.name}</p>`;
                    reader.readAsDataURL(file);
                } else {
                    previewArea.innerHTML = `<i class="fas fa-file-pdf text-5xl text-red-500"></i><p class="text-gray-600 text-sm mt-2">${file.name}</p>`;
                }
            } else {
                previewFileNameSpan.innerText = "No file selected";
                previewArea.innerHTML = `<i class="fas fa-file-image text-5xl text-gray-400"></i><p class="text-gray-500 text-sm mt-2">No file selected</p>`;
            }
        };

        submitBtn.onclick = async () => {
            if (!fileInput.files.length) {
                alert("⚠️ Please upload a supporting document.");
                return;
            }
            const formData = new FormData();
            formData.append('document', fileInput.files[0]);
            formData.append('user_id', user.id);
            // Replace with your actual verification request endpoint
            try {
                const res = await fetch('/BeSCMS/user/request_verification', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    alert("Verification request submitted. Please wait for verification approval.");
                    // Update local user status to "Verifying"
                    user.verification_status = 'Verifying';
                    localStorage.setItem('user', JSON.stringify(user));
                    refreshProfileUI();
                    modal.classList.add('hidden');
                } else {
                    alert("Error: " + (data.error || "Submission failed"));
                }
            } catch (err) {
                // Fallback for demo
                alert("✅ [Demo] Verification request submitted. In production, connect to your backend.");
                user.verification_status = 'Verifying';
                localStorage.setItem('user', JSON.stringify(user));
                refreshProfileUI();
                modal.classList.add('hidden');
            }
        };

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }
    </script>
</body>

</html>