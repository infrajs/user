import { View } from '/vendor/infrajs/view/View.js'
import { Global } from '/vendor/infrajs/layer-global/Global.js'
import { Load } from '/vendor/akiyatkin/load/Load.js'
import { Lang } from '/vendor/infrajs/lang/Lang.js'


let User = {
    is: function () {
        var user = this.get();
        return user.is;
    },
    token: () => {
        return View.getCOOKIE('token')
    },
    get: async () => {
        let src = '-user/whoami?token=' + User.token();
        Global.unload('user', src)
        let ans = await Load.fire('json', src)
        return ans.user
    },
    isAdmin: function () {
        var user = this.get();
        return user.admin;
    },
    email: async () => {
        var user = await this.get();
        return user.email;
    },
    lang: function (str) {
        if (typeof (str) == 'undefined') return Lang.name('user');
        return Lang.str('user', str);
    }
}
window.User = User
export { User }