<?php
/**
 * Project Class
 *
 * Project functions
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

class Project extends BaseClass  {

	public function __construct($utility) {
		parent::__construct();
		$this->z = $utility;
	}

	/**
	 * Project CRUD
	 * @param  string $method  action to take
	 * @param  array $vars    form variables
	 * @param  string $maincat maincat to edit
	 * @return various         
	 */
	public function doProject($method,$vars,$maincat=''){
		switch($method){
			case 'add':

				// set up some variables for query
				$stations = serialize($vars['stations']);
				$creative_users = serialize($vars['creative_users']);
				$startDate = substr($vars['start_date'],-4).'-'.substr($vars['start_date'],0,-5);
				$endDate = substr($vars['end_date'],-4).'-'.substr($vars['end_date'],0,-5);

				$creativeStatus='';
				$programmingStatus='';
				
				// set statuses
				if($vars['type_id']==1 || $vars['type_id']==3){ $creativeStatus = 1; $status = 2; }
				if($vars['type_id']==2 || $vars['type_id']==3){ $programmingStatus = 1; $status = 1; }

				
				// query 
				try {
					$q = $this->db->prepare("INSERT INTO " . PROJECTS_TBL . "
					(date_entered,start_date, end_date, type_id, project_name,stations,creative_users, creative_status, programming_status, author_id, client_id,ae_id, goal,budget,brainstorm_ideas,agreed_upon_ideas,status)
					values
					(NOW(),'" . $startDate . "','" . $endDate . "','". $vars['type_id'] . "', ?,'" . $stations . "','" . $creative_users . "','" . $creativeStatus . "','" . $programmingStatus . "','". $vars['author_id'] . "','". $vars['client_id'] . "','". $vars['ae_id'] . "',?,?,?,?,'" . $status . "')
					");
					$q->bindParam(1,$vars['project_name']);
					$q->bindParam(2,$vars['goal']);
					$q->bindParam(3,$vars['budget']);
					$q->bindParam(4,$vars['brainstorm_ideas']);
					$q->bindParam(5,$vars['agreed_upon_ideas']);
					$q->execute();
				} catch (Exception $e) {
					echo "Project::doProject - add - Project could not be created.";
					exit;
				}
							
				$id = $this->db->lastInsertId();

				// assign and notify users - sales only
				if($vars['type_id']==1){
					
					// a.e.
					if($vars['ae_id']!=''){ array_push($vars['creative_users'], $vars['ae_id']); }

					// get and asssign sales users
					$salesUsers = $this->z->getAssignableUsers($vars['stations'],1);
					$this->assignUsers($id,$salesUsers,1);
					
					// assign ae and creative users
					$this->assignUsers($id,$vars['creative_users']);
					
					// merge arrays to get all users
					$assignableUsers = array_merge($salesUsers,$vars['creative_users']);
					
					// notify users
					foreach($assignableUsers as $userID){
						$this->z->doNotification(12,$id,$userID);
					}
				}
				
				// assign and notify users - programming only
				if($vars['type_id']==2){

					// a.e.
					if($vars['ae_id']!=''){ array_push($vars['creative_users'], $vars['ae_id']); }
					
					// get and asssign programming users
					$programmingUsers = $this->z->getAssignableUsers($vars['stations'],2);
					$this->assignUsers($id,$programmingUsers,2);
					
					// assign creative users
					$this->assignUsers($id,$vars['creative_users']);
					
					// merge arrays to get all users
					$assignableUsers = array_merge($programmingUsers,$vars['creative_users']);
					
					// notify users
					foreach($assignableUsers as $userID){
						$this->z->doNotification(12,$id,$userID);
					}
				}

				// assign and notify users - sales and programming
				if($vars['type_id']==3){

					// a.e.
					if($vars['ae_id']!=''){ array_push($vars['creative_users'], $vars['ae_id']); }

					// get and asssign sales and programming users
					$salesUsers = $this->z->getAssignableUsers($vars['stations'],1);
					$this->assignUsers($id,$salesUsers,1);
					$programmingUsers = $this->z->getAssignableUsers($vars['stations'],2);
					$this->assignUsers($id,$programmingUsers,2);
					
					// assign creative users
					$this->assignUsers($id,$vars['creative_users']);
					
					// merge sales and programming users
					$standardUsers = array_merge($salesUsers,$programmingUsers);
					
					// merge arrays to get all users
					$assignableUsers = array_merge($standardUsers,$vars['creative_users']);
					
					// notify users
					foreach($assignableUsers as $userID){
						$this->z->doNotification(12,$id,$userID);
					}
				}
				

				return $id;
			break;

			// update
			case 'update':	

				// project type - sales
				if($vars['type_id']==1){
					
					// sales users
					$salesUsers = $this->z->getProjectUsers($vars['id'],1);
						
					// creative and a.e. users
					$otherUsers = $this->z->getProjectUsers($vars['id']);
					
					// all affected users
					$affectedUsers = array_merge($salesUsers,$otherUsers);
				}

				// project type - programming
				if($vars['type_id']==2){ 
					
					// programming users
					$programmingUsers = $this->z->getProjectUsers($vars['id'],2);
					
					// creative and a.e. users
					$otherUsers = $this->z->getProjectUsers($vars['id']);
					
					// all affected users
					$affectedUsers = array_merge($programmingUsers,$otherUsers);
				}

				// project type - sales and programming
				if($vars['type_id']==3){
					
					// sales and programming users
					$salesUsers = $this->z->getProjectUsers($vars['id'],1);
					$programmingUsers = $this->z->getProjectUsers($vars['id'],2);
					$standardUsers = array_merge($salesUsers,$programmingUsers);

					// creative and a.e. users
					$otherUsers = $this->z->getProjectUsers($vars['id']);
					
					// all affected users
					$affectedUsers = array_merge($standardUsers,$otherUsers);
				}

				// switcher
				switch($maincat){
					case 'on-air':
						
						try {
							$q = $this->db->prepare("UPDATE " . PROJECTS_TBL . "
								SET
								recorded_amount = ?,
								recorded_dates = ?,
								live_amount = ?,
								live_dates = ?,
								total_amount = ?,
								giveaways = ?
								WHERE id = '" . $vars['id'] . "'
								");
							$q->bindParam(1,$vars['recorded_amount']);
							$q->bindParam(2,$vars['recorded_dates']);
							$q->bindParam(3,$vars['live_amount']);
							$q->bindParam(4,$vars['live_dates']);
							$q->bindParam(5,$vars['total_amount']);
							$q->bindParam(6,$vars['giveaways']);
							$q->execute();

							// notify users
							foreach($affectedUsers as $userID){
								$this->z->doNotification(17,$vars['id'],$userID,2,1);
							}

							return true;
						} catch (Exception $e) {
							echo "Project::doProject - updateOnAir - Data could not be updated - on-air - from the databases.";
							exit;
						}

					break;

					case 'on-site':
						
						try {
							$eventDate = substr($vars['event_date'],-4).'-'.substr($vars['event_date'],0,-5).' '.$vars['event_time'].':00';
							$q = $this->db->prepare("UPDATE " . PROJECTS_TBL . "
								SET
								event_date = '" . $eventDate . "',
								talent_id = '" . $vars['talent_id'] . "',
								talent_fee = ?,
								techs_requested = ?,
								remote_details = ?,
								hard_costs = ?,
								used_for = ?
								WHERE id = '" . $vars['id'] . "'
								");
							$q->bindParam(1,$vars['talent_fee']);
							$q->bindParam(2,$vars['techs_requested']);
							$q->bindParam(3,$vars['remote_details']);
							$q->bindParam(4,$vars['hard_costs']);
							$q->bindParam(5,$vars['used_for']);
							$q->execute();

							// notify users
							foreach($affectedUsers as $userID){
								$this->z->doNotification(17,$vars['id'],$userID,2,2);
							}

							return true;
						} catch (Exception $e) {
							print_r($e);
							echo "Project::doProject - updateOnSite - Data could not be updated - on-site - from the databases.";
							exit;
						}

						

					break;

					case 'online':

						try {
							$ads = serialize(@$vars['ads']);

							$q = $this->db->prepare("UPDATE " . PROJECTS_TBL . "
								SET
								ads = '" . $ads . "',
								hpt_dates = ?,
								eblast_dates = ?,
								print_ad = '" . @$vars['print_ad'] . "',
								print_ad_details = ?,
								facebook = '" . @$vars['facebook'] . "',
								facebook_amount = ?,
								facebook_dates = ?,
								twitter = '" . @$vars['twitter'] . "',
								twitter_amount = ?,
								twitter_dates = ?,
								dynamic_lead = '" . @$vars['dynamic_lead'] . "',
								custom_page = '" . @$vars['custom_page'] . "',
								custom_page_overview = ?,
								custom_page_copy = ?,
								contest_page = '" . @$vars['contest_page'] . "',
								contest_page_overview = ?,
								contest_page_copy = ?,
								video = '" . @$vars['video'] . "',
								video_overview = ?
								WHERE id = '" . @$vars['id'] . "'
								");

							$q->bindParam(1,$vars['hpt_dates']);
							$q->bindParam(2,$vars['eblast_dates']);
							$q->bindParam(3,$vars['print_ad_details']);
							$q->bindParam(4,$vars['facebook_amount']);
							$q->bindParam(5,$vars['facebook_dates']);
							$q->bindParam(6,$vars['twitter_amount']);
							$q->bindParam(7,$vars['twitter_dates']);
							$q->bindParam(8,$vars['custom_page_overview']);
							$q->bindParam(9,$vars['custom_page_copy']);
							$q->bindParam(10,$vars['contest_page_overview']);
							$q->bindParam(11,$vars['contest_page_copy']);
							$q->bindParam(12,$vars['video_overview']);
							$q->execute();

							// notify users
							foreach($affectedUsers as $userID){
								$this->z->doNotification(17,$vars['id'],$userID,1,3);
							}

							return true;
						} catch (Exception $e) {
							print_r($e);
							echo "Project::doProject - updateOnline - Data could not be updated - online - from the databases.";
							exit;
						}
						
					break;


					case 'pending':

						$startDate = substr($vars['start_date'],-4).'-'.substr($vars['start_date'],0,-5);
						$endDate = substr($vars['end_date'],-4).'-'.substr($vars['end_date'],0,-5);
						$stations = serialize($vars['stations']);
						
						$creative_users='';
						if(isset($vars['creative_users'])){
							$creative_users = serialize($vars['creative_users']);	
						}
						else { $vars['creative_users'] = array(); }
						if($vars['creatives_current']!=''){
							$currentArray = unserialize(stripslashes($vars['creatives_current']));
						}
						else {
							$currentArray = array();
						}
						
						$allUsers = $this->z->getProjectUsers($vars['id']);

						// check for creative user update
						if($vars['creatives_current']!=serialize($vars['creative_users'])){
							//echo $vars['creatives_current'];
							
							$removed = array_diff($currentArray,$vars['creative_users']);
							$added = array_diff($vars['creative_users'],$currentArray);
							
							// notify removed users
							foreach($removed as $userID){
								$this->z->doNotification(15,$vars['id'],$userID);
							}

							// notify added users
							foreach($added as $userID){
								$this->z->doNotification(14,$vars['id'],$userID);
							}

							// assign and remove creative users
							$this->assignUsers($vars['id'],$added);
							$this->removeUsers($vars['id'],$removed);
						}

						// get programming and sales users
						$programmingUsers = $this->z->getProjectUsers($vars['id'],2);
						$salesUsers = $this->z->getProjectUsers($vars['id'],1);
						$standardUsers = array_merge($programmingUsers,$salesUsers);
						
						// get creative and a.e. users
						$otherUsers = $this->z->getProjectUsers($vars['id']);

						// get all affected users
						$affectedUsers = array_merge($standardUsers,$otherUsers);

						$updateSQL = '';

						// project status goes to approved.
						if(isset($vars['status_id']) && $vars['status_id']==2 && $vars['current_status_id']==1){
							$updateSQL .= "status = '" . $vars['status_id'] . "', ";
							
							// create notifications for all affected users
							foreach($affectedUsers as $userID){
								$this->z->doNotification(23,$vars['id'],$userID);
							}
						}

						// just project updates, no change to approved
						else {
							foreach($affectedUsers as $userID){
								$this->z->doNotification(16,$vars['id'],$userID);
							}
						}

						// run query
						try {
							$q = $this->db->prepare("UPDATE " . PROJECTS_TBL . "
								SET
								" . $updateSQL . "
								project_name = ?,
								type_id = '". $vars['type_id'] ."',
								start_date = '" . $startDate . "',
								end_date = '" . $endDate . "',
								stations = '" . $stations . "',
								ae_id = '" . $vars['ae_id'] . "',
								client_id = '" . $vars['client_id'] . "',
								creative_users = '" . $creative_users . "',
								budget = ?,
								goal = ?,
								brainstorm_ideas = ?,
								agreed_upon_ideas = ?
								WHERE id = '" . $vars['id'] . "'
								");

							$q->bindParam(1,$vars['project_name']);
							$q->bindParam(2,$vars['budget']);
							$q->bindParam(3,$vars['goal']);
							$q->bindParam(4,$vars['brainstorm_ideas']);
							$q->bindParam(5,$vars['agreed_upon_ideas']);
							$q->execute();

							return true;

						} catch (Exception $e) {
							print_r($e);
							echo "Project::doProject - updatePending - Data could not be updated - online - from the databases.";
							exit;
						}
						
					break;


					case 'status':
						$creative_users='';
						if(isset($vars['creative_users'])){
							$creative_users = serialize($vars['creative_users']);	
						}
						else { $vars['creative_users'] = array(); }
						if($vars['creatives_current']!=''){
							$currentArray = unserialize(stripslashes($vars['creatives_current']));
						}
						else {
							$currentArray = array();
						}

						// check for creative user update
						if($vars['creatives_current']!=serialize($vars['creative_users'])){
							$removed = array_diff($currentArray,$vars['creative_users']);
							$added = array_diff($vars['creative_users'],$currentArray);
							
							// notify removed users
							foreach($removed as $userID){
								$this->z->doNotification(15,$vars['id'],$userID);
							}

							// notify added users
							foreach($added as $userID){
								$this->z->doNotification(14,$vars['id'],$userID);
							}

							// assign and remove creative users
							$this->assignUsers($vars['id'],$added);
							$this->removeUsers($vars['id'],$removed);
						}


						$updateSQL = '';

						// project status update
						if(isset($vars['status_id']) && ($vars['status_id'] != $vars['current_status_id'])){
							$updateSQL .= "status = '" . $vars['status_id'] . "', ";
							
							// get programming and sales users
							$programmingUsers = $this->z->getProjectUsers($vars['id'],2);
							$salesUsers = $this->z->getProjectUsers($vars['id'],1);
							$standardUsers = array_merge($programmingUsers,$salesUsers);
							
							// get creative and a.e. users
							$otherUsers = $this->z->getProjectUsers($vars['id']);

							// get all affected users
							$affectedUsers = array_merge($standardUsers,$otherUsers);
							
							// set id to send correct notification
							if($vars['status_id']==1){ $notificationID = 24; }
							if($vars['status_id']==2){ $notificationID = 23; }
							
							// create notifications for all affected users
							foreach($affectedUsers as $userID){
								$this->z->doNotification($notificationID,$vars['id'],$userID,2,'','');
							}
						}


						// programming status update
						if(isset($vars['programming_status_id']) && ($vars['programming_status_id'] != $vars['current_programming_status_id'])){
							$updateSQL .= "programming_status = '" . $vars['programming_status_id'] . "', ";
							
							// get programming users
							$programmingUsers = $this->z->getProjectUsers($vars['id'],2);
							
							// get creative and a.e. users
							$otherUsers = $this->z->getProjectUsers($vars['id']);

							// get all affected users
							$affectedUsers = array_merge($programmingUsers,$otherUsers);
							
							// create notifications for all affected users
							foreach($affectedUsers as $userID){
								$this->z->doNotification(13,$vars['id'],$userID,2,'',$vars['programming_status_id']);
							}
						}


						// creative status update
						if(isset($vars['creative_status_id']) && ($vars['creative_status_id'] != $vars['current_creative_status_id'])){
							$updateSQL .= "creative_status = '" . $vars['creative_status_id'] . "', ";
							
							// get affected users - creative update
							$salesUsers = $this->z->getProjectUsers($vars['id'],1);

							// get creative and a.e. users
							$otherUsers = $this->z->getProjectUsers($vars['id']);

							// get all affected users
							$affectedUsers = array_merge($salesUsers,$otherUsers);
							
							// create notifications for all affected users
							foreach($affectedUsers as $userID){
								$this->z->doNotification(13,$vars['id'],$userID,1,'',$vars['creative_status_id']);
							}
						}

						// dfp update
						if(isset($vars['dfp']) && ($vars['dfp'] != $vars['current_dfp'])){
							$updateSQL .= "dfp = '" . $vars['dfp'] . "', ";

							// get affected users - dfp update
							$salesUsers = $this->z->getProjectUsers($vars['id'],1);
							
							// get creative and a.e. users
							$otherUsers = $this->z->getProjectUsers($vars['id']);

							// get all affected users
							$affectedUsers = array_merge($salesUsers,$otherUsers);
							
							// create notifications for all affected users
							foreach($affectedUsers as $userID){
								$this->z->doNotification(13,$vars['id'],$userID,1);
							}
						}
						
						$creative_users = serialize(@$vars['creative_users']);
						$updateSQL .= "creative_users = '" . @$creative_users . "', ";
						
						$updateSQL = substr($updateSQL,0,-2);

						
						try {
							
							$q = $this->db->query("UPDATE " . PROJECTS_TBL . "
								SET
								" . $updateSQL . "
								WHERE id = '" . $vars['id'] . "'
								");
							return true;
						} catch (Exception $e) {
							print_r($e);
							echo "Project::doProject - updateStatus - Data could not be updated - on-site - from the databases.";
							exit;
						}

					break;

					
				}

			break;

			case 'delete':

			break;
		}
	}


	/**
	 * Gets details for a project.
	 * @param  integer $projectID id of project
	 * @return array           project details
	 */
	public function getProjectDetails($projectID){
		$q = $this->db->query("SELECT p.*,pt.project_type,c.*,u.fname,u.lname,u.office_phone,u.mobile_phone FROM " . PROJECTS_TBL . " p
			LEFT JOIN " . CLIENTS_TBL . " c on c.id=p.client_id
			LEFT JOIN " . USERS_TBL . " u on u.id=p.author_id
			LEFT JOIN project_types pt on pt.id=p.type_id
			WHERE p.id = '". $projectID ."'");
		$project = $q->fetch(PDO::FETCH_ASSOC);
		$stationArray = unserialize($project['stations']);
		$project['stationArray'] = $project['stations'];
		if($project['ae_id']>0){ $project['ae'] = $this->getUserAETag($project['ae_id']); }
		else { $project['ae']= 'N/A'; }
		$project['stations'] = $this->getStationNames($stationArray);
		$project['event_time'] = substr($project['event_date'],11,5);
		if($project['event_date']!=''){ $project['event_date'] = substr($project['event_date'],5,2).'-'.substr($project['event_date'],8,2).'-'.substr($project['event_date'],0,4); }

		$project = array_map('stripslashes', $project);
		return $project;
	}


	/**
	 * Gettag for project A.E.
	 * @param  integer $aeID AE id
	 * @return string       html
	 */
	public function getUserAETag($aeID){
		$q = $this->db->query("SELECT fname,lname,office_phone,mobile_phone FROM " . USERS_TBL ."
			WHERE id = '". $aeID ."'");
		$ae = $q->fetch(PDO::FETCH_ASSOC);
		$mobilePhone='';
		if($ae['mobile_phone']!=''){ $mobilePhone = ' / Cell: ' . $ae['mobile_phone']; }
		$return = '<a href="#" data-toggle="popover" title="' . $ae['fname'] . ' ' . $ae['lname'] . '" data-placement="bottom" data-content="Ext. ' . substr($ae['office_phone'],-4) . ' ' . $mobilePhone . '">' . $ae['fname'].' '. $ae['lname'] . '</a>';
		
		return $return;

	}


	/**
	 * Returns station names for project
	 * @param  array $stationArray array of station ids
	 * @return string              html
	 */
	public function getStationNames($stationArray){
		$q = $this->db->query("SELECT id,station_name FROM " . STATIONS_TBL . "
			ORDER BY station_name ASC");
		$stations = $q->fetchAll(PDO::FETCH_ASSOC);
		$stationList = '';
		$partial='';
		foreach($stations as $station){
			if(in_array($station['id'],$stationArray,true)){ $stationList .= $station['station_name'] . ' &middot; '; }
			else { $partial=true; }
		}
		if($partial==true){ $stationList = substr($stationList, 0, -10); }
		else { $stationList = 'All Phoenix Market Stations'; }
		return $stationList;
	}


	public function getStationNamesOverview($stationsList){
		$q = $this->db->query("SELECT id,call_letters FROM " . STATIONS_TBL . "
			ORDER BY station_name ASC");
		$stations = $q->fetchAll(PDO::FETCH_ASSOC);
		$stationList = '';
		$partial='';
		$stationArray = unserialize($stationsList);
		foreach($stations as $station){
			if(in_array($station['id'],$stationArray,true)){ $stationList .= '<span class="station">' . strtoupper($station['call_letters']) . '</span>'; }
			
		}
		
		//$stationList = 'All Phoenix Market Stations';
		return $stationList;
	}


	/**
	 * Retrieves all notes for user
	 * @param  int $userID user's id
	 * @param  str $snippet length of result
	 * @param  int $page current page
	 * @return [type]         [description]
	 */
	public function getNotes($userID,$snippet='',$page=''){
		if($snippet=='y'){
			try {
				$q = $this->db->query("SELECT n.*,p.project_name FROM " . NOTES_TBL . " n
				LEFT JOIN " . PROJECTS_TBL . " p on p.id=n.project_id 
				WHERE n.user_id='" . $userID . "'
				ORDER BY n.date_entered
				DESC
				LIMIT 6
				");
				$r = $q->fetchAll(PDO::FETCH_ASSOC);
				if(count($r)>0){ 
					return $r;
				 }
				else { return false; }
			} catch (Exception $e) {
				echo "Project::getNotes - Data could not be retrieved from the databases.";
				exit;
			}

		}

		else {
			$offset = ($page-1)*RESULTS_PERPAGE;
			
			try {
				$q = $this->db->query("SELECT n.*,p.project_name FROM " . NOTES_TBL . " n
				LEFT JOIN " . PROJECTS_TBL . " p on p.id=n.project_id 
				WHERE n.user_id='" . $userID . "'
				ORDER BY n.date_entered
				DESC
				LIMIT " . $offset . ", " . RESULTS_PERPAGE . "
				");
				$r = $q->fetchAll(PDO::FETCH_ASSOC);
				if(count($r)>0){ 
					return $r;
				 }
				else { return false; }
			} catch (Exception $e) {
				echo "Project::getNotes - Data could not be retrieved from the databases.";
				exit;
			}

		}

	}


	/**
	 * Retrieves all revisions related to user's projects that apply to user
	 * @param  int $userID user's id
	 * @return [type]         [description]
	 */
	public function getNotifications($userID,$snippet='',$page=''){
		if($snippet=='y'){
			try {
				$q = $this->db->query("SELECT r.*,p.project_name FROM " . REVISIONS_TBL . " r
					LEFT JOIN " . PROJECTS_USERS_TBL . " pu
					ON pu.project_id=r.project_id
					LEFT JOIN " . PROJECTS_TBL . " p
					ON r.project_id=p.id
					WHERE pu.main_id=r.main_id
					AND pu.sub_id = r.sub_id
					AND pu.user_id=1
					ORDER BY r.date_entered DESC
					LIMIT 6
				");
				$r = $q->fetchAll(PDO::FETCH_ASSOC);
				if(count($r)>0){ 
					return $r;
				 }
				else { return false; }
			} catch (Exception $e) {
				echo "Project::getNotifications - Data could not be retrieved from the databases.";
				exit;
			}

		}

		else {
			$offset = ($page-1)*RESULTS_PERPAGE;
			
			try {
				$q = $this->db->query("SELECT r.*,p.project_name FROM " . REVISIONS_TBL . " r
					LEFT JOIN " . PROJECTS_USERS_TBL . " pu
					ON pu.project_id=r.project_id
					LEFT JOIN " . PROJECTS_TBL . " p
					ON r.project_id=p.id
					WHERE pu.main_id=r.main_id
					AND pu.sub_id = r.sub_id
					AND pu.user_id=1
					ORDER BY r.date_entered DESC
					LIMIT " . $offset . ", " . RESULTS_PERPAGE . "
				");
				$r = $q->fetchAll(PDO::FETCH_ASSOC);
				if(count($r)>0){ 
					return $r;
				 }
				else { return false; }
			} catch (Exception $e) {
				echo "Project::getNotifications - Data could not be retrieved from the databases.";
				exit;
			}

		}

	}


	/**
	 * Add a client to the system
	 * @param array $vars form inputs
	 */
	public function addClient($vars){
		$q = $this->db->query("INSERT INTO " . CLIENTS_TBL ."
			(name)
			values
			('" . $vars['client_name'] . "')
			");
		return true;
	}


	public function getUserProjects($userID,$projectType){
		$nowTime = date("Y-m-d h:i:s");
		$offset=0; $limit=5;
		$whereSQL = ''; 
		if($projectType=='in-progress'){
			$whereSQL .= "p.start_date <= '" . $nowTime . "' AND p.end_date >= '" . $nowTime . "' AND p.status=2 ";
			$orderSQL = "ORDER BY p.end_date ASC ";
		}
		if($projectType=='upcoming'){
			$whereSQL .= "p.start_date > '" . $nowTime . "' AND p.end_date > '" . $nowTime . "' AND p.status=2 ";
			$orderSQL = "ORDER BY p.start_date ASC ";
		}
		if($projectType=='pending'){
			$whereSQL .= "p.start_date > '" . $nowTime . "' AND p.end_date > '" . $nowTime . "' AND p.status=1 ";
			$orderSQL = "ORDER BY p.start_date ASC ";
		}

		
		try {
			$q = $this->db->query("SELECT p.*,c.name as client_name,u.fname as ae_fname,u.lname as ae_lname FROM " . PROJECTS_TBL . " p
				LEFT JOIN " . PROJECTS_USERS_TBL . " pu ON pu.project_id=p.id
				LEFT JOIN " . CLIENTS_TBL . " c ON c.id=p.client_id
				LEFT JOIN " . USERS_TBL . " u ON u.id=p.ae_id
				WHERE pu.user_id='". $userID ."' AND 
				" . $whereSQL . "
				" . $orderSQL . "
				LIMIT " . $offset . ", " . $limit . "
			");
			$r = $q->fetchAll(PDO::FETCH_ASSOC);
			if(count($r)>0){ 
				return $r;
			 }
			else { return false; }
		} catch (Exception $e) {
			print_r($e);
			echo "Project::getUserProjects - Data could not be retrieved from the databases.";
			exit;
		}
	}


	public function getProjects($userID, $projectType='', $page='',$searchArray=''){
		
		$nowTime = date("Y-m-d h:i:s");
		$selectSQL = '';
		$whereSQL = 'WHERE ';
		$joinSQL = '';
		$groupSQL = '';
		$limitSQL = '';
		$orderSQL = "ORDER BY p.start_date ASC ";
		$ownerSQL='';

		$selectSQL .= "p.id,p.ae_id,p.project_name,p.start_date,p.end_date,p.stations,p.status,c.name as client_name,u.fname as ae_fname,u.lname as ae_lname";
		$joinSQL .= "LEFT JOIN " . CLIENTS_TBL . " c on c.id=p.client_id ";
		$joinSQL .= "LEFT JOIN " . USERS_TBL . " u on u.id=p.ae_id ";

		// standard
		if($projectType!=''){

			$offset = ($page-1)*RESULTS_PERPAGE;
			if($projectType=='in-progress'){
				$whereSQL .= "p.start_date <= '" . $nowTime . "' AND p.end_date >= '" . $nowTime . "' AND p.status=2 AND ";
				$orderSQL = "ORDER BY p.end_date ASC ";
			}
			if($projectType=='upcoming'){
				$whereSQL .= "p.start_date > '" . $nowTime . "' AND p.end_date > '" . $nowTime . "' AND p.status=2 AND ";
				$orderSQL = "ORDER BY p.start_date ASC ";
			}
			if($projectType=='pending'){
				$whereSQL .= "p.start_date > '" . $nowTime . "' AND p.end_date > '" . $nowTime . "' AND p.status=1 AND ";
				$orderSQL = "ORDER BY p.start_date ASC ";
			}
			$limitSQL = "LIMIT " . $offset . ", " . RESULTS_PERPAGE;

		}

		$joinSQL .= "LEFT JOIN " . PROJECTS_USERS_TBL . " pu on pu.project_id=p.id ";
		$ownerSQL .= "pu.user_id = '" . $_COOKIE['userID'] . "'";

		// search
		if(isset($searchArray)){
			
			// set new cookie
			setcookie('searchArray',serialize($searchArray));
			
			if(isset($searchArray['client_id'])&& $searchArray['client_id']!=''){
				$whereSQL .= "p.client_id = '" . $searchArray['client_id'] . "' AND ";	
			}
			if(isset($searchArray['searchStr'])&& $searchArray['searchStr']!=''){
				$whereSQL .= "(p.project_name LIKE ? OR c.name LIKE ?) AND "; $searchStr = '%' . $searchArray['searchStr'] . '%';
			}
			if(isset($searchArray['owner']) && $searchArray['owner']==2){
				$ownerSQL = "";
				$groupSQL = "GROUP BY p.id ";
				$whereSQL = substr($whereSQL,0,-4);
			}
			$offset = ($page-1)*RESULTS_PERPAGE;
			$limitSQL = "LIMIT " . $offset . ", " . RESULTS_PERPAGE;
			
		}

		
		// run query
		try {
			$q = $this->db->prepare("SELECT " . $selectSQL . " FROM " . PROJECTS_TBL . " p
				" . $joinSQL . "
				" . $whereSQL . "
				" . $ownerSQL . "
				" . $groupSQL . "
				" . $orderSQL . "
				" . $limitSQL . "
			");
			
			// bind
			if(isset($searchArray['searchStr']) && $searchArray['searchStr']!=''){
				$q->bindParam(1,$searchStr);
				$q->bindParam(2,$searchStr);
			}
			
			$q->execute();
			$r = $q->fetchAll(PDO::FETCH_ASSOC);

			$q2 = $this->db->prepare("SELECT p.id FROM " . PROJECTS_TBL . " p
				" . $joinSQL . "
				" . $whereSQL . "
				" . $ownerSQL . "
				" . $groupSQL . "
				" . $orderSQL . "
			");

			// bind
			if(isset($searchArray['searchStr']) && $searchArray['searchStr']!=''){
				$q2->bindParam(1,$searchStr);
				$q2->bindParam(2,$searchStr);
			}
			$q2->execute();

			$projects = array();
			$projects['totalRecords'] = $q2->rowCount();

			if(count($r)>0){ 
				
				$projects['result']=$r;
				return $projects;

			}
			else { return false; }
		} catch (Exception $e) {
			echo '<pre>';
			print_r($e);
			echo "Project::getProjects - Data could not be retrieved from the databases.";
			exit;
		}

	}


	public function getProjectNotes($projectID){
		try {
			$q = $this->db->query("SELECT n.*, pt.note_type as noteType,u.fname,u.lname,u.office_phone FROM projects_notes n
				LEFT JOIN project_types pt
				ON pt.id = n.note_type_id
				LEFT JOIN " . USERS_TBL . " u
				ON u.id = n.user_id
				WHERE n.project_id='". $projectID ."'
				ORDER BY date_entered ASC
			");
			$r = $q->fetchAll(PDO::FETCH_ASSOC);
			if(count($r)>0){ 
				return $r;
			 }
			else { return false; }
		} catch (Exception $e) {
			echo "Project::getProjectNotes - Data could not be retrieved from the databases.";
			exit;
		}
	}

	public function noteTypeSelecter(){
		try {
				$q = $this->db->query("SELECT * from project_types 
					ORDER BY id ASC
				");
				$r = $q->fetchAll(PDO::FETCH_ASSOC);
				if(count($r)>0){ 
					echo '<select class="form-control" name="note_type_id" required>';
					foreach($r as $note){
						echo '<option value="' . $note['id'] . '">' . $note['note_type'] . '</option>';
					}
					echo '</select>';
				 }
				else { return false; }
			} catch (Exception $e) {
				echo "Project::noteTypeSelecter - Data could not be retrieved from the databases.";
				exit;
			}
	}

	public function doNote($vars){
		
		// get users - sales
		if($vars['note_type_id']==1){
			$spUsers = $this->z->getProjectUsers($vars['project_id'],1);
		}

		// get users - programming
		if($vars['note_type_id']==2){
			$spUsers = $this->z->getProjectUsers($vars['project_id'],2);
		}

		// get users - both
		if($vars['note_type_id']==3){
			$sUsers = $this->z->getProjectUsers($vars['project_id'],1);
			$pUsers = $this->z->getProjectUsers($vars['project_id'],2);
			$spUsers = array_merge($sUsers,$pUsers);
		}

		// get creative and a.e. users
		$otherUsers = $this->z->getProjectUsers($vars['project_id']);
		
		// merge arrays to get all users
		$assignableUsers = array_merge($spUsers,$otherUsers);
			
		// notify users
		foreach($assignableUsers as $userID){
			$this->z->doNotification(20,$vars['project_id'],$userID,$vars['note_type_id']);
		}

		try {
				$q = $this->db->prepare("INSERT INTO projects_notes
					(date_entered,user_id,note_type_id,project_id,note)
					values
					(NOW(),'" . $vars['user_id'] ."', '" . $vars['note_type_id'] ."', '" . $vars['project_id'] ."',?)
				");
				$q->bindParam(1,$vars['note']);
				$q->execute();
				return true;
			} catch (Exception $e) {
				echo "Project::doNote - Data could not be retrieved from the databases.";
				exit;
			}
	}


	public function doFile($vars){
		
		$fileNames = array();

		// file 1
		if($_FILES['file1']['size']>0){
			$fileName = Utility::cleanFilename($_FILES['file1']['name']);
			array_push($fileNames,$fileName);
			move_uploaded_file($_FILES['file1']['tmp_name'],ROOT_PATH . '/userfiles/' . $fileName );
		}

		// file 2
		if($_FILES['file2']['size']>0){
			$fileName = Utility::cleanFilename($_FILES['file2']['name']);
			array_push($fileNames,$fileName);
			move_uploaded_file($_FILES['file2']['tmp_name'],ROOT_PATH . '/userfiles/' . $fileName );
		}

		// file 3
		if($_FILES['file3']['size']>0){
			$fileName = Utility::cleanFilename($_FILES['file3']['name']);
			array_push($fileNames,$fileName);
			move_uploaded_file($_FILES['file3']['tmp_name'],ROOT_PATH . '/userfiles/' . $fileName );
		}


		// insert files
		foreach($fileNames as $fileName){
			try {
				$q = $this->db->query("INSERT INTO projects_files
					(date_entered,project_id,main_id,file_name)
					values
					(NOW(),'" . $vars['project_id'] ."', '" . $vars['group_id'] ."', '" . $fileName ."')
				");
				
				
			} catch (Exception $e) {
				echo "Project::doFile - Data could not be retrieved from the databases.";
				exit;
			}
		}

		// do notifications
		$projectUsers = $this->z->getProjectUsers($vars['project_id']);
		foreach($projectUsers as $userID){
			$this->z->doNotification(21,$vars['project_id'],$userID,'',$vars['group_id']);
		}

		return true;
		
	}


	public function getFiles($projectID,$groupID){
		try {
				$q = $this->db->query("SELECT * from projects_files
					WHERE project_id = '" . $projectID . "'
					AND main_id = '" . $groupID . "'
				");
				$r = $q->fetchAll(PDO::FETCH_ASSOC);
				if($q->rowCount()>0){
					echo '<div class="filebox">';
					foreach($r as $file){
						echo '<a href="' . BASE_URL . '/userfiles/' . $file['file_name'] . '" target="_blank">' . substr($file['file_name'],13) . '</a>';
					} 
					echo '</div>';
				}
				
			} catch (Exception $e) {
				echo "Project::doFile - Data could not be retrieved from the databases.";
				exit;
			}

	}

	private function assignUsers($projectID,$userArray,$mainID=''){
		
		foreach($userArray as $userID){
			
			try {
				$q = $this->db->query("INSERT INTO " . PROJECTS_USERS_TBL . "
					(project_id,main_id,user_id) 
					VALUES 
					('" . $projectID . "','" . $mainID . "','" . $userID . "')");
			} catch (Exception $e) {
				echo "Project::assignUsers - Data could not be inserted into the databases.";
				exit;
			}
		}

	}

	private function removeUsers($projectID,$userArray){
		
		foreach($userArray as $userID){
			try {
				$q = $this->db->query("DELETE FROM " . PROJECTS_USERS_TBL . "
					WHERE project_id = '". $projectID . "' 
					AND user_id = '" . $userID . "'");
			} catch (Exception $e) {
				echo "Project::removeUsers - Data could not be removed from the databases.";
				exit;
			}
		}

	}

	



}
