<?php

	session_start();

	if(!$_SESSION['valid']) header('Location: index.php');
?>
	<html>
		<form action="massms.php" method="post">
			Message: <input type="text" name="message" value="" size="160" maxlength="160"/><br />
			<input type="submit" name="submit" value="Svim posmatracima" />
		</form>
		<a href="logout.php">Logout</a><br>
		<small>&lt;rbr&gt;</small>
	</html>