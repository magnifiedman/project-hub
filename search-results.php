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
$projects='';
$step=1;
$userLevel=$_COOKIE['userLevel'];
$userLogged=true;
$searchText = '';
$searchArray = array();
if(isset($_COOKIE['searchArray'])){ $searchArray = unserialize(stripslashes($_COOKIE['searchArray'])); }


// project search results
if(isset($_POST['searchForm'])){
  if($_POST['owner']==2 && $_POST['client_id']=='' && $_POST['searchStr']==''){
    $searchText = '<p class="search-text">If you are going to search <strong>ALL PROJECTS</strong>, please do a more detailed search.</p>';
  }
  else {

    // clean search cookie
    setcookie('searchArray','',-3600);

    // get projects
    $projects = $p->getProjects($_COOKIE['userID'],'',$page,$_POST);
      $searchText = $z->getSearchText($_POST);
      $searchArray = $_POST;
  }
  
}

else {
  // get projects general
  $projects = $p->getProjects($_COOKIE['userID'],'',$page,$searchArray);
  $searchText = $z->getSearchText($searchArray);
}



?>
    <?php include('inc/header.inc.php'); ?>

    <?php include('inc/nav-top.inc.php'); ?>

    <div class="container-fluid">
      
      <div class="row">
        <div class="col-sm-1">
        </div>
        <div class="col-sm-10 button-row">
        <a href="my-projects.php" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> My Projects</a>
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
              &nbsp;<input type="text" name="searchStr" class="form-control" placeholder="Enter search text here" value="<?php echo stripslashes(@$searchArray['searchStr']); ?>" />
            </div>
          </div>
           <div class="col-sm-3">
            <div class="input-group fullwidth">
              <strong>Client:</strong> <?php $z->clientSelect(@$searchArray['client_id']); ?>
            </div>
          </div>
          <?php
          $mp='';
          $ap='';
          if(@$searchArray['owner']==1){ $mp = 'selected="selected"'; }
          if(@$searchArray['owner']==2){ $ap = 'selected="selected"'; }  
          ?>  
          <div class="col-sm-3">
            <div class="input-group fullwidth">
              <strong>Projects to Search:</strong> <select name="owner"class="form-control">
              <option value="2" <?php echo $ap; ?>>All Projects</option>
              <option value="1" <?php echo $mp; ?>>My Projects</option></select>
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
          <?php echo $searchText; ?>
          <h3><span class="glyphicon glyphicon-star"></span> Search Results</h3>

           
            <?php
              if($projects==false){ echo '<p class="panel padded">No projects match search.</p>'; }
              
              else {
                $nowTime = date("Y-m-d");

                echo '<table class="table table-striped table-responsive">';
                echo '<tr>';
                echo '<th>Dates</th>';
                echo '<th>Project Name</th>';
                echo '<th>Stations</th>';
                echo '<th>Client</th>';
                echo '<th>A.E.</th>';
                echo '<th>Project Status</th>';
                echo '<th class="text-center">View/Edit</th>';
                echo '</tr>';
                foreach($projects['result'] as $project){
                  $stationNames = $p->getStationNamesOverview($project['stations']);
                  if($project['status']==2 && $project['start_date']<=$nowTime && $project['end_date']>=$nowTime){ $projectStatus = 'In-Progress'; }
                  if($project['status']==2 && $project['start_date']>$nowTime){ $projectStatus = 'Upcoming'; }
                  if($project['status']==2 && $project['start_date']<$nowTime && $project['end_date']<$nowTime){ $projectStatus = 'Completed'; }
                  if($project['status']==1){ $projectStatus = 'Pending'; }
                  if($project['client_name']==''){ $project['client_name'] = 'N/A'; }
                  if($project['ae_fname']=='' && $project['ae_lname']==''){ $aeName = ''; }
                  else { $aeName = $p->getUserAETag($project['ae_id']); }
                  echo '<tr>';
                  echo '<td><em>' . date("m/d",strtotime($project['start_date'])) . ' - ' . date("m/d",strtotime($project['end_date'])) . '</em></td>';
                  echo '<td><a href="project-detail.php?id=' . $project['id'] .'">' . $project['project_name'] . '</a></td>';
                  echo '<td>' . $stationNames . '</td>';
                  echo '<td>' . $project['client_name'] . '</td>';
                  echo '<td>' . $aeName . '</td>';
                  echo '<td>' . $projectStatus . '</td>';
                  echo '<td class="text-center"><a href="project-detail.php?id=' . $project['id'] . '"><span class="glyphicon glyphicon-eye-open"></span></a></td>';
                  echo '</tr>';
                  
                }

                echo '</table>';
                $totalPages = ceil($projects['totalRecords']/RESULTS_PERPAGE);
                include('inc/pagination.inc.php');
              }

              

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