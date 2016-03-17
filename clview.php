<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once("models/header.php");

$code = $_POST['course_code'];
$year = $_POST['ayear'];

if (isset($_POST['send'])) {
	$successes[]=lang("CMR_ADDED");
}

$getgrades = $mysqli->prepare("SELECT  cw1, cw2, cw3, cw4, exam from gradestring where course_code='$code' and year(`timestamp`)=$year;");
$getgrades->execute();
$getgrades->bind_result($cw1, $cw2, $cw3, $cw4, $exam);
while ($getgrades->fetch()) {
	$grades[] = array('cw1' => $cw1,'cw2' => $cw2,'cw3' => $cw3,'cw4' => $cw4,'exam' => $exam);
}
$getgrades->close();

$getcoursename = $mysqli->prepare("SELECT course_name, cm_name from course where course_code = '$code'");
$getcoursename->execute();
$getcoursename->bind_result($name, $cm);
while ($getcoursename->fetch()) {
	$name = $name;
	$cm = $cm;
}
$getcoursename->close();

$a_cw1 = array(0,0,0,0,0,0,0,0,0,0);
$a_cw2 = array(0,0,0,0,0,0,0,0,0,0);
$a_cw3 = array(0,0,0,0,0,0,0,0,0,0);
$a_cw4 = array(0,0,0,0,0,0,0,0,0,0);
$a_exam = array(0,0,0,0,0,0,0,0,0,0);

foreach ($grades as $gradestring => $grade) {
for ($i=0; $i < 10; $i++) {if (($i*10 <= $grade['cw1'])and($grade['cw1']<$i*10+10)){$a_cw1[$i]++;}}

$cw1grades[] = $grade['cw1'];

for ($i=0; $i < 10; $i++) {if (($i*10 <= $grade['cw2'])and($grade['cw2']<$i*10+10)){$a_cw1[$i]++;}}

$cw2grades[] = $grade['cw2'];

for ($i=0; $i < 10; $i++) {if (($i*10 <= $grade['cw3'])and($grade['cw3']<$i*10+10)){$a_cw1[$i]++;}}

$cw3grades[] = $grade['cw3'];

for ($i=0; $i < 10; $i++) {if (($i*10 <= $grade['cw4'])and($grade['cw4']<$i*10+10)){$a_cw1[$i]++;}}

$cw4grades[] = $grade['cw4'];

for ($i=0; $i < 10; $i++) {if (($i*10 <= $grade['exam'])and($grade['exam']<$i*10+10)){$a_cw1[$i]++;}}

$examgrades[] = $grade['exam'];

}

$cw1mean = mean($cw1grades);
$cw2mean = mean($cw2grades);
$cw3mean = mean($cw3grades);
$cw4mean = mean($cw4grades);
$exammean = mean($examgrades);

$overall_grades = array($cw1mean, $cw2mean, $cw3mean, $cw4mean, $exammean);

$overallmean = mean($overall_grades);

$cw1median = median($cw1grades);
$cw2median = median($cw2grades);
$cw3median = median($cw3grades);
$cw4median = median($cw4grades);
$exammedian = median($examgrades);
$overallmedian = median($overall_grades);

$cw1dev = standard_deviation($cw1grades);
$cw2dev = standard_deviation($cw2grades);
$cw3dev = standard_deviation($cw3grades);
$cw4dev = standard_deviation($cw4grades);
$examdev = standard_deviation($examgrades);
$overalldev = standard_deviation($overall_grades);


$tier0 = array($a_cw1['0'], $a_cw2['0'], $a_cw3['0'], $a_cw4['0'], $a_exam['0']);
$tier1 = array($a_cw1['1'], $a_cw2['1'], $a_cw3['1'], $a_cw4['1'], $a_exam['1']);
$tier2 = array($a_cw1['2'], $a_cw2['2'], $a_cw3['2'], $a_cw4['2'], $a_exam['2']);
$tier3 = array($a_cw1['3'], $a_cw2['3'], $a_cw3['3'], $a_cw4['3'], $a_exam['3']);
$tier4 = array($a_cw1['4'], $a_cw2['4'], $a_cw3['4'], $a_cw4['4'], $a_exam['4']);
$tier5 = array($a_cw1['5'], $a_cw2['5'], $a_cw3['5'], $a_cw4['5'], $a_exam['5']);
$tier6 = array($a_cw1['6'], $a_cw2['6'], $a_cw3['6'], $a_cw4['6'], $a_exam['6']);
$tier7 = array($a_cw1['7'], $a_cw2['7'], $a_cw3['7'], $a_cw4['7'], $a_exam['7']);
$tier8 = array($a_cw1['8'], $a_cw2['8'], $a_cw3['8'], $a_cw4['8'], $a_exam['8']);
$tier9 = array($a_cw1['9'], $a_cw2['9'], $a_cw3['9'], $a_cw4['9'], $a_exam['9']);

$overall = array(0,0,0,0,0,0,0,0,0,0);
$overall['0'] = array_sum($tier0);
$overall['1'] = array_sum($tier1);
$overall['2'] = array_sum($tier2);
$overall['3'] = array_sum($tier3);
$overall['4'] = array_sum($tier4);
$overall['5'] = array_sum($tier5);
$overall['6'] = array_sum($tier6);
$overall['7'] = array_sum($tier7);
$overall['8'] = array_sum($tier8);
$overall['9'] = array_sum($tier9);

$stcount = count($grades);

echo "
</div>
	<div id='main' class='col-md-10'>";
	echo resultBlock($errors,$successes);
	echo "<div id='regbox'>"; 

ob_start();

		echo"<h1>Course Monitoring Report</h1>
		<table class='table table-bordered'>
		<tr><td>Academic Session:</td><td>$year</td></tr>
		<tr><td>Course Code:</td><td>$code</td></tr>
		<tr><td>Course Title:</td><td>$name</td></tr>
		<tr><td>Course Leader:</td><td>$loggedInUser->displayname</td></tr>
		<tr><td>Student Count:</td><td>$stcount</td></tr>
		</table>
		<table class='table table-bordered'>
		<thead><th colspan='7'>STATISTICS</th></thead>
		<tr><td></td><td>CW1</td><td>CW2</td><td>CW3</td><td>CW4</td><td>EXAM</td><td>OVERALL</td></tr>
		<tr><td>Mean</td><td>$cw1mean</td><td>$cw2mean</td><td>$cw3mean</td><td>$cw4mean</td><td>$exammean</td><td>$overallmean</td></tr>
		<tr><td>Median</td><td>$cw1median</td><td>$cw2median</td><td>$cw3median</td><td>$cw4median</td><td>$exammedian</td><td>$overallmedian</td></tr>
		<tr><td>Standard<br />Deviation</td><td>$cw1dev</td><td>$cw2dev</td><td>$cw3dev</td><td>$cw4dev</td><td>$examdev</td><td>$overalldev</td></tr>
		</table>
		<table class='table table-hover table-condensed table-cmr'><thead>
			<tr><th class='cmrtop'></th><th>0 - 9</th><th>10 - 19</th><th>20 - 29</th><th>30 - 39</th><th>40 - 49</th><th>50 - 59</th><th>60 - 69</th><th>70 - 79</th><th>80 - 89</th><th>90 +</th></tr></thead><tbody><tr><td>CW1</td>";
		foreach ($a_cw1 as $gradecount) {echo "<td>$gradecount</td>";}
	echo"</tr><tr><td>CW2</td>";
		foreach ($a_cw2 as $gradecount) {echo "<td>$gradecount</td>";}
	echo"</tr><tr><td>CW3</td>";
		foreach ($a_cw3 as $gradecount) {echo "<td>$gradecount</td>";}
	echo"</tr><tr><td>CW4</td>";
		foreach ($a_cw4 as $gradecount) {echo "<td>$gradecount</td>";}
	echo"</tr><tr><td>EXAM</td>";
		foreach ($a_exam as $gradecount) {echo "<td>$gradecount</td>";}
	echo"</tr><tr><td>OVERALL</td>";
		foreach ($overall as $gradecount) {echo "<td>$gradecount</td>";}

echo"</tr></tbody></table>";

$content = ob_get_contents();
$content = addslashes($content);

if (isset($_POST['send'])) {
	$today = date("Y-m-d");
	$comment = $_POST['comment'];
	$actions = $_POST['actions'];
	$addcmr = $mysqli->prepare("INSERT INTO cmr (`cl_name`, `course_code`, `cmr_timestamp`, `cmr_content`, `comment`, `actions`,`status`,`cm_name`) VALUES ('$loggedInUser->displayname', '$code', '$today', '$content', '$comment', '$actions','pending','$cm');");
	$addcmr->execute();
	$addcmr->close();
}

echo"<form class='form-horizontal' action='".$_SERVER['PHP_SELF']."' method='post'>
		<fieldset>
		<!-- Textarea -->
			<input type='hidden' name='course_code' value='".$code."' />
			<input type='hidden' name='ayear' value='".$year."' />

		<div>
			<h4>When you complete this section, at a minimum, you should address the following:</h4>
			<ol>
			<li>The overview of the Course Leader (to include comments on available statistics, the range of marks,<br />assessment diet and any issues affecting the delivery of the course this year).</li>
			<li>Student Evaluation and Feedback.</li>
			<li>Comments of the External Examiner.</li>
			<li>A review of the previous year’s action plan.</li>
			</ol>
		</div>

		<div class='form-group'>
		  <label class='col-md-12' for='comment'>General comment:</label>
		  <label class='col-md-2' for='comment'></label>
		  <div class='col-md-8'>                     
		    <textarea class='form-control' id='comment' name='comment' rows='10'></textarea>
		  </div>
		</div>

		<div class='form-group'>
		  <label class='col-md-12' for='actions'>Actions to be taken:</label>
		  <label class='col-md-2' for='actions'></label>
		  <div class='col-md-8'>                     
		    <textarea class='form-control' id='actions' name='actions' rows='10'></textarea>
		  </div>
		</div>

		<!-- Button -->
		<div class='form-group'>
		  <label class='col-md-2' for='send'></label>
		  <div class='col-md-4'><br />
		    <button id='send' name='send' class='btn btn-success'>Submit for approval</button>
		  </div>
		</div>

		</fieldset>
		</form>
	</div>
	</div>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";