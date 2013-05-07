<?php
if (isset($_SESSION['login_user'])) {
      include("auth/lock.php");
} else {
      include("auth/config.php");
}
include("header.php");
include_once('includes/common.php');

// Export Function
function export_csv($type) {
  $content="\"Timestamp\"\n";
  if($type=="coffee") {
    $file = 'tmp/coffees-'.$_SESSION['login_user'].'.csv';
    $sql = sprintf(
          "SELECT cdate 
           FROM cs_coffees
           WHERE cuid = %d",
           $dbconn->real_escape_string($_SESSION['login_id']));
  } elseif($type=="mate") {
    $file = 'tmp/mate-'.$_SESSION['login_user'].'.csv';
    $sql = sprintf(
          "SELECT mdate 
           FROM cs_mate
           WHERE cuid = %d",
           $dbconn->real_escape_string($_SESSION['login_id']));
  }
  if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
  }
  while ($row = mysql_fetch_array($result)) {
    $content .= "\"".$row['cdate']."\"\n"; 
  }
  file_put_contents($file, $content);
} 

// Switching between modes
if(isset($_POST['exportflag'])) {
  echo("<div class=\"white-box\">");
  export_csv(coffee);
  export_csv(mate);
  echo("</div>");
} elseif(isset($_POST['updateflag'])) {
  echo("<div class=\"white-box\">");
  echo("<h2>Updating...</h2>");
  echo("</div>");
} elseif(isset($_POST['deleteflag'])) {
  echo("<div class=\"white-box\">");
  echo("<h2>Deleting...</h2>");
  echo("</div>");
}
?>
		<div class="white-box">
          <h2>Update your profile</h2>
        <p>
        <b>General</b><br/>
        <input type="text" name="Login" maxlength="20" placeholder="Username" class="register_field_standard" />
        <input type="password" name="Password" maxlength="20" placeholder="Password" class="register_field_standard" />
        <input type="text" name="Email" maxlength="50" placeholder="E-Mail" class="register_field_standard" /></p>
        <p><b>Additional</b><br/>
        <input type="text" name="Forename" maxlength="20" placeholder="Forename" class="register_field_standard" />
        <input type="text" name="Name" maxlength="20" placeholder="Name" class="register_field_standard" />
        <input type="text" name="Location" maxlength="20" placeholder="Location" class="register_field_standard" />
        </p>
          
        </div>

	    <div class="white-box">
          <h2>Your Activity</h2>
          <p>Your data is yours. You will recieve the csv files via mail.</p>
           
				<form action="" method="post" id="exportform">
					<input class="imadecoffee" type="submit" value="Export, please!" id="coffee_plus_one" />
                    <input type='hidden' id='exportflag' name='exportflag' value='' />
				</form>
        </div>


		<div class="white-box">
          <h2>Export your data</h2>
          <p>Your data is yours. You will recieve the csv files via mail.</p>
           
				<form action="" method="post" id="exportform">
					<input class="imadecoffee" type="submit" value="Export, please!" id="coffee_plus_one" />
                    <input type='hidden' id='exportflag' name='exportflag' value='' />
				</form>
        </div>


<?php
	include("footer.php");
?>
