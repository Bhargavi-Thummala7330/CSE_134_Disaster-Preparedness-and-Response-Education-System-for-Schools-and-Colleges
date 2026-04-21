<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Check if user has already completed the quiz
$check = $conn->query("SELECT * FROM quiz_results WHERE user_id='$user_id' AND disaster='earthquake'");
$quiz_completed = ($check->num_rows > 0);

// If completed, fetch the user's score
$user_score = null;
$user_total = null;
$user_percentage = null;

if ($quiz_completed) {
    $result = $check->fetch_assoc();
    $user_score = $result['score'];
    $user_total = $result['total'];
    $user_percentage = round(($user_score / $user_total) * 100, 1);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earthquake Safety - Disaster Ready India</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1a472a 0%, #0a2f1a 100%);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo {
            font-size: 1.5em;
            font-weight: bold;
        }

        .logo span {
            color: #ffd700;
        }

        .header-buttons {
            display: flex;
            gap: 15px;
        }

        .dashboard-btn, .quiz-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .dashboard-btn {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .dashboard-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .quiz-btn {
            background: #ffd700;
            color: #1a472a;
        }

        .quiz-btn:hover {
            background: #ffed4e;
            transform: translateY(-2px);
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
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

        .hero h1 {
            color: #1a472a;
            font-size: 2em;
            margin-bottom: 15px;
        }

        .hero p {
            color: #666;
            font-size: 1.1em;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Key Points Section */
        .key-points {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .section-title i {
            font-size: 1.5em;
            color: #1a472a;
        }

        .section-title h2 {
            color: #333;
            font-size: 1.5em;
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
            transition: all 0.3s ease;
            border-left: 4px solid #1a472a;
        }

        .point-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .point-icon {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .point-card h3 {
            color: #1a472a;
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .point-card p {
            color: #666;
            line-height: 1.5;
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
            gap: 10px;
            font-size: 0.9em;
            color: #1a472a;
        }

        /* Quiz Button Section */
        .quiz-action {
            background: linear-gradient(135deg, #1a472a 0%, #0a2f1a 100%);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        .quiz-action h3 {
            font-size: 1.8em;
            margin-bottom: 15px;
        }

        .quiz-action p {
            margin-bottom: 25px;
            opacity: 0.9;
        }

        .attempt-quiz-btn {
            background: #ffd700;
            color: #1a472a;
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .attempt-quiz-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        /* Review Section */
        .review-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
        }

        .score-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a472a 0%, #0a2f1a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .score-value {
            font-size: 2em;
            font-weight: bold;
            color: white;
        }

        .review-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .review-btn {
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .review-quiz-btn {
            background: #1a472a;
            color: white;
        }

        .review-quiz-btn:hover {
            background: #0a2f1a;
            transform: translateY(-2px);
        }

        .certificate-btn {
            background: #ffd700;
            color: #1a472a;
        }

        .certificate-btn:hover {
            background: #ffed4e;
            transform: translateY(-2px);
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 30px;
            color: white;
            background: rgba(0,0,0,0.2);
            margin-top: 40px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
                flex-direction: column;
                text-align: center;
            }
            
            .hero {
                padding: 30px 20px;
            }
            
            .hero h1 {
                font-size: 1.5em;
            }
            
            .points-grid {
                grid-template-columns: 1fr;
            }
            
            .quiz-action {
                padding: 30px 20px;
            }
            
            .quiz-action h3 {
                font-size: 1.3em;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Disaster <span>Ready</span> India</div>
        <div class="header-buttons">
            <a href="dashboard.php" class="dashboard-btn" target="_parent">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <?php if (!$quiz_completed): ?>
                <a href="#" class="quiz-btn" onclick="openQuiz()">
                    <i class="fas fa-pen"></i> Take Quiz
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <!-- Hero Section -->
        <div class="hero">
            <div class="hero-icon">🌍</div>
            <h1>Earthquake Safety Training</h1>
            <p>Learn how to protect yourself and others during an earthquake</p>
        </div>

        <!-- Key Points Section -->
        <div class="key-points">
            <div class="section-title">
                <i class="fas fa-star-of-life"></i>
                <h2>Important Safety Points</h2>
            </div>
            <div class="points-grid">
                <div class="point-card">
                    <div class="point-icon">🏠</div>
                    <h3>Drop, Cover, and Hold On</h3>
                    <p>Drop to your hands and knees, cover your head and neck, and hold on until shaking stops.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🚪</div>
                    <h3>Stay Away from Windows</h3>
                    <p>Glass can shatter and cause serious injuries. Move to interior rooms away from windows.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🏃</div>
                    <h3>If Outside, Move to Open Area</h3>
                    <p>Stay away from buildings, trees, streetlights, and utility wires.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🛗</div>
                    <h3>Never Use Elevators</h3>
                    <p>Power outages can trap you inside. Use stairs if evacuation is necessary.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🛡️</div>
                    <h3>Protect Your Head</h3>
                    <p>Use your arms to protect your head and neck from falling objects.</p>
                </div>
                <div class="point-card">
                    <div class="point-icon">🔥</div>
                    <h3>Turn Off Gas</h3>
                    <p>If safe, turn off the main gas valve to prevent fires and explosions.</p>
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
                <video controls poster="../assets/images/earthquake-poster.jpg">
                    <source src="../assets/videos/earthquake.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="video-tips">
                <i class="fas fa-lightbulb"></i>
                <span>💡 Pro Tip: Watch the video carefully. Pay attention to the "Drop, Cover, and Hold On" technique - it's the most important safety measure during earthquakes!</span>
            </div>
        </div>

        <!-- Quiz Action Section -->
        <?php if (!$quiz_completed): ?>
            <div class="quiz-action">
                <h3>📝 Test Your Knowledge</h3>
                <p>Complete the quiz to earn your certificate and become earthquake ready!</p>
                <button class="attempt-quiz-btn" onclick="openQuiz()">
                    <i class="fas fa-play"></i> Attempt Quiz Now
                </button>
                <p style="margin-top: 15px; font-size: 0.85em;">
                    <i class="fas fa-info-circle"></i> You need 70% to pass and receive your certificate
                </p>
            </div>
        <?php else: ?>
            <!-- Review Section for Completed Users -->
            <div class="review-section">
                <div class="score-circle">
                    <div class="score-value"><?php echo $user_percentage; ?>%</div>
                </div>
                <h3 style="color: #1a472a; margin-bottom: 10px;">✓ Quiz Completed!</h3>
                <p style="color: #666; margin-bottom: 20px;">
                    Your Score: <?php echo $user_score; ?>/<?php echo $user_total; ?> 
                    (<?php echo $user_percentage; ?>%)
                </p>
                <div class="review-buttons">
                    <a href="earthquake_quiz_review.php" class="review-btn review-quiz-btn" target="_blank">
                        <i class="fas fa-eye"></i> Review Your Answers
                    </a>
                    <?php if ($user_percentage >= 70): ?>
                        <a href="certificate.php?disaster=earthquake&score=<?php echo $user_score; ?>&total=<?php echo $user_total; ?>" class="review-btn certificate-btn" target="_blank">
                            <i class="fas fa-download"></i> Download Certificate
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>© 2024 Disaster Ready India | Be Prepared, Stay Safe</p>
    </div>

    <script>
        function openQuiz() {
            // Open quiz in a new tab
            window.open('earthquake_quiz.php', '_blank');
        }
        
        // Prevent accidental navigation if quiz not completed
        window.addEventListener('beforeunload', function(e) {
            // Only show warning if video is playing
            const video = document.querySelector('video');
            if (video && !video.paused && !video.ended) {
                e.preventDefault();
                e.returnValue = 'Video is still playing. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
    </script>
</body>
</html>