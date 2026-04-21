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

if ($quiz_completed) {
    // If already completed, redirect to review page
    header("Location: earthquake_quiz_review.php");
    exit();
}

// Define questions
$questions = [
    1 => [
        "question" => "Best action during earthquake?",
        "options" => ["Drop Cover Hold" => 1, "Run" => 0, "Jump" => 0]
    ],
    2 => [
        "question" => "Stay away from?",
        "options" => ["Glass" => 1, "Open area" => 0]
    ],
    3 => [
        "question" => "Use elevator?",
        "options" => ["No" => 1, "Yes" => 0]
    ],
    4 => [
        "question" => "Safe place?",
        "options" => ["Under table" => 1, "Near window" => 0]
    ],
    5 => [
        "question" => "What to protect?",
        "options" => ["Head" => 1, "Feet" => 0]
    ],
    6 => [
        "question" => "After shaking?",
        "options" => ["Check injuries" => 1, "Ignore" => 0]
    ],
    7 => [
        "question" => "Fire risk?",
        "options" => ["Turn off gas" => 1, "Ignore" => 0]
    ],
    8 => [
        "question" => "Outside safety?",
        "options" => ["Open area" => 1, "Near building" => 0]
    ],
    9 => [
        "question" => "Emergency kit?",
        "options" => ["Yes" => 1, "No" => 0]
    ],
    10 => [
        "question" => "Stay calm?",
        "options" => ["Yes" => 1, "No" => 0]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earthquake Quiz - Disaster Ready India</title>
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

        .quiz-container {
            max-width: 800px;
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

        .quiz-header {
            background: linear-gradient(135deg, #1a472a 0%, #0a2f1a 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .quiz-header h2 {
            font-size: 1.8em;
            margin-bottom: 10px;
        }

        .quiz-header p {
            opacity: 0.9;
        }

        .quiz-content {
            padding: 30px;
        }

        .question-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .question-text {
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            font-size: 1.1em;
        }

        .options {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .option {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 25px;
            background: white;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .option:hover {
            background: #1a472a;
            border-color: #1a472a;
            color: white;
        }

        .option input {
            cursor: pointer;
            accent-color: #1a472a;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #1a472a 0%, #0a2f1a 100%);
            color: white;
            border: none;
            padding: 15px;
            font-size: 1.1em;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 71, 42, 0.3);
        }

        .timer {
            background: #f0f0f0;
            padding: 10px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
            color: #1a472a;
        }

        @media (max-width: 768px) {
            .quiz-container {
                margin: 20px;
            }
            
            .options {
                flex-direction: column;
            }
            
            .option {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <div class="quiz-header">
            <h2><i class="fas fa-pen"></i> Earthquake Safety Quiz</h2>
            <p>Test your knowledge - 10 questions, 70% to pass</p>
        </div>
        
        <div class="quiz-content">
            <div class="timer" id="timer">
                <i class="fas fa-clock"></i> Time Remaining: <span id="time">15:00</span>
            </div>
            
            <form action="submit_quiz.php" method="POST" id="quizForm">
                <?php $i = 1; foreach($questions as $num => $q): ?>
                    <div class="question-card">
                        <div class="question-text">
                            <?php echo $i . '. ' . htmlspecialchars($q['question']); ?>
                        </div>
                        <div class="options">
                            <?php foreach($q['options'] as $opt => $val): ?>
                                <label class="option">
                                    <input type="radio" name="q<?php echo $i; ?>" value="<?php echo $val; ?>" required>
                                    <span><?php echo htmlspecialchars($opt); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php $i++; endforeach; ?>
                
                <input type="hidden" name="total" value="10">
                <input type="hidden" name="disaster" value="earthquake">
                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-check-circle"></i> Submit Quiz
                </button>
            </form>
        </div>
    </div>

    <script>
        // Timer functionality (15 minutes)
        let timeLeft = 15 * 60; // 15 minutes in seconds
        const timerElement = document.getElementById('time');
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                alert('Time is up! Submitting your quiz...');
                document.getElementById('quizForm').submit();
            }
            timeLeft--;
        }
        
        const timerInterval = setInterval(updateTimer, 1000);
        
        // Form validation
        document.getElementById('quizForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            let allAnswered = true;
            
            for (let i = 1; i <= 10; i++) {
                const radios = document.getElementsByName('q' + i);
                let answered = false;
                for (let j = 0; j < radios.length; j++) {
                    if (radios[j].checked) {
                        answered = true;
                        break;
                    }
                }
                if (!answered) {
                    allAnswered = false;
                    alert('Please answer question ' + i);
                    e.preventDefault();
                    return false;
                }
            }
            
            if (allAnswered) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                submitBtn.disabled = true;
                clearInterval(timerInterval);
            }
        });
        
        // Warn before leaving
        window.addEventListener('beforeunload', function(e) {
            e.preventDefault();
            e.returnValue = 'Quiz is in progress. Are you sure you want to leave?';
            return e.returnValue;
        });
    </script>
</body>
</html>