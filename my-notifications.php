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
$errorMsg = '';
$successMsg = '';
$step=1;
$userLevel=$_COOKIE['userLevel'];
$userLogged=true;


// form submitted
if(isset($_POST['noteForm'])){
  if($u->createNote($_POST)){
    $successMsg = '<p class="panel padded"><span class="glyphicon glyphicon-plus"></span> Your note has been added.</p>';
  }
}
?>
    <?php include('inc/header.inc.php'); ?>

    <?php include('inc/nav-top.inc.php'); ?>

    <div class="container-fluid">
      
      <div class="row">
        <div class="col-sm-2">
        </div>
        <div class="col-sm-8 button-row">
        <a href="index.php" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> My Dashboard</a>
        </div>
        <div class="col-sm-2">
        </div>
      </div>

      <div class="row">
        <div class="col-sm-2 main">
        </div>
        <div class="col-sm-8 main">
          <?php echo $successMsg; ?>
          <h3><span class="glyphicon glyphicon-star"></span> My Notifications</h3>

           
            <?php $z->getUserNotifications($_COOKIE['userID'],'',$page); ?>

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

        $('#add-note').click(function(){
            $('.add-note').show();
        });

      });
      </script>
  </body>
</html>