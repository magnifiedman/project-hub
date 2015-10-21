<!-- pagination -->
<nav>
  <ul class="pagination pull-right">

	<?php
	$i=0;
	$preVars='';
	if(substr_count($_SERVER['QUERY_STRING'],'projects')>0){ $preVars = 'projects=' . $_GET['projects'].'&'; }
	while ($i < $totalPages){
		$i++;

		if($i == $page){ 

			echo '<li class="current"><a href="?'. $preVars .'page=' . $i . '">' . $i . '</a></li>';

		} else {

			echo '<li><a href="?'. $preVars .'page=' . $i . '">' . $i . '</a></li>';

		 }

	}
	?>
   
  </ul>
</nav>
