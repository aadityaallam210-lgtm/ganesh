<?php
session_start();
include("config.php");

// Check if user is logged in
if(!isset($_SESSION['login_user']))
{
    header("Location: index.php");
    exit();
}

$user = $_SESSION['login_user'];
$success_message = "";
$error_message = "";
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Handle Add Employee
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee']))
{
    // Validation
    $errors = array();
    
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $advance = trim($_POST['advance']);
    
    // Validate Name
    if(empty($name))
    {
        $errors[] = "Name is required";
    }
    elseif(strlen($name) < 2)
    {
        $errors[] = "Name must be at least 2 characters";
    }
    
    // Validate Phone
    if(empty($phone))
    {
        $errors[] = "Phone number is required";
    }
    elseif(!preg_match('/^[0-9]{10}$/', $phone))
    {
        $errors[] = "Phone number must be 10 digits";
    }
    
    // Validate Advance
    if(empty($advance))
    {
        $errors[] = "Advance amount is required";
    }
    elseif(!is_numeric($advance) || $advance < 0)
    {
        $errors[] = "Advance must be a valid amount";
    }
    
    if(empty($errors))
    {
        $name = mysql_real_escape_string($name);
        $phone = mysql_real_escape_string($phone);
        $advance = mysql_real_escape_string($advance);
        
        $query = "INSERT INTO employ (name, phone, advance) 
                  VALUES ('$name', '$phone', '$advance')";
        
        if(mysql_query($query))
        {
            $success_message = "Employee added successfully!";
            $action = 'list';
        }
        else
        {
            $error_message = "Error adding employee: " . mysql_error();
        }
    }
    else
    {
        $error_message = implode("<br>", $errors);
    }
}

// Handle Update Employee
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_employee']))
{
    // Validation
    $errors = array();
    
    $id = trim($_POST['id']);
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $advance = trim($_POST['advance']);
    
    // Validate Name
    if(empty($name))
    {
        $errors[] = "Name is required";
    }
    elseif(strlen($name) < 2)
    {
        $errors[] = "Name must be at least 2 characters";
    }
    
    // Validate Phone
    if(empty($phone))
    {
        $errors[] = "Phone number is required";
    }
    elseif(!preg_match('/^[0-9]{10}$/', $phone))
    {
        $errors[] = "Phone number must be 10 digits";
    }
    
    // Validate Advance
    if(empty($advance))
    {
        $errors[] = "Advance amount is required";
    }
    elseif(!is_numeric($advance) || $advance < 0)
    {
        $errors[] = "Advance must be a valid amount";
    }
    
    if(empty($errors))
    {
        $id = mysql_real_escape_string($id);
        $name = mysql_real_escape_string($name);
        $phone = mysql_real_escape_string($phone);
        $advance = mysql_real_escape_string($advance);
        
        $query = "UPDATE employ SET 
                  name='$name', 
                  phone='$phone', 
                  advance='$advance' 
                  WHERE id='$id'";
        
        if(mysql_query($query))
        {
            $success_message = "Employee updated successfully!";
            $action = 'list';
        }
        else
        {
            $error_message = "Error updating employee: " . mysql_error();
        }
    }
    else
    {
        $error_message = implode("<br>", $errors);
    }
}

// Handle Add Round
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_round']))
{
    $errors = array();
    
    $employ_id = trim($_POST['employ_id']);
    $name = trim($_POST['name']);
    $date_r = trim($_POST['date_r']);
    $rounds = trim($_POST['rounds']);
    
    // Validate Date
    if(empty($date_r))
    {
        $errors[] = "Date is required";
    }
    
    // Validate Rounds
    if(empty($rounds))
    {
        $errors[] = "Rounds is required";
    }
    elseif(!is_numeric($rounds) || $rounds < 0)
    {
        $errors[] = "Rounds must be a valid number";
    }
    
    if(empty($errors))
    {
        
        $name = mysql_real_escape_string($name);
        $date_r = mysql_real_escape_string($date_r);
        $rounds = mysql_real_escape_string($rounds);
        
        $query = "INSERT INTO rounds (name, date_r, rounds) 
                  VALUES ('$name', '$date_r', '$rounds')";
        
        if(mysql_query($query))
        {
            $success_message = "Round added successfully!";
            $action = 'list';
        }
        else
        {
            $error_message = "Error adding round: " . mysql_error();
        }
    }
    else
    {
        $error_message = implode("<br>", $errors);
    }
}

// Handle Delete Employee
if(isset($_GET['delete']))
{
    $id = mysql_real_escape_string($_GET['delete']);
    $query = "DELETE FROM employ WHERE id='$id'";
    
    if(mysql_query($query))
    {
        $success_message = "Employee deleted successfully!";
    }
    else
    {
        $error_message = "Error deleting employee: " . mysql_error();
    }
}

// Handle Delete Round
if(isset($_GET['delete_round']))
{
    $id = mysql_real_escape_string($_GET['delete_round']);
    $employ_name = mysql_real_escape_string($_GET['employ_name']);
    $query = "DELETE FROM rounds WHERE id='$id'";
    
    if(mysql_query($query))
    {
        $success_message = "Round deleted successfully!";
        header("Location: ?action=view_rounds&name=" . urlencode($employ_name));
        exit();
    }
    else
    {
        $error_message = "Error deleting round: " . mysql_error();
    }
}

// Handle Search by Name or Phone
$search_query = "";
$where_clause = "WHERE 1=1";

if(isset($_POST['search']) || isset($_GET['search_text']))
{
    $search_text = isset($_POST['search_text']) ? mysql_real_escape_string($_POST['search_text']) : mysql_real_escape_string($_GET['search_text']);
    
    if(!empty($search_text))
    {
        $where_clause .= " AND (name LIKE '%$search_text%' OR phone LIKE '%$search_text%')";
        $search_query = $search_text;
    }
}

// Fetch employees based on search
$query = "SELECT * FROM employ $where_clause ORDER BY id DESC";
$result = mysql_query($query);
if(!$result) {
    $result = mysql_query("SELECT * FROM employ ORDER BY id DESC");
}

// Get employee for edit
$edit_employee = null;
if($action == 'edit' && isset($_GET['id']))
{
    $id = mysql_real_escape_string($_GET['id']);
    $edit_query = "SELECT * FROM employ WHERE id='$id'";
    $edit_result = mysql_query($edit_query);
    if($edit_result) {
        $edit_employee = mysql_fetch_array($edit_result);
    }
}

// Get employee for rounds
$rounds_employee = null;
if($action == 'rounds' && isset($_GET['id']))
{
    $id = mysql_real_escape_string($_GET['id']);
    $rounds_query = "SELECT * FROM employ WHERE id='$id'";
    $rounds_result = mysql_query($rounds_query);
    if($rounds_result) {
        $rounds_employee = mysql_fetch_array($rounds_result);
    }
}

// Get employee and their rounds for viewing by name
$view_employee = null;
$view_rounds_result = null;
if($action == 'view_rounds' && isset($_GET['name']))
{
    $name = mysql_real_escape_string($_GET['name']);
    $view_query = "SELECT * FROM employ WHERE name='$name'";
    $view_result = mysql_query($view_query);
    if($view_result) {
        $view_employee = mysql_fetch_array($view_result);
    }
    
    // Fetch all rounds for this employee by name
    $rounds_query = "SELECT * FROM rounds WHERE name='$name' ORDER BY date_r DESC, id DESC";
    $view_rounds_result = mysql_query($rounds_query);
    if(!$view_rounds_result) {
        $view_rounds_result = false;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
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
            from { transform: translateY(0); }
            to { transform: translateY(-60px); }
        }

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
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
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
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header h1 {
            font-size: 2.2em;
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header h1::before {
            content: '👥';
            font-size: 1.2em;
        }

        .nav-buttons {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            color: white;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(255, 126, 95, 0.4);
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 30px rgba(255, 126, 95, 0.6);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #ffa726 0%, #ffcc80 100%);
            box-shadow: 0 5px 20px rgba(255, 167, 38, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ffa726 100%);
            box-shadow: 0 5px 20px rgba(255, 107, 107, 0.4);
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 30px 30px;
            position: relative;
            z-index: 1;
        }

        /* Messages */
        .message {
            padding: 15px 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }

        /* Search Section */
        .search-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
        }

        .search-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: end;
        }

        .search-group {
            flex: 1;
            min-width: 250px;
        }

        .search-group label {
            display: block;
            color: #555;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 1.05em;
        }

        .search-group input {
            width: 100%;
            padding: 14px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            font-size: 1.05em;
            transition: all 0.3s ease;
            background: white;
        }

        .search-group input:focus {
            outline: none;
            border-color: #ff7e5f;
            box-shadow: 0 0 0 3px rgba(255, 126, 95, 0.1);
        }

        .search-btn {
            padding: 14px 40px;
            border: none;
            border-radius: 50px;
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 126, 95, 0.4);
            font-size: 1.05em;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 126, 95, 0.6);
        }

        .clear-btn {
            background: linear-gradient(135deg, #95a5a6 0%, #bdc3c7 100%);
            box-shadow: 0 5px 15px rgba(149, 165, 166, 0.4);
            text-decoration: none;
            display: inline-block;
        }

        /* Form Container */
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 40px 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            margin-bottom: 30px;
            position: relative;
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            background: linear-gradient(45deg, #ff7e5f, #feb47b, #ff7e5f);
            border-radius: 25px;
            z-index: -1;
            opacity: 0.6;
            filter: blur(15px);
            animation: borderPulse 3s ease-in-out infinite;
        }

        @keyframes borderPulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 0.3; }
        }

        .form-container h2 {
            font-size: 2em;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .employee-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        .employee-info p {
            font-size: 1.15em;
            color: #1976d2;
            margin: 8px 0;
        }

        .employee-info strong {
            color: #0d47a1;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            color: #555;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 1em;
        }

        .form-group label .required {
            color: #ff6b6b;
            margin-left: 3px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            font-size: 1em;
            transition: all 0.3s ease;
            font-family: inherit;
            background: white;
        }

        .form-group textarea {
            border-radius: 20px;
            resize: vertical;
            min-height: 80px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ff7e5f;
            box-shadow: 0 0 0 4px rgba(255, 126, 95, 0.1),
                        0 5px 20px rgba(255, 126, 95, 0.2);
            transform: translateY(-2px);
        }

        .form-group small {
            color: #666;
            font-size: 0.85em;
            margin-top: 5px;
            display: block;
        }

        .form-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .form-buttons button,
        .form-buttons a {
            padding: 14px 40px;
            font-size: 1.05em;
        }

        /* Table Container */
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            overflow-x: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-header h3 {
            font-size: 1.8em;
            color: #333;
            font-weight: 700;
        }

        .stats-badge {
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(255, 126, 95, 0.4);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        thead {
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
        }

        thead th {
            padding: 16px 12px;
            color: white;
            font-weight: 600;
            text-align: left;
            font-size: 0.95em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: rgba(255, 126, 95, 0.05);
            transform: scale(1.01);
        }

        tbody td {
            padding: 16px 12px;
            color: #555;
            font-size: 0.95em;
        }

        tbody td strong {
            color: #333;
        }

        .amount-cell {
            font-weight: 600;
            color: #ff7e5f;
        }

        .rounds-badge {
            background: #e3f2fd;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            color: #1976d2;
            display: inline-block;
        }

        .action-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 8px 18px;
            font-size: 0.85em;
        }

        .btn-edit {
            background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%);
            box-shadow: 0 3px 10px rgba(76, 175, 80, 0.3);
        }

        .btn-edit:hover {
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.5);
        }

        .btn-rounds {
            background: linear-gradient(135deg, #2196F3 0%, #64B5F6 100%);
            box-shadow: 0 3px 10px rgba(33, 150, 243, 0.3);
        }

        .btn-rounds:hover {
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.5);
        }

        .btn-view {
            background: linear-gradient(135deg, #9C27B0 0%, #BA68C8 100%);
            box-shadow: 0 3px 10px rgba(156, 39, 176, 0.3);
        }

        .btn-view:hover {
            box-shadow: 0 5px 15px rgba(156, 39, 176, 0.5);
        }

        .btn-delete {
            background: linear-gradient(135deg, #f44336 0%, #e91e63 100%);
            box-shadow: 0 3px 10px rgba(244, 67, 54, 0.3);
        }

        .btn-delete:hover {
            box-shadow: 0 5px 15px rgba(244, 67, 54, 0.5);
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .no-data-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }

        /* Summary Cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 25px;
            color: white;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .summary-card h4 {
            font-size: 0.9em;
            opacity: 0.9;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .summary-card .value {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                padding: 20px 25px;
            }

            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .header h1 {
                font-size: 1.8em;
            }

            .nav-buttons {
                width: 100%;
                justify-content: flex-start;
            }

            .form-container {
                padding: 30px 25px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .search-form {
                flex-direction: column;
            }

            .search-group {
                width: 100%;
            }

            .form-buttons {
                flex-direction: column;
            }

            .form-buttons button,
            .form-buttons a {
                width: 100%;
            }

            .table-container {
                padding: 20px;
            }

            .action-btns {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <h1>Employee Management</h1>
            <div class="nav-buttons">
                <?php if($action == 'list'): ?>
                    <a href="?action=add" class="btn">➕ Add New Employee</a>
                <?php else: ?>
                    <a href="?" class="btn btn-secondary">← Back to List</a>
                <?php endif; ?>
                <a href="home.php" class="btn btn-secondary">🏠 Dashboard</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <!-- Messages -->
        <?php if($success_message): ?>
            <div class="message success">✓ <?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if($error_message): ?>
            <div class="message error">✗ <?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if($action == 'add' || $action == 'edit'): ?>
            <!-- Add/Edit Form -->
            <div class="form-container">
                <h2><?php echo $action == 'edit' ? '✏️ Edit Employee Details' : '➕ Add New Employee'; ?></h2>
                <form action="" method="POST">
                    <?php if($action == 'edit'): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_employee['id']; ?>">
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Employee Name <span class="required">*</span></label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo $edit_employee ? htmlspecialchars($edit_employee['name']) : ''; ?>" 
                                   placeholder="Enter employee name" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number <span class="required">*</span></label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo $edit_employee ? htmlspecialchars($edit_employee['phone']) : ''; ?>" 
                                   placeholder="Enter 10-digit phone number" 
                                   pattern="[0-9]{10}" 
                                   maxlength="10" required>
                            <small>Must be 10 digits</small>
                        </div>

                        <div class="form-group">
                            <label for="advance">Advance Amount (₹) <span class="required">*</span></label>
                            <input type="number" id="advance" name="advance" 
                                   value="<?php echo $edit_employee ? $edit_employee['advance'] : ''; ?>" 
                                   placeholder="Enter advance amount" 
                                   min="0" 
                                   step="0.01" required>
                        </div>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" name="<?php echo $action == 'edit' ? 'update_employee' : 'add_employee'; ?>" class="btn">
                            <?php echo $action == 'edit' ? '💾 Update Employee' : '➕ Add Employee'; ?>
                        </button>
                        <button type="reset" class="btn btn-danger">🔄 Reset</button>
                        <a href="?" class="btn btn-secondary">✖ Cancel</a>
                    </div>
                </form>
            </div>

        <?php elseif($action == 'rounds'): ?>
            <!-- Add Round Form -->
            <div class="form-container">
                <h2>🔢 Add Employee Round</h2>
                <?php if($rounds_employee): ?>
                
                <div class="employee-info">
                    <p><strong>Employee Name:</strong> <?php echo htmlspecialchars($rounds_employee['name']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($rounds_employee['phone']); ?></p>
                </div>

                <form action="" method="POST">
                    <input type="hidden" name="employ_id" value="<?php echo $rounds_employee['id']; ?>">
                    <input type="hidden" name="name" value="<?php echo htmlspecialchars($rounds_employee['name']); ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="date_r">Date <span class="required">*</span></label>
                            <input type="date" id="date_r" name="date_r" 
                                   value="<?php echo date('Y-m-d'); ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="rounds">Rounds <span class="required">*</span></label>
                            <input type="number" id="rounds" name="rounds" 
                                   placeholder="Enter number of rounds" 
                                   min="0" 
                                   step="1" required>
                        </div>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" name="add_round" class="btn">
                            ➕ Add Round
                        </button>
                        <a href="?" class="btn btn-secondary">✖ Cancel</a>
                    </div>
                </form>
                <?php else: ?>
                <div class="no-data">
                    <p>Employee not found.</p>
                </div>
                <?php endif; ?>
            </div>

        <?php elseif($action == 'view_rounds'): ?>
            <!-- View Rounds -->
            <?php if($view_employee): ?>
                <div class="employee-info" style="margin-bottom: 30px;">
                    <p><strong>Employee Name:</strong> <?php echo htmlspecialchars($view_employee['name']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($view_employee['phone']); ?></p>
                    <p><strong>Advance Amount:</strong> ₹ <?php echo number_format($view_employee['advance'], 2); ?></p>
                </div>

                <?php
                // Calculate total rounds
                $total_rounds = 0;
                if($view_rounds_result && mysql_num_rows($view_rounds_result) > 0) {
                    mysql_data_seek($view_rounds_result, 0);
                    while($round_row = mysql_fetch_array($view_rounds_result)) {
                        $total_rounds += $round_row['rounds'];
                    }
                    mysql_data_seek($view_rounds_result, 0);
                }
                ?>

                <div class="summary-cards">
                    <div class="summary-card">
                        <h4>Total Rounds</h4>
                        <div class="value"><?php echo number_format($total_rounds); ?></div>
                    </div>
                    <div class="summary-card">
                        <h4>Total Entries</h4>
                        <div class="value"><?php echo $view_rounds_result ? mysql_num_rows($view_rounds_result) : 0; ?></div>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h3>📋 Round History</h3>
                        <a href="?action=rounds&id=<?php echo $view_employee['id']; ?>" class="btn btn-rounds">➕ Add New Round</a>
                    </div>

                    <?php if($view_rounds_result && mysql_num_rows($view_rounds_result) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Rounds</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($round_row = mysql_fetch_array($view_rounds_result)): ?>
                                <tr>
                                    <td><?php echo $round_row['id']; ?></td>
                                    <td><strong><?php echo date('d M Y', strtotime($round_row['date_r'])); ?></strong></td>
                                    <td>
                                        <span class="rounds-badge"><?php echo number_format($round_row['rounds']); ?> Rounds</span>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="?delete_round=<?php echo $round_row['id']; ?>&employ_name=<?php echo urlencode($view_employee['name']); ?>" 
                                               class="btn btn-delete btn-sm" 
                                               onclick="return confirm('Are you sure you want to delete this round entry?')">🗑️ Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-data">
                            <div class="no-data-icon">📭</div>
                            <p>No rounds found for this employee. <a href="?action=rounds&id=<?php echo $view_employee['id']; ?>" style="color: #ff7e5f; font-weight: 600;">Add first round</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="form-container">
                    <div class="no-data">
                        <p>Employee not found.</p>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Search Section -->
            <div class="search-section">
                <form method="POST" action="" class="search-form">
                    <div class="search-group">
                        <label for="search_text">🔍 Search by Name or Phone</label>
                        <input type="text" id="search_text" name="search_text" 
                               value="<?php echo htmlspecialchars($search_query); ?>" 
                               placeholder="Enter employee name or phone number...">
                    </div>
                    <button type="submit" name="search" class="search-btn">Search</button>
                    <a href="?" class="search-btn clear-btn">Clear</a>
                </form>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <div class="table-header">
                    <h3>📋 All Employees</h3>
                    <div class="stats-badge">Total Records: <?php echo $result ? mysql_num_rows($result) : 0; ?></div>
                </div>

                <?php if($result && mysql_num_rows($result) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Advance Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysql_fetch_array($result)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td class="amount-cell">₹ <?php echo number_format($row['advance'], 2); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-edit btn-sm">✏️ Edit</a>
                                        <a href="?action=rounds&id=<?php echo $row['id']; ?>" class="btn btn-rounds btn-sm">🔢 Add Round</a>
                                        <a href="?action=view_rounds&name=<?php echo urlencode($row['name']); ?>" class="btn btn-view btn-sm">👁️ View Rounds</a>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-delete btn-sm" 
                                           onclick="return confirm('Are you sure you want to delete this employee record?')">🗑️ Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <div class="no-data-icon">📭</div>
                        <p>No employees found. <a href="?action=add" style="color: #ff7e5f; font-weight: 600;">Add your first employee</a></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>