<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Check if user has completed the quiz
$check = $conn->query("SELECT * FROM quiz_results WHERE user_id='$user_id' AND disaster='drought'");
if ($check->num_rows == 0) {
    header("Location: drought.php");
    exit();
}

$result = $check->fetch_assoc();
$user_score = $result['score'];
$user_total = $result['total'];
$user_percentage = round(($user_score / $user_total) * 100, 1);

// Define questions with correct answers and explanations
$questions = [
    1 => [
        "question" => "Water saving?",
        "options" => ["Yes" => 1, "Waste" => 0],
        "correct" => "Yes",
        "explanation" => "Water conservation is crucial during drought. Simple actions like fixing leaks, taking shorter showers, and using water-efficient appliances can save thousands of gallons."
    ],
    2 => [
        "question" => "Rainwater harvesting?",
        "options" => ["Yes" => 1, "No" => 0],
        "correct" => "Yes",
        "explanation" => "Rainwater harvesting collects and stores rainwater for later use. This provides an alternative water source during drought conditions."
    ],
    3 => [
        "question" => "Irrigation?",
        "options" => ["Efficient" => 1, "Flooding" => 0],
        "correct" => "Efficient",
        "explanation" => "Efficient irrigation methods like drip irrigation or sprinklers reduce water waste compared to flood irrigation, which can lose up to 50% of water to evaporation."
    ],
    4 => [
        "question" => "Food storage?",
        "options" => ["Yes" => 1, "No" => 0],
        "correct" => "Yes",
        "explanation" => "During drought, food supplies may be affected. Having adequate food storage ensures you have enough supplies during extended dry periods."
    ],
    5 => [
        "question" => "Government alerts?",
        "options" => ["Follow" => 1, "Ignore" => 0],
        "correct" => "Follow",
        "explanation" => "Always follow government alerts and water restriction notices. They provide critical information about water conservation measures and drought status."
    ],
    6 => [
        "question" => "Water reuse?",
        "options" => ["Yes" => 1, "No" => 0],
        "correct" => "Yes",
        "explanation" => "Reusing greywater from sinks, showers, and washing machines for gardening can significantly reduce freshwater consumption during drought."
    ],
    7 => [
        "question" => "Crop planning?",
        "options" => ["Yes" => 1, "No" => 0],
        "correct" => "Yes",
        "explanation" => "Planning drought-resistant crops and rotating crops helps farmers maintain food production even during water-scarce conditions."
    ],
    8 => [
        "question" => "Avoid waste?",
        "options" => ["Yes" => 1, "No" => 0],
        "correct" => "Yes",
        "explanation" => "Avoiding water waste is essential during drought. Don't leave taps running, fix leaks promptly, and use water wisely in daily activities."
    ],
    9 => [
        "question" => "Emergency plan?",
        "options" => ["Yes" => 1, "No" => 0],
        "correct" => "Yes",
        "explanation" => "Having an emergency plan for drought includes storing water, identifying alternative water sources, and knowing community water distribution points."
    ],
    10 => [
        "question" => "Stay aware?",
        "options" => ["Yes" => 1, "No" => 0],
        "correct" => "Yes",
        "explanation" => "Stay aware of drought conditions, water restrictions, and conservation tips through local news, weather alerts, and government announcements."
    ]
];

// Fetch user's answers
$user_answers = [];
$answers_query = $conn->query("SELECT * FROM quiz_answers WHERE user_id='$user_id' AND disaster='drought' ORDER BY question_number");
while ($ans = $answers_query->fetch_assoc()) {
    $user_answers[$ans['question_number']] = $ans['selected_answer'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Review - Drought Preparedness | Disaster Ready India</title>
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
            background: linear-gradient(135deg, #b8651a 0%, #8b4513 100%);
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
            color: #b8651a;
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

        .stats-bar {
            background: #f0f0f0;
            border-radius: 10px;
            margin-bottom: 30px;
            overflow: hidden;
        }

        .stats-progress {
            background: linear-gradient(90deg, #b8651a, #cc7b2e);
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
            background: #fff3e0;
            padding: 12px 15px;
            border-radius: 8px;
            margin-top: 12px;
            font-size: 0.9em;
            color: #8b4513;
            border-left: 3px solid #b8651a;
        }

        .explanation i {
            margin-right: 8px;
            color: #b8651a;
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
            background: linear-gradient(135deg, #b8651a 0%, #8b4513 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(184, 101, 26, 0.3);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #b8651a;
            border: 1px solid #b8651a;
        }

        .btn-secondary:hover {
            background: #b8651a;
            color: white;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: #ffd700;
            color: #8b4513;
        }

        .btn-warning:hover {
            background: #ffed4e;
            transform: translateY(-2px);
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
            
            .stats-info {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
        }
        
        /* Water drop animation */
        @keyframes drop {
            0% {
                transform: translateY(-10px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .score-circle {
            animation: drop 0.5s ease;
        }
    </style>
</head>
<body>
    <div class="review-container">
        <div class="review-header">
            <h2><i class="fas fa-eye"></i> Quiz Review</h2>
            <p>Drought Preparedness Assessment - Review your answers</p>
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
                    <span><i class="fas fa-tint" style="color: #3498db;"></i> Passing Score: 70%</span>
                </div>
            </div>
            
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
                <a href="certificate.php?disaster=drought&score=<?php echo $user_score; ?>&total=<?php echo $user_total; ?>" class="btn btn-warning" target="_blank">
                    <i class="fas fa-award"></i> Get Certificate
                </a>
            <?php endif; ?>
            <a href="drought.php" class="btn btn-primary">
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
                window.location.href = 'drought.php';
            }
        });
        
        // Print functionality
        function printReview() {
            window.print();
        }
        
        // Share results
        function shareResults() {
            const score = <?php echo $user_score; ?>;
            const total = <?php echo $user_total; ?>;
            const percentage = <?php echo $user_percentage; ?>;
            
            if (navigator.share) {
                navigator.share({
                    title: 'Drought Preparedness Quiz Results',
                    text: `I scored ${score}/${total} (${percentage}%) on the Drought Preparedness Quiz!`,
                    url: window.location.href
                });
            } else {
                alert(`Share your results: I scored ${score}/${total} (${percentage}%) on the Drought Preparedness Quiz!`);
            }
        }
    </script>
</body>
</html>