<!DOCTYPE html>
<html>
<head>
    <title>Request Certificate</title>
    <style>
        body { font-family: Arial; padding: 20px; max-width: 600px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #48bb78; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>Request a Barangay Certificate</h2>
    <div id="message"></div>
    <form id="requestForm">
        <div class="form-group">
            <label>Certificate Type *</label>
            <select id="certificate_type" required>
                <option value="">Select one</option>
                <option value="Barangay Indigency">Barangay Indigency</option>
                <option value="Barangay Clearance">Barangay Clearance</option>
                <option value="Certificate of Residency">Certificate of Residency</option>
            </select>
        </div>
        <div class="form-group">
            <label>Purpose *</label>
            <textarea id="purpose" rows="3" required placeholder="e.g., School scholarship, employment requirement..."></textarea>
        </div>
        <div class="form-group">
            <label>Quantity (copies) *</label>
            <input type="number" id="quantity" value="1" min="1" required>
        </div>
        <button type="submit">Submit Request</button>
    </form>
    <br><a href="/BeSCMS/views/resident/dashboard.php">Back to Dashboard</a>

    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/BeSCMS/views/auth/login.php';

        document.getElementById('requestForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const certificate_type = document.getElementById('certificate_type').value;
            const purpose = document.getElementById('purpose').value;
            const quantity = parseInt(document.getElementById('quantity').value);
            const messageDiv = document.getElementById('message');

            const res = await fetch('/BeSCMS/requests?action=submit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                body: JSON.stringify({ certificate_type, purpose, quantity })
            });
            const data = await res.json();
            if (data.success) {
                messageDiv.innerHTML = `<div class="success">Request submitted successfully! <a href="/BeSCMS/views/resident/my_requests.php">View my requests</a></div>`;
                document.getElementById('requestForm').reset();
            } else {
                messageDiv.innerHTML = `<div class="error">Error: ${data.error}</div>`;
            }
        });
    </script>
</body>
</html>