import { View } from '/vendor/infrajs/view/View.js'
import { Global } from '/vendor/infrajs/layer-global/Global.js'
import { Load } from '/vendor/akiyatkin/load/Load.js'
import { Lang } from '/vendor/infrajs/lang/Lang.js'

let User = {
    lang: Lang.fn('user'),
    token: () => View.getCOOKIE('token'),
    get: async () => {
        let src = '-user/whoami?token=' + User.token();
        Global.unload('user', src)
        let ans = await Load.fire('json', src)
        return ans.user
    }
}
window.User = User
export { User }