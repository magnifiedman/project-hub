<?php
include('lib/config.inc.php');
include('lib/classes/base.class.php');
include('lib/classes/user.class.php');
include('lib/classes/project.class.php');
include('lib/classes/utility.class.php');


// kick them out
if(!isset($_COOKIE['userLogged'])){ header("Location: index.php"); }

// set the page
if(isset($_GET['page'])){ $page = $_GET['page']; } else { $page = 1; }

// initiation
$u = new User();
$z = new Utility();
$p = new Project($z);
$error='';
$step=1;
$userLevel=$_COOKIE['userLevel'];
$userLogged=true;

// form submitted
if(isset($_POST['updateUser'])){
  if($u->updateUser($_POST)){
    $step=2;
  }
  else {
    $error = '<p class="bg-danger padded">* There was an error updating. Please contact mission control.</p>';
  }
}

// get data
$users = $u->getUsers($page);
?>
    <?php include('inc/header.inc.php'); ?>

    <?php include('inc/nav-top.inc.php'); ?>

    <div class="container-fluid">
      <div class="row">
      </div>
    </div>

    <div class="container-fluid">
      
      <!-- <div class="row">
        <div class="col-sm-2">
        </div>
        <div class="col-sm-8 button-row">
        <a href="index.php" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> User Overview</a>
        </div>
        <div class="col-sm-2">
        </div>
      </div> -->

      <div class="row">
        <div class="col-sm-2 main">
        </div>
        <div class="col-sm-8 main">
          <h3>System Users<span class="pull-right"><a href="add-user.php" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Add a User</a></span></h3>
          <div class="table-responsive">
            <?php 
              $totalPages = ceil($users['totalRecords']/USERS_PERPAGE);
              include('inc/pagination.inc.php');
            ?>
            <div class="clear"></div>
            <table class="table table-striped table-hover">
              <thead>
                <th>Name</th>
                <th>Email</th>
                <th>Office Phone</th>
                <th>Mobile Phone</th>
                <th>User Type</th>
                <th>Edit</th>
              </thead>
              <tbody>
            <?php foreach($users['result'] as $user){
              echo '<tr>';
              echo '<td>'.$user['fname'].' ' . $user['lname'] . '</td>';
              echo '<td><a href="mailto:' . $user['email'] . '">' . $user['email'] . '</a></td>';
              echo '<td>' . $user['office_phone'] . '</td>';
              echo '<td>' . $user['mobile_phone'] . '</td>';
              switch($user['user_type']){
                case 1:
                $userType = 'K.A.C.';
                break;
                case 2:
                $userType = 'Creative';
                break;
                case 3:
                $userType = 'Account Executive';
                break;
                case 4:
                $userType = 'Talent';
                break;
                case 5:
                $userType = 'Programming/Promotions';
                break;
              }
              echo '<td>' . $userType . '</td>';
              echo '<td><a href="edit-user.php?id=' . $user['id'] . '"><span class="glyphicon glyphicon-edit"></span> Edit User</a></td>';
            }
            ?>
              </tbody>
            </table>

            <?php 
              $totalPages = ceil($users['totalRecords']/USERS_PERPAGE);
              include('inc/pagination.inc.php');
            ?>

          </div>

        </div>
       
        <div class="col-sm-2 main">
          
        </div>
      </div>
    </div>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.2/js/bootstrapValidator.min.js"></script>
    
    <script>
      $(document).ready(function() {
        
        $('#updateUser').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            submitButtons: 'button[type="submit"]'
        });

      });
      </script>
  </body>
</html>