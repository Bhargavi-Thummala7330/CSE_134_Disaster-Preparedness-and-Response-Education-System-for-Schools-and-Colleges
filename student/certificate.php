<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$user_name = $_SESSION['user']['name'];
$user_email = $_SESSION['user']['email'];

// Get parameters
$disaster = isset($_GET['disaster']) ? mysqli_real_escape_string($conn, $_GET['disaster']) : '';
$score = isset($_GET['score']) ? (int)$_GET['score'] : 0;
$total = isset($_GET['total']) ? (int)$_GET['total'] : 0;

// If no disaster specified or invalid, try to get the latest completed quiz
if (empty($disaster)) {
    $latest = $conn->query("SELECT * FROM quiz_results WHERE user_id='$user_id' ORDER BY created_at DESC LIMIT 1");
    if ($latest->num_rows > 0) {
        $result = $latest->fetch_assoc();
        $disaster = $result['disaster'];
        $score = $result['score'];
        $total = $result['total'];
    } else {
        header("Location: dashboard_new.php");
        exit();
    }
}

// Verify that the user actually completed this quiz
// Instead of checking exact score/total, check if they completed this disaster
$check = $conn->query("SELECT * FROM quiz_results WHERE user_id='$user_id' AND disaster='$disaster'");

if ($check->num_rows == 0) {
    // No record found for this disaster
    header("Location: dashboard_new.php");
    exit();
}

$result = $check->fetch_assoc();
// Use the database values instead of URL parameters to ensure accuracy
$score = $result['score'];
$total = $result['total'];
$percentage = round(($score / $total) * 100, 1);
$passed = $percentage >= 70;
$date = date('F j, Y');

// Disaster specific details
$disaster_details = [
    'earthquake' => [
        'name' => 'Earthquake Safety',
        'icon' => '🌍',
        'color' => '#1a472a',
        'description' => 'Demonstrated understanding of earthquake safety protocols including Drop, Cover, and Hold On techniques.'
    ],
    'flood' => [
        'name' => 'Flood Preparedness',
        'icon' => '🌊',
        'color' => '#3498db',
        'description' => 'Demonstrated understanding of flood safety protocols and evacuation procedures.'
    ],
    'fire' => [
        'name' => 'Fire Safety',
        'icon' => '🔥',
        'color' => '#e74c3c',
        'description' => 'Demonstrated understanding of fire prevention and emergency response procedures.'
    ],
    'cyclone' => [
        'name' => 'Cyclone Preparedness',
        'icon' => '🌀',
        'color' => '#9b59b6',
        'description' => 'Demonstrated understanding of cyclone safety protocols and emergency planning.'
    ],
    'landslide' => [
        'name' => 'Landslide Safety',
        'icon' => '⛰️',
        'color' => '#95a5a6',
        'description' => 'Demonstrated understanding of landslide risk assessment and safety measures.'
    ],
    'drought' => [
        'name' => 'Drought Preparedness',
        'icon' => '💧',
        'color' => '#b8651a',
        'description' => 'Demonstrated understanding of water conservation and drought management strategies.'
    ],
    'tsunami' => [
        'name' => 'Tsunami Safety',
        'icon' => '🌊',
        'color' => '#006994',
        'description' => 'Demonstrated understanding of tsunami warning signs and evacuation procedures.'
    ],
    'lightening' => [
        'name' => 'Lightning Safety',
        'icon' => '⚡',
        'color' => '#f39c12',
        'description' => 'Demonstrated understanding of lightning safety protocols and the 30-30 rule.'
    ]
];

$details = $disaster_details[$disaster] ?? [
    'name' => ucfirst($disaster) . ' Safety',
    'icon' => '📚',
    'color' => '#667eea',
    'description' => 'Demonstrated understanding of ' . $disaster . ' safety protocols and emergency procedures.'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion - <?php echo $details['name']; ?> | Disaster Ready India</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, <?php echo $details['color']; ?> 0%, <?php echo $details['color']; ?>cc 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .certificate-container {
            max-width: 900px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Certificate Content */
        .certificate {
            padding: 50px;
            background: linear-gradient(135deg, #fff 0%, #fef9e6 100%);
            position: relative;
            border: 20px double #ffd700;
            margin: 20px;
            border-radius: 10px;
        }

        .certificate::before {
            content: '🎓';
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 3em;
            opacity: 0.1;
        }

        .certificate::after {
            content: '🏆';
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 3em;
            opacity: 0.1;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            font-size: 2em;
            font-weight: bold;
            color: #1a472a;
            margin-bottom: 10px;
        }

        .logo span {
            color: #ffd700;
        }

        .subtitle {
            color: #666;
            font-size: 0.9em;
        }

        .certificate-title {
            text-align: center;
            margin: 30px 0;
        }

        .certificate-title h1 {
            font-size: 2.5em;
            color: #1a472a;
            letter-spacing: 5px;
            text-transform: uppercase;
            font-family: 'Georgia', serif;
        }

        .certificate-title p {
            color: #999;
            font-size: 0.9em;
        }

        .recipient {
            text-align: center;
            margin: 40px 0;
        }

        .recipient h2 {
            font-size: 2.5em;
            color: #333;
            font-family: 'Georgia', serif;
            border-bottom: 2px solid #ffd700;
            display: inline-block;
            padding-bottom: 10px;
        }

        .recipient p {
            color: #666;
            margin-top: 10px;
        }

        .course-details {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .course-icon {
            font-size: 3em;
            margin-bottom: 10px;
        }

        .course-details h3 {
            color: <?php echo $details['color']; ?>;
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .course-details p {
            color: #666;
            line-height: 1.6;
        }

        .score-section {
            text-align: center;
            margin: 30px 0;
        }

        .score-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, <?php echo $details['color']; ?> 0%, <?php echo $details['color']; ?>cc 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .score-value {
            font-size: 1.5em;
            font-weight: bold;
            color: white;
        }

        .score-text {
            font-size: 1.1em;
            color: #333;
        }

        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }

        .signature-line {
            text-align: center;
        }

        .signature-line .line {
            width: 200px;
            border-top: 1px solid #333;
            margin-bottom: 10px;
        }

        .signature-line p {
            color: #666;
            font-size: 0.9em;
        }

        .date {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 0.9em;
        }

        /* Buttons */
        .action-buttons {
            padding: 20px 50px 50px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, <?php echo $details['color']; ?> 0%, <?php echo $details['color']; ?>cc 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: <?php echo $details['color']; ?>;
            border: 1px solid <?php echo $details['color']; ?>;
        }

        .btn-secondary:hover {
            background: <?php echo $details['color']; ?>;
            color: white;
            transform: translateY(-2px);
        }

        .btn-download {
            background: #ffd700;
            color: #1a472a;
        }

        .btn-download:hover {
            background: #ffed4e;
            transform: translateY(-2px);
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .action-buttons {
                display: none;
            }
            
            .certificate {
                margin: 0;
                padding: 30px;
            }
        }

        @media (max-width: 768px) {
            .certificate {
                padding: 30px;
            }
            
            .certificate-title h1 {
                font-size: 1.5em;
            }
            
            .recipient h2 {
                font-size: 1.5em;
            }
            
            .signature {
                flex-direction: column;
                gap: 20px;
                align-items: center;
            }
            
            .action-buttons {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate" id="certificate">
            <div class="header">
                <div class="logo">Disaster <span>Ready</span> India</div>
                <div class="subtitle">Building a Resilient Nation</div>
            </div>
            
            <div class="certificate-title">
                <h1>CERTIFICATE OF COMPLETION</h1>
                <p>This certificate is proudly presented to</p>
            </div>
            
            <div class="recipient">
                <h2><?php echo htmlspecialchars($user_name); ?></h2>
                <p><?php echo htmlspecialchars($user_email); ?></p>
            </div>
            
            <div class="course-details">
                <div class="course-icon"><?php echo $details['icon']; ?></div>
                <h3><?php echo $details['name']; ?> Training</h3>
                <p><?php echo $details['description']; ?></p>
            </div>
            
            <div class="score-section">
                <div class="score-circle">
                    <div class="score-value"><?php echo $percentage; ?>%</div>
                </div>
                <div class="score-text">
                    Score: <?php echo $score; ?>/<?php echo $total; ?> • <?php echo $passed ? 'PASSED' : 'COMPLETED'; ?>
                </div>
            </div>
            
            <div class="signature">
                <div class="signature-line">
                    <div class="line"></div>
                    <p>Training Coordinator</p>
                    <p>Disaster Ready India</p>
                </div>
                <div class="signature-line">
                    <div class="line"></div>
                    <p>Regional Director</p>
                    <p>NDMA India</p>
                </div>
            </div>
            
            <div class="date">
                <p>Issued on: <?php echo $date; ?></p>
                <p>Certificate ID: DR-<?php echo strtoupper(substr($disaster, 0, 3)); ?>-<?php echo $user_id; ?>-<?php echo date('Ymd'); ?></p>
            </div>
        </div>
        
        <div class="action-buttons">
            <button class="btn btn-download" onclick="downloadCertificate()">
                <i class="fas fa-download"></i> Download as Image
            </button>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Certificate
            </button>
            <a href="dashboard_new.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function downloadCertificate() {
            const certificate = document.getElementById('certificate');
            
            html2canvas(certificate, {
                scale: 2,
                backgroundColor: '#ffffff',
                logging: false
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'certificate_<?php echo $disaster; ?>_<?php echo $user_id; ?>_<?php echo date('Ymd'); ?>.png';
                link.href = canvas.toDataURL();
                link.click();
            }).catch(error => {
                alert('Error generating certificate. Please try printing instead.');
                console.error(error);
            });
        }
    </script>
</body>
</html>