<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Check if user has completed the quiz
$check = $conn->query("SELECT * FROM quiz_results WHERE user_id='$user_id' AND disaster='fire'");
if ($check->num_rows == 0) {
    header("Location: fire_module.php");
    exit();
}

$result = $check->fetch_assoc();
$user_score = $result['score'];
$user_total = $result['total'];
$user_percentage = round(($user_score / $user_total) * 100, 1);

// Define questions with correct answers and explanations
$questions = [
    1 => [
        "question" => "What to do during fire?",
        "options" => ["Use extinguisher" => 1, "Panic" => 0],
        "correct" => "Use extinguisher",
        "explanation" => "If the fire is small and you're trained, use a fire extinguisher. Never panic - stay calm and follow your escape plan."
    ],
    2 => [
        "question" => "Avoid?",
        "options" => ["Smoke inhalation" => 1, "Fresh air" => 0],
        "correct" => "Smoke inhalation",
        "explanation" => "Smoke inhalation is the leading cause of death in fires. Crawl low under smoke to avoid breathing toxic fumes."
    ],
    3 => [
        "question" => "Emergency number?",
        "options" => ["101" => 1, "100" => 0],
        "correct" => "101",
        "explanation" => "101 is the emergency number for fire services in India. 100 is for police, 102 for ambulance."
    ],
    4 => [
        "question" => "Clothes on fire?",
        "options" => ["Stop Drop Roll" => 1, "Run" => 0],
        "correct" => "Stop Drop Roll",
        "explanation" => "Stop, Drop, and Roll is the correct technique to extinguish flames on clothing. Running makes the fire worse by fanning it."
    ],
    5 => [
        "question" => "Use elevator?",
        "options" => ["No" => 1, "Yes" => 0],
        "correct" => "No",
        "explanation" => "Never use elevators during a fire. They may malfunction or open on a fire floor. Always use stairs."
    ],
    6 => [
        "question" => "Check doors?",
        "options" => ["Yes" => 1, "No" => 0],
        "correct" => "Yes",
        "explanation" => "Always feel doors with the back of your hand before opening. If hot, find another exit. If cool, open slowly and be ready to close if needed."
    ],
    7 => [
        "question" => "Smoke rises?",
        "options" => ["Stay low" => 1, "Stand tall" => 0],
        "correct" => "Stay low",
        "explanation" => "Smoke and toxic gases rise to the ceiling. Crawl low on your hands and knees to stay below the smoke layer."
    ],
    8 => [
        "question" => "Fire alarm?",
        "options" => ["Activate" => 1, "Ignore" => 0],
        "correct" => "Activate",
        "explanation" => "Activate the nearest fire alarm immediately if you discover a fire. This alerts others and initiates emergency response."
    ],
    9 => [
        "question" => "Escape plan?",
        "options" => ["Yes" => 1, "No" => 0],
        "correct" => "Yes",
        "explanation" => "Having a home fire escape plan with two ways out of every room saves lives. Practice it twice a year with your family."
    ],
    10 => [
        "question" => "Stay calm?",
        "options" => ["Yes" => 1, "No" => 0],
        "correct" => "Yes",
        "explanation" => "Staying calm helps you remember your escape plan and make rational decisions. Panic can lead to dangerous mistakes."
    ]
];

// Fetch user's answers
$user_answers = [];
$answers_query = $conn->query("SELECT * FROM quiz_answers WHERE user_id='$user_id' AND disaster='fire' ORDER BY question_number");
while ($ans = $answers_query->fetch_assoc()) {
    $user_answers[$ans['question_number']] = $ans['selected_answer'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Review - Fire Safety | Disaster Ready India</title>
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

        .review-header {
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
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
            color: #c0392b;
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

        .review-content {
            padding: 30px;
        }

        .stats-bar {
            background: #f0f0f0;
            border-radius: 10px;
            margin-bottom: 30px;
            overflow: hidden;
        }

        .stats-progress {
            background: linear-gradient(90deg, #c0392b, #e74c3c);
            height: 10px;
            transition: width 1s ease;
        }

        .stats-info {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
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
            background: #f0fff0;
        }

        .question-review.incorrect {
            border-left-color: #e74c3c;
            background: #fff5f5;
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
            background: #ffe0e0;
            padding: 12px 15px;
            border-radius: 8px;
            margin-top: 12px;
            font-size: 0.9em;
            color: #c0392b;
            border-left: 3px solid #e74c3c;
        }

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
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(192,57,43,0.3);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #c0392b;
            border: 1px solid #c0392b;
        }

        .btn-secondary:hover {
            background: #c0392b;
            color: white;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: #ffd700;
            color: #c0392b;
        }

        .btn-warning:hover {
            background: #ffed4e;
            transform: translateY(-2px);
        }

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
            <p>Fire Safety Assessment - Review your answers</p>
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
                    <span><i class="fas fa-fire-extinguisher" style="color: #e74c3c;"></i> Passing Score: 70%</span>
                </div>
            </div>
            
            <?php $i = 1; foreach($questions as $num => $q): 
                $user_answer = isset($user_answers[$num]) ? $user_answers[$num] : 'Not answered';
                $correct_answer = $q['correct'];
                
                // Check if the answer is correct
                // If the stored answer is "Correct" or matches the correct answer text
                $is_correct = false;
                
                if ($user_answer == "Correct" || $user_answer == $correct_answer) {
                    $is_correct = true;
                }
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
                                <span class="answer-value">
                                    <strong>Your answer:</strong> Correct
                                </span>
                            <?php else: ?>
                                <i class="fas fa-times-circle"></i>
                                <span class="answer-value">
                                    <strong>Your answer:</strong> Incorrect
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!$is_correct): ?>
                            <div class="correct-answer">
                                <i class="fas fa-check-circle"></i>
                                <span class="answer-value">
                                    <strong>Correct answer:</strong> <?php echo htmlspecialchars($correct_answer); ?>
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
                <a href="certificate.php?disaster=fire&score=<?php echo $user_score; ?>&total=<?php echo $user_total; ?>" class="btn btn-warning" target="_blank">
                    <i class="fas fa-award"></i> Get Certificate
                </a>
            <?php endif; ?>
            <a href="fire_module.php" class="btn btn-primary">
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
                window.location.href = 'fire_module.php';
            }
        });
    </script>
</body>
</html>