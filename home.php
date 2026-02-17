<?php
session_start();
include("config.php");

// Check if user is logged in
if(!isset($_SESSION['login_user']))
{
    header("Location:login.php");
    exit();
}

// Get user info
$user = $_SESSION['login_user'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background particles */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle, rgba(255,165,0,0.1) 2px, transparent 2px),
                radial-gradient(circle, rgba(255,215,0,0.1) 2px, transparent 2px);
            background-size: 60px 60px;
            background-position: 0 0, 30px 30px;
            animation: particleFloat 25s linear infinite;
            z-index: 0;
        }

        @keyframes particleFloat {
            from {
                transform: translateY(0);
            }
            to {
                transform: translateY(-60px);
            }
        }

        /* Floating orbs */
        body::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,140,0,0.05) 1px, transparent 1px);
            background-size: 100px 100px;
            animation: orbFade 30s linear infinite;
            z-index: 0;
        }

        @keyframes orbFade {
            0%, 100% {
                opacity: 0.5;
            }
            50% {
                opacity: 1;
            }
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px 50px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 10;
            backdrop-filter: blur(10px);
            animation: headerSlide 0.8s ease-out;
        }

        @keyframes headerSlide {
            from {
                transform: translateY(-100%);
            }
            to {
                transform: translateY(0);
            }
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 2.2em;
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info span {
            color: #555;
            font-size: 1.1em;
            font-weight: 500;
        }

        .logout-btn {
            background: linear-gradient(135deg, #ff6b6b 0%, #ffa726 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            color: white;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(255, 107, 107, 0.4);
            text-decoration: none;
            display: inline-block;
        }

        .logout-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.6);
        }

        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 50px auto;
            padding: 0 30px;
            position: relative;
            z-index: 1;
        }

        .welcome-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 25px;
            margin-bottom: 40px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeInUp 1s ease-out;
            backdrop-filter: blur(10px);
            position: relative;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            background: linear-gradient(45deg, #ff7e5f, #feb47b, #ff7e5f);
            border-radius: 25px;
            z-index: -1;
            opacity: 0;
            filter: blur(15px);
            animation: borderPulse 3s ease-in-out infinite;
        }

        @keyframes borderPulse {
            0%, 100% {
                opacity: 0.6;
            }
            50% {
                opacity: 0.3;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-section h2 {
            font-size: 2em;
            color: #333;
            margin-bottom: 10px;
        }

        .welcome-section p {
            color: #666;
            font-size: 1.1em;
        }

        /* Menu Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 35px;
            animation: fadeIn 1.2s ease-out 0.3s backwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Menu Cards */
        .menu-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 45px 35px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            text-decoration: none;
            display: block;
            animation: cardSlideIn 0.8s ease-out backwards;
        }

        .menu-card:nth-child(1) { animation-delay: 0.4s; }
        .menu-card:nth-child(2) { animation-delay: 0.6s; }
        .menu-card:nth-child(3) { animation-delay: 0.8s; }

        @keyframes cardSlideIn {
            from {
                opacity: 0;
                transform: translateY(80px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .menu-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 126, 95, 0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.5s ease;
        }

        .menu-card:hover::before {
            left: 100%;
        }

        .menu-card:hover {
            transform: translateY(-15px) scale(1.03);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        }

        .menu-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 25px;
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5em;
            color: white;
            box-shadow: 0 10px 30px rgba(255, 126, 95, 0.4);
            transition: all 0.4s ease;
        }

        .menu-card:hover .menu-icon {
            transform: scale(1.15) rotate(10deg);
            box-shadow: 0 15px 40px rgba(255, 126, 95, 0.6);
        }

        .menu-card h3 {
            font-size: 1.8em;
            color: #333;
            margin-bottom: 15px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .menu-card p {
            color: #666;
            font-size: 1.05em;
            line-height: 1.6;
        }

        /* Specific icon styles */
        .menu-card:nth-child(1) .menu-icon {
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
        }

        .menu-card:nth-child(2) .menu-icon {
            background: linear-gradient(135deg, #ffa726 0%, #ffcc80 100%);
        }

        .menu-card:nth-child(3) .menu-icon {
            background: linear-gradient(135deg, #ff8a65 0%, #ffab91 100%);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                padding: 20px 30px;
            }

            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .header h1 {
                font-size: 1.8em;
            }

            .container {
                margin: 30px auto;
                padding: 0 20px;
            }

            .menu-grid {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .welcome-section {
                padding: 30px 25px;
            }

            .menu-card {
                padding: 35px 25px;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.5em;
            }

            .welcome-section h2 {
                font-size: 1.5em;
            }

            .menu-card h3 {
                font-size: 1.5em;
            }

            .menu-icon {
                width: 75px;
                height: 75px;
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <h1>Dashboard</h1>
            <div class="user-info">
                <span>👤 <?php echo htmlspecialchars($user); ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h2>Welcome Back!</h2>
            <p>Select a module to get started</p>
        </div>

        <!-- Menu Grid -->
        <div class="menu-grid">
            <!-- Parties Card -->
            <a href="partys.php" class="menu-card">
                <div class="menu-icon">🤝</div>
                <h3>Parties</h3>
                <p>Manage party details, bookings, and event schedules</p>
            </a>

            <!-- Employees Card -->
            <a href="employe.php" class="menu-card">
                <div class="menu-icon">👥</div>
                <h3>Employees</h3>
                <p>View and manage employee information and records</p>
            </a>

            <!-- Materials Card -->
            <a href="materials.php" class="menu-card">
                <div class="menu-icon">📦</div>
                <h3>Materials</h3>
                <p>Track inventory, supplies, and material resources</p>
            </a>
        </div>
    </div>
</body>
</html>