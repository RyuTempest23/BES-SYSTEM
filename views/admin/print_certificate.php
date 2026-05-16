<?php
$requestId = $_GET['id'] ?? 0;
if (!$requestId) {
    die('Invalid request ID');
}
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
        const API_BASE = '/BeSCMS';
        const token = localStorage.getItem('token');
        if (!token) {
            alert('Authentication required');
            window.location.href = `${API_BASE}/views/auth/login.php`;
        }

        const requestId = <?php echo $requestId; ?>;
        async function loadCertificate() {
            try {
                const res = await fetch(`${API_BASE}/index.php?route=admin&action=get_request&id=${requestId}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.error);
                const req = data.data;
                const html = `
                    <h1>Republic of the Philippines</h1>
                    <h3>Barangay Lucero</h3>
                    <h2>CERTIFICATE OF ${req.certificate_type.toUpperCase()}</h2>
                    <div class="content">
                        <p>This is to certify that <strong>${escapeHtml(req.full_name)}</strong> is a resident of this barangay.</p>
                        <p>Purpose: ${escapeHtml(req.purpose)}</p>
                        <p>Number of copies: ${req.quantity}</p>
                        <p>Issued on: ${new Date().toLocaleDateString()}</p>
                        ${req.admin_notes ? `<p><em>Note: ${escapeHtml(req.admin_notes)}</em></p>` : ''}
                    </div>
                    <div class="footer">
                        <p>_________________________</p>
                        <p>Barangay Captain</p>
                    </div>
                `;
                document.getElementById('certificate').innerHTML = html;
            } catch (err) {
                document.getElementById('certificate').innerHTML = '<p>Error loading certificate data: ' + err.message + '</p>';
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        loadCertificate();
    </script>
</body>
</html>