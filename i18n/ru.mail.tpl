{test-subject:}Тестовое письмо
{test:} 
	<p>Добрый день! Это тестовое письмо.</p>
	<p>
		Сайт: {host}<br>
		Адрес: {path}<br>
		Email: {email}<br>
		Время: {time}<br>
	</p>

{signup-subject:}Подтвердите ваш аккаут на {host}
{signup:}
	<p>Добрый день!</p>
	<p>Перед тем, как Вам будут доступны все функции сайта, нужно подтвердить email. 
	Для этого перейдите по следующей ссылке.</p>

	<p><a href="{path}/user/confirmkey/{email}/{key}">{path}/user/confirmkey/{email}/{key}</a></p>

	{:footer}

{remind-subject:}Изменение пароля {host}
{remind:}
	<p>Добрый день!</p>
	<p>Если Вам действительно требуется изменить пароль, перейдите по следующей ссылке и укажите новый пароль.</p>

	<p><a href="{path}/user/remindkey/{email}/{key}">{path}/user/remindkey/{email}/{key}</a></p>

	{:footer}

{newpass-subject:}Ваш пароль на {host} был изменён
{newpass:}
	<p>Пароль от вашего аккаунта {email} был изменён на новый.</p>

	<p>Если эти изменения сделали вы, значит всё ок!</p>

	<p>Если вы этого не делали, то свяжитесь с поддержкой <a href="{path}{conf.support}">{path}{conf.support}</a> и мы с этим разберёмся.</p>

	{:footer}

{confirm-subject:}Подтвердите ваш аккаунт {host}
{confirm:}
	<p>Добрый день!</p>
	{:msgconfirm}
	{:footer}

{welcome-subject:}Добро пожаловать на {host}
{welcome:}
	<p>Добрый день!</p>
	<p>Спасибо за регистрацию!</p>
	{:msgconfirm}
	{:handauth}
	{:footer}

{userdata-subject:}Данные для авторизации на сайте {host}
{userdata:}
	<p>Добрый день!</p>

	{:handauth}

	{:footer}

{handauth:}
	<p>Ссылка для быстрой авторизации: <a href="{path}{page}?token={token}">{path}{page}?token={token}</a></p>
	<p>
		Ссылка для ввода логина и пароля: <a href="{path}/user/signin">{path}/user/signin</a><br>
		Логин: {email}<br>
		Пароль: {password}<br>
	</p>
{footer:}
	<p>
		С уважением, <a href="{host}">{host}</a><br>
		Поддержка: <a href="{path}{conf.support}">{path}{conf.support}</a>
	</p>
{msgconfirm:}
	<p>
		Для завершения регистрации нужно подтвердить Ваш email. Для этого перейдите по следующей ссылке:
	 	<a href="{path}/user/confirmkey/{email}/{key}">{path}/user/confirmkey/{email}/{key}</a>
	</p>