{test-subject:}Test letter
{test:}
	<p>Hi! It's a test letter. Enjoy!</p>
	<p>
		host: {host}
		path: {path}
		email: {email}
		time: {time}
	</p>

{signup-subject:}Confirm Your {host} Account
{signup:}
	<p>Hi!</p>
	<p>Before we can give you access to all features, please confirm your account by clicking the link below.</p>

	<p><a href="{path}/user/confirmkey/{email}/{key}">{path}/user/confirmkey/{email}/{key}</a></p>

	{:footer}

{remind-subject:}Change password on {host} account
{remind:}
	<p>Hi!</p>
	<p>If you wish to change your password, click the link below and enter your new password.</p>

	<p><a href="{path}/user/remindkey/{email}/{key}">{path}/user/remindkey/{email}/{key}</a></p>

	{:footer}

{newpass-subject:}Your {host} password has been changed
{newpass:}
	<p>The password for your account — {email} — was changed today.</p>
	<p>If you made this change, then it's all good!</p>
	<p>If you didn't make this change, contact support <a href="{path}{conf.support}">{path}{conf.support}</a> and we'll look into it for you.</p>
	{:footer}

{confirm-subject:}Confirm Your {host} Account
{confirm:}
	<p>Hi!</p>
	{:msgconfirm}
	{:footer}

{welcome-subject:}Welcome to {host}
{welcome:}
	<p>Welcome!</p>
	<p>Thank you for creating an account.</p>
	{:msgconfirm}
	{:handauth}
	{:footer}

{userdata-subject:}Данные для авторизации на сайте {host}
{userdata:}
	<p>Добрый день!</p>
	{:handauth}
	{:footer}
{footer:}
	<p>
		Thanks, {host}<br>
		Support: <a href="{path}{conf.support}">{path}{conf.support}</a>
	</p>
{soc:}
	Follow the {host} team on Twitter: {.}<br>
	We're also on Facebook: {.}<br>
{handauth:}
	<p>Quick login link: <a href="{path}{page}?token={token}">{path}{page}</a></p>
	<p>
		For manual authorization <a href="{path}/user/signin">{path}/user/signin</a><br>
		Login: {email}<br>
		Password: {password}<br>
	</p>
{msgconfirm:}
	<p>
		To complete your account, click the link below and confirm your email address.
		<a href="{path}/user/confirmkey/{email}/{key}">{path}/user/confirmkey/{email}/{key}</a>
	</p>