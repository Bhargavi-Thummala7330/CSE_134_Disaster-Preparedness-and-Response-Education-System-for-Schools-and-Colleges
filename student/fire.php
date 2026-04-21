<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Check if user has already completed the quiz
$check = $conn->query("SELECT * FROM quiz_results WHERE user_id='$user_id' AND disaster='fire'");
$quiz_completed = ($check->num_rows > 0);

if ($quiz_completed) {
    header("Location: fire_quiz_review.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Safety - Disaster Ready India</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
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
            background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
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
            content: '🔥';
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
            animation: flicker 1.5s ease-in-out infinite;
        }

        @keyframes flicker {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        .hero h2 {
            color: #c0392b;
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
            color: #e74c3c;
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
            border-left: 4px solid #e74c3c;
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
            color: #c0392b;
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .point-card p {
            color: #666;
            line-height: 1.5;
        }

        /* Fire Classes Section */
        .fire-classes {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .class-card {
            background: linear-gradient(135deg, #fff5f5 0%, #ffe0e0 100%);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #ffcccc;
        }

        .class-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .class-letter {
            font-size: 2.5em;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }

        .class-card h4 {
            color: #c0392b;
            margin-bottom: 10px;
        }

        .class-card p {
            color: #666;
            font-size: 0.85em;
        }

        /* PASS Method */
        .pass-section {
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            text-align: center;
        }

        .pass-section h3 {
            font-size: 1.5em;
            margin-bottom: 15px;
        }

        .pass-steps {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .pass-step {
            background: rgba(255,255,255,0.15);
            padding: 20px;
            border-radius: 12px;
            min-width: 120px;
            transition: all 0.3s ease;
        }

        .pass-step:hover {
            background: rgba(255,255,255,0.25);
            transform: scale(1.05);
        }

        .pass-letter {
            font-size: 2em;
            font-weight: bold;
            color: #ffd700;
        }

        .pass-word {
            font-size: 1.2em;
            margin-top: 8px;
        }

        .pass-desc {
            font-size: 0.8em;
            margin-top: 5px;
            opacity: 0.9;
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
            background: #ffe0e0;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9em;
            color: #c0392b;
        }

        /* Quiz Section */
        .quiz-section {
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
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
            color: #c0392b;
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

        /* Emergency Numbers */
        .emergency-numbers {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .emergency-number {
            background: rgba(255,255,255,0.15);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 1.1em;
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
            
            .pass-steps {
                flex-direction: column;
                align-items: center;
            }
            
            .pass-step {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-fire-extinguisher"></i> Fire Safety Training</h1>
            <p>Learn how to prevent fires and protect yourself during fire emergencies</p>
        </div>

        <!-- Hero Section -->
        <div class="hero">
            <div class="hero-icon">🔥</div>
            <h2>Be Fire Safe</h2>
            <p>Fires can spread rapidly. Knowing what to do can save lives and property.</p>
        </div>

        <!-- Key Points -->
        <div class="key-points">
            <div class="section-title">
                <i class="fas fa-star-of-life"></i>
                <h2>Critical Safety Points</h2>
            </div>
            <div class="points-grid">
                <div class="point-card">
                    <div class="point-icon">🚪</div>
                    <h3>Know Your Exits</h3>
                    <p>Always know at least two ways out of every room. Identify emergency exits in buildings.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🔔</div>
                    <h3>Smoke Alarms</h3>
                    <p>Install smoke alarms on every level of your home and test them monthly.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🧯</div>
                    <h3>Fire Extinguishers</h3>
                    <p>Keep fire extinguishers accessible and learn how to use them with the PASS method.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🏃</div>
                    <h3>Escape Plan</h3>
                    <p>Create and practice a home fire escape plan with your family.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">📞</div>
                    <h3>Emergency Number</h3>
                    <p>Know your local emergency number (101 for fire services).</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">⬇️</div>
                    <h3>Stay Low</h3>
                    <p>Smoke rises, so crawl low under smoke to escape.</p>
                </div>
            </div>
        </div>

        <!-- Fire Classes -->
        <div class="fire-classes">
            <div class="section-title">
                <i class="fas fa-fire"></i>
                <h2>Types of Fires</h2>
            </div>
            <div class="classes-grid">
                <div class="class-card">
                    <div class="class-letter">Class A</div>
                    <h4>Ordinary Combustibles</h4>
                    <p>Wood, paper, cloth, plastic</p>
                </div>
                <div class="class-card">
                    <div class="class-letter">Class B</div>
                    <h4>Flammable Liquids</h4>
                    <p>Gasoline, oil, paint, grease</p>
                </div>
                <div class="class-card">
                    <div class="class-letter">Class C</div>
                    <h4>Electrical Fires</h4>
                    <p>Appliances, wiring, circuit breakers</p>
                </div>
                <div class="class-card">
                    <div class="class-letter">Class D</div>
                    <h4>Combustible Metals</h4>
                    <p>Magnesium, titanium, sodium</p>
                </div>
                <div class="class-card">
                    <div class="class-letter">Class K</div>
                    <h4>Cooking Oils</h4>
                    <p>Kitchen fires, grease, cooking oils</p>
                </div>
            </div>
        </div>

        <!-- PASS Method -->
        <div class="pass-section">
            <h3><i class="fas fa-fire-extinguisher"></i> How to Use a Fire Extinguisher</h3>
            <div class="pass-steps">
                <div class="pass-step">
                    <div class="pass-letter">P</div>
                    <div class="pass-word">PULL</div>
                    <div class="pass-desc">Pull the pin</div>
                </div>
                <div class="pass-step">
                    <div class="pass-letter">A</div>
                    <div class="pass-word">AIM</div>
                    <div class="pass-desc">Aim at the base of the fire</div>
                </div>
                <div class="pass-step">
                    <div class="pass-letter">S</div>
                    <div class="pass-word">SQUEEZE</div>
                    <div class="pass-desc">Squeeze the handle</div>
                </div>
                <div class="pass-step">
                    <div class="pass-letter">S</div>
                    <div class="pass-word">SWEEP</div>
                    <div class="pass-desc">Sweep side to side</div>
                </div>
            </div>
            <div class="emergency-numbers">
                <div class="emergency-number"><i class="fas fa-phone-alt"></i> Fire Emergency: 101</div>
                <div class="emergency-number"><i class="fas fa-phone-alt"></i> Ambulance: 102</div>
                <div class="emergency-number"><i class="fas fa-phone-alt"></i> Police: 100</div>
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
                    <li><i class="fas fa-check-circle"></i> Crawl low under smoke to escape</li>
                    <li><i class="fas fa-check-circle"></i> Feel doors before opening - if hot, find another exit</li>
                    <li><i class="fas fa-check-circle"></i> Stop, Drop, and Roll if clothes catch fire</li>
                    <li><i class="fas fa-check-circle"></i> Close doors behind you to slow fire spread</li>
                    <li><i class="fas fa-check-circle"></i> Have a meeting place outside</li>
                    <li><i class="fas fa-check-circle"></i> Test smoke alarms monthly</li>
                    <li><i class="fas fa-check-circle"></i> Call 101 immediately for emergencies</li>
                </ul>
            </div>
            
            <div class="donts-card">
                <div class="section-title">
                    <i class="fas fa-times-circle"></i>
                    <h2>Don'ts ❌</h2>
                </div>
                <ul class="donts-list">
                    <li><i class="fas fa-times-circle"></i> Don't use elevators during a fire</li>
                    <li><i class="fas fa-times-circle"></i> Don't break windows unnecessarily</li>
                    <li><i class="fas fa-times-circle"></i> Don't go back inside for belongings</li>
                    <li><i class="fas fa-times-circle"></i> Don't hide in closets or under beds</li>
                    <li><i class="fas fa-times-circle"></i> Don't use water on electrical fires</li>
                    <li><i class="fas fa-times-circle"></i> Don't ignore fire alarms</li>
                    <li><i class="fas fa-times-circle"></i> Don't leave cooking unattended</li>
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
                <video controls poster="../assets/images/fire-poster.jpg">
                    <source src="../assets/videos/fire.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="video-tips">
                <i class="fas fa-lightbulb"></i>
                <span>💡 Pro Tip: Remember PASS for fire extinguishers and "Stop, Drop, and Roll" if your clothes catch fire!</span>
            </div>
        </div>

        <!-- Quiz Section -->
        <div class="quiz-section">
            <h2>📝 Test Your Knowledge</h2>
            <p>Complete the quiz to earn your certificate and become fire safe!</p>
            <a href="fire_quiz.php" class="quiz-btn" target="_blank">
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