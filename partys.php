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

// Handle Add Party
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_party']))
{
    // Validation
    $errors = array();
    
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $amount = trim($_POST['amount']);
    $advance = trim($_POST['advance']);
    $date = trim($_POST['date']);
    $number = trim($_POST['number']);
    $size = trim($_POST['size']);
    
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
    
    // Validate Total Amount
    if(empty($amount))
    {
        $errors[] = "Total amount is required";
    }
    elseif(!is_numeric($amount) || $amount < 0)
    {
        $errors[] = "Total amount must be a valid positive number";
    }
    
    // Validate Advance
    if(empty($advance))
    {
        $errors[] = "Advance amount is required";
    }
    elseif(!is_numeric($advance) || $advance < 0)
    {
        $errors[] = "Advance amount must be a valid positive number";
    }
    elseif($advance > $amount)
    {
        $errors[] = "Advance amount cannot be greater than total amount";
    }
    
    // Validate Date
    if(empty($date))
    {
        $errors[] = "Party date is required";
    }
    
    if(empty($errors))
    {
        $name = mysql_real_escape_string($name);
        $phone = mysql_real_escape_string($phone);
        $amount = mysql_real_escape_string($amount);
        $advance = mysql_real_escape_string($advance);
        $date = mysql_real_escape_string($date);
        $number = mysql_real_escape_string($number);
        $size = mysql_real_escape_string($size);
        
        $query = "INSERT INTO partys (name, phone, totalamount, advance, date, `order`, size) 
                  VALUES ('$name', '$phone', '$amount', '$advance', '$date', '$number', '$size')";
        
        if(mysql_query($query))
        {
            $success_message = "Party added successfully!";
            $action = 'list';
        }
        else
        {
            $error_message = "Error adding party: " . mysql_error();
        }
    }
    else
    {
        $error_message = implode("<br>", $errors);
    }
}

// Handle Update Party
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_party']))
{
    // Validation
    $errors = array();
    
    $id = trim($_POST['id']);
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $amount = trim($_POST['amount']);
    $advance = trim($_POST['advance']);
    $date = trim($_POST['date']);
    $number = trim($_POST['number']);
    $size = trim($_POST['size']);
    
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
    
    // Validate Total Amount
    if(empty($amount))
    {
        $errors[] = "Total amount is required";
    }
    elseif(!is_numeric($amount) || $amount < 0)
    {
        $errors[] = "Total amount must be a valid positive number";
    }
    
    // Validate Advance
    if(empty($advance))
    {
        $errors[] = "Advance amount is required";
    }
    elseif(!is_numeric($advance) || $advance < 0)
    {
        $errors[] = "Advance amount must be a valid positive number";
    }
    elseif($advance > $amount)
    {
        $errors[] = "Advance amount cannot be greater than total amount";
    }
    
    // Validate Date
    if(empty($date))
    {
        $errors[] = "Party date is required";
    }
    
    if(empty($errors))
    {
        $id = mysql_real_escape_string($id);
        $name = mysql_real_escape_string($name);
        $phone = mysql_real_escape_string($phone);
        $amount = mysql_real_escape_string($amount);
        $advance = mysql_real_escape_string($advance);
        $date = mysql_real_escape_string($date);
        $number = mysql_real_escape_string($number);
        $size = mysql_real_escape_string($size);
        
        $query = "UPDATE partys SET 
                  name='$name', 
                  phone='$phone', 
                  totalamount='$amount', 
                  advance='$advance', 
                  date='$date', 
                  `order`='$number', 
                  size='$size' 
                  WHERE id='$id'";
        
        if(mysql_query($query))
        {
            $success_message = "Party updated successfully!";
            $action = 'list';
        }
        else
        {
            $error_message = "Error updating party: " . mysql_error();
        }
    }
    else
    {
        $error_message = implode("<br>", $errors);
    }
}

// Handle Delete Party
if(isset($_GET['delete']))
{
    $id = mysql_real_escape_string($_GET['delete']);
    $query = "DELETE FROM partys WHERE id='$id'";
    
    if(mysql_query($query))
    {
        $success_message = "Party deleted successfully!";
    }
    else
    {
        $error_message = "Error deleting party: " . mysql_error();
    }
}

// Handle Search by Contact Number
$search_query = "";
$where_clause = "WHERE 1=1";

if(isset($_POST['search']) || isset($_GET['search_phone']))
{
    $search_phone = isset($_POST['search_phone']) ? mysql_real_escape_string($_POST['search_phone']) : mysql_real_escape_string($_GET['search_phone']);
    
    if(!empty($search_phone))
    {
        $where_clause .= " AND phone LIKE '%$search_phone%'";
        $search_query = $search_phone;
    }
}

// Fetch parties based on search
$query = "SELECT * FROM partys $where_clause ORDER BY id DESC";
$result = mysql_query($query);
if(!$result) {
    $result = mysql_query("SELECT * FROM partys ORDER BY id DESC");
}

// Get party for edit
$edit_party = null;
if($action == 'edit' && isset($_GET['id']))
{
    $id = mysql_real_escape_string($_GET['id']);
    $edit_query = "SELECT * FROM partys WHERE id='$id'";
    $edit_result = mysql_query($edit_query);
    if($edit_result) {
        $edit_party = mysql_fetch_array($edit_result);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parties Management</title>
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
            content: '🤝';
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

        .form-group small {
            color: #666;
            font-size: 0.85em;
            margin-top: 5px;
            display: block;
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
            min-width: 1000px;
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

        .pending-amount {
            background: #fff3cd;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            color: #856404;
            display: inline-block;
        }

        .action-btns {
            display: flex;
            gap: 8px;
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
            <h1>Parties Management</h1>
            <div class="nav-buttons">
                <?php if($action == 'list'): ?>
                    <a href="?action=add" class="btn">➕ Add New Party</a>
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
                <h2><?php echo $action == 'edit' ? '✏️ Edit Party Details' : '➕ Add New Party'; ?></h2>
                <form action="" method="POST">
                    <?php if($action == 'edit'): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_party['id']; ?>">
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Name <span class="required">*</span></label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo $edit_party ? htmlspecialchars($edit_party['name']) : ''; ?>" 
                                   placeholder="Enter customer name" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number <span class="required">*</span></label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo $edit_party ? htmlspecialchars($edit_party['phone']) : ''; ?>" 
                                   placeholder="Enter contact number" 
                                   pattern="[0-9]{10}"
                                   maxlength="10" required>
                            <small>Must be 10 digits</small>
                        </div>

                        <div class="form-group">
                            <label for="amount">Total Amount (₹) <span class="required">*</span></label>
                            <input type="number" id="amount" name="amount" 
                                   value="<?php echo $edit_party ? $edit_party['totalamount'] : ''; ?>" 
                                   placeholder="Enter total amount" 
                                   min="0"
                                   step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="advance">Advance Amount (₹) <span class="required">*</span></label>
                            <input type="number" id="advance" name="advance" 
                                   value="<?php echo $edit_party ? $edit_party['advance'] : ''; ?>" 
                                   placeholder="Enter advance paid" 
                                   min="0"
                                   step="0.01" required>
                            <small>Cannot exceed total amount</small>
                        </div>

                        <div class="form-group">
                            <label for="date">Party Date <span class="required">*</span></label>
                            <input type="date" id="date" name="date" 
                                   value="<?php echo $edit_party ? $edit_party['date'] : date('Y-m-d'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="number">Order Number</label>
                            <input type="text" id="number" name="number" 
                                   value="<?php echo $edit_party ? htmlspecialchars($edit_party['order']) : ''; ?>" 
                                   placeholder="Enter order number">
                        </div>

                        <div class="form-group">
                            <label for="size">Size</label>
                            <input type="text" id="size" name="size" 
                                   value="<?php echo $edit_party ? htmlspecialchars($edit_party['size']) : ''; ?>" 
                                   placeholder="Enter size details">
                        </div>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" name="<?php echo $action == 'edit' ? 'update_party' : 'add_party'; ?>" class="btn">
                            <?php echo $action == 'edit' ? '💾 Update Party' : '➕ Add Party'; ?>
                        </button>
                        <button type="reset" class="btn btn-danger">🔄 Reset</button>
                        <a href="?" class="btn btn-secondary">✖ Cancel</a>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <!-- Search Section -->
            <div class="search-section">
                <form method="POST" action="" class="search-form">
                    <div class="search-group">
                        <label for="search_phone">🔍 Search by Contact Number</label>
                        <input type="text" id="search_phone" name="search_phone" 
                               value="<?php echo htmlspecialchars($search_query); ?>" 
                               placeholder="Enter phone number to search...">
                    </div>
                    <button type="submit" name="search" class="search-btn">Search</button>
                    <a href="?" class="search-btn clear-btn">Clear</a>
                </form>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <div class="table-header">
                    <h3>📋 All Parties</h3>
                    <div class="stats-badge">Total Records: <?php echo $result ? mysql_num_rows($result) : 0; ?></div>
                </div>

                <?php if($result && mysql_num_rows($result) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Party Date</th>
                                <th>Total Amount</th>
                                <th>Advance</th>
                                <th>Pending</th>
                                <th>Order Number</th>
                                <th>Size</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysql_fetch_array($result)): 
                                $pending = $row['totalamount'] - $row['advance'];
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                                <td class="amount-cell">₹ <?php echo number_format($row['totalamount'], 2); ?></td>
                                <td class="amount-cell">₹ <?php echo number_format($row['advance'], 2); ?></td>
                                <td>
                                    <span class="pending-amount">₹ <?php echo number_format($pending, 2); ?></span>
                                </td>
                                <td><?php echo $row['order'] ? htmlspecialchars($row['order']) : '-'; ?></td>
                                <td><?php echo $row['size'] ? htmlspecialchars($row['size']) : '-'; ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-edit btn-sm">✏️ Edit</a>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-delete btn-sm" 
                                           onclick="return confirm('Are you sure you want to delete this party record?')">🗑️ Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <div class="no-data-icon">📭</div>
                        <p>No parties found. <a href="?action=add" style="color: #ff7e5f; font-weight: 600;">Add your first party</a></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    // Client-side validation
    document.addEventListener('DOMContentLoaded', function() {
        // Only allow numbers in phone input
        const phoneInput = document.getElementById('phone');
        if(phoneInput) {
            phoneInput.addEventListener('keypress', function(e) {
                if (e.key < '0' || e.key > '9') {
                    e.preventDefault();
                }
            });
        }

        // Validate advance doesn't exceed total amount
        const amountInput = document.getElementById('amount');
        const advanceInput = document.getElementById('advance');
        
        if(amountInput && advanceInput) {
            advanceInput.addEventListener('input', function() {
                const total = parseFloat(amountInput.value) || 0;
                const advance = parseFloat(advanceInput.value) || 0;
                
                if(advance > total) {
                    advanceInput.setCustomValidity('Advance cannot exceed total amount');
                } else {
                    advanceInput.setCustomValidity('');
                }
            });

            amountInput.addEventListener('input', function() {
                const total = parseFloat(amountInput.value) || 0;
                const advance = parseFloat(advanceInput.value) || 0;
                
                if(advance > total) {
                    advanceInput.setCustomValidity('Advance cannot exceed total amount');
                } else {
                    advanceInput.setCustomValidity('');
                }
            });
        }
    });
    </script>
</body>
</html>