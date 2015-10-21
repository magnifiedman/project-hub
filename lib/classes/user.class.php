<?php
/**
 * User Class
 *
 * User functions
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

class User extends BaseClass  {

	function __construct() {
		parent::__construct();
	}

	function doLogin($vars){
		try {
			$q = $this->db->prepare("SELECT id,user_level,evernote_notify,evernote_email,user_type FROM " . USERS_TBL . "
			WHERE email = ? 
			AND pword = ?
			");
			$q->bindParam(1,$vars['email']);
			$q->bindParam(2,$vars['pword']);
			$q->execute();
			$r = $q->fetch(PDO::FETCH_ASSOC);
			if(count($r)>0){
				if($r['evernote_notify']=='y' && $r['evernote_email'] != ''){
					setcookie('evernote_email',$r['evernote_email']);
					setcookie('evernotify','y');
				} 
				setcookie('userLogged','y');
				setcookie('userID',$r['id']);
				setcookie('userLevel',$r['user_level']);
				setcookie('userType',$r['user_type']);
				//return $r['user_level'];
				header("Location: login.php");
			 }
			else { return false; }
		} catch (Exception $e) {
			echo "User::tryLogin - Data could not be retrieved from the databases.";
			exit;
		}
	}


	/**
	 * Gets users details
	 * @param  integer $userID User's id in db
	 * @return array         
	 */
	public function getUserDetails($userID){
		// user details
		$q = $this->db->query("SELECT * FROM " . USERS_TBL . " WHERE id = '" . $userID . "'");
		$r = $q->fetch(PDO::FETCH_ASSOC);
		
		// user groups
		$q2 = $this->db->query("SELECT * FROM " . USERS_GROUPS_TBL . " WHERE user_id = '" . $userID . "'");
		$r2 = $q2->fetchAll(PDO::FETCH_ASSOC);
		$userGroups = array();
		foreach($r2 as $ug){
			array_push($userGroups,$ug['station_id'].'-'.$ug['main_id']);
		}
		
		$r['userGroups'] = $userGroups;
		
		return $r;

	}


	/**
	 * Returns array of users
	 * @param  integer $page    Page number
	 * @param  string $orderBy sort by field
	 * @param  string $sort    sort description
	 * @return array
	 */
	public function getUsers($page,$orderBy='',$sort=''){
		if($orderBy==''){ $orderBy = 'fname'; }
		if($sort==''){ $sort = 'ASC'; }

		$offset = ($page-1)*USERS_PERPAGE;
		$q = $this->db->query("SELECT * FROM " . USERS_TBL . " ORDER BY " . $orderBy . " " . $sort . " LIMIT " . $offset . ", " . USERS_PERPAGE);

		$users = array();
		$users['result'] = $q->fetchAll(PDO::FETCH_ASSOC);
		
		$q2 = $this->db->query("SELECT * FROM " . USERS_TBL);
		$users['totalRecords'] = $q2->rowCount();

		return $users;
	}


	/**
	 * Adds a system user
	 * @param array $vars form field data
	 */
	public function addUser($vars){
		$q = $this->db->query("INSERT INTO " . USERS_TBL . "
			(date_registered,status,fname,lname,email,pword,user_level,user_type,office_phone,mobile_phone,email_notify,evernote_notify,evernote_email)
			values
			(
				NOW(),
				1,
				'" . ucfirst(strtolower($vars['fname'])) . "',
				'" . ucfirst(strtolower($vars['lname'])) . "',
				'" . $vars['email'] . "',
				'" . $vars['pword'] . "',
				'" . $vars['user_level'] . "',
				'" . $vars['user_type'] . "',
				'" . $vars['office_phone'] . "',
				'" . @$vars['mobile_phone'] . "',
				'" . @$vars['email_notify'] . "',
				'" . @$vars['evernote_notify'] . "',
				'" . @$vars['evernote_email'] . "'
			)
			");

		$userID = $this->db->lastInsertId();
		
		if(isset($vars['user_groups']) && is_array($vars['user_groups'])){
			foreach($vars['user_groups'] as $ugs){
				$ug = explode('-',$ugs);
				$q = $this->db->query("INSERT INTO " . USERS_GROUPS_TBL . "
					(user_id,station_id,main_id)
					values
					(
						'" . $userID . "',
						'" . $ug[0] . "',
						'" . $ug[1] . "'
					)"
				);
			}
		}

		return $vars;
	}


	/**
	 * Updates user's details
	 * @param  array $vars  form field data
	 * @param  string $uType user level
	 * @return boolean
	 */
	public function updateUser($vars,$uType){
		// admin or logged in user?
		$adminSQL='';
		if($uType=='admin'){ 
			$adminSQL = "status='" . $vars['status'] . "', user_level='" . $vars['user_level'] . "',";
		}
		// update password?
		$pwordSQL='';
		if($vars['pword']!=''){ $pwordSQL = "pword = '" . $vars['pword'] ."', "; }
		
		$q = $this->db->query("UPDATE " . USERS_TBL . "
			SET
			" . $adminSQL . "
			" . $pwordSQL . "
			fname='" . ucfirst(strtolower($vars['fname'])) . "',
			lname='" . ucfirst(strtolower($vars['lname'])) . "',
			user_type='" . @$vars['user_type'] . "',
			email='" . $vars['email'] . "',
			office_phone='" . $vars['office_phone'] . "',
			mobile_phone='" . @$vars['mobile_phone'] . "',
			email_notify='" . @$vars['email_notify'] . "',
			evernote_notify='" . @$vars['evernote_notify'] . "',
			evernote_email='" . @$vars['evernote_email'] . "'
			WHERE id = '" . $vars['id'] . "'
			");
		
		// delete group permissions
		$q = $this->db->query("DELETE FROM " . USERS_GROUPS_TBL . "
			WHERE user_id = '" . $vars['id'] . "'");

		// reset group permissions
		if(isset($vars['user_groups'])){
				foreach($vars['user_groups'] as $ugs){
				$ug = explode('-',$ugs);
				$q = $this->db->query("INSERT INTO " . USERS_GROUPS_TBL . "
					(user_id,station_id,main_id)
					values
					(
						'" . $vars['id']. "',
						'" . $ug[0] . "',
						'" . $ug[1] . "'
					)"
				);
			}
		}

		return true;
	}


	/**
	 * Updates user's details
	 * @param  array $vars  form field data
	 * @param  string $uType user level
	 * @return boolean
	 */
	public function updateProfile($vars){
		// update password?
		$pwordSQL='';
		if($vars['pword']!='' && $vars['pword_conf']!='' && $vars['pword']==$vars['pword_conf']){ $pwordSQL = "pword = '" . $vars['pword'] ."', "; }
		
		$q = $this->db->query("UPDATE " . USERS_TBL . "
			SET
			" . $pwordSQL . "
			fname='" . ucfirst(strtolower($vars['fname'])) . "',
			lname='" . ucfirst(strtolower($vars['lname'])) . "',
			office_phone='" . $vars['office_phone'] . "',
			mobile_phone='" . @$vars['mobile_phone'] . "',
			email_notify='" . @$vars['email_notify'] . "',
			evernote_notify='" . @$vars['evernote_notify'] . "',
			evernote_email='" . @$vars['evernote_email'] . "'
			WHERE id = '" . $vars['id'] . "'
			");

		return true;
	}


	/**
	 * Form select for user levels
	 * @param  string $userLevel current value
	 * @return echo
	 */
	function userLevelSelect($userLevel=''){
		
		try {
			$q = $this->db->query("SELECT id,level_name,notes FROM " . USER_LEVEL_TBL . "
				ORDER BY id ASC");
			$r = $q->fetchAll(PDO::FETCH_ASSOC);
			if(count($r)>0){
				echo '<p><label>User Access Level</label>';
				echo '<select class="form-control" name="user_level">';

				foreach($r as $level){
					if($level['id']==$userLevel){ $sel = 'selected="selected"'; }
					echo '<option value="' . $level['id'] .'" ' . $sel . '>' . $level['level_name'] .' - (' . $level['notes'] .')</option>';
					$sel='';
				}
				echo '</select></p>';
				
			 }
			else { return false; }
		} catch (Exception $e) {
			echo "User::userAssignmentFields - Data could not be retrieved from the databases.";
			exit;
		}
	}


	/**
	 * Form select for user types
	 * @param  string $userType current value
	 * @return echo
	 */
	function userTypeSelect($userType=''){
		
		try {
			$q = $this->db->query("SELECT id,type_name,notes FROM " . USER_TYPE_TBL . "
				ORDER BY type_name ASC");
			$r = $q->fetchAll(PDO::FETCH_ASSOC);
			if(count($r)>0){
				echo '<p><label>User Type</label>';
				echo '<select class="form-control user-type" name="user_type">';

				foreach($r as $uType){
					if($uType['id']==$userType){ $sel = 'selected="selected"'; }
					echo '<option value="' . $uType['id'] .'" ' . $sel . '>' . $uType['type_name'] .'</option>';
					$sel='';
				}
				echo '</select></p>';
				
			 }
			else { return false; }
		} catch (Exception $e) {
			echo "User::userTypeSelect - Data could not be retrieved from the databases.";
			exit;
		}
	}

	function userStatusSelect($status=''){
		
		echo '<p><label>User Status</label>';
		echo '<select class="form-control" name="status">';
		if($status==1){ $sel1 = 'selected="selected"'; }
		if($status==2){ $sel2 = 'selected="selected"'; }
		echo '<option value="1" ' . $sel1 . '>Active</option>';
		echo '<option value="2" ' . $sel2 . '>Inactive</option>';
		echo '</select></p>';
		
	}


	function userAssignmentFields($userGroups=''){
		try {
			$q = $this->db->query("SELECT id,station_name FROM " . STATIONS_TBL . "
				ORDER BY station_name ASC");
			$r = $q->fetchAll(PDO::FETCH_ASSOC);
			if(count($r)>0){
				echo '<div class="table-responsive">';
				echo '<table class="table">
				<thead>
                  <tr>
                    <th>Station</th>
                    <th class="text-center">Programming</th>
                    <th class="text-center">Sales</th>
                  </tr>
                </thead>
	                <tbody>';

				foreach($r as $station){
					echo '<tr>';
					echo '<td>' . $station['station_name'] . '</td>';
					
					// on-air 2
					echo '<td class="text-center bg-white"><label class="checkbox-inline">
					<input type="checkbox" name="user_groups[]" value="'.$station['id'].'-2"';
					if(@in_array($station['id'].'-2',$userGroups)){ echo 'checked="checked"'; }
					echo '> &nbsp;
					</label></td>';
					echo '<td class="text-center"><label class="checkbox-inline">
					<input type="checkbox" name="user_groups[]" value="'.$station['id'].'-1"';
					if(@in_array($station['id'].'-1',$userGroups)){ echo 'checked="checked"'; }
					echo ' > &nbsp;
					</label></td>';
					
					/* on-site 3
					echo '<td><label class="checkbox-inline">
					<input type="checkbox" name="user_groups[]" value="'.$station['id'].'-3-2" ';
					if(@in_array($station['id'].'-3-2',$userGroups)){ echo 'checked="checked"'; }
					echo '> Prog
					</label></td>';
					echo '<td><label class="checkbox-inline">
					<input type="checkbox" name="user_groups[]" value="'.$station['id'].'-3-1" ';
					if(@in_array($station['id'].'-3-1',$userGroups)){ echo 'checked="checked"'; }
					echo '> Sales
					</label></td>';
					
					// online 1
					echo '<td class="bg-white"><label class="checkbox-inline">
					<input type="checkbox" name="user_groups[]" value="'.$station['id'].'-1-2" ';
					if(@in_array($station['id'].'-1-2',$userGroups)){ echo 'checked="checked"'; }
					echo '> Prog
					</label></td>';
					echo '<td class="bg-white"><label class="checkbox-inline">
					<input type="checkbox" name="user_groups[]" value="'.$station['id'].'-1-1" ';
					if(@in_array($station['id'].'-1-1',$userGroups)){ echo 'checked="checked"'; }
					echo '> Sales
					</label></td>';*/
					
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				echo '</div>';
			 }
			else { return false; }
		} catch (Exception $e) {
			echo "User::userAssignmentFields - Data could not be retrieved from the databases.";
			exit;
		}
	}


	public function userProjectsSelect($userID,$selectedProject=''){
		
		try {
			$q = $this->db->query("SELECT p.* FROM " . PROJECTS_USERS_TBL . " up
			LEFT JOIN " . PROJECTS_TBL . " p on p.id=up.project_id 
			WHERE up.user_id='" . $userID . "'
			AND p.status < 3
			ORDER BY p.project_name
			ASC");
			$r = $q->fetchAll(PDO::FETCH_ASSOC);
			
			if(count($r)>0){
				echo '<p><label>Note is regarding: </label>';
				echo '<select class="form-control user-type" name="project_id">';
				echo '<option value="">- No particular project -</option>'; 
				foreach($r as $project){
					if($project['id']==$selectedProject){ $selected = 'selected="selected"'; }
					echo '<option value="' . $project['id'] . '" ' . $selected . '>' . $project['project_name'] . '</option>';
					$selected='';
				}
				echo '</select></p>';
			}

			else { return false; }
		} catch (Exception $e) {
			echo "User::projectsSelect - Data could not be retrieved from the databases.";
			exit;
		}
	}

	public function createNote($vars){
		try {
			$q = $this->db->prepare("INSERT INTO " . NOTES_TBL . "
				(user_id,date_entered,project_id,text)
				values
				('" . $vars['user_id'] ."',NOW(),'" . @$vars['project_id'] ."',?)
				");
			$q->bindParam(1,$vars['text']);
			if($q->execute()){
				if(isset($vars['evernote_email'])){
					Utility::autoResponder($vars['evernote_email'],'Project Note','Here');
				}
				return true;
			}

			else { return false; }
		} catch (Exception $e) {
			echo "User::createNote - Data could not be retrieved from the databases.";
			exit;
		}
	}

}
