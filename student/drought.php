<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Check if user has already completed the quiz
$check = $conn->query("SELECT * FROM quiz_results WHERE user_id='$user_id' AND disaster='drought'");
$quiz_completed = ($check->num_rows > 0);

if ($quiz_completed) {
    header("Location: drought_quiz_review.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drought Preparedness - Disaster Ready India</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #d4a373 0%, #cc7b2e 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Header */
        .header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #8b4513;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
        }

        /* Hero Section */
        .hero {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .hero-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }

        /* Key Points */
        .key-points {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .points-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .point-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #b8651a;
        }

        .point-card h3 {
            color: #8b4513;
            margin-bottom: 10px;
        }

        /* Video Section */
        .video-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 12px;
            margin-top: 20px;
        }

        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* Quiz Button */
        .quiz-section {
            text-align: center;
            background: linear-gradient(135deg, #b8651a 0%, #8b4513 100%);
            border-radius: 20px;
            padding: 40px;
            color: white;
        }

        .quiz-btn {
            display: inline-block;
            background: #ffd700;
            color: #8b4513;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1em;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .quiz-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .hero {
                padding: 30px 20px;
            }
            
            .points-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💧 Drought Preparedness Training</h1>
            <p>Learn how to conserve water and prepare for drought conditions</p>
        </div>

        <div class="hero">
            <div class="hero-icon">💧</div>
            <h2>Be Water Wise</h2>
            <p>Droughts can have severe impacts on communities, agriculture, and ecosystems. Learning to conserve water and prepare for dry conditions is essential.</p>
        </div>

        <div class="key-points">
            <div class="section-title">
                <i class="fas fa-star-of-life"></i>
                <h2>Important Drought Preparedness Tips</h2>
            </div>
            <div class="points-grid">
                <div class="point-card">
                    <h3>💧 Save Water</h3>
                    <p>Fix leaks, take shorter showers, and use water-efficient appliances to conserve water.</p>
                </div>
                <div class="point-card">
                    <h3>🌧️ Rainwater Harvesting</h3>
                    <p>Collect rainwater for gardening and other non-potable uses.</p>
                </div>
                <div class="point-card">
                    <h3>🚜 Efficient Irrigation</h3>
                    <p>Use drip irrigation and water plants during cooler hours to reduce evaporation.</p>
                </div>
                <div class="point-card">
                    <h3>📦 Food Storage</h3>
                    <p>Keep adequate food supplies as agriculture may be affected during drought.</p>
                </div>
                <div class="point-card">
                    <h3>📢 Follow Alerts</h3>
                    <p>Pay attention to government water restrictions and drought alerts.</p>
                </div>
                <div class="point-card">
                    <h3>🔄 Water Reuse</h3>
                    <p>Reuse greywater from sinks and showers for gardening purposes.</p>
                </div>
            </div>
        </div>

        <div class="video-section">
            <div class="section-title">
                <i class="fas fa-video"></i>
                <h2>Training Video</h2>
            </div>
            <div class="video-container">
                <video controls poster="../assets/images/drought-poster.jpg">
                    <source src="../assets/videos/drought.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>

        <div class="quiz-section">
            <h2>📝 Test Your Knowledge</h2>
            <p>Complete the quiz to earn your certificate and become drought ready!</p>
            <a href="drought_quiz.php" class="quiz-btn" target="_blank">
                <i class="fas fa-play"></i> Take Quiz Now
            </a>
            <p style="margin-top: 15px; font-size: 0.85em;">
                <i class="fas fa-info-circle"></i> You need 70% to pass and receive your certificate
            </p>
        </div>
    </div>
</body>
</html>