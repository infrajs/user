# Личный кабинет пользователя

## Установка через composer.json

```
{
	"require":{
		"infrajs/user":"~1"
	}
}
```
js и css подключаются автоматически с помощью [infrajs/collect](https://github.com/infrajs/collect)

Добавить слой [infrajs/controller](https://github.com/infrajs/controller)

```{
	"external":"-user/user.layer.json"
}```



## Использование
Плагин предоставляет слой 
/-user/user.layer.json объект window.User
В конфиге user.admins массив email адресов, при совпадении user.isAdmin возвращает true

user::isAdmin
user::get
user::getEmail

user::sentEmail