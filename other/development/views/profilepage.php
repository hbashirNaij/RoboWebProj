<?php
session_start();
// autoloader code
// loads classes as needed, eliminates the need for a long list of includes at the top
spl_autoload_register(function ($className) { 
    $possibilities = array( 
        '../controllers'.DIRECTORY_SEPARATOR.$className.'.php', 
        '../back_end'.DIRECTORY_SEPARATOR.$className.'.php', 
        '../views'.DIRECTORY_SEPARATOR.$className.'.php', 
        $className.'.php' 
    ); 
    foreach ($possibilities as $file) { 
        if (file_exists($file)) { 
            require_once($file); 
            return true; 
        } 
    } 
    return false; 
});

if (!(isset($_SESSION['robo'])))
{
	header('Location: index.php');
	exit;
}

if(isset($_POST['logout']))
{
	unset($_SESSION['robo']);
	header('Location: index.php');
	exit;
}
$username = $_SESSION['robo'];
$controller = new profileController();
$userInfo = $controller->getUserInfo($username);
if (isset($_POST['update']))
{
	// if any values are null, it will simply write null values to db, perfectly allowable
	//$firstname = $_POST['firstname'];
	//$lastname = $_POST['lastname'];
	//$email = $_POST['email'];
	//$cellphone = $_POST['cellphone'];
	$subteam = $_POST['subteam'];
	$fullname = $_POST['fullname'];
	$phone = $_POST['phone'];
	$gradyear = $_POST['gradyear'];
	$email = $_POST['email'];
	$pemail = $_POST['pemail'];
	$newUserInfo = array(
	"UserFullName" => $fullname,
	"UserPhoneNumber" => $phone,
	"UserYear" => $gradyear,
	"UserParentsEmail" => $pemail,
	"UserEmail" => $email,
	"UserSubteam" => $subteam
	);
	// calls controller to input
	$controller->updateUserInfo($username, $newUserInfo);
	header('Location: profilepage.php');
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Harker Robotics 1072</title>
	
	<link rel="stylesheet" href="stylesheets/form.css" type="text/css" />
	
</head>
<body>
	<div id="mainWrapper">
		<div id="floater"></div>
		<div id="dashboardWindow" class="clearfix">
			<div id="nav">
				<div id="navbar">
					<ul>
						<li><a href="dashboard.php">Home</a></li>
						<li><a href="profilepage.php">My Profile</a></li>
						<li><a href="viewmyforms.php">Purchase Orders</a></li>
						<?php
						$username = $_SESSION['robo'];
						$api = new roboSISAPI();
						if ($api->isAdmin($username))
						{
							echo '<li><a href="admin_dashboard.php">Admin</a></li>';
						}
						?>
					</ul>
				</div>
				<div id="login_status">
					<p>Logged in as: <?php echo $_SESSION['robo']; // echos the username?></p>
					<form method="post" name="form" action="">
					<fieldset>
						<input name="logout" type="submit" class="logout" value="Logout" />
					</fieldset>
					</form>
				</div> <!-- end of login_status -->
			</div>
			
			<h1>The Harker School - Robotics Team 1072</h1>
			
			<div id="dashboard-checkin" class="clearfix">
				<div id="forms" class="clearfix">
					<h2><?php echo $_SESSION['robo']; ?>&#x27;s Profile</h2>
				</div>
				<div id="forms-submit">
					<form id="orderform" method="post" action="">
							<?php
							// getting all variables
							$subteam = $userInfo["UserSubteam"];
							$fullname = $userInfo["UserFullName"];
							$phone = $userInfo["UserPhoneNumber"];
							$email = $userInfo["UserEmail"];
							$pemail = $userInfo["UserParentsEmail"];
							$gradyear = $userInfo["UserYear"];
							$usertype = $userInfo['UserType'];
							if (is_null($usertype))
								$usertype = "Regular";
							echo "<h2>Type: $usertype</h2>";
							
							echo "<br />";
							
							echo "<fieldset id=\"subteam_select\">\n";
							echo "<label for=\"subteam\">Subteam</label>\n";
							echo "<fieldset>\n";
							if (is_null($subteam) || empty($subteam))
							{
								echo "<input type=\"radio\" name=\"subteam\" value=\"Mechanical\" /> M\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Electronics\" /> E\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Programming\" /> P\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Operational\" /> O\n";
							}
							if ($subteam == "Mechanical")
							{
								echo "<input type=\"radio\" name=\"subteam\" value=\"Mechanical\" checked=\"checked\" /> M\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Electronics\" /> E\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Programming\" /> P\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Operational\" /> O\n";
							}
							if ($subteam == "Electronics")
							{
								echo "<input type=\"radio\" name=\"subteam\" value=\"Mechanical\" /> M\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Electronics\" checked=\"checked\" /> E\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Programming\" /> P\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Operational\" /> O\n";
							}
							if ($subteam == "Operational")
							{
								echo "<input type=\"radio\" name=\"subteam\" value=\"Mechanical\" /> M\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Electronics\" /> E\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Programming\" /> P\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Operational\" checked=\"checked\" /> O\n";
							}
							if ($subteam == "Programming")
							{
								echo "<input type=\"radio\" name=\"subteam\" value=\"Mechanical\" /> M\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Electronics\" /> E\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Programming\" checked=\"checked\" /> P\n";
								echo "<input type=\"radio\" name=\"subteam\" value=\"Operational\" /> O\n";
							}
								echo "</fieldset>\n";
							echo "</fieldset>\n";
							
							//<!-- End temp block -->
							
							echo "<fieldset>\n
							 	<label id=\"fullname\">Full Name</label>\n
							 	<input type=\"text\" name=\"fullname\" id=\"fullname\" class=\"field\" value=\"$fullname\" />\n
							 </fieldset>\n
							 <fieldset>\n
							 	<label id=\"phone\" >Phone Number</label>\n
							 	<input type=\"text\" name=\"phone\" id=\"phone\" class=\"field\" value=\"$phone\" />\n
							 </fieldset>\n
							 <fieldset>\n
							 	<label for=\"email\">Email</label>\n
							 	<input type=\"text\" name=\"email\" id=\"email\" class=\"field\" value=\"$email\"/>\n
							 </fieldset>\n
							 <fieldset>\n
							 	<label id=\"pemail\" >Parent's Email</label>\n
							 	<input type=\"text\" name=\"pemail\" id=\"pemail\" class=\"field\" value=\"$pemail\" />\n
							 </fieldset>\n
							 <fieldset>\n
							 	<label id=\"gradyear\" >Graduation Year</label>\n
							 	<input type=\"text\" name=\"gradyear\" id=\"gradyear\" class=\"field\" value=\"$gradyear\" />\n
							 </fieldset>\n";
							
							
							?>
							
							<div id="form-submitbuttons">
							<fieldset>
								<input name="update" type="submit" class="save" value="update" />
							</fieldset>
							</div>
							
					</form>
				</div>
			</div>
			
		</div>
		<footer>
		</footer>
	</div>
</body>
</html>