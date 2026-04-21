<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Check if user has completed the quiz
$check = $conn->query("SELECT * FROM quiz_results WHERE user_id='$user_id' AND disaster='lightening'");
if ($check->num_rows == 0) {
    header("Location: lightening_module.php");
    exit();
}

$result = $check->fetch_assoc();
$user_score = $result['score'];
$user_total = $result['total'];
$user_percentage = round(($user_score / $user_total) * 100, 1);

// Define questions with correct answers and explanations
$questions = [
    1 => [
        "question" => "During lightning?",
        "correct" => "Stay indoors",
        "explanation" => "When lightning strikes, the safest place is indoors. No place outside is safe when thunderstorms are in your area."
    ],
    2 => [
        "question" => "Avoid trees?",
        "correct" => "Yes",
        "explanation" => "Never stand under tall trees during a lightning storm. Trees are frequent targets for lightning strikes."
    ],
    3 => [
        "question" => "Water safety?",
        "correct" => "Avoid",
        "explanation" => "Avoid all water-related activities during a thunderstorm. Lightning can travel through plumbing pipes."
    ],
    4 => [
        "question" => "Electronics?",
        "correct" => "Unplug",
        "explanation" => "Unplug electronics before the storm arrives. Lightning strikes can cause power surges that damage devices."
    ],
    5 => [
        "question" => "Open fields?",
        "correct" => "Avoid",
        "explanation" => "Open fields are extremely dangerous during lightning storms. You become the tallest object and a potential lightning target."
    ],
    6 => [
        "question" => "Shelter?",
        "correct" => "Building",
        "explanation" => "Substantial buildings with walls and plumbing provide the best protection. Open structures do not offer adequate protection."
    ],
    7 => [
        "question" => "Metal objects?",
        "correct" => "Avoid",
        "explanation" => "Avoid metal objects like fences, umbrellas, golf clubs, and bicycles. Metal conducts electricity."
    ],
    8 => [
        "question" => "Wait after storm?",
        "correct" => "Yes",
        "explanation" => "Wait at least 30 minutes after the last thunder clap before leaving shelter. Lightning can strike up to 10 miles away."
    ],
    9 => [
        "question" => "Emergency help?",
        "correct" => "Call",
        "explanation" => "If someone is struck by lightning, call emergency services immediately. Lightning strike victims need immediate medical attention."
    ],
    10 => [
        "question" => "Stay calm?",
        "correct" => "Yes",
        "explanation" => "Staying calm helps you think clearly and follow safety protocols. Panic can lead to poor decisions during dangerous weather."
    ]
];

// Fetch user's answers from database
$user_answers = [];
$answers_query = $conn->query("SELECT * FROM quiz_answers WHERE user_id='$user_id' AND disaster='lightening' ORDER BY question_number");
while ($ans = $answers_query->fetch_assoc()) {
    $user_answers[$ans['question_number']] = $ans['selected_answer'];
}

// Also get the is_correct values directly from database
$correct_status = [];
$correct_query = $conn->query("SELECT question_number, is_correct FROM quiz_answers WHERE user_id='$user_id' AND disaster='lightening'");
while ($row = $correct_query->fetch_assoc()) {
    $correct_status[$row['question_number']] = $row['is_correct'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Review - Lightning Safety | Disaster Ready India</title>
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
            background: linear-gradient(135deg, #2c3e50 0%, #1a1a2e 100%);
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
            color: #2c3e50;
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
            background: linear-gradient(90deg, #f39c12, #e67e22);
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
            background: #fff3e0;
            padding: 12px 15px;
            border-radius: 8px;
            margin-top: 12px;
            font-size: 0.9em;
            color: #2c3e50;
            border-left: 3px solid #f39c12;
        }

        .explanation i {
            margin-right: 8px;
            color: #f39c12;
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
            background: linear-gradient(135deg, #2c3e50 0%, #1a1a2e 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44,62,80,0.3);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #2c3e50;
            border: 1px solid #2c3e50;
        }

        .btn-secondary:hover {
            background: #2c3e50;
            color: white;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: #f39c12;
            color: white;
        }

        .btn-warning:hover {
            background: #e67e22;
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
            
            .stats-info {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="review-container">
        <div class="review-header">
            <h2><i class="fas fa-eye"></i> Quiz Review</h2>
            <p>Lightning Safety Assessment - Review your answers</p>
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
                    <span><i class="fas fa-bolt" style="color: #f39c12;"></i> Passing Score: 70%</span>
                </div>
            </div>
            
            <?php $i = 1; foreach($questions as $num => $q): 
                // Get the stored answer and correct status from database
                $stored_answer = isset($user_answers[$num]) ? $user_answers[$num] : 'Not recorded';
                $is_correct = isset($correct_status[$num]) ? $correct_status[$num] : 0;
                
                // Display logic
                if ($is_correct == 1) {
                    $display_answer = "Correct";
                } else {
                    $display_answer = $stored_answer;
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
                                    <strong>Your answer:</strong> <?php echo htmlspecialchars($display_answer); ?>
                                </span>
                            <?php endif; ?>
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
                <a href="certificate.php?disaster=lightening&score=<?php echo $user_score; ?>&total=<?php echo $user_total; ?>" class="btn btn-warning" target="_blank">
                    <i class="fas fa-award"></i> Get Certificate
                </a>
            <?php endif; ?>
            <a href="lightening.php" class="btn btn-primary">
                <i class="fas fa-book-open"></i> Back to Module
            </a>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>
    </div>

    <script>
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
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.location.href = 'lightening.php';
            }
        });
    </script>
</body>
</html>