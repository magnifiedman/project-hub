<?php
include('lib/config.inc.php');
include('lib/classes/base.class.php');
include('lib/classes/user.class.php');
include('lib/classes/project.class.php');
include('lib/classes/utility.class.php');


// kick them out
if(!isset($_COOKIE['userLogged'])){ header("Location: index.php"); }
if(isset($_COOKIE['newProjectID'])){ $projectID = $_COOKIE['newProjectID']; }


// initiation
$u = new User();
$z = new Utility();
$p = new Project($z);
$error='';
$showModal='';
$fileAdded='';
$osuText='';
$oauText='';
$oluText='';
$os=false;
$ol=false;
$step=1;
$userLevel=$_COOKIE['userLevel'];
$userLogged=true;


// form submitted - add project - step 1
if(isset($_POST['addProject'])){
  if($projectID = $p->doProject('add',$_POST)){
    setcookie('newProjectID',$projectID);
    $project = $p->getProjectDetails($projectID);
  }
  else {
    $error = '<p class="bg-danger padded">* There was an error creating the project. Please contact mission control.</p>';
  }
}


// form submitted - project - add on-air details
if(isset($_POST['onAirUpdate'])){
  if($p->doProject('update',$_POST,'on-air')){
    $oauText = '<p class="bg-success padded"><span class="glyphicon glyphicon-thumbs-up"></span> On-Air Details Updated.</p>';
  }
  else {
    $error = '<p class="bg-danger padded">* There was an error adding the project. Please contact mission control.</p>';
  }
}


// form submitted - project - add on-site details
if(isset($_POST['onSiteUpdate'])){
  if($p->doProject('update',$_POST,'on-site')){
    $os=true;
    $osuText = '<p class="bg-success padded"><span class="glyphicon glyphicon-thumbs-up"></span> On-Site Details Updated.</p>';
  }
  else {
    $error = '<p class="bg-danger padded">* There was an error adding the project. Please contact mission control.</p>';
  }
}


// form submitted - project - add online details
if(isset($_POST['onLineUpdate'])){
  if($p->doProject('update',$_POST,'online')){
    $ol=true;
    $oluText = '<p class="bg-success padded"><span class="glyphicon glyphicon-thumbs-up"></span> Online Details Updated.</p>';
  }
  else {
    $error = '<p class="bg-danger padded">* There was an error adding the project. Please contact mission control.</p>';
  }
}


// form submitted - add client to system
if(isset($_POST['addClient'])){
  if($p->addClient(@$_POST)){ $showModal=true; }
}

// note added
if(isset($_POST['addFile'])){
  if($p->doFile($_POST)){
    $fileAdded = true;
    switch($_POST['group_id']){
      case 1:
      $showOnlineModal = true;
      break;

      case 2:
      $showOnAirModal = true;
      break;

      case 3:
      $showOnSiteModal = true;
      break;
    }
  }
  else {
    $fileError = true;
    $error = '<p class="bg-danger padded">* There was an error adding the project. Please contact mission control.</p>';
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
      <a href="index.php" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Dashboard</a>
      </div>
      <div class="col-sm-1">
      </div>
    </div>

    
    <!-- intro form -->
    <div class="row">
      
      <div class="col-sm-1 main">
      </div>

      <?php if(!isset($projectID)){ ?>
      
      <div class="col-sm-10 main panel">

        <h3>Create Project</h3>
        <form action="" role="form" id="addUser" method="post">
        <input type="hidden" name="addProject" value="y" />
        <input type="hidden" name="author_id" value="<?php echo $_COOKIE['userID']; ?>" />
        <?php echo $error; ?>

        <div class="form-group clearfix">

          <div class="col-sm-8 clearfix">
          <p><label>Project Name:</label>
          <input type="text" name="project_name" class="form-control" placeholder="Project Name" required /></p>
          </div> 

          <div class="col-sm-4 clearfix">
          <p><label>Type of Project:</label>
          <?php $z->projectTypeSelect(); ?></p>
          </div> 

          <div class="col-sm-6 clearfix">
          <p><label>Start Date:</label>
          <input type="text" name="start_date" class="datepicker form-control" placeholder="Click to Enter" required /></p>
          </div> 

          <div class="col-sm-6 clearfix">
          <p><label>End Date:</label>
          <input type="text" name="end_date" class="datepicker form-control" placeholder="Click to Enter" required /></p>
          </div> 

          

          <div class="col-sm-6">
          <p><label>Stations:</label><br />
          <?php $z->stationCheckboxes(); ?></p>
          </div> 

          <div class="col-sm-6 clearfix">
          <p><label>A.E. (if applicable):</label>
          <?php $z->aeSelect($_COOKIE['userID']); ?></p>
          </div> 

          

          <div class="col-sm-9 clearfix">
          <p><label>Client (if applicable):</label>
          <?php $z->clientSelect(); ?></p>
          </div> 
          <div class="col-sm-3">
          <p><label>&nbsp;</label><br />
          <a href="#" data-toggle="modal" data-target="#addClientModal" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-plus"></span> Add Client</a></p>
          </div>

          <div class="col-sm-12 clearfix">
          <p><label>Assign to Creative?</label><br />
          <?php $z->creativeUsersCheckboxes(); ?></p>
          </div> 

        

          <div class="col-sm-12 clearfix">
            <label>Budget:</label>
            <div class="input-group">
            <div class="input-group-addon">$</div>
            <input type="number" required="" data-bv-digits-message="" placeholder="Enter budget in dollars" class="form-control" name="budget" data-bv-field="budget">
            </div> 
          </div>

             </div> 

          <div class="col-sm-12 clearfix">
            <p><label>Goal:</label><br />
            <textarea name="goal" class="form-control"></textarea></p>
          </div>

          <div class="col-sm-12 clearfix">
            <p><label>Brainstorm Ideas:</label><br />
            <textarea name="brainstorm_ideas" class="form-control"></textarea></p>
          </div>   

          <div class="col-sm-12 clearfix">
            <p><label>Agreed Upon Ideas:</label><br />
            <textarea name="agreed_upon_ideas" class="form-control"></textarea></p>
          </div>     
                  
          
          
        <div class="form-group clearfix">
          <div class="col-sm-12 clearfix">
          <input type="submit" name="add" value="Create Project" class="btn btn-primary btn-lg">
          </div>
        </div>
        
        </form>
        
      </div>

      <?php } 

      else { 
        $project = $p->getProjectDetails($projectID);
      ?>

      <div class="col-sm-10 main">

        <h3><span class="text-muted">Project Details</span> : <?php echo $project['project_name']; ?><span class="pull-right"><small><span class="glyphicon glyphicon-user"></span> <?php echo date("M d @ H:ia",strtotime($project['date_entered'])); ?> by <a href="#" data-toggle="popover" title="<?php echo $project['fname'].' '.$project['lname']; ?>" data-placement="bottom" data-content="Extension <?php echo substr($project['office_phone'],-4); ?>"><?php echo $project['fname'].' '.substr($project['lname'],0,1); ?></a>.</small></span></h3>

        <div class="form-group clearfix">   
          <div class="col-sm-6">
            <p><label>Run Dates:</label> <?php echo date("M d, Y",strtotime($project['start_date'])); ?> - <?php echo date("M d, Y",strtotime($project['end_date'])); ?></p>
          </div> 

          <div class="col-sm-6">
            <p><label>Stations:</label> <?php echo $project['stations']; ?></p>
          </div> 

          <div class="col-sm-6 clearfix">
            <p><label>A.E.:</label> <?php echo $project['ae']; ?></p>
          </div>   

          <div class="col-sm-6 clearfix">
            <p><label>Client:</label> <?php echo $project['name']; ?></p>
          </div> 

          <div class="col-sm-6 clearfix">
            <p><label>Budget:</label> $<?php echo $project['budget']; ?></p>
          </div>

          <div class="col-sm-6">
            <p><label>Goal:</label> <?php echo $project['goal']; ?></p>
          </div>

          <div class="col-sm-12 clearfix">
            <p><label>Brainstorm Ideas:</label> <?php echo $project['brainstorm_ideas']; ?></p>
          </div>   

          <div class="col-sm-12 clearfix">
            <p><label>Agreed Upon Ideas:</label> <?php echo $project['agreed_upon_ideas']; ?></p>
          </div>     
            
        </div>

      </div> 

      <?php } ?>
     
      <div class="col-sm-1 main">  
      </div>

    </div>
    <!-- end intro form -->


    <?php include('inc/project-form.inc.php'); ?>

    </div>


    <!-- add a client modal -->
    <div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">Project Hub: <span class="text-muted">Add a New Client</span></h4>
          </div>
          <form id="clientForm" action="" method="post">
          <div class="modal-body">
           <?php if($showModal==true){ ?> 
            <p class="bg-success padded">Client has been created.</p>
            <?php } else { ?>
            
            <input type="hidden" name="addClient" value="y" />
            <label>Client Name:</label>
            <input name="client_name" type="text" class="form-control" placeholder="Enter Client Name">
            <?php } ?>
          </div>
          <div class="modal-footer">
            <?php if($showModal==true){ ?><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><?php } else { ?>
            <input type="submit" class="btn btn-primary" value="Submit" /><?php } ?>
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- end add client modal -->

    <!-- add a file modal - on-air -->
    <div class="modal fade" id="addFileOnAirModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">Project : <span class="text-primary"><?php echo $project['project_name']; ?></span> : <span class="text-muted">Add File(s)</span></h4>
          </div>
          <form id="addFileOnAirForm" action="" method="post" enctype="multipart/form-data">
          <div class="modal-body">
           <?php if($fileAdded==true){ ?> 
            <p class="bg-success padded">Files have been uploaded.</p>
            <?php } ?>
            
            <input type="hidden" name="addFile" value="y" />
            <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
            <input type="hidden" name="project_id" value="<?php echo $projectID; ?>" />
            <input type="hidden" name="group_id" value="1" />
            <label>Add Your File(s):</label>
            <input name="file1" type="file">
            <input name="file2" type="file">
            <input name="file3" type="file">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-primary" value="Upload Now" />
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- end add client modal -->

    <!-- add a file modal - on-site -->
    <div class="modal fade" id="addFileOnSiteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">Project : <span class="text-primary"><?php echo $project['project_name']; ?></span> : <span class="text-muted">Add File(s)</span></h4>
          </div>
          <form id="addFileOnSiteForm" action="" method="post" enctype="multipart/form-data">
          <div class="modal-body">
           <?php if($fileAdded==true){ ?> 
            <p class="bg-success padded">Files have been uploaded.</p>
            <?php } ?>
            
            <input type="hidden" name="addFile" value="y" />
            <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
            <input type="hidden" name="project_id" value="<?php echo $projectID; ?>" />
            <input type="hidden" name="group_id" value="2" />
            <label>Add Your File(s):</label>
            <input name="file1" type="file">
            <input name="file2" type="file">
            <input name="file3" type="file">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-primary" value="Upload Now" />
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- end add client modal -->

    <!-- add a file modal - on-site -->
    <div class="modal fade" id="addFileOnlineModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">Project : <span class="text-primary"><?php echo $project['project_name']; ?></span> : <span class="text-muted">Add File(s)</span></h4>
          </div>
          <form id="addFileOnlineForm" action="" method="post" enctype="multipart/form-data">
          <div class="modal-body">
           <?php if($fileAdded==true){ ?> 
            <p class="bg-success padded">Files have been uploaded.</p>
            <?php } ?>
            
            <input type="hidden" name="addFile" value="y" />
            <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
            <input type="hidden" name="project_id" value="<?php echo $projectID; ?>" />
            <input type="hidden" name="group_id" value="3" />
            <label>Add Your File(s):</label>
            <input name="file1" type="file">
            <input name="file2" type="file">
            <input name="file3" type="file">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-primary" value="Upload Now" />
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- end add client modal -->


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/tooltip.js"></script>
    <script src="js/popover.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.2/js/bootstrapValidator.min.js"></script>
    
    <script>
      $(document).ready(function() {

        var dateToday = new Date();
        $( ".datepicker" ).datepicker({
          inline: true,
          dateFormat: "mm-dd-yy",
          minDate: dateToday
        });

        $("a[data-toggle=popover]")
        .popover()
        .click(function(e) {
          e.preventDefault()
        });

        <?php if($showModal==true){ ?>$('#addClientModal').modal('show'); <?php } ?>

        <?php if($os==true){ ?>
          $('.on-air-form').hide();
          $('.on-site-form').show();
        <?php } ?>
        <?php if($ol==true){ ?>
          $('.on-air-form').hide();
          $('.online-form').show(); 
        <?php } ?>
        <?php if(isset($projectID)){ ?>
          $('.maincat-nav').show();
          $('.on-air-form').show();
          $('.on-air-icon').show();
          $('.on-site-icon').hide();
          $('.online-icon').hide();
        <?php } ?>

         <?php if($os==true){ ?>
          $('.on-air-form').hide();
          $('.on-site-form').show();
          $('.on-air-icon').hide();
          $('.on-site-icon').show();
          $('#on-air-button').removeClass('btn-primary');
          $('#on-air-button').addClass('btn-default');
          $('#on-site-button').removeClass('btn-default');
          $('#on-site-button').addClass('btn-primary'); 
        <?php } ?>
        <?php if($ol==true){ ?>
          $('.on-air-form').hide();
          $('.online-form').show(); 
          $('.on-line-icon').hide();
          $('.on-site-icon').show();
          $('#on-air-button').removeClass('btn-primary');
          $('#on-air-button').addClass('btn-default');
          $('#online-button').removeClass('btn-default');
          $('#online-button').addClass('btn-primary');  
        <?php } ?>
        
        $('#addProject').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            submitButtons: 'button[type="submit"]'
        });

        $('#on-air-button').click(function(){
          
          $('.on-site-form').hide();
          $('.on-site-icon').hide();
          $('#on-site-button').removeClass('btn-primary');
          $('#on-site-button').addClass('btn-default');

          $('.online-form').hide();
          $('.online-icon').hide();
          $('#online-button').removeClass('btn-primary');
          $('#online-button').addClass('btn-default');

          $('.on-air-form').show();
          $('.on-air-icon').show();
          $('#on-air-button').removeClass('btn-default');
          $('#on-air-button').addClass('btn-primary'); 
          
        });

        $('#on-site-button').click(function(){
          
          $('.on-air-form').hide();
          $('.on-air-icon').hide();
          $('#on-air-button').removeClass('btn-primary');
          $('#on-air-button').addClass('btn-default');

          $('.online-form').hide();
          $('.online-icon').hide();
          $('#online-button').removeClass('btn-primary');
          $('#online-button').addClass('btn-default');

          $('.on-site-form').show();
          $('.on-site-icon').show();
          $('#on-site-button').removeClass('btn-default');
          $('#on-site-button').addClass('btn-primary');
            
        });

        $('#online-button').click(function(){
          
          $('.on-air-form').hide();
          $('.on-air-icon').hide();
          $('#on-air-button').removeClass('btn-primary');
          $('#on-air-button').addClass('btn-default');

          $('.on-site-form').hide();
          $('.on-site-icon').hide();
          $('#on-site-button').removeClass('btn-primary');
          $('#on-site-button').addClass('btn-default');

          $('.online-form').show();
          $('.online-icon').show();
          $('#online-button').removeClass('btn-default');
          $('#online-button').addClass('btn-primary');   
          
        });

      });
    </script>
  </body>
</html>