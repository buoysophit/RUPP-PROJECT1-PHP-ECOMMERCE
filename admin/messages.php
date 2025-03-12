<?php
include '../components/connect.php';
session_start();
  $current_page = 'messages';
$admin_id = $_SESSION['admin_id'];
if(!isset($admin_id)){
    header('location:admin_login.php');
}

if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
    $delete_message->execute([$delete_id]);
    header('location:messages.php');
  
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include "include/head.php" ?>
<body id="page-top">
<div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
            <div class="sidebar-brand-text mx-3">Admin</div>
        </a>
        <hr class="sidebar-divider my-0">
        <?php include "include/nav.php" ?>
        <hr class="sidebar-divider">
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid">
                <section class="contacts">
                    <h1 class="h3 mb-4 text-gray-800">Messages</h1>

                    <div class="row">
                        <?php
                        $select_messages = $conn->prepare("SELECT * FROM `messages`");
                        $select_messages->execute();
                        if($select_messages->rowCount() > 0){
                            while($fetch_message = $select_messages->fetch(PDO::FETCH_ASSOC)){
                                ?>
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Message
                                            </div>
                                            <div class="mb-0 font-weight-bold text-gray-800">
                                                <p>User ID: <span><?= $fetch_message['user_id']; ?></span></p>
                                                <p>Name: <span><?= $fetch_message['name']; ?></span></p>
                                                <p>Email: <span><?= $fetch_message['email']; ?></span></p>
                                                <p>Number: <span><?= $fetch_message['number']; ?></span></p>
                                                <p>Message: <span><?= $fetch_message['message']; ?></span></p>
                                                <div class="d-flex justify-content-end mt-3">
                                                    <a href="messages.php?delete=<?= $fetch_message['id']; ?>"
                                                       onclick="return confirm('delete this message?');"
                                                       class="btn btn-danger btn-sm">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }else{
                            echo '<div class="col-12"><div class="card"><div class="card-body"><p class="text-center mb-0">You have no messages</p></div></div></div>';
                        }
                        ?>
                    </div>
                </section>
            </div>
        </div>
        <?php include "include/footer.php" ?>
    </div>
</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="login.html">Logout</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>