<?php
use itlife\user\User;

infra_test(true);

if ($_GET['email']) {
	User::sentEmail($_GET['email'], 'test');
	header('Location: ?*user/sentEmail.php');
}

?>
Sent: <input class="input" style="padding:1px 5px" placeholder="Email" type="email" name="useremail" 
onkeypress=" if (event.keyCode === 13) location.href='?*user/sentEmail.php?email='+this.value;">

