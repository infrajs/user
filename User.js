let User = {
    is: function() {
        var user = this.get();
        return user.is;
    },
    get: function() {
        var json = '-user/get.php';
        Global.unload('user', json)
        return Load.loadJSON(json);
    },
    isAdmin: function() {
        var user = this.get();
        return user.admin;
    },
    getEmail: function() {
        var user = this.get();
        return user.email;
    },
    lang: function(str) {
        if (typeof(str) == 'undefined') return Lang.name('user');
        return Lang.str('user', str);
    }
}
window.User = User
export { User }