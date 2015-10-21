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

// get data
$notes = $p->getNotes($_COOKIE['userID'],'',$page);
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
          <h3><span class="glyphicon glyphicon-list-alt"></span> My Notepad<span class="pull-right"><a href="#" id="add-note" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Add a Note</a></span></h3>
            
            <?php if(isset($_COOKIE['evernotify']) && isset($_COOKIE['evernote_email'])){ echo '<p><span class="glyphicon glyphicon-refresh evernote"></span> Currently syncing to <em>Evernote</em> account.</p>'; }
            else { echo '<p><span class="glyphicon glyphicon-link evernote"></span> Did you know you can sync your notes to your <em>Evernote</em> account? <a href="my-profile.php">Update your profile here.</a></p>';}
            ?>
            
            <div class="add-note">
              <form action="" role="form" id="addNote" method="post">
              <input type="hidden" name="noteForm" value="y" />
              <input type="hidden" name="user_id" value="<?php echo $_COOKIE['userID']; ?>" />
              <?php if(isset($_COOKIE['evernotify']) && isset($_COOKIE['evernote_email'])){ ?>
              <input type="hidden" name="evernote_email" value="<?php echo $_COOKIE['evernote_email']; ?>" />
              <?php } ?>

              <?php echo $errorMsg; ?>
              
              <div class="form-group clearfix">
              <label>Add Your Note:</label>
              <textarea name="text" class="form-control" placeholder="Enter notes here" rows="4"></textarea>
              </div>
              <div class="form-group clearfix">
                <?php $u->userProjectsSelect($_COOKIE['userID']); ?>
              <input type="submit" name="login" value="Submit" class="btn btn-primary">
              </div>
              </form>
            </div>

           
            <?php
            if($notes==false){ echo '<p class="panel padded"><span class="glyphicon glyphicon-pencil"></span> You currently have no notes entered.</p>'; }
              else {
                foreach($notes as $note){
                  $re='';
                  if($note['project_name']!=''){ $re = ' - Re: <a href="project-detail.php?id='. $note['project_id'] .'">'. $note['project_name'] . '</a>'; }
                  echo '<p class="panel padded"><span class="glyphicon glyphicon-map-marker"></span> <strong>'. date("M d, Y @ h:ia",strtotime($note['date_entered'])).'</strong>'.$re.'<br />'.stripslashes($note['text']) . '</p>';
                  
                }
              }
            ?>

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