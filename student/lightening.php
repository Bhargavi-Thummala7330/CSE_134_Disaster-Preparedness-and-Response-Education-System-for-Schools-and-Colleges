<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Check if user has already completed the quiz
$check = $conn->query("SELECT * FROM quiz_results WHERE user_id='$user_id' AND disaster='lightening'");
$quiz_completed = ($check->num_rows > 0);

if ($quiz_completed) {
    header("Location: lightening_quiz_review.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightning Safety - Disaster Ready India</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c3e50 0%, #1a1a2e 100%);
            min-height: 100vh;
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #1a1a2e 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .header h1 {
            font-size: 2.2em;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1em;
        }

        /* Hero Section */
        .hero {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '⚡';
            position: absolute;
            font-size: 150px;
            right: -30px;
            bottom: -30px;
            opacity: 0.05;
            pointer-events: none;
        }

        .hero-icon {
            font-size: 4em;
            margin-bottom: 20px;
            animation: flash 2s ease-in-out infinite;
        }

        @keyframes flash {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.7;
                transform: scale(1.1);
            }
        }

        .hero h2 {
            color: #2c3e50;
            font-size: 1.8em;
            margin-bottom: 15px;
        }

        .hero p {
            color: #666;
            font-size: 1.1em;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Section Title */
        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }

        .section-title i {
            font-size: 1.8em;
            color: #f39c12;
        }

        .section-title h2 {
            color: #333;
            font-size: 1.5em;
        }

        /* Key Points Grid */
        .key-points {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .points-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .point-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            border-left: 4px solid #f39c12;
        }

        .point-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .point-icon {
            font-size: 2em;
            margin-bottom: 12px;
        }

        .point-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .point-card p {
            color: #666;
            line-height: 1.5;
        }

        /* Lightning Facts */
        .facts-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .facts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .fact-card {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .fact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .fact-number {
            font-size: 2em;
            font-weight: bold;
            color: #f39c12;
            margin-bottom: 10px;
        }

        .fact-card p {
            color: #666;
            font-size: 0.9em;
        }

        /* Do's and Don'ts Section */
        .dos-donts {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .dos-card, .donts-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .dos-card .section-title i {
            color: #27ae60;
        }

        .donts-card .section-title i {
            color: #e74c3c;
        }

        .dos-list, .donts-list {
            list-style: none;
            padding: 0;
        }

        .dos-list li, .donts-list li {
            padding: 12px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #f0f0f0;
        }

        .dos-list li:last-child, .donts-list li:last-child {
            border-bottom: none;
        }

        .dos-list i {
            color: #27ae60;
            font-size: 1.2em;
        }

        .donts-list i {
            color: #e74c3c;
            font-size: 1.2em;
        }

        /* 30-30 Rule Section */
        .rule-section {
            background: linear-gradient(135deg, #2c3e50 0%, #1a1a2e 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            text-align: center;
        }

        .rule-section h3 {
            font-size: 1.5em;
            margin-bottom: 15px;
        }

        .rule-content {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .rule-item {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 12px;
            min-width: 150px;
        }

        .rule-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #f39c12;
        }

        .rule-label {
            margin-top: 10px;
            font-size: 0.9em;
        }

        /* Video Section */
        .video-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 12px;
            margin-top: 20px;
            background: #000;
        }

        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 12px;
        }

        .video-tips {
            background: #e8f0fe;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9em;
            color: #2c3e50;
        }

        /* Quiz Section */
        .quiz-section {
            background: linear-gradient(135deg, #2c3e50 0%, #1a1a2e 100%);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            color: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .quiz-section h2 {
            font-size: 1.8em;
            margin-bottom: 15px;
        }

        .quiz-section p {
            margin-bottom: 25px;
            opacity: 0.9;
        }

        .quiz-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #f39c12;
            color: #2c3e50;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1em;
            transition: all 0.3s ease;
        }

        .quiz-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            background: #ffd700;
        }

        .quiz-info {
            margin-top: 20px;
            font-size: 0.85em;
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 1.5em;
            }
            
            .hero {
                padding: 30px 20px;
            }
            
            .hero h2 {
                font-size: 1.3em;
            }
            
            .dos-donts {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .points-grid {
                grid-template-columns: 1fr;
            }
            
            .quiz-section {
                padding: 30px 20px;
            }
            
            .rule-content {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-bolt"></i> Lightning Safety Training</h1>
            <p>Learn how to protect yourself during thunderstorms and lightening strikes</p>
        </div>

        <!-- Hero Section -->
        <div class="hero">
            <div class="hero-icon">⚡</div>
            <h2>Be Lightning Safe</h2>
            <p>Lightning strikes can be deadly. Knowing what to do during a thunderstorm can save your life.</p>
        </div>

        <!-- Key Points -->
        <div class="key-points">
            <div class="section-title">
                <i class="fas fa-star-of-life"></i>
                <h2>Critical Safety Points</h2>
            </div>
            <div class="points-grid">
                <div class="point-card">
                    <div class="point-icon">🏠</div>
                    <h3>When Thunder Roars, Go Indoors</h3>
                    <p>No place outside is safe when thunderstorms are in your area. Get inside a substantial building or hard-topped vehicle.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">⏱️</div>
                    <h3>The 30-30 Rule</h3>
                    <p>If you see lightening, count seconds until you hear thunder. If less than 30 seconds, seek shelter. Wait 30 minutes after last thunder before going out.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🌳</div>
                    <h3>Avoid Tall Objects</h3>
                    <p>Never stand under tall trees, telephone poles, or other tall objects during a lightening storm.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">💧</div>
                    <h3>Avoid Water</h3>
                    <p>Don't shower, bathe, or use plumbing during a thunderstorm. Lightening can travel through pipes.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">📱</div>
                    <h3>Unplug Electronics</h3>
                    <p>Unplug appliances and electronics before the storm arrives to prevent damage from power surges.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🚗</div>
                    <h3>Safe Vehicle</h3>
                    <p>If caught outside, a hard-topped metal vehicle provides protection. Avoid convertibles.</p>
                </div>
            </div>
        </div>

        <!-- Lightning Facts -->
        <div class="facts-section">
            <div class="section-title">
                <i class="fas fa-chart-line"></i>
                <h2>Lightning Facts</h2>
            </div>
            <div class="facts-grid">
                <div class="fact-card">
                    <div class="fact-number">5x</div>
                    <p>Hotter than the surface of the sun</p>
                </div>
                <div class="fact-card">
                    <div class="fact-number">300M</div>
                    <p>Volts of electricity</p>
                </div>
                <div class="fact-card">
                    <div class="fact-number">30,000°C</div>
                    <p>Temperature of a lightening bolt</p>
                </div>
                <div class="fact-card">
                    <div class="fact-number">10 miles</div>
                    <p>Lightning can strike up to 10 miles away</p>
                </div>
            </div>
        </div>

        <!-- Do's and Don'ts -->
        <div class="dos-donts">
            <div class="dos-card">
                <div class="section-title">
                    <i class="fas fa-check-circle"></i>
                    <h2>Do's ✅</h2>
                </div>
                <ul class="dos-list">
                    <li><i class="fas fa-check-circle"></i> Go indoors immediately when thunder roars</li>
                    <li><i class="fas fa-check-circle"></i> Wait 30 minutes after last thunder before going out</li>
                    <li><i class="fas fa-check-circle"></i> Use the 30-30 rule for safety</li>
                    <li><i class="fas fa-check-circle"></i> Unplug electronics before the storm</li>
                    <li><i class="fas fa-check-circle"></i> Stay in your vehicle if caught outside</li>
                    <li><i class="fas fa-check-circle"></i> Keep emergency kit ready</li>
                    <li><i class="fas fa-check-circle"></i> Seek shelter in substantial buildings</li>
                </ul>
            </div>
            
            <div class="donts-card">
                <div class="section-title">
                    <i class="fas fa-times-circle"></i>
                    <h2>Don'ts ❌</h2>
                </div>
                <ul class="donts-list">
                    <li><i class="fas fa-times-circle"></i> Don't stand under tall trees</li>
                    <li><i class="fas fa-times-circle"></i> Don't use water (shower, bath, wash dishes)</li>
                    <li><i class="fas fa-times-circle"></i> Don't use corded electronics</li>
                    <li><i class="fas fa-times-circle"></i> Don't lie on concrete floors</li>
                    <li><i class="fas fa-times-circle"></i> Don't take shelter in open structures</li>
                    <li><i class="fas fa-times-circle"></i> Don't wait until the last minute</li>
                    <li><i class="fas fa-times-circle"></i> Don't use umbrellas near metal objects</li>
                </ul>
            </div>
        </div>

        <!-- 30-30 Rule Section -->
        <div class="rule-section">
            <h3><i class="fas fa-clock"></i> The 30-30 Rule</h3>
            <div class="rule-content">
                <div class="rule-item">
                    <div class="rule-number">30</div>
                    <div class="rule-label">If time between lightening and thunder is <strong>less than 30 seconds</strong>, seek shelter immediately</div>
                </div>
                <div class="rule-item">
                    <div class="rule-number">30</div>
                    <div class="rule-label">Wait <strong>30 minutes</strong> after last thunder before leaving shelter</div>
                </div>
            </div>
        </div>

        <!-- Video Section -->
        <div class="video-section">
            <div class="section-title">
                <i class="fas fa-video"></i>
                <h2>Training Video</h2>
            </div>
            <div class="video-container">
                <video controls poster="../assets/images/lightening-poster.jpg">
                    <source src="../assets/videos/lightening.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="video-tips">
                <i class="fas fa-lightbulb"></i>
                <span>💡 Pro Tip: Remember "When Thunder Roars, Go Indoors!" No place outside is safe when thunderstorms are in your area.</span>
            </div>
        </div>

        <!-- Quiz Section -->
        <div class="quiz-section">
            <h2>📝 Test Your Knowledge</h2>
            <p>Complete the quiz to earn your certificate and become lightening safe!</p>
            <a href="lightening_quiz.php" class="quiz-btn" target="_blank">
                <i class="fas fa-play"></i> Take Quiz Now
            </a>
            <div class="quiz-info">
                <i class="fas fa-info-circle"></i> 10 questions • 70% to pass • 15 minutes time limit
            </div>
        </div>
    </div>

    <script>
        // Warn before leaving if video is playing
        const video = document.querySelector('video');
        if (video) {
            video.addEventListener('play', function() {
                window.addEventListener('beforeunload', function(e) {
                    if (!video.paused && !video.ended) {
                        e.preventDefault();
                        e.returnValue = 'Video is still playing. Are you sure you want to leave?';
                        return e.returnValue;
                    }
                });
            });
            
            video.addEventListener('pause', function() {
                window.removeEventListener('beforeunload', () => {});
            });
            
            video.addEventListener('ended', function() {
                window.removeEventListener('beforeunload', () => {});
            });
        }
    </script>
</body>
</html>