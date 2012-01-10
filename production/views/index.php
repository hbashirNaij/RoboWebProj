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

if ((isset($_SESSION['robo'])))
{
	header('Location: dashboard.php');
}
global $result; // global definition of result
if(isset($_POST['login']))
{
	if(isset($_POST['pwd']))
	{
		if(isset($_POST['username']))
		{
			$password = ($_POST['pwd']);
			$username = $_POST['username'];
			$login = new login();
			global $result; // allows $results to be used later in script
			$result = $login->checkLogin($username, $password);
			if(!is_string($result))
			{
				$_SESSION['robo'] = "$username";
				header('Location: dashboard.php');
			}
		}
	}
}

echo '<!doctype html>';
echo '<head>';
echo '	<meta charset="utf-8">';
echo '	<title>Robotics 1072 Login</title>';
echo '	<meta name="description" content="">';
echo '	<meta name="author" content="">';
echo '	<link rel="stylesheet" type="text/css" href="stylesheets/style.css">';
//echo '  <link rel="stylesheet" type="text/css" href="reset.css">';
echo '</head>';
echo '<body>';
echo '	<div id="floater"></div>';
echo '	<div id="loginWindowWrap" class="clearfix">';
echo '		<div id="loginWindow">';
echo '			<h1>Login</h1>';
echo '			<form id="loginForm" method="post" name="loginForm" action="">';
echo '				<fieldset>';
echo '					<label for="username">Username </label>';
echo '					<input type="text" name="username" id="username" class="bigform" value=""/>';
echo '				</fieldset>';
echo '				<fieldset>';
echo '					<label id="password" >Password </label>';
echo '					<input type="password" name="pwd" class="bigform" id="password"value="" />';
echo '				</fieldset>';
if (is_string($result))
{
	echo $result; // outputs error message, if login was unsuccessful exits
}
echo '				<fieldset>';
echo '				<input name="login" type="submit" class="login" value="login" />';
echo '				</fieldset>';
echo '			</form>';
echo '			<p><a href="registration.php">Don\'t have an account yet? Register!</a></p><br />';
//echo '			<p><a href="resetpass.php">Click Here to reset your password.</a></p>';
echo '		</div>';
echo '	</div>';
echo '	<footer>';
echo '	</footer>';
echo '</body>';
echo '</html>';
?>