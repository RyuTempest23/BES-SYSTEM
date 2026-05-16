<?php
// request_form.php - User profile with editable details + verification upload
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
        <aside class="w-72 bg-white shadow-lg border-r fixed h-full z-10">
            <div class="p-6 border-b cursor-pointer hover:bg-gray-50 transition" id="sidebarUser" onclick="goToDashboard()"></div>
            <nav class="mt-6 px-4 space-y-2"><a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a><a href="my_requests.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><i class="fas fa-file-alt"></i><span>My Request</span></a></nav>
            <div class="p-6 border-t text-xs text-gray-400"><i class="fas fa-id-card"></i> Account management</div>
        </aside>
        <div class="flex-1 ml-72">
            <header class="bg-white shadow-sm px-6 py-3 flex justify-between items-center border-b">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center"><i class="fas fa-landmark text-amber-700"></i></div><span class="font-semibold">Barangay Lucero, Bolinao, Pangasinan</span>
                </div><button onclick="logout()"><i class="fas fa-sign-out-alt text-gray-500 text-xl"></i></button>
            </header>
            <div class="flex flex-col lg:flex-row p-6 gap-6">
                <div class="flex-1 max-w-2xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-md p-8">
                        <div class="w-32 h-32 mx-auto bg-gray-200 rounded-full flex items-center justify-center text-gray-400 text-5xl"><i class="fas fa-user-circle"></i></div>
                        <div id="profileDisplay" class="text-center">
                            <h2 class="text-2xl font-bold mt-4 text-gray-800" id="profileName"></h2>
                            <div class="inline-flex items-center text-sm px-3 py-1 rounded-full mt-2 gap-1" id="verifyBadge"><i class="fas fa-info-circle"></i> Status: <span id="verifyStatusText"></span></div>
                            <div class="mt-6 bg-gray-50 rounded-xl p-4 text-left space-y-3" id="userDetails"></div>
                        </div>
                        <div id="editForm" class="hidden mt-6">
                            <div class="space-y-4">
                                <div><label class="block text-gray-700 font-medium mb-1">Full Name</label><input type="text" id="editFullName" class="w-full border border-gray-300 rounded-lg p-2"></div>
                                <div><label class="block text-gray-700 font-medium mb-1">Username</label><input type="text" id="editUsername" class="w-full border border-gray-300 rounded-lg p-2"></div>
                                <div><label class="block text-gray-700 font-medium mb-1">Email</label><input type="email" id="editEmail" class="w-full border border-gray-300 rounded-lg p-2"></div>
                                <div><label class="block text-gray-700 font-medium mb-1">Barangay</label><input type="text" id="editBarangay" class="w-full border border-gray-300 rounded-lg p-2"></div>
                                <div class="flex gap-3 mt-4"><button id="saveProfileBtn" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">Save Changes</button><button id="cancelEditBtn" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">Cancel</button></div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-center gap-2">
                            <button id="editDetailsBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow"><i class="fas fa-edit mr-2"></i> Edit Details</button>
                            <button id="verifyAccountBtn" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow"><i class="fas fa-id-card mr-2"></i> Verify Account</button>
                        </div>
                    </div>
                </div>
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
    <!-- Upload Modal (same) -->
    <div id="uploadModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4">
            <div class="flex justify-between items-center border-b p-5">
                <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-upload mr-2"></i> Upload Verification Document</h3><button id="closeModalBtn" class="text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 bg-blue-50 p-2 rounded-lg mb-3"><i class="fas fa-info-circle mr-1"></i> Upload valid ID, Birth Certificate, etc.</p>
                <div class="border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 p-4 text-center">
                    <div id="imagePreviewArea" class="flex flex-col items-center justify-center min-h-[160px]"><i class="fas fa-file-image text-5xl text-gray-400"></i>
                        <p class="text-gray-500 text-sm mt-2" id="previewFileName">No file selected</p>
                    </div>
                </div><input type="file" id="docUploadInput" class="hidden" accept="image/*,application/pdf"><button id="triggerUploadBtn" class="mt-5 w-full bg-gray-700 hover:bg-gray-800 text-white py-2.5 rounded-xl flex items-center justify-center gap-2"><i class="fas fa-folder-open"></i> Choose File</button><button id="submitDocsBtn" class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl">Submit Verification</button>
            </div>
        </div>
    </div>
    <script>
        const token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('user') || '{}');
        if (!token || !user.id) window.location.href = '/BeSCMS/views/auth/login.php';
        // Populate profile display
        function updateProfileDisplay() {
            document.getElementById('profileName').innerText = user.name || 'Resident';
            document.getElementById('verifyStatusText').innerText = user.verification_status || 'Pending';
            const badge = document.getElementById('verifyBadge');
            let status = user.verification_status || 'Pending';
            if (status === 'Verified') badge.className = 'inline-flex items-center bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full mt-2 gap-1';
            else if (status === 'Verifying') badge.className = 'inline-flex items-center bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full mt-2 gap-1';
            else badge.className = 'inline-flex items-center bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full mt-2 gap-1';
            document.getElementById('userDetails').innerHTML = `<div class="flex justify-between border-b pb-2"><span class="text-gray-500">Full Name:</span><span class="font-medium">${user.name || ''}</span></div><div class="flex justify-between border-b pb-2"><span class="text-gray-500">Username:</span><span class="font-medium">${user.username || user.email || ''}</span></div><div class="flex justify-between border-b pb-2"><span class="text-gray-500">Email:</span><span class="font-medium">${user.email || ''}</span></div><div class="flex justify-between"><span class="text-gray-500">Barangay:</span><span class="font-medium">${user.barangay || 'Lucero, Bolinao'}</span></div>`;
            document.getElementById('sidebarUser').innerHTML = `<div class="flex items-center space-x-3"><div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">${(user.name?.charAt(0) || 'G').toUpperCase()}</div><div><h3 class="font-semibold">${user.name || 'Resident'}</h3><p class="text-xs text-gray-400">Resident</p></div></div><div class="mt-3 text-xs p-2 rounded-lg ${status==='Verified'?'bg-green-100 text-green-800':(status==='Verifying'?'bg-blue-100 text-blue-800':'bg-yellow-100 text-yellow-800')}"><span class="font-semibold">Verification:</span> ${status}</div>`;
        }
        updateProfileDisplay();
        // Edit mode
        const editBtn = document.getElementById('editDetailsBtn');
        const displayDiv = document.getElementById('profileDisplay');
        const editFormDiv = document.getElementById('editForm');
        editBtn.onclick = () => {
            document.getElementById('editFullName').value = user.name || '';
            document.getElementById('editUsername').value = user.username || '';
            document.getElementById('editEmail').value = user.email || '';
            document.getElementById('editBarangay').value = user.barangay || 'Lucero, Bolinao';
            displayDiv.classList.add('hidden');
            editFormDiv.classList.remove('hidden');
        };
        document.getElementById('cancelEditBtn').onclick = () => {
            displayDiv.classList.remove('hidden');
            editFormDiv.classList.add('hidden');
        };
        document.getElementById('saveProfileBtn').onclick = () => {
            user.name = document.getElementById('editFullName').value.trim() || user.name;
            user.username = document.getElementById('editUsername').value.trim() || user.username;
            user.email = document.getElementById('editEmail').value.trim() || user.email;
            user.barangay = document.getElementById('editBarangay').value.trim() || 'Lucero, Bolinao';
            localStorage.setItem('user', JSON.stringify(user));
            updateProfileDisplay();
            displayDiv.classList.remove('hidden');
            editFormDiv.classList.add('hidden');
            alert('Profile updated successfully!');
        };
        // Verification upload modal (unchanged but works)
        const modal = document.getElementById('uploadModal');
        const verifyBtn = document.getElementById('verifyAccountBtn');
        const closeModal = document.getElementById('closeModalBtn');
        const fileInput = document.getElementById('docUploadInput');
        const uploadTrigger = document.getElementById('triggerUploadBtn');
        const previewFileNameSpan = document.getElementById('previewFileName');
        const submitBtn = document.getElementById('submitDocsBtn');
        const imagePreviewArea = document.getElementById('imagePreviewArea');

        function openModalFunc() {
            modal.classList.remove('hidden');
            fileInput.value = '';
            previewFileNameSpan.innerText = 'No file selected';
            imagePreviewArea.innerHTML = `<i class="fas fa-file-image text-5xl text-gray-400"></i><p class="text-gray-500 text-sm mt-2">No file selected</p>`;
        }
        verifyBtn.onclick = openModalFunc;
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
                alert("Please upload a document.");
                return;
            }
            // Simulate upload
            await new Promise(r => setTimeout(r, 1000));
            user.verification_status = 'Verifying';
            localStorage.setItem('user', JSON.stringify(user));
            updateProfileDisplay();
            alert("Document submitted! Your account is now under review.");
            modal.classList.add('hidden');
        };

        function goToDashboard() {
            window.location.href = 'dashboard.php';
        }

        function logout() {
            localStorage.clear();
            window.location.href = '/BeSCMS/views/auth/login.php';
        }
    </script>
</body>

</html>