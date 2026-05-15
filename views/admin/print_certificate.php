<?php
// No API call needed; directly fetch using token from URL parameter
$requestId = $_GET['id'] ?? 0;
if (!$requestId) {
    die('Invalid request ID');
}
// We'll use JavaScript to fetch data via API and then print
?>
<!DOCTYPE html>
<html>
<head>
    <title>Print Certificate</title>
    <style>
        body { font-family: 'Times New Roman', serif; padding: 40px; }
        .certificate { border: 2px solid #333; padding: 40px; max-width: 800px; margin: auto; text-align: center; }
        h1 { font-size: 28px; margin-bottom: 10px; }
        h3 { margin-top: 0; }
        .content { text-align: left; margin: 30px 0; line-height: 1.8; }
        .footer { margin-top: 50px; text-align: right; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
        .no-print { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div id="certificate" class="certificate">Loading certificate data...</div>
    <div class="no-print">
        <button onclick="window.print()">Print Certificate</button>
        <button onclick="window.close()">Close</button>
    </div>

    <script>
        const token = localStorage.getItem('token');
        if (!token) {
            alert('Authentication required');
            window.location.href = '/BeSCMS/views/auth/login.php';
        }

        const requestId = <?php echo $requestId; ?>;
        async function loadCertificate() {
            const res = await fetch(`/BeSCMS/admin?action=pending_requests`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            if (!data.success) return;
            const request = data.data.find(r => r.id == requestId);
            if (!request) {
                document.getElementById('certificate').innerHTML = '<p>Request not found or already processed.</p>';
                return;
            }
            // Also fetch resident details from profile? We already have full_name, etc.
            const html = `
                <h1>Republic of the Philippines</h1>
                <h3>Barangay ${request.full_name ? 'eServices' : ''}</h3>
                <h2>CERTIFICATE OF ${request.certificate_type.toUpperCase()}</h2>
                <div class="content">
                    <p>This is to certify that <strong>${request.full_name}</strong> is a resident of this barangay.</p>
                    <p>Purpose: ${request.purpose}</p>
                    <p>Number of copies: ${request.quantity}</p>
                    <p>Issued on: ${new Date().toLocaleDateString()}</p>
                    ${request.admin_notes ? `<p><em>Note: ${request.admin_notes}</em></p>` : ''}
                </div>
                <div class="footer">
                    <p>_________________________</p>
                    <p>Barangay Captain</p>
                </div>
            `;
            document.getElementById('certificate').innerHTML = html;
        }
        loadCertificate();
    </script>
</body>
</html>