<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Check if user has already completed the quiz
$check = $conn->query("SELECT * FROM quiz_results WHERE user_id='$user_id' AND disaster='tsunami'");
$quiz_completed = ($check->num_rows > 0);

if ($quiz_completed) {
    header("Location: tsunami_quiz_review.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tsunami Preparedness - Disaster Ready India</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #006994 0%, #003d5c 100%);
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
            background: linear-gradient(135deg, #006994 0%, #003d5c 100%);
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
            content: '🌊';
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
            animation: wave 2s ease-in-out infinite;
        }

        @keyframes wave {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-10px) rotate(5deg);
            }
        }

        .hero h2 {
            color: #006994;
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
            color: #006994;
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
            border-left: 4px solid #006994;
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
            color: #006994;
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .point-card p {
            color: #666;
            line-height: 1.5;
        }

        /* Warning Signs Section */
        .warning-signs {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .signs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .sign-card {
            background: #fff3e0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #ffd700;
        }

        .sign-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .sign-icon {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .sign-card h4 {
            color: #cc7b2e;
            margin-bottom: 10px;
        }

        .sign-card p {
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
            color: #006994;
        }

        /* Quiz Section */
        .quiz-section {
            background: linear-gradient(135deg, #006994 0%, #003d5c 100%);
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
            background: #ffd700;
            color: #006994;
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
            background: #ffed4e;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-water"></i> Tsunami Preparedness Training</h1>
            <p>Learn how to recognize tsunami warnings and protect yourself</p>
        </div>

        <!-- Hero Section -->
        <div class="hero">
            <div class="hero-icon">🌊</div>
            <h2>Be Tsunami Ready</h2>
            <p>Tsunamis are powerful ocean waves that can cause massive destruction. Early recognition and quick action can save lives.</p>
        </div>

        <!-- Key Points -->
        <div class="key-points">
            <div class="section-title">
                <i class="fas fa-star-of-life"></i>
                <h2>Critical Safety Points</h2>
            </div>
            <div class="points-grid">
                <div class="point-card">
                    <div class="point-icon">⚠️</div>
                    <h3>Recognize Natural Warnings</h3>
                    <p>Strong earthquake, rapid rise or fall of coastal water, and roaring ocean sound are natural tsunami warnings.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🏃</div>
                    <h3>Evacuate Immediately</h3>
                    <p>If you feel a strong earthquake near the coast, don't wait for official warnings. Move to higher ground immediately.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">📻</div>
                    <h3>Stay Informed</h3>
                    <p>Listen to official tsunami warnings and evacuation orders from local authorities.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🗺️</div>
                    <h3>Know Your Zone</h3>
                    <p>Learn if you live in a tsunami evacuation zone and identify safe evacuation routes.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🎒</div>
                    <h3>Emergency Kit</h3>
                    <p>Keep a grab-and-go bag ready with essentials in case of sudden evacuation.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">📱</div>
                    <h3>Alert Systems</h3>
                    <p>Enable emergency alerts on your phone and know the tsunami warning signals in your area.</p>
                </div>
            </div>
        </div>

        <!-- Warning Signs Section -->
        <div class="warning-signs">
            <div class="section-title">
                <i class="fas fa-exclamation-triangle"></i>
                <h2>Natural Tsunami Warning Signs</h2>
            </div>
            <div class="signs-grid">
                <div class="sign-card">
                    <div class="sign-icon">🌍</div>
                    <h4>Strong Earthquake</h4>
                    <p>If you feel a strong earthquake near the coast, a tsunami may follow</p>
                </div>
                <div class="sign-card">
                    <div class="sign-icon">🌊</div>
                    <h4>Rapid Water Change</h4>
                    <p>Sudden sea level rise or dramatic water recession exposing the seafloor</p>
                </div>
                <div class="sign-card">
                    <div class="sign-icon">🔊</div>
                    <h4>Roaring Sound</h4>
                    <p>A loud ocean roar like a jet engine or train approaching</p>
                </div>
                <div class="sign-card">
                    <div class="sign-icon">📢</div>
                    <h4>Official Warning</h4>
                    <p>Tsunami sirens, emergency alerts, or official announcements</p>
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
                    <li><i class="fas fa-check-circle"></i> Move inland or to higher ground immediately</li>
                    <li><i class="fas fa-check-circle"></i> Follow evacuation routes and signs</li>
                    <li><i class="fas fa-check-circle"></i> Listen to official warnings and alerts</li>
                    <li><i class="fas fa-check-circle"></i> Take your emergency kit with you</li>
                    <li><i class="fas fa-check-circle"></i> Help others who need assistance</li>
                    <li><i class="fas fa-check-circle"></i> Stay in safe area until all-clear is given</li>
                    <li><i class="fas fa-check-circle"></i> Know multiple evacuation routes</li>
                </ul>
            </div>
            
            <div class="donts-card">
                <div class="section-title">
                    <i class="fas fa-times-circle"></i>
                    <h2>Don'ts ❌</h2>
                </div>
                <ul class="donts-list">
                    <li><i class="fas fa-times-circle"></i> Don't stay near the beach or coast</li>
                    <li><i class="fas fa-times-circle"></i> Don't go to watch the tsunami</li>
                    <li><i class="fas fa-times-circle"></i> Don't wait for official warning if you see natural signs</li>
                    <li><i class="fas fa-times-circle"></i> Don't return to low-lying areas until all-clear</li>
                    <li><i class="fas fa-times-circle"></i> Don't use elevators during evacuation</li>
                    <li><i class="fas fa-times-circle"></i> Don't ignore evacuation orders</li>
                    <li><i class="fas fa-times-circle"></i> Don't drive through flood waters</li>
                </ul>
            </div>
        </div>

        <!-- Video Section -->
        <div class="video-section">
            <div class="section-title">
                <i class="fas fa-video"></i>
                <h2>Training Video</h2>
            </div>
            <div class="video-container">
                <video controls poster="../assets/images/tsunami-poster.jpg">
                    <source src="../assets/videos/tsunami.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="video-tips">
                <i class="fas fa-lightbulb"></i>
                <span>💡 Pro Tip: Tsunamis can arrive within minutes. If you feel a strong earthquake near the coast, evacuate immediately - don't wait for official warnings!</span>
            </div>
        </div>

        <!-- Quiz Section -->
        <div class="quiz-section">
            <h2>📝 Test Your Knowledge</h2>
            <p>Complete the quiz to earn your certificate and become tsunami ready!</p>
            <a href="tsunami_quiz.php" class="quiz-btn" target="_blank">
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