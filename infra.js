
import { Template } from '/vendor/infrajs/template/Template.js'
import { User} from '/vendor/infrajs/user/User.js'

Template.scope['User'] = {}
Template.scope['User']['lang'] = function (str) {
	return User.lang(str)
}
