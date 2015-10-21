<?php
include('lib/config.inc.php');
include('lib/classes/base.class.php');
include('lib/classes/utility.class.php');
include('lib/classes/user.class.php');
include('lib/classes/project.class.php');

// initiation
$u = new User();
$z = new Utility();
$p = new Project($z);
$error='';
$step=1;
$userlevel='';
setcookie('newProjectID','',-3600);

// set landmark
$z->setLandmark();

// check for logged
if(isset($_COOKIE['userLogged'])){
  $step=2;
  $userLogged=true;
  $userLevel = $_COOKIE['userLevel'];
  $notes = $p->getNotes($_COOKIE['userID'],'y','');
  $upcomingProjects = $p->getUserProjects($_COOKIE['userID'],'upcoming');
  $inprogressProjects = $p->getUserProjects($_COOKIE['userID'],'in-progress');
  $pendingProjects = $p->getUserProjects($_COOKIE['userID'],'pending');
}

// form submitted
if(isset($_POST['loginForm'])){
  if($userLevel = $u->doLogin($_POST)){
    $step=2;
    $userLogged=true;
    $notes = $p->getNotes($_COOKIE['userID'],'y','');
    $upcomingProjects = $p->getUserProjects($_COOKIE['userID'],'upcoming');
  $inprogressProjects = $p->getUserProjects($_COOKIE['userID'],'in-progress');
  $pendingProjects = $p->getUserProjects($_COOKIE['userID'],'pending');
  }
  else {
    $error = '<p class="bg-danger padded">* Incorrect username/password. Please retry.</p>';
  }
}


?>
    <?php include('inc/header.inc.php'); ?>

    <?php include('inc/nav-top.inc.php'); ?>

    <div class="container-fluid">
      <div class="row">
      </div>
    </div>

    <?php if($step==1){ ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 main">
          
        </div>
        <div class="col-sm-6 main panel">
          <h3>Log In Below</h3>
          <form action="" role="form" id="loginForm" method="post">
          <input type="hidden" name="loginForm" value="y" />
          <?php echo $error; ?>
          
          <div class="form-group clearfix">
          <label>Email:</label>
          <input type="email" name="email" class="form-control" placeholder="Your Email" data-bv-email-message required />
          </div>
          <div class="form-group clearfix">
          <label>Password:</label>
          <input type="password" name="pword" class="form-control" placeholder="Your Password" required />
          </div>
          <div class="form-group clearfix">
          <input type="submit" name="login" value="Submit" class="btn btn-primary btn-lg">
          </div>
          </form>
        </div>
       
        <div class="col-sm-3 main">
          
        </div>
      </div>
    </div>
    <?php } ?>

    <?php if($step==2){ ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-1 main">
        </div>
        <div class="col-sm-10 main">
        
        <!-- Notifications -->
        <h3 class="clear"><span class="text-muted">Recent</span> Notifications</h3>
        <?php $z->getUserNotifications($_COOKIE['userID'],'short'); ?>
        <p class="pull-right"><a href="my-notifications.php"><span class="glyphicon glyphicon-star"></span> View all Notifications...</a></p>
  
        <!-- Calendar 
        <h3><span class="text-muted">Recent</span> Calendar</h3>
        <p class="pull-right"><a href="my-calendars.php"><span class="glyphicon glyphicon-calendar"></span> View all Calendars...</a></p>
        -->
        
        
        <?php
          if($notes==false){ }
          else {
            ?>
            <!-- Notes -->
          <h3 class="clear"><span class="text-muted">My</span> Notepad</h3>
          <?php
            foreach($notes as $note){
              $re='';
              if($note['project_name']!=''){ $re = 'Re: <a href="project-detail.php?id='. $note['project_id'] .'">'. $note['project_name'] . '</a> - '; }
              echo '<p class=""><span class="date-display">'. date("M d, Y @ h:ia",strtotime($note['date_entered'])).'</span> '.$re.''.stripslashes($note['text']) . '</p>';
              
            }
        ?>
         
          <p class="pull-right"><a href="my-notes.php"><span class="glyphicon glyphicon-list-alt"></span> View all Notes...</a></p>
        <?php }
        ?>
        <div class="clear"></div>
        <!-- Projects -->
        <a href="add-project.php" class="btn btn-primary pull-right" style="margin-top:10px"><span class="glyphicon glyphicon-plus"></span> Add Project</a>
        <h3 class="no-border"><span class="text-muted">Recent</span> Projects</h3>
        <table class="table table-responsive">
        <tr class="border-none panel">
          <th>Dates</th>
          <th>Project Name</th>
          <th>Stations</th>
          <th>Client</th>
          <th>A.E.</th>
          <th class="text-center">View/Edit</th>
        </tr>
        
        <?php
        echo '<tr class="border-none">';
        echo '<td colspan="6"><h4>Currently Running</h4></td>';
        echo '</tr>';
        
        if($inprogressProjects==false){ echo '<tr><td colspan="6"><p class="panel padded">You currently have no projects <strong>CURRENTLY RUNNING</strong>.</p></td></tr>'; }
                 
        else {
          
          foreach($inprogressProjects as $project){
            $stationNames = $p->getStationNamesOverview($project['stations']);
            if($project['client_name']==''){ $project['client_name'] = 'N/A'; }
            if($project['ae_fname']=='' && $project['ae_lname']==''){ $aeName = ''; }
            else { $aeName = $p->getUserAETag($project['ae_id']); }
            echo '<tr class="border-none">';
            echo '<td><em>' . date("m/d",strtotime($project['start_date'])) . ' - ' . date("m/d",strtotime($project['end_date'])) . '</em></td>';
            echo '<td><a href="project-detail.php?id=' . $project['id'] .'">' . $project['project_name'] . '</a></td>';
            echo '<td>' . $stationNames . '</td>';
            echo '<td>' . $project['client_name'] . '</td>';
            echo '<td>' . $aeName . '</td>';
            echo '<td class="text-center"><a href="project-detail.php?id=' . $project['id'] . '"><span class="glyphicon glyphicon-eye-open"></span></a></td>';
            echo '</tr>';
            
          }

        }

        echo '<tr class="border-none">';
        echo '<td colspan="6"><h4>Starting Soon</h4></td>';
        echo '</tr>';
        
        if($upcomingProjects==false){ echo '<tr><td colspan="6"><p class="panel padded">You currently have no projects <strong>STARTING SOON</strong>.</p></td></tr>'; }
                 
        else { 
          
          foreach($upcomingProjects as $project){
            $stationNames = $p->getStationNamesOverview($project['stations']);
            if($project['client_name']==''){ $project['client_name'] = 'N/A'; }
            if($project['ae_fname']=='' && $project['ae_lname']==''){ $aeName = ''; }
            else { $aeName = $p->getUserAETag($project['ae_id']); }
            echo '<tr class="border-none">';
            echo '<td><em>' . date("m/d",strtotime($project['start_date'])) . ' - ' . date("m/d",strtotime($project['end_date'])) . '</em></td>';
            echo '<td><a href="project-detail.php?id=' . $project['id'] .'">' . $project['project_name'] . '</a></td>';
            echo '<td>' . $stationNames . '</td>';
            echo '<td>' . $project['client_name'] . '</td>';
            echo '<td>' . $aeName . '</td>';
            echo '<td class="text-center"><a href="project-detail.php?id=' . $project['id'] . '"><span class="glyphicon glyphicon-eye-open"></span></a></td>';
            echo '</tr>';
            
          }
         
        }

        echo '<tr class="border-none">';
        echo '<td colspan="6"><h4>Pending</h4></td>';
        echo '</tr>';

        if($pendingProjects==false){ echo '<tr><td colspan="6"><p class="panel padded">You currently have no projects <strong>PENDING</strong>.</p></td></tr>'; }
              
        else {   
          
          foreach($pendingProjects as $project){
            $stationNames = $p->getStationNamesOverview($project['stations']);
            if($project['client_name']==''){ $project['client_name'] = 'N/A'; }
            if($project['ae_fname']=='' && $project['ae_lname']==''){ $aeName = ''; }
            else { $aeName = $p->getUserAETag($project['ae_id']); }
            echo '<tr class="border-none">';
            echo '<td><em>' . date("m/d",strtotime($project['start_date'])) . ' - ' . date("m/d",strtotime($project['end_date'])) . '</em></td>';
            echo '<td><a href="project-detail.php?id=' . $project['id'] .'">' . $project['project_name'] . '</a></td>';
            echo '<td>' . $stationNames . '</td>';
            echo '<td>' . $project['client_name'] . '</td>';
            echo '<td>' . $aeName . '</td>';
            echo '<td class="text-center"><a href="project-detail.php?id=' . $project['id'] . '"><span class="glyphicon glyphicon-eye-open"></span></a></td>';
            echo '</tr>';
            
          }
 
        }

        echo '</table>';

        ?>
        <p class="pull-right"><a href="my-projects.php"><span class="glyphicon glyphicon-fire"></span> View all Projects...</a></p>


        </div>
        <div class="col-sm-1 main">
        </div>
      </div>
    </div>

    <?php } ?>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.2/js/bootstrapValidator.min.js"></script>
    
    <script>
      $(document).ready(function() {

        $("a[data-toggle=popover]")
        .popover()
        .click(function(e) {
          e.preventDefault()
        });
        
        $('#loginForm').bootstrapValidator({
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