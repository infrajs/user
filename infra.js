
import { Template } from '/vendor/infrajs/template/Template.js'
import { User } from '/vendor/infrajs/user/User.js'

Template.scope['User'] = {}
Template.scope['User']['lang'] = str => User.lang(str)
Template.scope['User']['token'] = () => User.token()
