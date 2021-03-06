<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once("models/header.php");

$cid = $_GET['cid'];

if (!empty($_POST)) {
	$course_code = trim($_POST['course_code']);
	$course_name = trim($_POST['course_name']);
	$lecturer_name = trim($_POST['lecturer_name']);
	$cl_name = trim($_POST['cl_name']);
	$cm_name = trim($_POST['cm_name']);
	$faculty_id = trim($_POST['faculty_id']);
	if (!empty($lecturer_name) and !empty($cl_name) and !empty($cm_name) and !empty($faculty_id)) {
	$addcourse = $mysqli->prepare("UPDATE course SET
		course_code='$course_code',
		lecturer_name='$lecturer_name',
		cl_name='$cl_name',
		cm_name='$cm_name',
		course_name='$course_name',
		faculty_id='$faculty_id'
		WHERE course_code='$cid';");
	$addcourse->execute();
	$addcourse->close();
	$cid = $course_code;
	$successes[] = lang("COURSE_ADDED");
	}
}

$getcoursedata = $mysqli->prepare("SELECT course_name, lecturer_name, cl_name, cm_name, faculty_id from course where course_code='$cid'");
$getcoursedata->execute();
$getcoursedata->bind_result($course_name, $lecturer_name, $cl_name, $cm_name, $faculty_id);
while ($getcoursedata->fetch()) {
	$course = array('course_code' => $cid,'course_name' => $course_name,'lecturer_name' => $lecturer_name,'cl_name' => $cl_name,'cm_name' => $cm_name,'faculty_id' => $faculty_id);
}
$getcoursedata->close();

$getlecturers = $mysqli->prepare("SELECT display_name FROM uc_users where id in (select user_id from uc_user_permission_matches where permission_id = '7');");
$getlecturers->execute();
$getlecturers->bind_result($lecturer_name);
while ($getlecturers->fetch()){
  $lecturers[] = $lecturer_name;
  }
$getlecturers->close();

$getleaders = $mysqli->prepare("SELECT display_name FROM uc_users where id in (select user_id from uc_user_permission_matches where permission_id = '4');");
$getleaders->execute();
$getleaders->bind_result($leader_name);
while ($getleaders->fetch()){
  $leaders[] = $leader_name;
  }
$getleaders->close();

$getmoders = $mysqli->prepare("SELECT display_name FROM uc_users where id in (select user_id from uc_user_permission_matches where permission_id = '5');");
$getmoders->execute();
$getmoders->bind_result($moder_name);
while ($getmoders->fetch()){
  $moders[] = $moder_name;
  }
$getmoders->close();

$getfaculties = $mysqli->prepare("SELECT faculty_id, faculty_name FROM faculty");
$getfaculties->execute();
$getfaculties->bind_result($faculty_id, $faculty_name);
while ($getfaculties->fetch()){
	$faculties[] = array('faculty_id' => $faculty_id, 'faculty_name' => $faculty_name);
	}
$getfaculties->close();

echo "
</div>
	<div id='main' class='col-md-10'>";
	echo resultBlock($errors,$successes);
	echo "
		<div id='regbox'><h1>Edit course ".$course['course_code'].":</h1>
		<form name='admincourses' action='".$_SERVER['PHP_SELF']."?cid=$cid' method='post' class='form-horizontal'>
		<fieldset>

		<!-- Text input-->
		<div class='form-group'>
		  <label class='col-md-2 control-label' for='course_code'>Code:</label>  
		  <div class='col-md-4'>
		  <input id='course_code' name='course_code' value='".$course['course_code']."' class='form-control input-md' required=' type='text'>
		    
		  </div>
		</div>

		<!-- Text input-->
		<div class='form-group'>
		  <label class='col-md-2 control-label' for='course_name'>Name:</label>  
		  <div class='col-md-4'>
		  <input id='course_name' name='course_name' value='".$course['course_name']."' class='form-control input-md' required=' type='text'>
		    
		  </div>
		</div>

		<!-- Select Basic -->
		<div class='form-group'>
		  <label class='col-md-2 control-label' for='lecturer_name'>Lecturer:</label>
		  <div class='col-md-4'>
		    <select id='lecturer_name' name='lecturer_name' class='form-control'>
		      <option value=''>- select lecturer -</option>";
		      foreach ($lecturers as $lecturer) {
		      	echo "<option value='$lecturer'>$lecturer</option>";
		      }
		echo "</select>
		  </div>
		</div>

		<!-- Select Basic -->
		<div class='form-group'>
		  <label class='col-md-2 control-label' for='cl_name'>Leader:</label>
		  <div class='col-md-4'>
		    <select id='cl_name' name='cl_name' class='form-control'>
		      <option value=''>- select leader -</option>";
		      foreach ($leaders as $leader) {
		      	echo "<option value='$leader'>$leader</option>";
		      }
		echo "</select>
		  </div>
		</div>

		<!-- Select Basic -->
		<div class='form-group'>
		  <label class='col-md-2 control-label' for='cm_name'>Moderator:</label>
		  <div class='col-md-4'>
		    <select id='cm_name' name='cm_name' class='form-control'>
		      <option value=''>- select moderator -</option>";
		      foreach ($moders as $moder) {
		      	echo "<option value='$moder'>$moder</option>";
		      }
		echo "</select>
		  </div>
		</div>

		<!-- Select Basic -->
		<div class='form-group'>
		  <label class='col-md-2 control-label' for='faculty_name'>Faculty:</label>
		  <div class='col-md-4'>
		    <select id='faculty_name' name='faculty_id' class='form-control'>
		      <option value=''>- select faculty -</option>";
		      foreach ($faculties as $faculty) {
		      	echo "<option value='".$faculty['faculty_id']."'>".$faculty['faculty_name']."</option>";
		      }
		echo "</select>
		  </div>
		</div>

		<!-- Button -->
		<div class='form-group'>
		  <label class='col-md-2 control-label' for='submit'></label>
		  <div class='col-md-4'>
		    <button id='submit' name='submit' class='btn btn-primary'>Submit</button>
		  </div>
		</div>

		</fieldset>
		</form>";

echo"	</div>
	</div>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";