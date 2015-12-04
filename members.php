<?php
	// Connects to the Database 
	include('connect.php');
	connect();
	
	//if the login form is submitted 
	if (isset($_POST['submit'])) {

        $failedAttempts = mysql_query("SELECT * from failed_logins ORDER BY date DESC LIMIT 5")or die(mysql_error());
        $numFailed = mysql_num_rows($failedAttempts);
        
        if ( $numFailed > 4 ) { // 5 or more failures
            mysql_data_seek($failedAttempts, 4); // Seek to fifth failure
            $fifthFailure = mysql_fetch_array( $failedAttempts );
            $fifteen_min = 15 * 60; // 15 min x 60 sec
            if ( (time() - $fifteen_min) < $fifthFailure['date'] ) { // If fifth failure within 15 mins of current time
                die('<h1>Too many failed attempts. Try again later.</h1>');
            }
        }
		
		$_POST['username'] = mysql_real_escape_string(htmlspecialchars(trim($_POST['username'])));
		if(!$_POST['username'] | !$_POST['password']) {
			die('<p>You did not fill in a required field.
			Please go back and try again!</p>');
		}
		
		$passwordHash = sha1($_POST['password']);
		
		$check = mysql_query("SELECT * FROM users WHERE username = '".$_POST['username']."'")or die(mysql_error());
		
 		//Gives error if user already exist
 		$check2 = mysql_num_rows($check);
		if ($check2 == 0) {
			die("<p>Sorry, user name does not exisits.</p>");
		}
		else
		{
			while($info = mysql_fetch_array( $check )) 	{
			 	//gives error if the password is wrong
				if ($passwordHash != $info['pass']) {
                    mysql_query("INSERT INTO failed_logins (username, date) VALUES('".$_POST['username']."', '".time()."')")or die(mysql_error());
					die('Incorrect password, please try again.');
				}
			}
            $hour = time() + 3600; 
			setcookie(hackme, $_POST['username'], $hour); 
			setcookie(hackme_pass, $passwordHash, $hour);
			header("Location: members.php");
		}
	}
		?>  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>hackme</title>
<link href="style.css" rel="stylesheet" type="text/css" media="screen" />
<?php
	include('header.php');
?>
<div class="post">
	<div class="post-bgtop">
		<div class="post-bgbtm">
        <h2 class = "title">hackme bulletin board</h2>
        	<?php
            if(!isset($_COOKIE['hackme'])){
				 die('Why are you not logged in?!');
			}else
			{
				print("<p>Logged in as <a>$_COOKIE[hackme]</a></p>");
			}
			?>
        </div>
    </div>
</div>

<?php
	$threads = mysql_query("SELECT * FROM threads ORDER BY date DESC")or die(mysql_error());
	while($thisthread = mysql_fetch_array( $threads )){
?>
	<div class="post">
	<div class="post-bgtop">
	<div class="post-bgbtm">
		<h2 class="title"><a href="show.php?pid=<? echo $thisthread[id] ?>"><? echo $thisthread[title]?></a></h2>
							<p class="meta"><span class="date"> <? echo date('l, d F, Y',$thisthread[date]) ?> - Posted by <a href="#"><? echo $thisthread[username] ?> </a></p>

	</div>
	</div>
	</div> 

<?php
}
	include('footer.php');
?>
</body>
</html>
