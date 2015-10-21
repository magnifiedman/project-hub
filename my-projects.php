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
if(isset($_GET['projects'])){ $projectType = $_GET['projects']; } else { $projectType = 'in-progress'; }


// initiation
$u = new User();
$z = new Utility();
$p = new Project($z);
$errorMsg = '';
$successMsg = '';
$step=1;
$userLevel=$_COOKIE['userLevel'];
$userLogged=true;
$searchText = '';

// landmark
$z->setLandmark();

// get display for project type
switch($projectType){
  case 'in-progress':
  $projectTypeName='In Progress';
  break;
  case 'upcoming':
  $projectTypeName='Upcoming';
  break;
  case 'pending':
  $projectTypeName='Pending';
  break;
}


// get projects general
$projects = $p->getProjects($_COOKIE['userID'],$projectType,$page);


// project search results
if(isset($_POST['searchForm'])){
  if($projects = $p->getProjects($_COOKIE['userID'],'','',$_POST)){
    $successMsg = '<p class="panel padded"><span class="glyphicon glyphicon-plus"></span> Your note has been added.</p>';
  }
}


?>
    <?php include('inc/header.inc.php'); ?>

    <?php include('inc/nav-top.inc.php'); ?>

    <div class="container-fluid">
      
      <div class="row">
        <div class="col-sm-1">
        </div>
        <div class="col-sm-10 button-row">
        <a href="index.php" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> My Dashboard</a>
        <a href="add-project.php" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus"></span> Add Project</a>
        </div>
        <div class="col-sm-1">
        </div>
      </div>

      <!-- search projects -->
      <form action="search-results.php" role="form" id="addUser" method="post">
      <input type="hidden" name="searchForm" value="y" />
      
        <!-- filter box -->
        <div class="row">
          <div class="col-sm-1">
          </div>

          <div class="col-sm-10 panel padded">
           <div class="col-sm-12">
            <h4>PROJECT SEARCH BOX</h4>
          </div>

           <div class="col-sm-3">
            <div class="input-group fullwidth">
              &nbsp;<input type="text" name="searchStr" class="form-control" placeholder="Enter search text here" value="<?php echo ''; ?>" />
            </div>
          </div>
           <div class="col-sm-3">
            <div class="input-group fullwidth">
              <strong>Client:</strong> <?php $z->clientSelect(); ?>
            </div>
          </div>     
          <div class="col-sm-3">
            <div class="input-group fullwidth">
              <strong>Projects to Search:</strong> <select name="owner"class="form-control">
              <option value="2">All Projects</option>
              <option value="1">My Projects</option></select>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="input-group fullwidth">
           &nbsp; <button type="submit" name="search" class="form-control btn btn-primary" /><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Search Projects</button>
            </div>
          </div>

        </div>
          <div class="col-sm-1">
          </div>
        </div>

      </form>
      <!-- end search projects -->

      <div class="row">
        <div class="col-sm-1 main">
        </div>
        <div class="col-sm-10 main">
          <div class="pull-right">
          <a href="my-projects.php?projects=in-progress" id="add-note" class="btn <?php if($projectType=='in-progress'){ echo 'btn-primary'; } else { echo 'btn-default'; }  ?>">Currently Running</a>
          <a href="my-projects.php?projects=upcoming" id="add-note" class="btn <?php if($projectType=='upcoming'){ echo 'btn-primary'; } else { echo 'btn-default'; }  ?>">Starting Soon</a>
          <a href="my-projects.php?projects=pending" id="add-note" class="btn <?php if($projectType=='pending'){ echo 'btn-primary'; } else { echo 'btn-default'; } ?>">Pending</a>
        </div>
          <h3><span class="glyphicon glyphicon-star"></span> My Projects</h3>

           
            <?php
              if($projects==false){ echo '<p class="panel padded">You currently have no projects <strong>' . $projectTypeName . '</strong>.</p>'; }
              
              else {
                echo '<table class="table table-striped table-responsive">';
                echo '<tr>';
                echo '<th>Dates</th>';
                echo '<th>Project Name</th>';
                echo '<th>Stations</th>';
                echo '<th>Client</th>';
                echo '<th>A.E.</th>';
                echo '<th class="text-center">View/Edit</th>';
                echo '</tr>';
                foreach($projects['result'] as $project){
                  $stationNames = $p->getStationNamesOverview($project['stations']);
                  if($project['client_name']==''){ $project['client_name'] = 'N/A'; }
                  if($project['ae_fname']=='' && $project['ae_lname']==''){ $aeName = ''; }
                  else { $aeName = $p->getUserAETag($project['ae_id']); }
                  echo '<tr>';
                  echo '<td><em>' . date("m/d",strtotime($project['start_date'])) . ' - ' . date("m/d",strtotime($project['end_date'])) . '</em></td>';
                  echo '<td><a href="project-detail.php?id=' . $project['id'] .'">' . $project['project_name'] . '</a></td>';
                  echo '<td>' . $stationNames . '</td>';
                  echo '<td>' . $project['client_name'] . '</td>';
                  echo '<td>' . $aeName . '</td>';
                  echo '<td class="text-center"><a href="project-detail.php?id=' . $project['id'] . '"><span class="glyphicon glyphicon-eye-open"></span></a></td>';
                  echo '</tr>';
                  
                }

                echo '</table>';
              }
              
              $totalPages = ceil($projects['totalRecords']/RESULTS_PERPAGE);
              include('inc/pagination.inc.php');
            ?>

        </div>
       
        <div class="col-sm-1 main">
          
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

        $("a[data-toggle=popover]")
        .popover()
        .click(function(e) {
          e.preventDefault()
        });
        
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