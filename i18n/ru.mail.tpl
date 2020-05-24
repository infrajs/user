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

	<p><a href="{path}user/confirmkey/{email}/{key}">{path}user/confirmkey/{email}/{key}</a></p>

	{:footer}

{remind-subject:}Изменение пароля {host}
{remind:}
	<p>Добрый день!</p>
	<p>Если Вам действительно требуется изменить пароль, перейдите по следующей ссылке и укажите новый пароль.</p>

	<p><a href="{path}user/remindkey/{email}/{key}">{path}user/remindkey/{email}/{key}</a></p>

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
	<p>Для завершения регистрации нужно подтвердить Ваш email. Для этого нужно перейти по следующей ссылке.</p>

	<p><a href="{path}user/confirmkey/{email}/{key}">{path}user/confirmkey/{email}/{key}</a></p>

	{:footer}

{welcome-subject:}Добро пожаловать на {host}
{welcome:}
	<p>Добрый день!</p>

	<p>Спасибо за регистрацию!</p>

	<p>Ссылка для быстрого входа: <a href="{link}">{path}</a></p>
	{:handauth}

	{:footer}
{userdata-subject:}Данные для авторизации на сайте {host}
{userdata:}
	<p>Добрый день!</p>
	<p>
		Перейдите по <b><a href="{link}&src={page}">ссылке</a></b> для быстрой авторизации
	</p>
	{:handauth}
	{:footer}
{handauth:}
	<p>
		Для <a href="{path}user/signin">авторизации вручную</a> используйте:<br>
		Логин: {email}<br>
		Пароль: {user.password}<br>
	</p>
{footer:}
	<p>
		С уважением, <a href="{host}">{host}</a><br>
		Поддержка: <a href="{path}{conf.support}">{path}{conf.support}</a>

		{conf.vk:vk}
		{conf.twitter:twitter}
		{conf.facebook:facebook}
	</p>
{twitter:}Twitter: <a href="{.}">{.}</a>
{facebook:}Facebook: <a href="{.}">{.}</a>
{vk:}Наша группа ВКонтакте: <a href="{.}">{.}</a>