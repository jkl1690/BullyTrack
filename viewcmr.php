<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once("models/header.php");

$id=$_GET['id'];

$dltc = '';

$getcmr = $mysqli->prepare("SELECT cmr_content, comment, actions from cmr where cmr_id='$id';");
$getcmr->execute();
$getcmr->bind_result($content, $comment, $actions);
while ($getcmr->fetch()) {
	$content = stripcslashes($content);
    $cmr = array('content' => $content, 'comment' => $comment, 'actions' => $actions);
}
$getcmr->close();

if (isset($_POST['approve'])) {
	$decline = $mysqli->prepare("UPDATE cmr SET `status`='approved' WHERE `cmr_id`='$id';");
	$decline->execute();
	$decline->close();
	$successes[] = lang("APPROVED");

	$today = date("Y-m-d");

	$subject = "[BT.GA]:Admin - CMR for $code from $today";

	$message = "<!DOCTYPE html><html><head><style>table{border:2px dotted grey;}</style></head><body>".$cmr['content']."<p><h2>General Comment:</h2>".$cmr['comment']."</p><p><h2>Actions to be taken:</h2>".$cmr['actions']."</p></body></html>";

	$headers = "From: bot@bullytrack.ga\r\nReply-To: bot@bullytrack.ga\r\nContent-type:text/html;charset=UTF-8\r\n";

	$getemails = $mysqli->prepare("SELECT email from uc_users where id in (SELECT user_id from uc_user_permission_matches where permission_id = '3' or permission_id = '6')");
	$getemails->execute();
	$getemails->bind_result($email);
	while ($getemails->fetch()) {
		$emails[] = $email;
	}
	$getemails->close();

	foreach ($emails as $email) {
		$sendcmr = mail($email, $subject, $message, $headers);
	}

} elseif (isset($_POST['decline'])) {
	$approve = $mysqli->prepare("UPDATE cmr SET `status`='declined' WHERE `cmr_id`='$id';");
	$approve->execute();
	$approve->close();
	$successes[] = lang("DECLINED");
} 

if (isset($_POST['dltcomment'])) {
	$getexpiry = $mysqli->prepare("SELECT cmr_timestamp from cmr where cmr_id = '$id';");
	$getexpiry->execute();
	$getexpiry->bind_result($ts);
	while ($getexpiry->fetch()) {
		$ts = new DateTime("$ts");
		$expiry = $ts->add(new DateInterval('P10D'));
	}
	$getexpiry->close();

	$today = new DateTime("now");
	print_r($expiry->format('Y-m-d'));
	print_r($today->format('Y-m-d'));
 	if ($expiry > $today) {
		$dltc = $mysqli->prepare("UPDATE cmr SET `dlt_comment`='".$_POST['dltcomment']."' WHERE `cmr_id`='$id';");
		$dltc->execute();
		$dltc->close();
		$successes[] = lang("DLT_COMMENT");
 	} else {
 		$errors[] = lang("DLT_EXPIRED");
 	}

	$getdltc =$mysqli->prepare("SELECT dlt_comment from cmr where `cmr_id`='$id';");
	$getdltc->execute();
	$getdltc->bind_result($dltc);
	while ($getdltc->fetch()) {
		$dltc = $dltc;
	}
	$getdltc->close();
}

echo "
</div>
	<div id='main' class='col-md-10'>";
	echo resultBlock($errors,$successes);
	echo "<script type='text/javascript' src='chart/Chart.js'></script>
		<div id='regbox'><canvas id='myChart' width='600' height='400'></canvas>
<script>
var ctx = document.getElementById('myChart').getContext('2d');
var data = {
    labels: ['CW1', 'CW2', 'CW3', 'CW4', 'EXAM'],
    datasets: [
        {
            label: 'My First dataset',
            fillColor: 'rgba(220,220,220,0.2)',
            strokeColor: 'rgba(220,220,220,1)',
            pointColor: 'rgba(220,220,220,1)',
            pointStrokeColor: '#fff',
            pointHighlightFill: '#fff',
            pointHighlightStroke: 'rgba(220,220,220,1)',
            data: [65, 59, 80, 81, 56, 55, 40]
        }
    ]
};
var myLineChart = new Chart(ctx).Line(data);
</script>";
	echo "".$cmr['content']."<br /><h3>General comment:</h3>".$cmr['comment']."<br /><h3>Actions to be taken:</h3>".$cmr['actions']."";
	if ($loggedInUser->checkPermission(array(5))){
	echo "<form class='form-horizontal' action='".$_SERVER['PHP_SELF']."?id=$id' method='post'>
			<fieldset>

			<!-- Button (Double) -->
			<div class='form-group'>
			  <label class='col-md-4 control-label' for='approve'></label>
			  <div class='col-md-8'>
			    <button id='approve' name='approve' class='btn btn-success'>Approve</button>
			    <button id='decline' name='decline' class='btn btn-danger'>Decline</button>
			  </div>
			</div>

			</fieldset>
			</form>";
			}
	if ($loggedInUser->checkPermission(array(6))){
		echo "<form class='form-horizontal' action='".$_SERVER['PHP_SELF']."?id=$id' method='post'><fieldset>
				<div class='form-group'>
				  <label class='col-md-12' for='comment'>DLT Comment:</label>
				  <div class='col-md-12'>                     
				    <textarea class='form-control' id='dltcomment' name='dltcomment' rows='5'>$dltc</textarea>
				  </div>
				</div>
				<!-- Button (Double) -->
				<div class='form-group'>
				  <div class='col-md-8'>
				    <button id='dltc' name='dltc' class='btn btn-success'>Submit comment</button>
				  </div>
				</div>
			</fieldset></form>";
	}
	if ($loggedInUser->checkPermission(array(3))){
	$getdltc =$mysqli->prepare("SELECT dlt_comment from cmr where `cmr_id`='$id';");
	$getdltc->execute();
	$getdltc->bind_result($dltc);
	while ($getdltc->fetch()) {
		if (is_null($dltc)) {
			$dltc = 'DLT comment is not available';
		}
		$dltc = $dltc;
	}
	$getdltc->close();
	echo "<label class='col-md-12' for='comment'>DLT Comment:</label>
	<div class='col-md-12'><textarea disabled class='form-control' id='dltcomment' name='dltcomment' rows='5'>$dltc</textarea></div>";
	}
echo"	</div>
	</div>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";