{test-subject:}Тестовое письмо
{test:} Добрый день! Это тестовое письмо.

Сайт: {host}
Адрес: {path}
Email: {email}
Время: {time}

{signup-subject:}Подтвердите ваш аккаут на {host}
{signup:}Добрый день!
Перед тем, как Вам будут доступны все функции сайта нужно подтвердить email. 
Для этого перейдите по следующей ссылке.

<a href="{path}user/confirmkey/{email}/{key}">{path}user/confirmkey/{email}/{key}</a>

{:footer}

{remind-subject:}Изменение пароля {host}
{remind:}Добрый день!
Если Вам действительно требуется изменить пароль, перейдите по следующей ссылке и укажите новый пароль.

<a href="{path}user/remindkey/{email}/{key}">{path}user/remindkey/{email}/{key}</a>

{:footer}

{newpass-subject:}Ваш пароль на {host} был изменён
{newpass:}Пароль от вашего аккаунта {email} был изменён на новый.

Если эти изменения сделали вы, значит всё ок!

Если вы этого не делали, то свяжитесь с поддержкой <a href="{path}{conf.support}">{path}{conf.support}</a> и мы с этим разберёмся.

{:footer}

{confirm-subject:}Подтвердите ваш аккаунт {host}
{confirm:}Добрый день!
Для завершения регистрации нужно подтвердить Ваш email. Для этого нужно перейти по следующей ссылке.

<a href="{path}user/confirmkey/{email}/{key}">{path}user/confirmkey/{email}/{key}</a>

{:footer}

{welcome-subject:}Добро пожаловать на {host}
{welcome:}Добрый день!

Спасибо за регистрацию!

Ваш аккаунт: <a href="{path}user">{path}user</a>

{:footer}

{footer:}С уважением, <a href="{host}">{host}</a>
Поддержка: <a href="{path}{conf.support}">{path}{conf.support}</a>

{conf.vk:vk}
{conf.twitter:twitter}
{conf.facebook:facebook}

{twitter:}Twitter: <a href="{.}">{.}</a>
{facebook:}Facebook: <a href="{.}">{.}</a>
{vk:}Наша группа ВКонтакте: <a href="{.}">{.}</a>