
Event.one('Controller.oninit', function () {
	Template.scope['User'] = {};
	Template.scope['User']['lang'] = function (str) {
		return User.lang(str);
	};
});
