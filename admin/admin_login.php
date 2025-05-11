<?php
include '../components/connect.php';
session_start();
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ? AND password = ?");
    $select_admin->execute([$name, $pass]);
    $row = $select_admin->fetch(PDO::FETCH_ASSOC);

    if($select_admin->rowCount() > 0){
        $_SESSION['admin_id'] = $row['id'];
        header('location:dashboard.php');
    }else{
        $message[] = 'incorrect username or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #E53888;
            --secondary-color: #E53888;
            --accent-color: #f8f9fc;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .login-container {
            max-width: 420px;
            width: 90%;
            margin: 20px;
            perspective: 1000px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: var(--primary-color);
            color: white;
            text-align: center;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
            pointer-events: none;
        }

        .card-header h2 {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
        }

        .card-subheader {
            color: var(--accent-color);
            font-size: 1rem;
            margin-top: 10px;
            opacity: 0.9;
        }

        .card-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-control {
            border: 2px solid #e1e5ee;
            border-radius: 10px;
            padding: 15px 45px 15px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .btn-login {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }

        .message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: #fff;
            color: #dc3545;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .message i {
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .message i:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <?php
    if(isset($message)){
        foreach($message as $msg){
            echo '
                <div class="message">
                    <span>'.$msg.'</span>
                    <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
                </div>';
        }
    }
    ?>

    <div class="login-container">
        <div class="login-card">
            <div class="card-header">
                <h2>ADMIN LOGIN</h2>
                <div class="card-subheader">Sign in to manage your website</div>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <div class="form-group">
                        <input type="text"
                               name="name"
                               required
                               placeholder="Username"
                               maxlength="20"
                               class="form-control"
                               oninput="this.value = this.value.replace(/\s/g, '')">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                    </div>

                    <div class="form-group">
                        <input type="password"
                               name="pass"
                               required
                               placeholder="Password"
                               maxlength="20"
                               class="form-control"
                               oninput="this.value = this.value.replace(/\s/g, '')">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                    </div>

                    <button type="submit" class="btn-login" name="submit">
                        Login <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>