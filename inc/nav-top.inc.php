<!-- navigation -->
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php">iHeartMedia Project Hub</a>
    </div>
     
    <div class="navbar-collapse collapse">

         
      <?php if(isset($userLogged)){ ?>
      <ul class="nav navbar-nav navbar-right">
        <li class="nav-item dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">PROJECTS</a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="my-projects.php">MY PROJECTS</a></li>
            <li><a href="add-project.php">ADD PROJECT</a></li>
          </ul>
        </li>
        <li class="pipe"><a>|</a></li>
        <li class="nav-item"><a href="my-notifications.php">NOTIFICATIONS</a></li>
        <li class="pipe"><a>|</a></li>
        <!-- <li class="nav-item"><a href="my-calendar.php">CALENDAR</a></li>
        <li class="pipe"><a>|</a></li> -->
        <li class="nav-item"><a href="my-notepad.php">NOTEPAD</a></li>
        <li class="pipe"><a>|</a></li>
        <li class="nav-item dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">MY ACCOUNT</a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="my-profile.php">UPDATE PROFILE</a></li>
            <li><a href="logout.php">LOGOUT</a></li>
          </ul>
        </li>
        <?php if($userLevel==1){ ?>
          <li class="dropdown admin">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">ADMIN</a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="add-user.php">ADD USER</a></li>
              <li><a href="user-overview.php">USER OVERVIEW</a></li>
            </ul>
          </li>
        <?php } ?>
      </ul>
      <?php } ?>
      
    </div>
  </div>
</div>