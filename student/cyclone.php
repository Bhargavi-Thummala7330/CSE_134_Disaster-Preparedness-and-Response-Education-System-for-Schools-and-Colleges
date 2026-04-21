<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Check if user has already completed the quiz
$check = $conn->query("SELECT * FROM quiz_results WHERE user_id='$user_id' AND disaster='cyclone'");
$quiz_completed = ($check->num_rows > 0);

if ($quiz_completed) {
    header("Location: cyclone_quiz_review.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyclone Preparedness - Disaster Ready India</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
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
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
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
            content: '🌀';
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
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .hero h2 {
            color: #1e3c72;
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
            color: #1e3c72;
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
            border-left: 4px solid #1e3c72;
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
            color: #1e3c72;
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .point-card p {
            color: #666;
            line-height: 1.5;
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
            color: #1e3c72;
        }

        .video-tips i {
            font-size: 1.2em;
        }

        /* Emergency Kit Section */
        .emergency-kit {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .kit-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .kit-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .kit-item:hover {
            transform: translateY(-3px);
            background: #1e3c72;
            color: white;
        }

        .kit-item i {
            font-size: 1.5em;
            margin-bottom: 8px;
            display: block;
        }

        .kit-item span {
            font-size: 0.85em;
        }

        /* Quiz Section */
        .quiz-section {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
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
            color: #1e3c72;
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
            
            .kit-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .quiz-section {
                padding: 30px 20px;
            }
            
            .quiz-btn {
                padding: 12px 30px;
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-cyclone"></i> Cyclone Preparedness Training</h1>
            <p>Learn how to protect yourself and your family during cyclones</p>
        </div>

        <!-- Hero Section -->
        <div class="hero">
            <div class="hero-icon">🌀</div>
            <h2>Be Cyclone Ready</h2>
            <p>Cyclones can cause devastating winds, heavy rainfall, and storm surges. Proper preparation can save lives and minimize damage.</p>
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
                    <h3>Secure Your Home</h3>
                    <p>Reinforce doors and windows, trim trees, and secure loose objects that could become projectiles in strong winds.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🆘</div>
                    <h3>Emergency Kit</h3>
                    <p>Prepare an emergency kit with water, food, medications, flashlight, batteries, and important documents.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">📻</div>
                    <h3>Stay Informed</h3>
                    <p>Monitor weather updates, cyclone warnings, and evacuation orders from official sources.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🚪</div>
                    <h3>Evacuation Plan</h3>
                    <p>Know your evacuation routes and identify safe shelters in your area before a cyclone hits.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">💧</div>
                    <h3>Flood Awareness</h3>
                    <p>Cyclones bring heavy rainfall and storm surges. Know if you're in a flood-prone area.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🔌</div>
                    <h3>Power Safety</h3>
                    <p>Turn off gas and electricity before evacuating. Avoid fallen power lines after the storm.</p>
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
                    <li><i class="fas fa-check-circle"></i> Stay indoors during the cyclone</li>
                    <li><i class="fas fa-check-circle"></i> Keep emergency supplies ready</li>
                    <li><i class="fas fa-check-circle"></i> Listen to official warnings and alerts</li>
                    <li><i class="fas fa-check-circle"></i> Move to higher ground if flooding occurs</li>
                    <li><i class="fas fa-check-circle"></i> Have a family communication plan</li>
                    <li><i class="fas fa-check-circle"></i> Board up windows and secure doors</li>
                    <li><i class="fas fa-check-circle"></i> Keep important documents in waterproof bags</li>
                </ul>
            </div>
            
            <div class="donts-card">
                <div class="section-title">
                    <i class="fas fa-times-circle"></i>
                    <h2>Don'ts ❌</h2>
                </div>
                <ul class="donts-list">
                    <li><i class="fas fa-times-circle"></i> Don't go outside during the storm</li>
                    <li><i class="fas fa-times-circle"></i> Don't drive through flood waters</li>
                    <li><i class="fas fa-times-circle"></i> Don't use candles - use flashlights</li>
                    <li><i class="fas fa-times-circle"></i> Don't touch fallen power lines</li>
                    <li><i class="fas fa-times-circle"></i> Don't ignore evacuation orders</li>
                    <li><i class="fas fa-times-circle"></i> Don't use elevators during power outages</li>
                    <li><i class="fas fa-times-circle"></i> Don't stay in low-lying coastal areas</li>
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
                <video controls poster="../assets/images/cyclone-poster.jpg">
                    <source src="../assets/videos/cyclone.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="video-tips">
                <i class="fas fa-lightbulb"></i>
                <span>💡 Pro Tip: Watch the video carefully to understand cyclone formation, warning signals, and safety measures. Pay special attention to evacuation procedures!</span>
            </div>
        </div>

        <!-- Emergency Kit -->
        <div class="emergency-kit">
            <div class="section-title">
                <i class="fas fa-first-aid"></i>
                <h2>Emergency Kit Checklist</h2>
            </div>
            <div class="kit-grid">
                <div class="kit-item">
                    <i class="fas fa-tint"></i>
                    <span>Water (3+ days)</span>
                </div>
                <div class="kit-item">
                    <i class="fas fa-utensils"></i>
                    <span>Non-perishable Food</span>
                </div>
                <div class="kit-item">
                    <i class="fas fa-battery-full"></i>
                    <span>Flashlight & Batteries</span>
                </div>
                <div class="kit-item">
                    <i class="fas fa-band-aid"></i>
                    <span>First Aid Kit</span>
                </div>
                <div class="kit-item">
                    <i class="fas fa-radio"></i>
                    <span>Battery-powered Radio</span>
                </div>
                <div class="kit-item">
                    <i class="fas fa-pills"></i>
                    <span>Medications</span>
                </div>
                <div class="kit-item">
                    <i class="fas fa-file-alt"></i>
                    <span>Important Documents</span>
                </div>
                <div class="kit-item">
                    <i class="fas fa-phone-alt"></i>
                    <span>Power Bank</span>
                </div>
                <div class="kit-item">
                    <i class="fas fa-tshirt"></i>
                    <span>Warm Clothing</span>
                </div>
                <div class="kit-item">
                    <i class="fas fa-money-bill"></i>
                    <span>Cash & Cards</span>
                </div>
            </div>
        </div>

        <!-- Quiz Section -->
        <div class="quiz-section">
            <h2>📝 Test Your Knowledge</h2>
            <p>Complete the quiz to earn your certificate and become cyclone ready!</p>
            <a href="cyclone_quiz.php" class="quiz-btn" target="_blank">
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
        
        // Add animation to kit items
        const kitItems = document.querySelectorAll('.kit-item');
        kitItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.05}s`;
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>