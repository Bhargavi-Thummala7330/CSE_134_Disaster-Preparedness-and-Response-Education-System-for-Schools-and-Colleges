<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Check if user has completed the quiz
$check = $conn->query("SELECT * FROM quiz_results WHERE user_id='$user_id' AND disaster='earthquake'");
if ($check->num_rows == 0) {
    header("Location: earthquake_module.php");
    exit();
}

$result = $check->fetch_assoc();
$user_score = $result['score'];
$user_total = $result['total'];
$user_percentage = round(($user_score / $user_total) * 100, 1);

// Define questions with correct answers and explanations
$questions = [
    1 => [
        "question" => "Best action during earthquake?",
        "options" => ["Drop Cover Hold" => 1, "Run" => 0, "Jump" => 0],
        "correct" => "Drop Cover Hold",
        "explanation" => "The 'Drop, Cover, and Hold On' technique is the internationally recommended safety measure during earthquakes. Drop to your hands and knees, cover your head and neck, and hold on until shaking stops."
    ],
    2 => [
        "question" => "Stay away from?",
        "options" => ["Glass" => 1, "Open area" => 0],
        "correct" => "Glass",
        "explanation" => "Stay away from windows, glass panels, and mirrors as they can shatter and cause serious injuries during an earthquake. Glass fragments can cause severe cuts."
    ],
    3 => [
        "question" => "Use elevator?",
        "options" => ["No" => 1, "Yes" => 0],
        "correct" => "No",
        "explanation" => "Never use elevators during an earthquake. Power outages can trap you inside. Always use stairs if evacuation is necessary."
    ],
    4 => [
        "question" => "Safe place?",
        "options" => ["Under table" => 1, "Near window" => 0],
        "correct" => "Under table",
        "explanation" => "Take cover under sturdy furniture like a table or desk to protect yourself from falling objects and debris. Avoid being near windows."
    ],
    5 => [
        "question" => "What to protect?",
        "options" => ["Head" => 1, "Feet" => 0],
        "correct" => "Head",
        "explanation" => "Always protect your head and neck as they are most vulnerable to falling objects and debris during an earthquake. Use your arms to cover your head."
    ],
    6 => [
        "question" => "After shaking?",
        "options" => ["Check injuries" => 1, "Ignore" => 0],
        "correct" => "Check injuries",
        "explanation" => "After the shaking stops, check yourself and others for injuries. Provide first aid if necessary before evacuating the area."
    ],
    7 => [
        "question" => "Fire risk?",
        "options" => ["Turn off gas" => 1, "Ignore" => 0],
        "correct" => "Turn off gas",
        "explanation" => "Earthquakes can rupture gas lines. If safe, turn off the main gas valve to prevent fires and explosions. Only do this if you smell gas."
    ],
    8 => [
        "question" => "Outside safety?",
        "options" => ["Open area" => 1, "Near building" => 0],
        "correct" => "Open area",
        "explanation" => "If you're outside, move to an open area away from buildings, trees, streetlights, and utility wires to avoid falling debris."
    ],
    9 => [
        "question" => "Emergency kit?",
        "options" => ["Yes" => 1, "No" => 0],
        "correct" => "Yes",
        "explanation" => "Always have an emergency kit ready with water, food, first aid supplies, flashlight, batteries, and important documents for post-disaster survival."
    ],
    10 => [
        "question" => "Stay calm?",
        "options" => ["Yes" => 1, "No" => 0],
        "correct" => "Yes",
        "explanation" => "Staying calm helps you think clearly and make better decisions during an emergency situation. Panic can lead to poor choices and injury."
    ]
];

// Fetch user's answers
$user_answers = [];
$answers_query = $conn->query("SELECT * FROM quiz_answers WHERE user_id='$user_id' AND disaster='earthquake' ORDER BY question_number");
while ($ans = $answers_query->fetch_assoc()) {
    $user_answers[$ans['question_number']] = $ans['selected_answer'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Review - Earthquake Safety | Disaster Ready India</title>
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
            padding: 40px 20px;
        }

        .review-container {
            max-width: 1000px;
            margin: 0 auto;
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

        /* Header */
        .review-header {
            background: linear-gradient(135deg, #1a472a 0%, #0a2f1a 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .review-header h2 {
            font-size: 1.8em;
            margin-bottom: 10px;
        }

        .score-summary {
            display: inline-flex;
            align-items: center;
            gap: 20px;
            background: rgba(255,255,255,0.2);
            padding: 15px 25px;
            border-radius: 12px;
            margin-top: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .score-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .score-value {
            font-size: 1.5em;
            font-weight: bold;
            color: #1a472a;
        }

        .score-stats {
            text-align: left;
        }

        .score-stats .score {
            font-size: 1.5em;
            font-weight: bold;
        }

        .score-stats .status {
            margin-top: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.85em;
            display: inline-block;
        }

        .status.passed {
            background: #4caf50;
            color: white;
        }

        .status.failed {
            background: #e74c3c;
            color: white;
        }

        /* Content */
        .review-content {
            padding: 30px;
        }

        .question-review {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid;
            transition: all 0.3s ease;
        }

        .question-review.correct {
            border-left-color: #27ae60;
        }

        .question-review.incorrect {
            border-left-color: #e74c3c;
        }

        .question-review:hover {
            transform: translateX(5px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .question-text {
            font-weight: 600;
            color: #333;
            font-size: 1.05em;
        }

        .answer-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }

        .answer-status.correct {
            background: #d4edda;
            color: #155724;
        }

        .answer-status.incorrect {
            background: #f8d7da;
            color: #721c24;
        }

        .answers {
            margin-bottom: 15px;
        }

        .your-answer, .correct-answer {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .your-answer i, .correct-answer i {
            width: 24px;
            font-size: 1em;
        }

        .your-answer i.fa-check-circle {
            color: #27ae60;
        }

        .your-answer i.fa-times-circle {
            color: #e74c3c;
        }

        .correct-answer i {
            color: #27ae60;
        }

        .answer-value {
            font-weight: 500;
        }

        .explanation {
            background: #e8f0fe;
            padding: 12px 15px;
            border-radius: 8px;
            margin-top: 12px;
            font-size: 0.9em;
            color: #1a472a;
            border-left: 3px solid #1a472a;
        }

        .explanation i {
            margin-right: 8px;
            color: #1a472a;
        }

        /* Action Buttons */
        .action-buttons {
            padding: 20px 30px 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            border-top: 1px solid #e0e0e0;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 0.95em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1a472a 0%, #0a2f1a 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 71, 42, 0.3);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #1a472a;
            border: 1px solid #1a472a;
        }

        .btn-secondary:hover {
            background: #1a472a;
            color: white;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: #ffd700;
            color: #1a472a;
        }

        .btn-warning:hover {
            background: #ffed4e;
            transform: translateY(-2px);
        }

        /* Stats Bar */
        .stats-bar {
            background: #f0f0f0;
            border-radius: 10px;
            margin-bottom: 30px;
            overflow: hidden;
        }


        .stats-info {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .review-container {
                margin: 20px;
            }
            
            .review-header {
                padding: 20px;
            }
            
            .review-content {
                padding: 20px;
            }
            
            .question-review {
                padding: 15px;
            }
            
            .question-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .action-buttons {
                padding: 20px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="review-container">
        <div class="review-header">
            <h2><i class="fas fa-eye"></i> Quiz Review</h2>
            <p>Earthquake Safety Assessment - Review your answers</p>
            <div class="score-summary">
                <div class="score-circle">
                    <div class="score-value"><?php echo $user_percentage; ?>%</div>
                </div>
                <div class="score-stats">
                    <div class="score"><?php echo $user_score; ?>/<?php echo $user_total; ?></div>
                    <div class="status <?php echo $user_percentage >= 70 ? 'passed' : 'failed'; ?>">
                        <?php echo $user_percentage >= 70 ? '✓ PASSED' : '✗ NEEDS IMPROVEMENT'; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="review-content">
           <div class="stats-bar">
    <div class="stats-progress" style="width: <?php echo $user_percentage; ?>%;"></div>
    <div class="stats-info">
        <span><i class="fas fa-check-circle" style="color: #27ae60;"></i> Correct: <?php echo $user_score; ?></span>
        <span><i class="fas fa-times-circle" style="color: #e74c3c;"></i> Incorrect: <?php echo $user_total - $user_score; ?></span>
        <span><i class="fas fa-star" style="color: #ffd700;"></i> Passing Score: 70%</span>
    </div>
</div>

<style>
    .stats-progress {
        background: linear-gradient(90deg, #1a472a, #2ecc71);
        height: 10px;
        transition: width 1s ease;
    }
</style>
            
            <?php $i = 1; foreach($questions as $num => $q): 
                $user_answer = $user_answers[$num] ?? 'Not answered';
                $is_correct = ($user_answer == $q['correct']);
            ?>
                <div class="question-review <?php echo $is_correct ? 'correct' : 'incorrect'; ?>">
                    <div class="question-header">
                        <div class="question-text">
                            <strong>Q<?php echo $i; ?>.</strong> <?php echo htmlspecialchars($q['question']); ?>
                        </div>
                        <div class="answer-status <?php echo $is_correct ? 'correct' : 'incorrect'; ?>">
                            <?php echo $is_correct ? '✓ Correct' : '✗ Incorrect'; ?>
                        </div>
                    </div>
                    
                    <div class="answers">
                        <div class="your-answer">
                            <?php if ($is_correct): ?>
                                <i class="fas fa-check-circle"></i>
                            <?php else: ?>
                                <i class="fas fa-times-circle"></i>
                            <?php endif; ?>
                            <span class="answer-value">
                                <strong>Your answer:</strong> <?php echo htmlspecialchars($user_answer); ?>
                            </span>
                        </div>
                        
                        <?php if (!$is_correct): ?>
                            <div class="correct-answer">
                                <i class="fas fa-check-circle"></i>
                                <span class="answer-value">
                                    <strong>Correct answer:</strong> <?php echo htmlspecialchars($q['correct']); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="explanation">
                        <i class="fas fa-info-circle"></i>
                        <strong>Explanation:</strong> <?php echo htmlspecialchars($q['explanation']); ?>
                    </div>
                </div>
            <?php $i++; endforeach; ?>
        </div>
        
        <div class="action-buttons">
            <?php if ($user_percentage >= 70): ?>
                <a href="certificate.php?disaster=earthquake&score=<?php echo $user_score; ?>&total=<?php echo $user_total; ?>" class="btn btn-warning" target="_blank">
                    <i class="fas fa-award"></i> Get Certificate
                </a>
            <?php endif; ?>
            <a href="earthquake.php" class="btn btn-primary">
                <i class="fas fa-book-open"></i> Back to Module
            </a>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>
    </div>

    <script>
        // Animate progress bar on load
        window.addEventListener('load', function() {
            const progressBar = document.querySelector('.stats-progress');
            if (progressBar) {
                const width = progressBar.style.width;
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.width = width;
                }, 100);
            }
        });
        
        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.location.href = 'earthquake.php';
            }
        });
    </script>
</body>
</html>