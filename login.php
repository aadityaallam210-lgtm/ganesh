<?php

session_start();
include("config.php");

if(isset($_SESSION['login_user']))
{
    header("Location:home.php");
}

$error = "";

if($_SERVER['REQUEST_METHOD']=='POST')
{
    $errors = array();
    
    $a = trim($_POST['cont']);
    $b = trim($_POST['upass']);
    
    // Validate Phone
    if(empty($a))
    {
        $errors[] = "Phone number is required";
    }
    elseif(!preg_match('/^[0-9]{10}$/', $a))
    {
        $errors[] = "Phone number must be 10 digits";
    }
    
    // Validate Password
    if(empty($b))
    {
        $errors[] = "Password is required";
    }
    elseif(strlen($b) < 4)
    {
        $errors[] = "Password must be at least 4 characters";
    }
    
    if(empty($errors))
    {
        $a = mysql_real_escape_string($a);
        $b = mysql_real_escape_string($b);
        
        $qry = "SELECT * FROM admin WHERE phone='$a' AND pass='$b'";
        $result = mysql_query($qry);
        $row = mysql_fetch_array($result);
        $count = mysql_num_rows($result);

        if($count == 1)
        {
            $_SESSION['login_user'] = $a;
            header('Location:home.php');
            exit();
        }
        else
        {
            $error = "Invalid phone number or password";
        }
    }
    else
    {
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background particles with festival motifs */
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
        }

        @keyframes particleFloat {
            from {
                transform: translateY(0);
            }
            to {
                transform: translateY(-60px);
            }
        }

        /* Floating orbs with festival glow */
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
        }

        @keyframes orbFade {
            0%, 100% {
                opacity: 0.5;
            }
            50% {
                opacity: 1;
            }
        }

        center {
            position: relative;
            z-index: 1;
            width: 100%;
            padding: 20px;
        }

        form#re {
            background: rgba(255, 255, 255, 0.95);
            padding: 50px 70px;
            border-radius: 25px;
            box-shadow: 
                0 25px 70px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            max-width: 450px;
            margin: 0 auto;
            animation: formSlideIn 1s ease-out;
            backdrop-filter: blur(10px);
            position: relative;
        }

        @keyframes formSlideIn {
            0% {
                opacity: 0;
                transform: translateY(100px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Glowing border effect on form */
        form#re::before {
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
            transition: opacity 0.5s;
            animation: borderPulse 3s ease-in-out infinite;
        }

        form#re:hover::before {
            opacity: 0.8;
        }

        @keyframes borderPulse {
            0%, 100% {
                opacity: 0.8;
            }
            50% {
                opacity: 0.4;
            }
        }

        h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 15px;
            text-align: center;
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: titleFade 1.2s ease-out 0.3s both;
            font-weight: 700;
            text-transform: capitalize;
        }

        @keyframes titleFade {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        p {
            color: #555;
            margin-bottom: 35px;
            font-size: 1.1em;
            text-align: center;
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Error message */
        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
            padding: 12px 20px;
            border-radius: 50px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Input container for password toggle */
        .input-container {
            position: relative;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 18px 25px;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            font-size: 1.05em;
            transition: all 0.4s ease;
            background: white;
            animation: inputFadeIn 0.8s ease-out backwards;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .input-container input[type="password"],
        .input-container input[type="text"] {
            padding-right: 55px;
        }

        input[type="text"]:nth-of-type(1),
        .input-container:nth-of-type(1) input { 
            animation-delay: 0.7s; 
        }
        
        .input-container:nth-of-type(2) input { 
            animation-delay: 0.85s; 
        }

        @keyframes inputFadeIn {
            0% {
                opacity: 0;
                transform: translateX(-50px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #ff7e5f;
            box-shadow: 
                0 0 0 4px rgba(255, 126, 95, 0.1),
                0 5px 20px rgba(255, 126, 95, 0.2);
            transform: translateY(-3px);
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #999;
        }

        /* Toggle password visibility button */
        .toggle-password {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.3em;
            color: #999;
            padding: 5px;
            transition: all 0.3s ease;
            z-index: 10;
            width: auto;
            height: auto;
            box-shadow: none;
            margin: 0;
            animation: none;
        }

        .toggle-password:hover {
            color: #ff7e5f;
            transform: translateY(-50%) scale(1.1);
            box-shadow: none;
        }

        .toggle-password::before {
            display: none;
        }

        button {
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            border: none;
            border-radius: 50px;
            padding: 16px 50px;
            color: white;
            font-size: 1.15em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(255, 126, 95, 0.4);
            position: relative;
            overflow: hidden;
            margin: 8px;
            animation: buttonFade 0.8s ease-out 1s backwards;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        button[type="reset"] {
            animation-delay: 1.1s;
        }

        @keyframes buttonFade {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Ripple effect */
        button:not(.toggle-password)::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        button:not(.toggle-password):hover::before {
            width: 350px;
            height: 350px;
        }

        button:not(.toggle-password):hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 10px 30px rgba(255, 126, 95, 0.6);
        }

        button:not(.toggle-password):active {
            transform: translateY(-2px) scale(1.02);
        }

        button[type="reset"] {
            background: linear-gradient(135deg, #ff6b6b 0%, #ffa726 100%);
            box-shadow: 0 5px 20px rgba(255, 107, 107, 0.4);
        }

        button[type="reset"]:hover {
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.6);
        }

        br {
            display: block;
            content: "";
            margin: 12px 0;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            form#re {
                padding: 40px 50px;
            }

            h1 {
                font-size: 2em;
            }

            input[type="text"],
            input[type="password"] {
                padding: 15px 20px;
                font-size: 1em;
            }

            .input-container input[type="password"],
            .input-container input[type="text"] {
                padding-right: 50px;
            }

            button:not(.toggle-password) {
                padding: 14px 40px;
                font-size: 1em;
            }
        }

        @media (max-width: 480px) {
            form#re {
                padding: 35px 40px;
            }

            h1 {
                font-size: 1.6em;
            }

            button:not(.toggle-password) {
                padding: 12px 35px;
                font-size: 0.95em;
                margin: 5px;
            }
        }
    </style>
</head>
<body>
<center>
    <form id="re" action="" method="post">
        <h1>Login Page</h1>
        <p><i>Enter your details</i></p>
        
        <?php if(!empty($error)): ?>
            <div class="error-message">✗ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <input type="text" name="cont" id="cont" value="<?php echo isset($_POST['cont']) ? htmlspecialchars($_POST['cont']) : ''; ?>" placeholder="Enter contact number" maxlength="10"><br><br>
        
        <div class="input-container">
            <input type="password" name="upass" id="pass" value="" placeholder="Enter password">
            <button type="button" class="toggle-password" onclick="togglePassword()">👁️</button>
        </div>
        <br>

        <button type="submit">Submit</button>
        <button type="reset">Reset</button>
    </form>
</center>

<script>
function togglePassword() {
    var passwordInput = document.getElementById('pass');
    var toggleButton = document.querySelector('.toggle-password');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.textContent = '🙈';
    } else {
        passwordInput.type = 'password';
        toggleButton.textContent = '👁️';
    }
}

// Client-side validation
document.getElementById('re').addEventListener('submit', function(e) {
    var phone = document.getElementById('cont').value.trim();
    var password = document.getElementById('pass').value.trim();
    var errors = [];
    
    // Validate phone
    if (phone === '') {
        errors.push('Phone number is required');
    } else if (!/^[0-9]{10}$/.test(phone)) {
        errors.push('Phone number must be 10 digits');
    }
    
    // Validate password
    if (password === '') {
        errors.push('Password is required');
    } else if (password.length < 4) {
        errors.push('Password must be at least 4 characters');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join('\n'));
        return false;
    }
});

// Only allow numbers in phone input
document.getElementById('cont').addEventListener('keypress', function(e) {
    if (e.key < '0' || e.key > '9') {
        e.preventDefault();
    }
});
</script>
</body>
</html>