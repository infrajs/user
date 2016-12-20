window.User = {
	is:function(){
		var user=this.get();
		return user.is;
	},
	get:function(){
		var json='*user/get.php';
		infrajs.global.unload('user',json)
		return infra.loadJSON(json);
	},
	isAdmin:function(){
		var user=this.get();
		return user.admin;
	},
	getEmail:function(){
		var user=this.get();
		return user.email;
	},
	lang: function (str){
		if(typeof(str) == 'undefined') return Lang.name('user');
		return Lang.str('user', str);
	}
}