
import { Template } from '/vendor/infrajs/template/Template.js'
import { User } from '/vendor/infrajs/user/User.js'
import { Env } from '/vendor/infrajs/env/Env.js'
import { City } from '/vendor/akiyatkin/city/City.js'

Template.scope['User'] = {}
Template.scope['User']['lang'] = str => User.lang(str)
Template.scope['User']['token'] = () => User.token()

Env.hand('change', async () => {
    let timezone = Intl.DateTimeFormat ? Intl.DateTimeFormat().resolvedOptions().timeZone : ''
    let city_id = City.id()
    let param = { timezone, city_id }
    await User.post('setenv', param)
})