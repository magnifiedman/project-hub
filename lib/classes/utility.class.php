<?php
/**
 * Utililty Class
 *
 * Utility functions
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @author     traviswachendorf@iheartmedia.com
 * @copyright  2014
 * @version    1.0
 */

class Utility extends BaseClass  {

	function __construct() {
		parent::__construct();
	}


	/**
	 * Send autoresponder email
	 * @param  string $subject  email subject
	 * @param  string $bodyText email content
	 * @return boolean          
	 */
	function autoResponder($emailTo, $subject, $bodyText){

		// send email
		$header  ="MIME-Version: 1.0\n";
		$header .= "Content-type: text/html; charset=iso-8859-1\n";
		$header .= "From: iHeartMedia Phoenix Projects <" . EMAIL_FROM . ">\r\n";
		$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			  <html xmlns="http://www.w3.org/1999/xhtml">
			  <head>
			  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			  <title>Untitled Document</title>
			  </head>
			  
			  <body>
			  <center>
			  <table cellpadding="0" cellspacing="0" border="0" bgcolor="#d1d1d1">
			  <tr>
			  <td>
			  <table width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#e6e6e6">
			  <tr>
			  <td>'. $bodyText .'</td>
			  </tr>
			  </table>
			  </td>
			  </tr>
			  </table>
			  <table width="600" cellpadding="8" cellspacing="0" border="0" bgcolor="#ffffff">
			  <tr>
			  <td align="center">&copy; '.date("Y").' iHeartMedia</td>
			  </tr>
			  </table>
			  </center>
			  </body>
			  </html>';
		//echo $emailTo.$subject.$message.$header;
		// send email		
		mail(trim($emailTo), $subject, $message, $header);
		
	}

	function timeSelect($activeTime=''){
		$times = array(
			'-- N/A --' => '',
			'5:00am' => '05:00',
			'6:00am' => '06:00',
			'7:00am' => '07:00',
			'8:00am' => '08:00',
			'9:00am' => '09:00',
			'10:00am' => '10:00',
			'11:00am' => '11:00',
			'12:00pm' => '12:00',
			'1:00pm' => '13:00',
			'2:00pm' => '14:00',
			'3:00pm' => '15:00',
			'4:00pm' => '16:00',
			'5:00pm' => '17:00',
			'6:00pm' => '18:00',
			'7:00pm' => '19:00',
			'8:00pm' => '20:00',
			'9:00pm' => '21:00',
			'10:00pm' => '22:00',
			'11:00pm' => '23:00',
			'12:00am' => '00:00',
			);

		echo '<select class="form-control" name="event_time">';
		$sel='';
		foreach($times as $key=>$value){
			if($activeTime==$value){ $sel = 'selected="selected"'; }
			echo '<option value="' . $value .'" ' . $sel . '>' . $key .'</option>';
			$sel='';
		}
		echo '</select>';
	}


	/**
	 * Generates a clean filename for user uploaded file
	 * @param  string $filename
	 * @return string adjusted filename
	 */
	public static function cleanFilename($filename){
			$replace="-";
			$filename = strtolower(date("mdyHis").'-'.preg_replace("/[^a-zA-Z0-9\.]/",$replace,$filename));
			return $filename;
	}

	function userLevelSelect($userLevel=''){
		$userLevels = array(
			1=>'Master',
			2=>'Admin',
			3=>'Editor'
			);
		echo '<p><label>User Access Level</label>';
		echo '<select class="form-control" name="user_level">';
		foreach($userLevels as $level=>$name){
			if($level==$userLevel){ $sel = 'selected="selected"'; }
			echo '<option value="' . $level .'" ' . $sel . '>' . $name .'</option>';
			$sel='';
		}
		echo '</select></p>';
	}

	public function stationCheckboxes($stationArray=array()){
		$q = $this->db->query("SELECT * FROM " . STATIONS_TBL . "
			ORDER BY call_letters ASC");
		$r = $q->fetchAll(PDO::FETCH_ASSOC);
		$checked='';
		foreach($r as $station){
			if(in_array($station['id'],$stationArray)){ $checked = 'checked="checked"'; }
			echo '<label for="' . $station['id'] .'" class="checkbox-inline"><input type="checkbox" name="stations[]" value="' . $station['id'] .'" id="' . $station['id'] .'" ' . $checked . '>'. strtoupper($station['call_letters']).'</label>';
			$checked='';
		}

	}

	public function clientSelect($clientID=''){
		$q = $this->db->query("SELECT * FROM " . CLIENTS_TBL . "
			ORDER BY name ASC");
		$r = $q->fetchAll(PDO::FETCH_ASSOC);
		echo '<select class="form-control" name="client_id">';
		echo '<option value="">-- N/A --</option>';
		$sel='';
		foreach($r as $client){
			if($client['id']==$clientID){ $sel = 'selected="selected"'; }
			echo '<option value="' . $client['id'] . '" '. $sel .'>' . $client['name'] . '</option>';
			$sel='';
		}
		echo '</select>';
	}

	public function aeSelect($userID){
		$q = $this->db->query("SELECT id,fname,lname FROM " . USERS_TBL . "
			WHERE user_type=3
			ORDER BY fname ASC");
		$r = $q->fetchAll(PDO::FETCH_ASSOC);
		echo '<select class="form-control" name="ae_id">';
		echo '<option value="">-- N/A --</option>';
		$sel='';
		foreach($r as $ae){
			if($userID==$ae['id']){ $sel = 'selected="selected"'; }
			echo '<option value="' . $ae['id'] . '" ' . $sel . '>' . $ae['fname'] . ' ' . $ae['lname'] . '</option>';
			$sel='';
		}
		echo '</select>';
	}

	public function creativeSelect($creativeID=''){
		$q = $this->db->query("SELECT * FROM " . USERS_TBL . "
			WHERE user_type=2
			ORDER BY fname ASC");
		$r = $q->fetchAll(PDO::FETCH_ASSOC);
		echo '<select class="form-control" name="creative_id">';
		echo '<option value="">-- N/A --</option>';
		foreach($r as $creative){
			if($creativeID==$creative['id']){ $se = 'selected="selected"'; }
			echo '<option value="' . $creative['id'] . '" ' . $sel . '>' . $creative['fname'] . ' ' . $creative['lname'] . '</option>';
			$sel='';
		}
		echo '</select>';
	}

	
	public function creativeStatusSelect($statusID=''){
		$q = $this->db->query("SELECT * FROM creative_status
			ORDER BY sort_order ASC");
		$r = $q->fetchAll(PDO::FETCH_ASSOC);
		$html = '<select class="form-control" name="creative_status_id">';
		$sel='';
		//echo '<option value="">-- N/A --</option>';
		foreach($r as $status){
			if($statusID==$status['id']){ $sel = 'selected="selected"'; }
			$html .= '<option value="' . $status['id'] . '" ' . $sel . '>' . $status['status_name'] . '</option>';
			$sel='';
		}
		$html .= '</select>';
		return $html;
	}


	public function getCreativeStatus($statusID){
		$q = $this->db->query("SELECT status_name FROM creative_status
			WHERE id = '" . $statusID . "'");
		$r = $q->fetch(PDO::FETCH_ASSOC);
		return $r['status_name'];
	}


	public function projectStatusSelect($statusID=''){
		
		$r = array('1'=>'Pending',
			'2'=>'Approved'
			);
		$sel='';
		$html = '<select class="form-control" name="status_id">';
		//echo '<option value="">-- N/A --</option>';
		foreach($r as $id=>$name){
			if($statusID==$id){ $sel = 'selected="selected"'; }
			$html .= '<option value="' . $id . '" ' . $sel . '>' . $name . '</option>';
			$sel='';
		}
		$html .= '</select>';
		return $html;
	}

	public function getProjectStatus($statusID){
		if($statusID==1){ return 'Pending'; }
		if($statusID==2){ return 'Approved'; }
	}


	public function programmingStatusSelect($statusID=''){
		$q = $this->db->query("SELECT * FROM programming_status
			ORDER BY sort_order ASC");
		$r = $q->fetchAll(PDO::FETCH_ASSOC);
		$sel='';
		$html = '<select class="form-control" name="programming_status_id">';
		//echo '<option value="">-- N/A --</option>';
		foreach($r as $status){
			if($statusID==$status['id']){ $sel = 'selected="selected"'; }
			$html .= '<option value="' . $status['id'] . '" ' . $sel . '>' . $status['status_name'] . '</option>';
			$sel='';
		}
		$html .= '</select>';
		return $html;
	}

	public function getProgrammingStatus($statusID){
		$q = $this->db->query("SELECT status_name FROM programming_status
			WHERE id = '" . $statusID . "'");
		$r = $q->fetch(PDO::FETCH_ASSOC);
		return $r['status_name'];
	}


	public function dfpCheckbox($dfp=''){
		$checked='';
		if($dfp=='y'){ $checked = 'checked="checked"'; }
		return '<input type="checkbox" class="form-control" name="dfp" ' . $checked .' >';
	}

	public function getStatusHTML($section,$projectType,$userType,$statusID,$creatives=''){
		//echo $projectType.' '.$userType. ' ' . $statusID. '.<br />';
		switch($section){
			
			// project html
			case 'project':
				// sales and programming/programming - programming users
				if($_COOKIE['userLevel']==1){
					return $this->projectStatusSelect($statusID);
				}
				
				else {
					return '<span class="statusText">' . $this->getProjectStatus($statusID) . '</span>';
				}
				
			break;

			// programming html
			case 'programming':
				// sales and programming/programming - programming users
				if(($projectType==2 || $projectType==3) && $userType==5){
					return $this->programmingStatusSelect($statusID);
				}
				
				elseif(($projectType==2 || $projectType==3) && $userType!=5){
					return '<span class="statusText">' . $this->getProgrammingStatus($statusID) . '</span>';
				}
				else {
					return '<span class="noStatusText">N/A</span>';
				}
			break;

			// sales html
			case 'creative':
				// sales and programming/sales - creative users and creatives assigned
				if(($projectType==1 || $projectType==3) && $userType==2 && $creatives=='y'){	
					return $this->creativeStatusSelect($statusID);
				}
				elseif(($projectType==1 || $projectType==3) && $creatives=='y'){
					return '<span class="statusText">' . $this->getCreativeStatus($statusID) . '</span>';
				}
				else {
					return '<span class="noStatusText">N/A</span>';
				}
			break;

			// dfp html
			case 'dfp':
				// sales and programming/sales - creative users and creatives assigned
				if(($projectType==1 || $projectType==3) && $userType==2){
					return $this->dfpCheckbox($statusID);
				}
				elseif(($projectType==1 || $projectType==3)){
					if($statusID=='y'){ return '<span class="statusText">Loaded</span>'; }
					else { return '<span class="statusText">Not Loaded</span>';}
				}
				else {
					return '<span class="noStatusText">N/A</span>';
				}
			break;

			// button html
			case 'button':
				// sales people with sales type / prorgramming people with programming type
				if((($projectType==1 || $projectType==3) && $userType==2) || (($projectType==1 || $projectType==2) && $userType==5) || $_COOKIE['userLevel']==1){
					return '<p><input type="submit" class="btn btn-primary form-control" value="Update" /></p>';
				}
				else {
					return '';
				}
			break;
		}
	}


	public function getClientName($clientID){
		$q = $this->db->query("SELECT name FROM " . CLIENTS_TBL . "
			WHERE id = '" . $clientID . "'");
		$r = $q->fetch(PDO::FETCH_ASSOC);
		return $r['name'];
	}


	public function talentSelect($userID=''){
		$q = $this->db->query("SELECT * FROM " . USERS_TBL . "
			WHERE user_type=4
			ORDER BY fname ASC");
		$r = $q->fetchAll(PDO::FETCH_ASSOC);
		echo '<select class="form-control" name="talent_id">';
		echo '<option value="">-- N/A --</option>';
		foreach($r as $ae){
			if($userID==$ae['id']){ $sel = 'selected="selected"'; }
			echo '<option value="' . $ae['id'] . '" ' . $sel . '>' . $ae['fname'] . ' ' . $ae['lname'] . '</option>';
			$sel='';
		}
		echo '</select>';
	}


	public function projectTypeSelect($projectType=''){
		$q = $this->db->query("SELECT * FROM project_types
			ORDER BY id ASC");
		$r = $q->fetchAll(PDO::FETCH_ASSOC);
		echo '<select class="form-control" name="type_id" required>';
		echo '<option value="">-- Select Project Type --</option>';
		$sel='';
		foreach($r as $t){
			if($projectType==$t['id']){ $sel = 'selected="selected"'; }
			echo '<option value="' . $t['id'] . '" ' . $sel . '>' . $t['project_type'] . '</option>';
			$sel='';
		}
		echo '</select>';
	}


	public function projectNoteTypeSelect(){
		$q = $this->db->query("SELECT * FROM project_note_types
			ORDER BY id ASC");
		$r = $q->fetchAll(PDO::FETCH_ASSOC);
		echo '<select class="form-control" name="note_type_id" required>';
		echo '<option value="">-- Select Who Gets This Note --</option>';
		foreach($r as $nt){
			echo '<option value="' . $nt['id'] . '">' . $nt['note_type'] . '</option>';
		}
		echo '</select>';
	}


	public function creativeUsersCheckboxes($creativeUsers=''){
		try {
			$q = $this->db->query("SELECT id,fname,lname FROM " . USERS_TBL . "
				WHERE user_type=2
				ORDER BY fname ASC");
			$r = $q->fetchAll(PDO::FETCH_ASSOC);
			$checked='';
			foreach($r as $user){
				if(is_array($creativeUsers) && in_array($user['id'],$creativeUsers)){ $checked = 'checked="checked"'; }
				echo '<span class="userCheckbox"><label for="' . $user['id'] . '" class="checkbox-inline"><input name="creative_users[]" type="checkbox" id="'. $user['id'] .'" value="'. $user['id'] .'" ' . $checked . '/> ' . $user['fname'] . ' ' . $user['lname'] . '</label></span>' ;
				$checked='';
			}
		} catch (Exception $e) {
			echo "Utility::creativeUsersCheckboxes - Data could not be retrieved from the databases.";
			exit;
		}
	}

	
	// section updated 
	public function doNotification($notificationTypeID,$projectID,$userID='',$mainID='',$sectionID='',$statusID=''){
		try {
			$q = $this->db->query("INSERT INTO " . USER_NOTIFICATIONS_TBL . "
				(date_entered,user_id,project_id,main_id,type_id,status_id,section_id)
				value
				(NOW(),'" . $userID . "','" . $projectID . "','" . $mainID . "', '" . $notificationTypeID . "','" . $statusID . "','" . $sectionID . "')");
		} catch (Exception $e) {
			echo "Utility::doNotification - Data could not be inserted into the databases.";
			exit;
		}
	}



	public function getProjectUsers($projectID,$mainID=''){
		$mainSQL='';
		if($mainID!=''){ $mainSQL = "AND main_id = '" . $mainID ."' "; }
		try {
			$q = $this->db->query("SELECT user_id FROM " . PROJECTS_USERS_TBL . "
				WHERE
				project_id = '" . $projectID . "' 
				". $mainSQL);
			$r = $q->fetchAll(PDO::FETCH_ASSOC);
			$users =array();
			foreach ($r as $user){
				array_push($users,$user['user_id']);
			}
			return $users;
		} catch (Exception $e) {
			echo "Utility::getProjectUsers - Data could not be retrieved from the databases.";
			exit;
		}
	}


	public function getAssignableUsers($stationArray,$mainID=''){
		
		// format stations for query
		$stations = implode(',',$stationArray);
		
		try {
			$q = $this->db->query("SELECT user_id FROM " . USERS_GROUPS_TBL . "
				WHERE
				main_id = '" . $mainID . "' AND 
				station_id IN (". $stations .")
				GROUP BY user_id");
			$r = $q->fetchAll(PDO::FETCH_ASSOC);
			
			$users =array();
			foreach ($r as $user){
				array_push($users,$user['user_id']);
			}
			return $users;
		} catch (Exception $e) {
			echo "Utility::getProjectUsers - Data could not be retrieved from the databases.";
			exit;
		}
	}


	public function getUserNotifications($userID,$view,$page=1){
		
		// how many to retrieve
		if($view=='short'){ $offset = 0; $limit = 5; }
		else { $offset = ($page-1)*RESULTS_PERPAGE; $limit = RESULTS_PERPAGE; }
		

		try {
			$q = $this->db->query("SELECT un.*,p.project_name as projectName,m.group_name as maincatName,ps.section_name as sectionName, n.revision_text as ntext FROM " . USER_NOTIFICATIONS_TBL . " un
				LEFT JOIN " . PROJECTS_TBL . " p
				ON p.id=un.project_id 
				LEFT JOIN groups_main m
				ON m.id = un.main_id 
				LEFT JOIN project_sections ps
				ON ps.id = un.section_id 
				LEFT JOIN notifications n
				ON n.id = un.type_id
				WHERE
				un.user_id = '" . $userID . "' 
				ORDER BY date_entered DESC 
				LIMIT " . $offset .", " . $limit);
			$r = $q->fetchAll(PDO::FETCH_ASSOC);
			
			$notificationHTML = '';

			foreach($r as $notification){
				
				//print_r($notification);
				if($notification['maincatName']=='Sales'){ $statusName = $this->getStatusName($notification['status_id'],1); }
				if($notification['maincatName']=='Programming'){ $statusName = $this->getStatusName($notification['status_id'],2); }
				if($notification['maincatName']=='Sales'){ $notification['maincatName']='Creative'; }
				if($notification['maincatName']==''){ $notification['maincatName']='General'; }
				$notification1 = str_replace('projectName','<strong><a href="project-detail.php?id='.$notification['project_id'] . '">"' . $notification['projectName'] . '"</a></strong>',$notification['ntext']);
				$notification2 = str_replace('sectionName','<strong>' . strtoupper($notification['sectionName']) . '</strong>',$notification1);
				$notification3 = str_replace('maincatName','<strong>' . strtoupper($notification['maincatName']) . '</strong>',$notification2);
				$notification4 = str_replace('statusName','<strong><em>' . @$statusName . '</em></strong>',$notification3);
				$notificationHTML .= '<p class=""><span class="date-display">'. date("M d, Y @ h:ia",strtotime($notification['date_entered'])) .'</span> ' . $notification4.'</p>';
				$statusName='';
			}
			echo $notificationHTML;
		} catch (Exception $e) {
			echo "Utility::getUserNotifications - Data could not be retrieved from the databases.";
			exit;
		}
	}
	

	function getStatusName($statusID,$statusType){
		switch($statusType){
			case 1:
			$tblName = 'creative_status';
			break;
			case 2:
			$tblName = 'programming_status';
			break;
		}
		try {
			$q = $this->db->query("SELECT status_name FROM " . $tblName . " 
				WHERE id = '" . $statusID . "'");
			$r = $q->fetch(PDO::FETCH_ASSOC);
			return $r['status_name'];
		} catch (Exception $e) {
			echo "Utility::getUserNotifications - Data could not be retrieved from the databases.";
			exit;
		}

	}

	public function setLandmark(){
		setcookie('landmark','',-3600);
		$qs='';
		if($_SERVER['QUERY_STRING']!=''){ $qs = '&' . $_SERVER['QUERY_STRING']; }
		setcookie('landmark',$_SERVER['REQUEST_URI'] . $qs);
	}

	public function getSearchText($searchArray){
		if($searchArray['owner']==1){ $ownerText = '<strong>MY PROJECTS</strong>'; }
		if($searchArray['owner']==2){ $ownerText = '<strong>ALL PROJECTS</strong>'; }
		$searchText = '<p class="search-Text">Showing ' . $ownerText . ' ';
		if($searchArray['searchStr']!=''){ $searchText .= 'which contain <strong>"' . stripslashes($searchArray['searchStr']) . '"</strong> '; }
		if($searchArray['client_id']!=''){
			$clientName = $this->getClientName($searchArray['client_id']);
			$searchText .= 'created for <strong>"' . $clientName . '"</strong> '; 
		}
		$searchText .= '</p>';
		return $searchText;
	}

}
