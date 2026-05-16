<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload ID - Barangay eServices</title>
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 40px; }
        .container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h2 { color: #1e3c72; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-bottom: 8px; }
        input[type="file"] { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 8px; }
        button { background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
        .message { margin-top: 20px; padding: 12px; border-radius: 8px; display: none; }
        .success { background: #dcfce7; color: #166534; display: block; }
        .error { background: #fee2e2; color: #b91c1c; display: block; }
        .info { background: #e0e7ff; color: #1e3a8a; display: block; }
    </style>
</head>
<body>
<div class="container">
    <h2>Upload Government ID for Verification</h2>
    <p class="info" id="statusMsg">Please upload a valid ID (JPG, PNG, PDF, max 5MB).</p>
    <form id="uploadForm" enctype="multipart/form-data">
        <div class="form-group">
            <label>Select ID Document</label>
            <input type="file" id="verification_doc" name="verification_doc" accept=".jpg,.jpeg,.png,.pdf" required>
        </div>
        <button type="submit">Upload ID</button>
    </form>
    <div id="message" class="message"></div>
</div>

<script>
    const API_BASE = '/BeSCMS';
    const token = localStorage.getItem('token');
    if (!token) window.location.href = '/BeSCMS/views/auth/login.php';

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

        const msgDiv = document.getElementById('message');
        msgDiv.className = 'message';
        msgDiv.style.display = 'none';
        const submitBtn = document.querySelector('button');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Uploading...';

        try {
            const response = await fetch(`${API_BASE}/index.php?route=auth&action=upload_id`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}` },
                body: formData   // Do NOT set Content-Type header manually – browser sets it with boundary
            });
            const data = await response.json();
            if (data.success) {
                showMessage(data.message, 'success');
                fileInput.value = '';
            } else {
                showMessage(data.error || 'Upload failed', 'error');
            }
        } catch (err) {
            showMessage('Network error. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Upload ID';
        }
    });

    function showMessage(msg, type) {
        const msgDiv = document.getElementById('message');
        msgDiv.textContent = msg;
        msgDiv.className = `message ${type}`;
        msgDiv.style.display = 'block';
        setTimeout(() => {
            msgDiv.style.display = 'none';
        }, 5000);
    }
</script>
</body>
</html>