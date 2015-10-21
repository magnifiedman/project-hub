<?php
include('lib/config.inc.php');
include('lib/classes/base.class.php');
include('lib/classes/user.class.php');
include('lib/classes/project.class.php');
include('lib/classes/utility.class.php');


// kick them out
if(!isset($_COOKIE['userLogged'])){ header("Location: index.php"); }
if(isset($_COOKIE['newProjectID'])){ $projectID = $_COOKIE['newProjectID']; }
if(!isset($_GET['id'])){ header("Location: add-project.php");  }
if(isset($_GET['id'])){ $projectID = $_GET['id']; }


// initiation
$u = new User();
$z = new Utility();
$p = new Project($z);

$error='';
$msg='';
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
$noteError='';
$showOnlineModal='';
$showOnAirModal='';
$showOnSiteModal='';





// form submitted - project - add online details
if(isset($_POST['updateStatus'])){
  if($p->doProject('update',$_POST,'status')){
    $msg = '<p class="bg-success padded"><span class="glyphicon glyphicon-thumbs-up"></span> Details Updated.</p>';
  }
  else {
    $error = '<p class="bg-danger padded">* There was an error updating the project. Please contact mission control.</p>';
  }
}


// form submitted - project - add online details
if(isset($_POST['updatePending'])){
  if($p->doProject('update',$_POST,'pending')){
    $msg = '<p class="bg-success padded"><span class="glyphicon glyphicon-thumbs-up"></span> Details Updated.</p>';
  }
  else {
    $error = '<p class="bg-danger padded">* There was an error updating the project. Please contact mission control.</p>';
  }
}


// form submitted - project - add on-air details
if(isset($_POST['onAirUpdate'])){
  if($p->doProject('update',$_POST,'on-air')){
    $oauText = '<p class="bg-success padded" id="oau"><span class="glyphicon glyphicon-thumbs-up"></span> On-Air Details Updated.</p>';
  }
  else {
    $error = '<p class="bg-danger padded">* There was an error adding the project. Please contact mission control.</p>';
  }
}


// form submitted - project - add on-site details
if(isset($_POST['onSiteUpdate'])){
  if($p->doProject('update',$_POST,'on-site')){
    $os=true;
    $osuText = '<p class="bg-success padded" id="osu"><span class="glyphicon glyphicon-thumbs-up"></span> On-Site Details Updated.</p>';
  }
  else {
    $error = '<p class="bg-danger padded">* There was an error adding the project. Please contact mission control.</p>';
  }
}


// form submitted - project - add online details
if(isset($_POST['onLineUpdate'])){
  if($p->doProject('update',$_POST,'online')){
    $ol=true;
    $oluText = '<p class="bg-success padded" id="olu"><span class="glyphicon glyphicon-thumbs-up"></span> Online Details Updated.</p>';
  }
  else {
    $error = '<p class="bg-danger padded">* There was an error adding the project. Please contact mission control.</p>';
  }
}


// note added
if(isset($_POST['addNote'])){
  if($p->doNote($_POST)){
    $msg = '<p class="bg-success padded"><span class="glyphicon glyphicon-thumbs-up"></span> Note added.</p>';
  }
  else {
    $noteError = true;
    $error = '<p class="bg-danger padded">* There was an error adding the project. Please contact mission control.</p>';
  }
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


$project = $p->getProjectDetails($projectID);
$creativeUsers = unserialize($project['creative_users']);
$stations = unserialize($project['stationArray']);
$projectNotes = $p->getProjectNotes($projectID);
$projectStatusHTML = $z->getStatusHTML('project',$project['type_id'],$_COOKIE['userType'],$project['status']);
$projectUsers = $z->getProjectUsers($projectID);
//echo '<pre>';
//print_r($projectUsers);
?>
  <?php include('inc/header.inc.php'); ?>

  <?php include('inc/nav-top.inc.php'); ?>

  <div class="container-fluid">

    <div class="row">
      <div class="col-sm-1">
      </div>
      <div class="col-sm-10 button-row">
      <a href="<?php echo $_COOKIE['landmark']; ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Back</a>
      </div>
      <div class="col-sm-1">
      </div>
    </div>

    
    <!-- intro form -->
    <div class="row">
      
      <div class="col-sm-1 main">
      </div>

      <div class="col-sm-10 main panel">
        
        <?php echo $msg; ?>
        <h3 class="no-border"><span class="text-muted">Project</span> : <?php echo $project['project_name']; ?><span class="pull-right"><small><span class="glyphicon glyphicon-user"></span> <?php echo date("M d @ H:ia",strtotime($project['date_entered'])); ?> by <a href="#" data-toggle="popover" title="<?php echo $project['fname'].' '.$project['lname']; ?>" data-placement="left" data-content="Ext. <?php echo substr($project['office_phone'],-4); ?>"><?php echo $project['fname'].' '.substr($project['lname'],0,1); ?></a>.</small></span></h3>

        <div class="form-group clearfix">

        <?php
        // pending project
        if($project['status']==1){ ?>

          <div class="col-sm-12 main">

          <h3>Project Spec</h3>
          <form action="" role="form" id="addUser" method="post">
          <input type="hidden" name="updatePending" value="y" />
          <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
          <input type="hidden" name="creatives_current" value="<?php echo htmlentities(stripslashes($project['creative_users'])); ?>" />
          <input type="hidden" name="current_status_id" value="<?php echo $project['status']; ?>" />      

          <?php echo $error; ?>

          <div class="form-group clearfix">

            <div class="col-sm-8 clearfix">
            <p><label>Project Name:</label>
            <input type="text" name="project_name" class="form-control" placeholder="Project Name" value="<?php echo stripslashes($project['project_name']); ?>" required /></p>
            </div> 

            <div class="col-sm-4 clearfix">
            <p><label>Type of Project:</label>
            <?php $z->projectTypeSelect($project['type_id']); ?></p>
            </div> 

            <div class="col-sm-6 clearfix">
            <p><label>Start Date:</label>
            <input type="text" name="start_date" class="datepicker form-control" placeholder="Click to Enter" value="<?php echo date("m-d-Y",strtotime($project['start_date'])); ?>" required /></p>
            </div> 

            <div class="col-sm-6 clearfix">
            <p><label>End Date:</label>
            <input type="text" name="end_date" class="datepicker form-control" placeholder="Click to Enter" value="<?php echo date("m-d-Y",strtotime($project['end_date'])); ?>" required /></p>
            </div> 

            

            <div class="col-sm-6">
            <p><label>Stations:</label><br />
            <?php $z->stationCheckboxes($stations); ?></p>
            </div> 

            <div class="col-sm-6 clearfix">
            <p><label>A.E. (if applicable):</label>
            <?php $z->aeSelect($project['ae_id']); ?></p>
            </div> 

            

            <div class="col-sm-9 clearfix">
            <p><label>Client (if applicable):</label>
            <?php $z->clientSelect($project['client_id']); ?></p>
            </div> 
            <div class="col-sm-3">
            <p><label>&nbsp;</label><br />
            <a href="#" data-toggle="modal" data-target="#addClientModal" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-plus"></span> Add Client</a></p>
            </div>

            <div class="col-sm-12 clearfix">
            <p><label>Assign to Creative?</label><br />
            <?php $z->creativeUsersCheckboxes($creativeUsers); ?></p>
            </div> 

          

            <div class="col-sm-12 clearfix">
              <label>Budget:</label>
              <div class="input-group">
              <div class="input-group-addon">$</div>
              <input type="number" value="<?php echo $project['budget']; ?>" required="" data-bv-digits-message="" placeholder="Enter budget in dollars" class="form-control" name="budget" data-bv-field="budget">
              </div> 
            </div>

               </div> 

            <div class="col-sm-12 clearfix">
              <p><label>Goal:</label><br />
              <textarea name="goal" class="form-control"><?php echo $project['goal']; ?></textarea></p>
            </div>

            <div class="col-sm-12 clearfix">
              <p><label>Brainstorm Ideas:</label><br />
              <textarea name="brainstorm_ideas" class="form-control"><?php echo $project['brainstorm_ideas']; ?></textarea></p>
            </div>   

            <div class="col-sm-12 clearfix">
              <p><label>Agreed Upon Ideas:</label><br />
              <textarea name="agreed_upon_ideas" class="form-control"><?php echo $project['agreed_upon_ideas']; ?></textarea></p>
            </div>     
                    
            
            
          <div class="form-group clearfix">
            <div class="col-sm-9">
                <p class=""><label>Project Status:</label><br />
                  <?php echo $projectStatusHTML; ?></p>
            </div>  
            <div class="col-sm-3 clearfix">
              <p><label>&nbsp;</label><br />
              <input type="submit" name="add" value="Update Project" class="btn btn-primary btn-lg btn-block"></p>
            </div>
          </div>
          
          </form>
          
        </div>
          
        <?php }
        // approved project
        else { 
     
          $creatives = '';
          if(!empty($creativeUsers)){ $creatives = 'y'; }
          $creatives_current = serialize($creativeUsers);


          // get html
          
          $programmingStatusHTML = $z->getStatusHTML('programming',$project['type_id'],$_COOKIE['userType'],$project['programming_status']);
          $creativeStatusHTML = $z->getStatusHTML('creative',$project['type_id'],$_COOKIE['userType'],$project['creative_status'],$creatives);
          $dfpStatusHTML = $z->getStatusHTML('dfp',$project['type_id'],$_COOKIE['userType'],$project['dfp'],$creatives);
          $statusButtonHTML = $z->getStatusHTML('button',$project['type_id'],$_COOKIE['userType'],'','');
          
          ?>

          <form action="" method="post">
          <input type="hidden" name="updateStatus" value="y" />
          <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
          <input type="hidden" name="creatives_current" value="<?php echo htmlentities($creatives_current); ?>" />
          <input type="hidden" name="type_id" value="<?php echo $project['type_id']; ?>" />
          <input type="hidden" name="current_creative_status_id" value="<?php echo $project['creative_status']; ?>" /> 
          <input type="hidden" name="current_programming_status_id" value="<?php echo $project['programming_status']; ?>" /> 
          <input type="hidden" name="current_status_id" value="<?php echo $project['status']; ?>" />      

          <!-- status boxes -->
          <div class="col-sm-12 panel clearfix top-pad">       
              <div class="col-sm-3">
                <p><label>Project Status:</label><br /><?php echo $projectStatusHTML; ?></p>
              </div>  
            

              <div class="col-sm-3">
                <p><label>Programming Status:</label><br /><?php echo $programmingStatusHTML; ?></p>
              </div>

              <div class="col-sm-3">
                <p><label>Creative Status:</label><br /><?php echo $creativeStatusHTML; ?></p>
              </div> 

              <div class="col-sm-2 text-center">
              <p><label>Loaded in DFP</label><br /><?php echo $dfpStatusHTML; ?></p>
             </div> 

             
            
              <div class="col-sm-12 clearfix border-top">
             <p><label>Assign Creatives:</label><br /><?php $z->creativeUsersCheckboxes($creativeUsers); ?></p>
              </div>
              
              <div class="col-sm-10">
              </div>


              <div class="col-sm-2 clearfix text-center">
              <?php echo $statusButtonHTML; ?>
             </div>

          </div>
          <!-- status boxes -->

          </form>

       
          <div class="col-md-12">
            <h4>OVERVIEW</h4>
          </div> 

          <div class="col-sm-6 top-pad">
            <p><label>Run Dates:</label> <?php echo date("M d, Y",strtotime($project['start_date'])); ?> - <?php echo date("M d, Y",strtotime($project['end_date'])); ?></p>
          </div> 

          <div class="col-sm-6 top-pad clearfix">
            <p><label>Project Type:</label> <?php echo $project['project_type']; ?></p>
          </div> 


          <div class="col-sm-6">
            <p><label>Stations:</label> <?php echo $project['stations']; ?></p>
          </div> 

          <div class="col-sm-6 clearfix">
            <p><label>A.E.:</label> <?php echo $project['ae']; ?></p>
          </div>   

          <div class="col-sm-6">
            <p><label>Client:</label> <?php echo $project['name']; ?></p>
          </div> 

          <div class="col-sm-6 clearfix">
            <p><label>Budget:</label> $<?php echo $project['budget']; ?></p>
          </div>
            
          <div class="col-sm-12 clearfix">
            <p><strong>Goal:</strong> <?php echo $project['goal']; ?></p>
          </div>


          
          <div class="col-sm-6 clearfix">
            
          </div>

          

          <div class="col-sm-12 clearfix">
            <p><strong>Brainstorm Ideas:</strong> <?php echo $project['brainstorm_ideas']; ?></p>
          </div>   

          <div class="col-sm-12 clearfix">
            <p><strong>Agreed Upon Ideas:</strong> <?php echo $project['agreed_upon_ideas']; ?></p>
          </div>

        <?php } ?>

        

          <a id="form"></a>
          
          <div class="col-md-12 clerafix">
            <h4>NOTES</h4>
          </div>   
        
          <div class="col-md-3 clearfix">
            <a href="javascript:void();" id="showNotes" class="btn btn-default active btn-block"><span class="glyphicon glyphicon-list-alt"></span> <span class="noteText">Show Notes</span></a>
          </div>
          <div class="col-md-3 clearfix">
            <a href="#" data-toggle="modal" data-target="#addNoteModal" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-plus"></span> Add Note</a>
          </div> 
        
    

          <div class="col-sm-12 clearfix hr" id="notes" style="display:none;">
          <?php if(is_array($projectNotes)){
            foreach($projectNotes as $note){
              echo '<div class="col-sm-12 clearfix notes">';
              echo '<div class="col-sm-3"><p><strong>' . date("M d,Y @ h:ia",strtotime($note['date_entered'])) . '</strong><br /><em>in ' . $note['noteType'] . '<br /><a href="#" data-toggle="popover" title="'. $note['fname'].' '.$note['lname'] . '" data-placement="bottom" data-content="Ext. '. substr($note['office_phone'],-4) . '">' . $note['fname'].' '.substr($note['lname'],0,1) . '.</a></em></div>';
              echo '<div class="col-sm-9 clearfix"><p>' . stripslashes(nl2br($note['note'])) . '</p></div>';
              echo '</div>';
            }
          } 
          else {
            echo '<p>* No project notes.</p>';
          }
          ?>
          </div>

        </div> 
        <!-- end form group -->
            
        </div>
        <!-- end 10 col -->
    
      <div class="col-sm-1 main">  
      </div>
      <!-- end 2 col -->

    </div>
    <!-- end intro form -->
    
    <!-- detail form header -->
    <div class="row">
      <div class="col-sm-1 main">  
      </div>
      <div class="col-sm-10 main">
       

          <div class="col-sm-12">
          <h4>DETAILS</h4>
          </div>
      

      </div>
      <div class="col-sm-1 main">  
      </div>
    </div>
     
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
            <p class="bg-success padded">On-Air Files have been uploaded.</p>
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
    <!-- end add file modal -->

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
            <p class="bg-success padded">On-Site Files have been uploaded.</p>
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
    <!-- end add file modal -->

    <!-- add a file modal - online -->
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
            <p class="bg-success padded">Online Files have been uploaded.</p>
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
    <!-- end add file modal -->

    <!-- add a note modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">Project : <span class="text-primary"><?php echo $project['project_name']; ?></span> : <span class="text-muted">Add Note</span></h4>
          </div>
          <form id="addFileOnlineForm" action="" method="post" class="theForm">
            
          <div class="modal-body">
           <?php echo $error; ?>
            <input type="hidden" name="addNote" value="y" />
            <input type="hidden" name="project_id" value="<?php echo $_GET['id']; ?>" />
            <input type="hidden" name="user_id" value="<?php echo $_COOKIE['userID']; ?>" />
            <div class="form-group clearfix">
            <label>This note is for:</label>
            <?php $p->noteTypeSelecter(); ?>
          </div>
          <div class="form-group clearfix">
            <label>Add Your Note:</label>
            <textarea class="form-control" rows="5" name="note" required></textarea>
          </div>
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-primary" value="Submit" />
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

        $( ".datepicker" ).datepicker({
          inline: true,
          dateFormat: "mm-dd-yy"
        });

        $("a[data-toggle=popover]")
        .popover()
        .click(function(e) {
          e.preventDefault()
        });

        <?php if($showModal==true){ ?>$('#addClientModal').modal('show'); <?php } ?>
        <?php if($noteError==true){ ?>$('#addNoteModal').modal('show'); <?php } ?>
        
        <?php if($showOnlineModal==true){ ?>$('#addFileOnlineModal').modal('show'); <?php } ?>
        <?php if($showOnSiteModal==true){ ?>$('#addFileOnSiteModal').modal('show'); <?php } ?>
        <?php if($showOnAirModal==true){ ?>$('#addFileOnAirModal').modal('show'); <?php } ?>

        $("#showNotes").click(function(){
          $('.noteText').text($('.noteText').text() == 'Show Notes' ? 'Hide Notes' : 'Show Notes');  
          $("#notes").slideToggle("slow")

        });

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