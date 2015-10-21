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
if(isset($_POST['updateUser'])){
  if($u->updateUser($_POST,'admin')){
    $step=2;
  }
  else {
    $error = '<p class="bg-danger padded">* There was an error updating. Please contact mission control.</p>';
  }
}

//get data
$user = $u->getUserDetails($_GET['id']);
?>
    <?php include('inc/header.inc.php'); ?>

    <?php include('inc/nav-top.inc.php'); ?>

    <div class="container-fluid">
      <div class="row">
      </div>
    </div>

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

          <?php if($step==2){ ?>
            <p class="margin-20"><span class="glyphicon glyphicon-cloud-upload"></span> User <strong><?php echo $user['fname'].' '.$user['lname'].'</strong> (' .$user['email'] . ')'; ?> has been updated.</p>
          <?php } ?>

            <h3>User Details: <span class="text-muted"><?php echo $user['fname'].' '.$user['lname']; ?></span></h3>
            <form action="" role="form" id="updateUser" method="post">
            <input type="hidden" name="updateUser" value="y" />
            <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
            <?php echo $error; ?>
            
            <div class="form-group clearfix">
            
              <div class="col-sm-6">
              <p><label>First Name:</label>
              <input type="text" name="fname" class="form-control" value="<?php echo $user['fname']; ?>" required /></p>
              </div>
              <div class="col-sm-6">
              <p><label>Last Name:</label>
              <input type="text" name="lname" class="form-control" value="<?php echo $user['lname']; ?>" required /></p>
              </div>
              
              <div class="col-sm-12 clearfix">
              <p><label>Email:</label>
              <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" data-bv-email-message required /></p>
              </div>            
              
              <div class="col-sm-6">
              <p><label>Office Phone:</label>
              <input type="text" name="office_phone" class="form-control" value="<?php echo $user['office_phone']; ?>" required /></p>
              </div>
              <div class="col-sm-6">
              <p><label>Mobile Phone:</label>
              <input type="text" name="mobile_phone" class="form-control" value="<?php echo $user['mobile_phone']; ?>" /></p>
              </div>

              <div class="col-sm-6">
              <p><label>Update Password:</label>
              <input type="password" name="pword" class="form-control" /></p>
              </div>
              <div class="col-sm-6">
              <p><label>Repeat Password:</label>
              <input type="password" name="pword_conf" class="form-control" /></p>
              </div>

              <div class="col-sm-12 clearfix">
              <?php $u->userLevelSelect($user['user_level']); ?></p>
              </div>

              <div class="col-sm-12 clearfix">
              <?php $u->userStatusSelect($user['status']); ?></p>
              </div>

              <div class="col-sm-12 clearfix">
              <?php $u->userTypeSelect($user['user_type']); ?></p>
              </div>
            </div>
            
            <div class="standard-user">
              <h3>User Assignments</h3>
              <div class="form-group clearfix">
                

                <div class="col-sm-12 clearfix">
                <?php $u->userAssignmentFields(@$user['userGroups']); ?>
                </div>

              </div>
            </div>

            <h3>Notification Settings</h3>
            <div class="form-group clearfix">
              <div class="col-sm-12">
              <p><label class="checkbox-inline"><input type="checkbox" name="email_notify" value="y" <?php if($user['email_notify']=='y'){ echo 'checked="checked"'; } ?>> Recieve Email Notifications</label></p>
              </div>
              <div class="col-sm-4">
                <p><label class="checkbox-inline"><input type="checkbox" name="evernote_notify" value="y" <?php if($user['evernote_notify']=='y'){ echo 'checked="checked"'; } ?>> Post notes to Evernote</label></p>
              </div> 
              <div class="col-sm-8">
                <p>
                <input type="email" name="evernote_email" class="form-control" value="<?php echo $user['evernote_email']; ?>" laceholder="Evernote Email Address"></p>
              </div>
            </div>
            
            
            <div class="form-group clearfix">
            <input type="submit" name="add" value="Update User" class="btn btn-primary btn-lg">
            </div>
            </form>
          

          

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

        var $typeSelector = $('.user-type');

        if ($typeSelector.val() === '2') {
                $('.standard-user').hide(); 
            }

            if ($typeSelector.val() === '3') {
                $('.standard-user').hide(); 
            }

            if ($typeSelector.val() === '4') {
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