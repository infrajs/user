window.user={
	get:function(){
		var json='*user/rest.php';
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
	}
}