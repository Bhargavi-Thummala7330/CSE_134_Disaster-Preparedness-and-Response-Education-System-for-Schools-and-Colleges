<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$total = isset($_POST['total']) ? (int)$_POST['total'] : 0;
$disaster = isset($_POST['disaster']) ? mysqli_real_escape_string($conn, $_POST['disaster']) : '';

// Default fallback
if (empty($disaster)) {
    $disaster = 'earthquake';
}

$score = 0;

// 🔥 OPTIONS (ONLY USED TO STORE ANSWER TEXT)
$question_options = [
    'earthquake' => [
        1 => ["Drop Cover Hold" => 1, "Run" => 0, "Jump" => 0],
        2 => ["Glass" => 1, "Open area" => 0],
        3 => ["No" => 1, "Yes" => 0],
        4 => ["Under table" => 1, "Near window" => 0],
        5 => ["Head" => 1, "Feet" => 0],
        6 => ["Check injuries" => 1, "Ignore" => 0],
        7 => ["Turn off gas" => 1, "Ignore" => 0],
        8 => ["Open area" => 1, "Near building" => 0],
        9 => ["Yes" => 1, "No" => 0],
        10 => ["Yes" => 1, "No" => 0]
    ],

    'flood' => [
        1 => ["Move to higher ground" => 1, "Stay in low area" => 0],
        2 => ["Floodwater" => 1, "Clear area" => 0],
        3 => ["No" => 1, "Yes" => 0],
        4 => ["Yes" => 1, "No" => 0],
        5 => ["Yes" => 1, "No" => 0],
        6 => ["Yes" => 1, "No" => 0],
        7 => ["Yes" => 1, "No" => 0],
        8 => ["Yes" => 1, "No" => 0],
        9 => ["Yes" => 1, "No" => 0],
        10 => ["Stay calm" => 1, "Panic" => 0]
    ],

    'fire' => [
        1 => ["Use extinguisher" => 1, "Panic" => 0],
        2 => ["Smoke inhalation" => 1, "Fresh air" => 0],
        3 => ["101" => 1, "100" => 0],
        4 => ["Stop Drop Roll" => 1, "Run" => 0],
        5 => ["No" => 1, "Yes" => 0],
        6 => ["Yes" => 1, "No" => 0],
        7 => ["Stay low" => 1, "Stand tall" => 0],
        8 => ["Activate" => 1, "Ignore" => 0],
        9 => ["Yes" => 1, "No" => 0],
        10 => ["Yes" => 1, "No" => 0]
    ],

    'cyclone' => [
        1 => ["Prepare supplies" => 1, "Ignore" => 0],
        2 => ["Yes" => 1, "No" => 0],
        3 => ["Close tightly" => 1, "Open" => 0],
        4 => ["High" => 1, "None" => 0],
        5 => ["Yes" => 1, "No" => 0],
        6 => ["Avoid" => 1, "Touch" => 0],
        7 => ["Yes" => 1, "No" => 0],
        8 => ["Avoid" => 1, "Continue" => 0],
        9 => ["Safe place" => 1, "Outside" => 0],
        10 => ["Yes" => 1, "No" => 0]
    ],

    'drought' => [
        1 => ["Yes" => 1, "Waste" => 0],
        2 => ["Yes" => 1, "No" => 0],
        3 => ["Efficient" => 1, "Flooding" => 0],
        4 => ["Yes" => 1, "No" => 0],
        5 => ["Follow" => 1, "Ignore" => 0],
        6 => ["Yes" => 1, "No" => 0],
        7 => ["Yes" => 1, "No" => 0],
        8 => ["Yes" => 1, "No" => 0],
        9 => ["Yes" => 1, "No" => 0],
        10 => ["Yes" => 1, "No" => 0]
    ],

    'tsunami' => [
        1 => ["Move inland" => 1, "Stay beach" => 0],
        2 => ["Run inland" => 1, "Wait" => 0],
        3 => ["Very high" => 1, "Small" => 0],
        4 => ["Yes" => 1, "No" => 0],
        5 => ["Yes" => 1, "No" => 0],
        6 => ["No" => 1, "Yes" => 0],
        7 => ["Immediate" => 1, "Delay" => 0],
        8 => ["Higher ground" => 1, "Stay low" => 0],
        9 => ["Follow" => 1, "Ignore" => 0],
        10 => ["Yes" => 1, "No" => 0]
    ],

    'lightning' => [ // ✅ FIXED SPELLING
        1 => ["Stay indoors" => 1, "Go outside" => 0],
        2 => ["Yes" => 1, "No" => 0],
        3 => ["Avoid" => 1, "Safe" => 0],
        4 => ["Unplug" => 1, "Use" => 0],
        5 => ["Avoid" => 1, "Stay" => 0],
        6 => ["Building" => 1, "Open area" => 0],
        7 => ["Avoid" => 1, "Touch" => 0],
        8 => ["Yes" => 1, "No" => 0],
        9 => ["Call" => 1, "Ignore" => 0],
        10 => ["Yes" => 1, "No" => 0]
    ]
];

// Clear old answers
$conn->query("DELETE FROM quiz_answers WHERE user_id='$user_id' AND disaster='$disaster'");

// Process answers
for ($i = 1; $i <= $total; $i++) {
    if (isset($_POST["q$i"])) {

        $answer_value = (int)$_POST["q$i"];

        // ✅ VALUE-BASED SCORING (MAIN FIX)
        $is_correct = ($answer_value == 1) ? 1 : 0;

        if ($is_correct) {
            $score++;
        }

        // Get answer text
        $selected_answer = "";
        if (isset($question_options[$disaster][$i])) {
            foreach ($question_options[$disaster][$i] as $text => $val) {
                if ($val == $answer_value) {
                    $selected_answer = $text;
                    break;
                }
            }
        }

        // Save answer
        $conn->query("INSERT INTO quiz_answers 
            (user_id, disaster, question_number, selected_answer, is_correct) 
            VALUES ('$user_id', '$disaster', '$i', '$selected_answer', '$is_correct')");
    }
}

// Save result
$conn->query("INSERT INTO quiz_results 
(user_id, disaster, score, total, created_at)
VALUES ('$user_id', '$disaster', '$score', '$total', NOW())");

// Redirect
header("Location: {$disaster}_quiz_review.php");
exit();
?>