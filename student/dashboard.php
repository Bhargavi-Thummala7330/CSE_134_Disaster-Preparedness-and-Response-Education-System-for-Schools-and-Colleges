<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$user_name = $_SESSION['user']['name'];
$user_email = $_SESSION['user']['email'];

// FIXED: Count DISTINCT modules completed (not total attempts)
$completed_modules = $conn->query("SELECT COUNT(DISTINCT disaster) as completed FROM quiz_results WHERE user_id='$user_id'")->fetch_assoc()['completed'];

$total_modules = 7; // Earthquake, Flood, Fire, Cyclone, Drought, Tsunami, Lightning

// Calculate average score from all quiz attempts
$avg_score_query = $conn->query("SELECT AVG(score/total)*100 as avg FROM quiz_results WHERE user_id='$user_id'");
$avg_score = round($avg_score_query->fetch_assoc()['avg'] ?? 0, 1);

// Get recent alerts
$alerts = $conn->query("SELECT * FROM alerts ORDER BY id DESC LIMIT 3");

// Define all 7 modules with their details
$modules = [
    'earthquake' => [
        'name' => 'Earthquake Safety',
        'icon' => '🌍',
        'description' => 'Learn how to protect yourself during earthquakes using Drop, Cover, and Hold On techniques.',
        'duration' => '15 mins',
        'level' => 'Beginner',
        'level_color' => '#27ae60',
        'color' => '#667eea',
        'file' => 'earthquake.php'
    ],
    'flood' => [
        'name' => 'Flood Preparedness',
        'icon' => '🌊',
        'description' => 'Stay safe during floods and waterlogging with proper evacuation and safety measures.',
        'duration' => '20 mins',
        'level' => 'Intermediate',
        'level_color' => '#f39c12',
        'color' => '#3498db',
        'file' => 'flood.php'
    ],
    'fire' => [
        'name' => 'Fire Safety',
        'icon' => '🔥',
        'description' => 'Prevent and respond to fire emergencies with proper evacuation and extinguisher use.',
        'duration' => '15 mins',
        'level' => 'Beginner',
        'level_color' => '#27ae60',
        'color' => '#e74c3c',
        'file' => 'fire.php'
    ],
    'cyclone' => [
        'name' => 'Cyclone Preparedness',
        'icon' => '🌀',
        'description' => 'Prepare for and survive tropical cyclones with early warning and safety protocols.',
        'duration' => '25 mins',
        'level' => 'Advanced',
        'level_color' => '#e74c3c',
        'color' => '#9b59b6',
        'file' => 'cyclone.php'
    ],
    'drought' => [
        'name' => 'Drought Management',
        'icon' => '💧',
        'description' => 'Learn water conservation techniques and drought preparedness strategies.',
        'duration' => '20 mins',
        'level' => 'Intermediate',
        'level_color' => '#f39c12',
        'color' => '#b8651a',
        'file' => 'drought.php'
    ],
    'tsunami' => [
        'name' => 'Tsunami Safety',
        'icon' => '🌊',
        'description' => 'Recognize tsunami warning signs and learn evacuation procedures.',
        'duration' => '20 mins',
        'level' => 'Intermediate',
        'level_color' => '#f39c12',
        'color' => '#006994',
        'file' => 'tsunami.php'
    ],
    'lightning' => [
        'name' => 'Lightning Safety',
        'icon' => '⚡',
        'description' => 'Learn the 30-30 rule and how to stay safe during thunderstorms.',
        'duration' => '15 mins',
        'level' => 'Beginner',
        'level_color' => '#27ae60',
        'color' => '#f39c12',
        'file' => 'lightening.php'
    ]
];

// Check which modules are completed (using DISTINCT)
$completed_modules_list = [];
$result_check = $conn->query("SELECT DISTINCT disaster FROM quiz_results WHERE user_id='$user_id'");
while ($row = $result_check->fetch_assoc()) {
    $disaster = $row['disaster'];
    // Normalize disaster names for comparison
    if ($disaster == 'lightening') {
        $completed_modules_list[] = 'lightning';
    } else {
        $completed_modules_list[] = $disaster;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Disaster Ready India</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            overflow-x: hidden;
        }

        /* Dashboard Layout */
        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #1a472a 0%, #0a2f1a 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo {
            font-size: 1.8em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .logo span {
            color: #ffd700;
        }

        .tagline {
            font-size: 0.8em;
            opacity: 0.8;
        }

        .user-info {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ffd700, #ffb347);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 2.5em;
            font-weight: bold;
            color: #1a472a;
        }

        .user-name {
            font-size: 1.2em;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .user-role {
            font-size: 0.85em;
            opacity: 0.8;
        }

        .nav-menu {
            padding: 20px 0;
        }

        .nav-item {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            padding-left: 30px;
        }

        .nav-item.active {
            background: rgba(255,255,255,0.15);
            color: #ffd700;
            border-right: 3px solid #ffd700;
        }

        .nav-item i {
            width: 24px;
            font-size: 1.2em;
        }

        .nav-item span {
            font-size: 0.95em;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }

        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-title h1 {
            font-size: 1.5em;
            color: #333;
        }

        .page-title p {
            color: #666;
            font-size: 0.85em;
            margin-top: 5px;
        }

        .top-actions {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .location-box {
            font-size: 0.9em;
            color: #555;
            background: #f0f0f0;
            padding: 8px 15px;
            border-radius: 20px;
        }

        .notification-btn, .logout-btn {
            background: none;
            border: none;
            font-size: 1.2em;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .notification-btn {
            color: #666;
            position: relative;
        }

        .notification-btn:hover {
            background: #f0f0f0;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 15px;
            font-size: 0.9em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-info h3 {
            font-size: 0.85em;
            color: #666;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #1a472a;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: #e8f5e9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
        }

        /* Section Header */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .section-header h2 {
            font-size: 1.3em;
            color: #333;
        }

        .section-header a {
            color: #1a472a;
            text-decoration: none;
            font-size: 0.9em;
        }

        /* Modules Grid */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .module-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .module-header {
            background: linear-gradient(135deg, var(--module-color), var(--module-color-dark));
            padding: 20px;
            color: white;
            position: relative;
        }

        .module-icon {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .module-header h3 {
            font-size: 1.2em;
            margin-bottom: 5px;
        }

        .module-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7em;
        }

        .module-body {
            padding: 20px;
        }

        .module-description {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .module-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            font-size: 0.85em;
            color: #999;
            flex-wrap: wrap;
        }

        .module-meta i {
            margin-right: 5px;
        }

        .level-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7em;
            font-weight: 600;
        }

        .module-btn {
            display: block;
            text-align: center;
            padding: 10px;
            background: #f5f7fa;
            color: #1a472a;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .module-btn:hover {
            background: #1a472a;
            color: white;
        }

        .module-btn.completed {
            background: #e8f5e9;
            color: #27ae60;
        }

        .module-btn.completed:hover {
            background: #27ae60;
            color: white;
        }

        /* Alerts Section */
        .alerts-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .alert-item {
            padding: 15px;
            border-left: 3px solid #e74c3c;
            background: #fff5f5;
            margin-bottom: 10px;
            border-radius: 8px;
        }

        .alert-item i {
            color: #e74c3c;
            margin-right: 10px;
        }

        /* Emergency Contacts */
        .contacts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .contact-card {
            background: white;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .contact-card i {
            font-size: 2em;
            color: #1a472a;
            margin-bottom: 10px;
        }

        .contact-card h4 {
            font-size: 0.9em;
            margin-bottom: 5px;
        }

        .contact-card p {
            color: #666;
            font-size: 0.85em;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .top-bar {
                flex-direction: column;
                text-align: center;
            }
        }

        .menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 101;
            background: #1a472a;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <button class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">Disaster <span>Ready</span></div>
                <div class="tagline">India</div>
            </div>
            
            <div class="user-info">
                <div class="avatar">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                <div class="user-role">Student</div>
            </div>
            
            <div class="nav-menu">
                <a href="#" class="nav-item active" onclick="showSection('dashboard')">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="nav-item" onclick="showSection('modules')">
                    <i class="fas fa-book"></i>
                    <span>Learning Modules</span>
                </a>
                <a href="#" class="nav-item" onclick="showSection('drills')">
                    <i class="fas fa-dumbbell"></i>
                    <span>Virtual Drills</span>
                </a>
                <a href="#" class="nav-item" onclick="showSection('alerts')">
                    <i class="fas fa-bell"></i>
                    <span>Regional Alerts</span>
                </a>
                <a href="#" class="nav-item" onclick="showSection('contacts')">
                    <i class="fas fa-phone-alt"></i>
                    <span>Emergency Contacts</span>
                </a>
                <a href="#" class="nav-item" onclick="showSection('leaderboard')">
                    <i class="fas fa-trophy"></i>
                    <span>Leaderboard</span>
                </a>
                <a href="help.php" class="nav-item">
                    <i class="fas fa-question-circle"></i>
                    <span>Help Center</span>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h1 id="pageTitle">Dashboard</h1>
                    <p id="pageSubtitle">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</p>
                </div>
                <div class="top-actions">
                    <div class="location-box" id="locationBox">
                        📍 Detecting location...
                    </div>
                    <button class="notification-btn" onclick="showNotifications()">
                        <i class="fas fa-bell"></i>
                    </button>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            
            <!-- Dashboard Content -->
            <div id="dashboardSection">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Modules Completed</h3>
                            <div class="stat-number"><?php echo $completed_modules; ?>/<?php echo $total_modules; ?></div>
                        </div>
                        <div class="stat-icon">📚</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Average Score</h3>
                            <div class="stat-number"><?php echo $avg_score; ?>%</div>
                        </div>
                        <div class="stat-icon">📊</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Certificates Earned</h3>
                            <div class="stat-number"><?php echo $completed_modules; ?></div>
                        </div>
                        <div class="stat-icon">🏆</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Training Hours</h3>
                            <div class="stat-number"><?php echo $completed_modules * 0.25; ?>+</div>
                        </div>
                        <div class="stat-icon">⏱️</div>
                    </div>
                </div>
                
                <!-- Featured Learning Modules -->
                <div class="section-header">
                    <h2>📚 Featured Learning Modules</h2>
                    <a href="#" onclick="showSection('modules')">View All <?php echo $total_modules; ?> Modules →</a>
                </div>
                
                <div class="modules-grid">
                    <?php 
                    $count = 0;
                    foreach($modules as $key => $module): 
                        if($count++ >= 4) break;
                        $isCompleted = in_array($key, $completed_modules_list);
                    ?>
                        <div class="module-card">
                            <div class="module-header" style="--module-color: <?php echo $module['color']; ?>; --module-color-dark: <?php echo $module['color']; ?>cc;">
                                <div class="module-icon"><?php echo $module['icon']; ?></div>
                                <h3><?php echo $module['name']; ?></h3>
                                <div class="module-badge"><?php echo $module['duration']; ?></div>
                            </div>
                            <div class="module-body">
                                <p class="module-description"><?php echo $module['description']; ?></p>
                                <div class="module-meta">
                                    <span><i class="fas fa-clock"></i> <?php echo $module['duration']; ?></span>
                                    <span class="level-badge" style="background: <?php echo $module['level_color']; ?>20; color: <?php echo $module['level_color']; ?>;">
                                        <?php echo $module['level']; ?>
                                    </span>
                                </div>
                                <a href="<?php echo $module['file']; ?>" class="module-btn <?php echo $isCompleted ? 'completed' : ''; ?>" target="_blank">
                                    <?php echo $isCompleted ? '✓ Review Module' : 'Start Learning →'; ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Regional Alerts -->
                <div class="section-header">
                    <h2>⚠️ Regional Alerts</h2>
                    <a href="#" onclick="showSection('alerts')">View All →</a>
                </div>
                
                <div class="alerts-section">
                    <?php if ($alerts && $alerts->num_rows > 0): ?>
                        <?php while($alert = $alerts->fetch_assoc()): ?>
                            <div class="alert-item">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?php echo htmlspecialchars($alert['message']); ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert-item" style="background: #f0f0f0; border-left-color: #27ae60;">
                            <i class="fas fa-check-circle"></i>
                            No active alerts at this time. Stay safe!
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Learning Modules Full Section (All 7 Modules) -->
            <div id="modulesSection" style="display: none;">
                <div class="section-header">
                    <h2>📚 All Learning Modules</h2>
                    <p style="color: #666; font-size: 0.9em;">Complete all <?php echo $total_modules; ?> modules to become disaster ready!</p>
                </div>
                <div class="modules-grid">
                    <?php foreach($modules as $key => $module): 
                        $isCompleted = in_array($key, $completed_modules_list);
                    ?>
                        <div class="module-card">
                            <div class="module-header" style="--module-color: <?php echo $module['color']; ?>; --module-color-dark: <?php echo $module['color']; ?>cc;">
                                <div class="module-icon"><?php echo $module['icon']; ?></div>
                                <h3><?php echo $module['name']; ?></h3>
                                <div class="module-badge"><?php echo $module['duration']; ?></div>
                            </div>
                            <div class="module-body">
                                <p class="module-description"><?php echo $module['description']; ?></p>
                                <div class="module-meta">
                                    <span><i class="fas fa-clock"></i> <?php echo $module['duration']; ?></span>
                                    <span class="level-badge" style="background: <?php echo $module['level_color']; ?>20; color: <?php echo $module['level_color']; ?>;">
                                        <?php echo $module['level']; ?>
                                    </span>
                                </div>
                                <a href="<?php echo $module['file']; ?>" class="module-btn <?php echo $isCompleted ? 'completed' : ''; ?>" target="_blank">
                                    <?php echo $isCompleted ? '✓ Review Module' : 'Start Learning →'; ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Virtual Drills Section -->
            <div id="drillsSection" style="display: none;">
                <div class="section-header">
                    <h2>🎯 Virtual Drills</h2>
                    <p>Practice your emergency response skills</p>
                </div>
                <div class="modules-grid">
                    <div class="module-card">
                        <div class="module-header" style="--module-color: #667eea; --module-color-dark: #764ba2;">
                            <div class="module-icon">🌍</div>
                            <h3>Earthquake Drill</h3>
                        </div>
                        <div class="module-body">
                            <p class="module-description">Practice "Drop, Cover, and Hold On" technique</p>
                            <a href="#" class="module-btn" onclick="startDrill('earthquake')">Start Drill →</a>
                        </div>
                    </div>
                    <div class="module-card">
                        <div class="module-header" style="--module-color: #e74c3c; --module-color-dark: #c0392b;">
                            <div class="module-icon">🔥</div>
                            <h3>Fire Evacuation Drill</h3>
                        </div>
                        <div class="module-body">
                            <p class="module-description">Practice safe evacuation procedures</p>
                            <a href="#" class="module-btn" onclick="startDrill('fire')">Start Drill →</a>
                        </div>
                    </div>
                    <div class="module-card">
                        <div class="module-header" style="--module-color: #3498db; --module-color-dark: #2980b9;">
                            <div class="module-icon">🌊</div>
                            <h3>Flood Response Drill</h3>
                        </div>
                        <div class="module-body">
                            <p class="module-description">Practice flood evacuation and safety</p>
                            <a href="#" class="module-btn" onclick="startDrill('flood')">Start Drill →</a>
                        </div>
                    </div>
                    <div class="module-card">
                        <div class="module-header" style="--module-color: #9b59b6; --module-color-dark: #8e44ad;">
                            <div class="module-icon">🌀</div>
                            <h3>Cyclone Drill</h3>
                        </div>
                        <div class="module-body">
                            <p class="module-description">Practice cyclone safety protocols</p>
                            <a href="#" class="module-btn" onclick="startDrill('cyclone')">Start Drill →</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alerts Full Section -->
            <div id="alertsSection" style="display: none;">
                <div class="section-header">
                    <h2>⚠️ Regional Alerts</h2>
                </div>
                <div class="alerts-section">
                    <?php 
                    $all_alerts = $conn->query("SELECT * FROM alerts ORDER BY id DESC");
                    if ($all_alerts && $all_alerts->num_rows > 0):
                        while($alert = $all_alerts->fetch_assoc()):
                    ?>
                        <div class="alert-item">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo htmlspecialchars($alert['message']); ?>
                            <small style="display: block; margin-top: 5px; color: #999;">
                                <?php echo date('F j, Y H:i', strtotime($alert['created_at'] ?? 'now')); ?>
                            </small>
                        </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <div class="alert-item" style="background: #f0f0f0; border-left-color: #27ae60;">
                            <i class="fas fa-check-circle"></i>
                            No active alerts at this time.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Emergency Contacts Section -->
            <div id="contactsSection" style="display: none;">
                <div class="section-header">
                    <h2>📞 Emergency Contacts</h2>
                </div>
                <div class="contacts-grid">
                    <div class="contact-card">
                        <i class="fas fa-ambulance"></i>
                        <h4>Ambulance</h4>
                        <p>102</p>
                    </div>
                    <div class="contact-card">
                        <i class="fas fa-fire-extinguisher"></i>
                        <h4>Fire Brigade</h4>
                        <p>101</p>
                    </div>
                    <div class="contact-card">
                        <i class="fas fa-shield-alt"></i>
                        <h4>Police</h4>
                        <p>100</p>
                    </div>
                    <div class="contact-card">
                        <i class="fas fa-tree"></i>
                        <h4>Disaster Management</h4>
                        <p>1070</p>
                    </div>
                    <div class="contact-card">
                        <i class="fas fa-hand-holding-heart"></i>
                        <h4>NDRF Helpline</h4>
                        <p>011-23469528</p>
                    </div>
                    <div class="contact-card">
                        <i class="fas fa-hospital"></i>
                        <h4>Emergency Services</h4>
                        <p>112</p>
                    </div>
                </div>
            </div>
            
            <!-- Leaderboard Section -->
            <div id="leaderboardSection" style="display: none;">
                <div class="section-header">
                    <h2>🏆 Leaderboard</h2>
                    <p>Top performers in disaster preparedness</p>
                </div>
                <div class="modules-grid">
                    <?php
                    $leaderboard = $conn->query("
                        SELECT u.name, AVG(r.score/r.total)*100 as avg_score, COUNT(DISTINCT r.disaster) as completed
                        FROM users u
                        JOIN quiz_results r ON u.id = r.user_id
                        WHERE u.role = 'student'
                        GROUP BY u.id
                        ORDER BY avg_score DESC
                        LIMIT 10
                    ");
                    $rank = 1;
                    if ($leaderboard && $leaderboard->num_rows > 0):
                        while($user = $leaderboard->fetch_assoc()):
                    ?>
                        <div class="stat-card" style="display: flex; align-items: center; gap: 15px;">
                            <div style="font-size: 1.5em; font-weight: bold; color: #1a472a;">#<?php echo $rank++; ?></div>
                            <div>
                                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                                <p><?php echo round($user['avg_score'], 1); ?>% Average • <?php echo $user['completed']; ?>/<?php echo $total_modules; ?> Modules</p>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <div class="stat-card">
                            <p style="text-align: center;">No leaderboard data yet. Complete quizzes to appear here!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
        
        function showSection(section) {
            // Hide all sections
            document.getElementById('dashboardSection').style.display = 'none';
            document.getElementById('modulesSection').style.display = 'none';
            document.getElementById('drillsSection').style.display = 'none';
            document.getElementById('alertsSection').style.display = 'none';
            document.getElementById('contactsSection').style.display = 'none';
            document.getElementById('leaderboardSection').style.display = 'none';
            
            // Show selected section
            if (section === 'dashboard') {
                document.getElementById('dashboardSection').style.display = 'block';
                document.getElementById('pageTitle').innerHTML = 'Dashboard';
                document.getElementById('pageSubtitle').innerHTML = 'Welcome back, <?php echo htmlspecialchars($user_name); ?>!';
            } else if (section === 'modules') {
                document.getElementById('modulesSection').style.display = 'block';
                document.getElementById('pageTitle').innerHTML = 'Learning Modules';
                document.getElementById('pageSubtitle').innerHTML = 'Complete all <?php echo $total_modules; ?> modules to become disaster ready';
            } else if (section === 'drills') {
                document.getElementById('drillsSection').style.display = 'block';
                document.getElementById('pageTitle').innerHTML = 'Virtual Drills';
                document.getElementById('pageSubtitle').innerHTML = 'Practice your emergency response skills';
            } else if (section === 'alerts') {
                document.getElementById('alertsSection').style.display = 'block';
                document.getElementById('pageTitle').innerHTML = 'Regional Alerts';
                document.getElementById('pageSubtitle').innerHTML = 'Stay informed about local emergencies';
            } else if (section === 'contacts') {
                document.getElementById('contactsSection').style.display = 'block';
                document.getElementById('pageTitle').innerHTML = 'Emergency Contacts';
                document.getElementById('pageSubtitle').innerHTML = 'Important numbers for emergencies';
            } else if (section === 'leaderboard') {
                document.getElementById('leaderboardSection').style.display = 'block';
                document.getElementById('pageTitle').innerHTML = 'Leaderboard';
                document.getElementById('pageSubtitle').innerHTML = 'Top performers in disaster preparedness';
            }
            
            // Update active nav item
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
            
            // Close sidebar on mobile
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.remove('active');
            }
        }
        
        function startDrill(type) {
            alert('🎯 Virtual Drill: ' + type.toUpperCase() + ' Safety\n\n' +
                  'Follow these steps:\n' +
                  '1. Stay calm and assess the situation\n' +
                  '2. Follow safety protocols you learned\n' +
                  '3. Practice evacuation if needed\n' +
                  '4. Help others if possible\n\n' +
                  'Remember: Practice makes perfect!');
        }
        
        function showNotifications() {
            alert('📢 Notifications\n\nNo new notifications at this time.\n\n' +
                  'Check back later for important updates and alerts!');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
                if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });

        // Get user's location
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                document.getElementById("locationBox").innerHTML = "📍 Location not supported";
            }
        }

        function showPosition(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
            .then(res => res.json())
            .then(data => {
                const city = data.address.city || data.address.town || data.address.village || "Unknown";
                const country = data.address.country || "India";

                document.getElementById("locationBox").innerHTML = `📍 ${city}, ${country}`;
            })
            .catch(() => {
                document.getElementById("locationBox").innerHTML = "📍 Location unavailable";
            });
        }

        function showError() {
            document.getElementById("locationBox").innerHTML = "📍 Location access denied";
        }

        // Call location function
        getLocation();
    </script>
</body>
</html>