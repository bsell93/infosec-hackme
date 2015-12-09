<?php
// Connects to the Database 
  session_start();
	include('connect.php');
	include('csrf.php');
	connect();
	
	$csrf = new csrf();
	
	$token_id = $csrf->get_token_id();
	$token_value = $csrf->get_token($token_id);
	
	$form_names = $csrf->form_names(array('message','post_submit', 'title'), false);
	
	//if the login form is submitted 
	if (isset($_POST[$form_names['post_submit']])) {
		if ($csrf->check_valid('post')) {
			$_COOKIE['hackme'] = mysql_real_escape_string(htmlspecialchars(trim($_COOKIE['hackme']))); 
			$_COOKIE['hackme_pass'] = mysql_real_escape_string(htmlspecialchars(trim($_COOKIE['hackme_pass']))); 
			$check = mysql_query("SELECT * FROM users WHERE username = '".$_COOKIE['hackme']."' AND pass = '".$_COOKIE['hackme_pass']."'");
	
			$check2 = mysql_num_rows($check);
			if ($check2 == 0) {
				die("<p>Please Login.</p>");
			} else {		
				$_POST[$form_names['title']] = mysql_real_escape_string(htmlspecialchars(trim($_POST[$form_names['title']])));
				$_POST[$form_names['message']] = mysql_real_escape_string(htmlspecialchars(trim($_POST[$form_names['message']])));
				if(!$_POST[$form_names['title']] | !$_POST[$form_names['message']]) {
					include('header.php');
					die('<p>You did not fill in a required field.
					Please go back and try again!</p>');
				}
				
				mysql_query("INSERT INTO threads (username, title, message, date) VALUES('".$_COOKIE['hackme']."', '". $_POST[$form_names['title']]."', '". $_POST[message]."', '".time()."')")or die(mysql_error());
				
				//mysql_query("INSERT INTO threads (username, title, message, date) VALUES('".$_COOKIE['hackme']."', '". $_POST['title']."', '". $_POST[message]."', CURDATE() )")or die(mysql_error());
				
				
				header("Location: members.php");
			}
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
            
            <h2 class="title">NEW POST</h2>
            <p class="meta">by <a href="#"><? echo $_COOKIE['hackme'] ?> </a></p>
            <p> do not leave any fields blank... </p>
            
            <form method="post" action="post.php">
            Title: <input type="text" name="<?= $form_names['title']; ?>" maxlength="50"/>
            <br />
            <br />
            Posting:
            <br />
            <br />
            <textarea name="<?= $form_names['message']; ?>" cols="120" rows="10" id="message"></textarea>
            <br />
            <br />
						<input type="hidden" name="<?= $token_id; ?>" value="<?= $token_value; ?>" />
            <input name="post_submit" type="submit" id="post_submit" value="POST" />
            </form>
        </div>
    </div>
</div>

<?php
	include('footer.php');
?>
</body>
</html>
