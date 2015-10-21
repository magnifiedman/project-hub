<?php
include('lib/config.inc.php');
include('lib/classes/base.class.php');
include('lib/classes/user.class.php');
include('lib/classes/project.class.php');
include('lib/classes/utility.class.php');

// kick them out
if(!isset($_COOKIE['userLogged'])){ header("Location: index.php"); }

// initiation
$u = new User();
$z = new Utility();
$p = new Project($z);
$error='';
$step=1;
$userLevel=$_COOKIE['userLevel'];
$userLogged=true;

// form submitted
if(isset($_POST['addUser'])){
  if($user = $u->addUser($_POST)){
    $step=2;
  }
  else {
    $error = '<p class="bg-danger padded">* There was an error adding user. Please contact mission control.</p>';
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
        <a href="user-overview.php" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> User Overview</a>
        </div>
        <div class="col-sm-2">
        </div>
      </div>
      
      
      <div class="row">
        <div class="col-sm-2 main">
        </div>
        <div class="col-sm-8 main panel">

          <?php if($step==1){ ?>
            <h3>User Details</h3>
            <form action="" role="form" id="addUser" method="post">
            <input type="hidden" name="addUser" value="y" />
            <?php echo $error; ?>
            
            <div class="form-group clearfix">
            
              <div class="col-sm-6">
              <p><label>First Name:</label>
              <input type="text" name="fname" class="form-control" placeholder="First Name" required /></p>
              </div>
              <div class="col-sm-6">
              <p><label>Last Name:</label>
              <input type="text" name="lname" class="form-control" placeholder="Last Name" required /></p>
              </div>
              
              <div class="col-sm-12 clearfix">
              <p><label>Email:</label>
              <input type="email" name="email" class="form-control" placeholder="Your Email" data-bv-email-message required /></p>
              </div>            
              
              <div class="col-sm-6">
              <p><label>Office Phone:</label>
              <input type="text" name="office_phone" class="form-control" placeholder="Office Phone" required /></p>
              </div>
              <div class="col-sm-6">
              <p><label>Mobile Phone:</label>
              <input type="text" name="mobile_phone" class="form-control" placeholder="Mobile Phone" required /></p>
              </div>

              <div class="col-sm-6">
              <p><label>Choose Password:</label>
              <input type="password" name="pword" class="form-control" placeholder="" required /></p>
              </div>
              <div class="col-sm-6">
              <p><label>Repeat Password:</label>
              <input type="password" name="pword_conf" class="form-control" placeholder="" required /></p>
              </div>

              <div class="col-sm-12 clearfix">
              <?php $u->userLevelSelect(); ?></p>
              </div>

              <div class="col-sm-12 clearfix">
              <?php $u->userTypeSelect(); ?></p>
              </div>
            </div>
            
            <div class="standard-user">
              <h3>User Assignments</h3>
              <div class="form-group clearfix">

                <div class="col-sm-12 clearfix">
                <?php $u->userAssignmentFields(); ?>
                </div>

              </div>
            </div>

            <h3>Notification Settings</h3>
            <div class="form-group clearfix">
              <div class="col-sm-12">
              <p><label class="checkbox-inline"><input type="checkbox" name="email_notify" value="y" > Recieve Email Notifications</label></p>
              </div>
              <div class="col-sm-4">
                <p><label class="checkbox-inline"><input type="checkbox" name="evernote_notify" value="y" > Post notes to Evernote</label></p>
              </div> 
              <div class="col-sm-8">
                <p>
                <input type="email" name="evernote_email" class="form-control" placeholder="Evernote Email Address"></p>
              </div>
            </div>
            
            
            <div class="form-group clearfix">
            <input type="submit" name="add" value="Create User" class="btn btn-primary btn-lg">
            </div>
            </form>
          <?php } ?>

          <?php if($step==2){ ?>
            <p class="margin-20 pull-left"><span class="glyphicon glyphicon-user"></span> User <strong><?php echo $user['fname'].' '.$user['lname'].'</strong> (' .$user['email'] . ')'; ?> has been created.</p>
            <p class="pull-left margin-20 bm-20"><a href="user-overview.php" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Back to Dashboard</a> <a href="add-user.php" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Add Another User</a></p>
          <?php } ?>

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
        
        $('#addUser').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            submitButtons: 'button[type="submit"]'
        });

        $('.standard-user').hide(); 

        var $typeSelector = $('.user-type');

        if ($typeSelector.val() === '2') {
                $('.standard-user').hide(); 
            }

        $typeSelector.change(function(){
            if ($typeSelector.val() === '1') {
                $('.standard-user').show(); 
            }
            if ($typeSelector.val() === '2') {
                $('.standard-user').hide(); 
            }
            if ($typeSelector.val() === '3') {
                $('.standard-user').hide(); 
            }
            if ($typeSelector.val() === '4') {
                $('.standard-user').hide(); 
            }
            if ($typeSelector.val() === '5') {
                $('.standard-user').show(); 
            }
        });

      });
    </script>
  </body>
</html>