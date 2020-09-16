import { View } from '/vendor/infrajs/view/View.js'
import { Global } from '/vendor/infrajs/layer-global/Global.js'
import { Load } from '/vendor/akiyatkin/load/Load.js'
import { Lang } from '/vendor/infrajs/lang/Lang.js'

let User = {
    lang: Lang.fn('user'),
    token: () => {
    	const token = View.getCOOKIE('token')
    	if (token) return token
    	const session_id = View.getCOOKIE('infra_session_id')
    	const pass = View.getCOOKIE('infra_session_pass')
    	return session_id+'-'+pass
    },
    // get: async () => {
    //     let src = '-user/whoami?token=' + User.token();
    //     Global.unload('user', src)
    //     let ans = await Load.fire('json', src)
    //     return ans.user
    // },
    logout: () => {
    	View.setCOOKIE('token')
    	View.setCOOKIE('infra_session_id')
    	View.setCOOKIE('infra_session_pass')
    	Global.check('user')
    },
    get: (type, param) => {
		let src = User.src(type, param)
		Global.unload('user', src)
		return Load.fire('json', src)
	},
	post: async (type, param) => {
		let submit = 1
		param = { ...param, submit }
		let src = User.src(type, param)
		let ans = await Load.puff('json', src)
		Global.set('user')
		return ans
	},
	src: (type, param) => {
        let token = User.token()
        let lang = Lang.name()
        param = { ...param, lang, token }
		let args = [];
		for (let key in param) args.push(key + '=' + encodeURIComponent(param[key]))
		args = args.join('&')
		let src = '-user/api/' + type + '?' + args
		return src
	},
}
window.User = User
export { User }