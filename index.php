<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location:./login.php");
    exit;
}

require_once('DBConnection.php');

$user_id = $_SESSION['user_id'];
$users = $conn->query("SELECT * FROM user_list WHERE user_id=$user_id");

$role = $users->fetch_assoc()['type'];

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
if($role != 1 && in_array($page,array('maintenance','products','sales_report','users'))){
    header("Location:./");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucwords(str_replace('_','',$page)) ?> | TD Sweets</title>
    <link rel="stylesheet" href="./Font-Awesome-master/css/all.min.css">
    <link href="./css/bootstrap-v5.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./select2/css/select2.min.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./DataTables/datatables.min.css">
    <script src="./DataTables/datatables.min.js"></script>
    <script src="./select2/js/select2.full.min.js"></script>
    <script src="./Font-Awesome-master/js/all.min.js"></script>
    <script src="./js/script.js"></script>
    <link rel="shortcut icon" href="./images/favicon.png" type="image/png">
    <script src="./js/chart.min.js"></script>

    <style>
        :root{
            --bs-success-rgb:71, 222, 152 !important;
        }
        html,body{
            height:100%;
            width:100%;
        }
        @media screen{
            _body{
                background-image:url('./images/ss3.jpg') !important;
                background-size:cover;
                background-repeat:no-repeat;
                background-position:center center;
            }

            body{
                background-color: #ddf2eb;
            }
        }
        main{
            height:100%;
            display:flex;
            flex-flow:column;
        }
        #page-container{
            flex: 1 1 auto; 
            overflow:auto;
        }
        #topNavBar{
             flex: 0 1 auto;
             background-color:;
        }
        .thumbnail-img{
            width:50px;
            height:50px;
            margin:2px
        }
        .truncate-1 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }
        .truncate-3 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        .modal-dialog.large {
            width: 80% !important;
            max-width: unset;
        }
        .modal-dialog.mid-large {
            width: 50% !important;
            max-width: unset;
        }
        @media (max-width:720px){
            
            .modal-dialog.large {
                width: 100% !important;
                max-width: unset;
            }
            .modal-dialog.mid-large {
                width: 100% !important;
                max-width: unset;
            }  
        
        }
        .display-select-image{
            width:60px;
            height:60px;
            margin:2px
        }
        img.display-image {
            width: 100%;
            height: 45vh;
            object-fit: cover;
            background: black;
        }
        /* width */
        ::-webkit-scrollbar {
        width: 0px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
        background: #f1f1f1; 
        }
        
        /* Handle */
        ::-webkit-scrollbar-thumb {
        background: #888; 
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
        background: #555; 
        }
        .img-del-btn{
            right: 2px;
            top: -3px;
        }
        .img-del-btn>.btn{
            font-size: 10px;
            padding: 0px 2px !important;
        }

        #salesbg {
            background-color: #6A89A0;
        }
        #iconcolor{
            color:#FC9281;
        }
        #topNavBar{
            background:#;
            opacity: 100%;
        }
        #logosize{
            width:50%;
            height: 50%;
        }
        #logosize1{
            width:20%;
            height: 10%;

        }
        
    </style>
</head>

<body>
    <main>
    <nav class="navbar navbar-expand-lg" id="topNavBar">
    
    <div class="container">
        <!--<strong><a class="navbar-brand" href="./">TD Sweets</a></strong>-->
         <a href="index.html" class="logo me-auto me-lg-0"><img src="images/logo2.png" alt="" class="img-fluid" id="logosize"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"><i id="iconcolor" class="fa fa-solid fa-bars"></i></span>
           
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <?php if($role=='1'){ ?>
                    <li class="nav-item">
                        <strong><a class="nav-link <?php echo ($page == 'home') ? 'active' : '' ?>" href="./">Home</a></strong>
                    </li>
                    <li class="nav-item">
                       <strong><a class="nav-link <?php echo ($page == 'products') ? 'active' : '' ?>" href="./?page=products">Products</a></strong>
                    </li>
                    <li class="nav-item">
                        <strong><a class="nav-link <?php echo ($page == 'stocks') ? 'active' : '' ?>" href="./?page=stocks">Stocks</a></strong>
                    </li>
                    <li class="nav-item">
                        <strong><a class="nav-link <?php echo ($page == 'sales') ? 'active' : '' ?>" href="./?page=sales">POS</a></strong>
                    </li>
                    <li class="nav-item">
                        <strong><a class="nav-link <?php echo ($page == 'sales_report') ? 'active' : '' ?>" href="./?page=sales_report">Sales</a></strong>
                    </li>
                    <li class="nav-item">
                       <strong><a class="nav-link <?php echo ($page == 'users') ? 'active' : '' ?>" href="./?page=users">Users</a></strong>
                    </li>
                    <li class="nav-item">
                        <strong><a class="nav-link" href="./?page=maintenance">Maintenance</a></strong>
                    </li>
                <?php }?> 
                <?php if($role=='0'){ ?>
                    <li class="nav-item">
                     <strong> <a class="nav-link <?php echo ($page == 'home') ? 'active' : '' ?>" href="./">Home</a></strong>
                    </li>
                    <li class="nav-item">
                    <strong> <a class="nav-link <?php echo ($page == 'stocks') ? 'active' : '' ?>" href="./?page=stocks">Stocks</a></strong>
                    </li>
                    <li class="nav-item">
                    <strong> <a class="nav-link <?php echo ($page == 'sales') ? 'active' : '' ?>" href="./?page=sales">POS</a></strong>
                    </li>
                <?php }?>      
            </ul>
        </div>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle bg-transparent text-primary border-0" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                Hello <?php echo $_SESSION['fullname'] ?>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                <li><a class="dropdown-item" href="./?page=manage_account">Manage Account</a></li>
                <li><a class="dropdown-item" href="./Actions.php?a=logout">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

    <div class="container py-3" id="page-container">
        <?php 
            if(isset($_SESSION['flashdata'])):
        ?>
        <div class="dynamic_alert alert alert-<?php echo $_SESSION['flashdata']['type'] ?> rounded-0 shadow">
        <div class="float-end"><a href="javascript:void(0)" class="text-dark text-decoration-none" onclick="$(this).closest('.dynamic_alert').hide('slow').remove()">x</a></div>
            <?php echo $_SESSION['flashdata']['msg'] ?>
        </div>
        <?php unset($_SESSION['flashdata']) ?>
        <?php endif; ?>
        <?php
            include $page.'.php';
        ?>
    </div>
    </main>
    <div class="modal fade" id="uni_modal" role='dialog' data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header py-2">
            <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-sm rounded-0 btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
            <button type="button" class="btn btn-sm rounded-0 btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="uni_modal_secondary" role='dialog' data-bs-backdrop="static" data-bs-keyboard="true">
<div class="modal-dialog modal-md modal-dialog-centered  rounded-0" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header py-2">
            <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-sm rounded-0 btn-primary" id='submit' onclick="$('#uni_modal_secondary form').submit()">Save</button>
            <button type="button" class="btn btn-sm rounded-0 btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="confirm_modal" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered  rounded-0" role="document">
        <div class="modal-content rounded-0 rounded-0">
            <div class="modal-header py-2">
            <h5 class="modal-title">Confirmation</h5>
        </div>
        <div class="modal-body">
            <div id="delete_content"></div>
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-primary btn-sm rounded-0" id='confirm' onclick="">Continue</button>
            <button type="button" class="btn btn-secondary btn-sm rounded-0" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
</body>
</html>