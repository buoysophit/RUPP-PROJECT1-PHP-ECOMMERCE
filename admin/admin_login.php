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

    <!-- Font Awesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajaxlibs/font-awesome/6.1.1/css/all.min.css">

    <!-- Bootstrap CSS for layout -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f0f7ff; /* Light blue background matching the image */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            margin: 20px;
        }

        .login-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: #4e73df; /* Purple header as in the image */
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .card-subheader {
            text-align: center;
            padding: 10px 0;
            color: #ffffff;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        .form-control {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px 40px 10px 15px; /* Space for icons */
            font-size: 1rem;
            width: 100%;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #4e73df;
            outline: none;
            box-shadow: 0 0 5px rgba(106, 27, 154, 0.3);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .btn-login {
            background: #4e73df; /* Purple button matching header */
            color: white;
            border: none;
            border-radius: 25px; /* Rounded button as in image */
            padding: 10px 30px;
            font-size: 1rem;
            font-weight: bold;
            width: 100%;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-login:hover {
            background: #4e73df; /* Darker purple on hover */
        }

        .message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: #dc3545;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
        }

        .message i {
            cursor: pointer;
        }

        /* Default credentials styling (optional, if you want to keep it) */
        .default-creds {
            text-align: center;
            margin-top: 15px;
            color: #666;
            font-size: 0.9rem;
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
        <div class="card-header">ADMIN LOGIN</div>
        <div class="card-subheader">Hello there, Sign in and start managing your website</div>
        <div class="card-body p-4">
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

                <input type="submit"
                       value="LOGIN →"
                       class="btn-login"
                       name="submit">
            </form>

            <!-- Optional: Default credentials display
            <div class="default-creds">
                Default: <span class="fw-bold">admin</span> / <span class="fw-bold">111</span>
            </div> -->
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js (for Bootstrap components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>