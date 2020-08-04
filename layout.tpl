{root:}
	<div id="userbreadcrumbs"></div>
	<div id="userbody"></div>
{breadcrumbs:}
	<ol class="breadcrumb" style="float:right;">
		<li class="breadcrumb-item active">
			<a style="opacity:0.5; font-weight:{User.lang()=:ru?:bold}" href="?-env={Env.getName()}:lang=ru">RU</a>
			&nbsp;
			<a style="opacity:0.5; font-weight:{User.lang()=:en?:bold}" href="?-env={Env.getName()}:lang=en">EN</a>
		</li>
	</ol>
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="/">{User.lang(:Home)}</a></li>
	  {crumb.child?:blong?:bshort}
	</ol>
	{bshort:}
		<li class="breadcrumb-item active">{User.lang(:User)} {data.user.email}</li>
	{blong:}
		<li class="breadcrumb-item"><a href="/user">{User.lang(:User)} {data.user.email}</a></li>
		<li class="breadcrumb-item active">{User.lang(Controller.ids.user.childs[crumb.child.name].title)}</li>
{user:}
	{:hat}
	{userbody:}
	<div class="row">
		<div class="col-md-8">
			{data.user.email?:userauth?:userguest}
		</div>
	</div>
	{userguest:}
		
		{:lang.welcome-guest}
		<p>
			<a href="/user/signup">{User.lang(Controller.ids.user.childs.signup.title)}</a>,
			<a href="/user/signin">{User.lang(Controller.ids.user.childs.signin.title)}</a>,
			<a href="/user/remind">{User.lang(Controller.ids.user.childs.remind.title)}</a>
		</p>
	{userauth:}
		{:lang.welcome-user}
		{data.user.verify?:verified?:notverified}
		<p>
			<a href="/user/change">{User.lang(Controller.ids.user.childs.change.title)}</a>,
			<a href="/user/logout">{User.lang(Controller.ids.user.childs.logout.title)}</a>
		</p>
		{data.user.admin?:adminlink}
		{adminlink:}
		<p>
			<a class="text-danger" href="/user/list">{User.lang(Controller.ids.user.childs.list.title)}</a>
		</p>
		{notverified:}
			<p>
				{User.lang(:Your email has not been confirmed)}! <a href="/{crumb}/confirm">{User.lang(:Confirm)}</a>.
			</p>
		{verified:}
			<p>
				{User.lang(:Your email is confirmed)}!
			</p>
	{strsignup:}signup
	{strsignin:}signin
	{strremind:}remind
	{strlogout:}logout
	{strchange:}change
	{struser:}user
{remind:}
	{:hat}
	{remindbody:}
		{:lang.welcome-remind}
		<p>{data.time?:alreadysent?:firstsentremind}</p>
		{:form}
			{:inp-email}
			<div class="form-group" style="margin-top:20px">
				<div class="controls">
					<p>
						<button class="btn btn-success">{User.lang(:Remind)}</button>
					</p>
					<p>
						<a href="/user/signup">{User.lang(Controller.ids.user.childs.signup.title)}</a>,
						<a href="/user/signin">{User.lang(Controller.ids.user.childs.signin.title)}</a>
					</p>
				</div>
			</div>
		{:/form}
	{firstsentremind:}
	{:lang.descr-remind}
{remindkey:}
	{:hat}
	{remindkeybody:}
		{:lang.welcome-remindkey}
		{:form}
		<input type="hidden" value="{crumb.child.name}" name="email">
		<input type="hidden" value="{crumb.child.child.name}" name="key">
		{:inp-password}
		{:inp-repeatpassword}
		<div class="form-group" style="margin-top:20px">
			<button class="btn btn-success">{User.lang(:Submit)}</button>
		</div>
		{:/form}
{logout:}
	{:hat}
	{logoutbody:}
	{:form}
		<div class="form-group" style="margin-top:20px">
			<button class="btn btn-danger">{User.lang(:Logout Now)}</button>
		</div>
	{:/form}
{confirm:}
	{:hat}
	{confirmbody:}
	{:form}
		{:firstsent}
		{data.datemail?:alreadysent}

		<div class="form-group" style="margin-top:20px">
			<button class="btn btn-success">{User.lang(:Send a letter to confirm)}</button>
		</div>
	{:/form}
	{alreadysent:}
		<p><i>{User.lang(:Еmail was sent on)} {~date(:Y-m-d H:i:s,data.datemail)}</i></p>
	{firstsent:}
		{:lang.descr-confirm}
{confirmkey:}
	<h1>{User.lang(title)}</h1>
	{data.msg:alert}
{change:}
	{:hat}
	{changebody:}
		{:lang.welcome-change}
		{:form}
			{:inp-oldpassword}
			{:inp-newpassword}
			{:inp-repeatnewpassword}
			<div class="form-group" style="margin-top:20px">
				<!-- Button -->
				<div class="controls">
					<button class="btn btn-success">{User.lang(:Change)}</button>
				</div>
			</div>
		{:/form}
{hat:}
	<h1>{User.lang(title)}</h1>
	{config.ans.msg?config.ans.msg:alert}
	{config.ans.result??:datamsg}
	{datamsg:}
		{data.msg?data.msg:alert?:{tplroot}body}
{statename:}tplroot
{signin:}
	{:hat}
	{signinbody:}
	{:lang.welcome-signin}
	{:form}
		{:inp-email}
		{:inp-password}
		<div class="form-group" style="margin-top:20px">
			<!-- Button -->
			<div class="controls">
				<p>
					<button class="btn btn-success">{User.lang(Controller.ids.user.childs.signin.title)}</button>
				</p>
				<p>
					<a href="/user/signup">{User.lang(Controller.ids.user.childs.signup.title)}</a>,
					<a href="/user/remind">{User.lang(Controller.ids.user.childs.remind.title)}</a>
				</p>
			</div>
		</div>
	{:/form}
{signup:}
	{:hat}
	{signupbody:}
	{:lang.welcome-signup}
	{:form}
		{:inp-email}
		{:inp-password}
		{:inp-repeatpassword}
		
		<div class="form-check">
			<input class="form-check-input" name="terms" autosave="0" type="checkbox">{User.lang(:I have read and agree to the)} <a href="{~path(Config.get(:struser).terms)}">{User.lang(:terms of service)}</a>
		</div>
		<div class="form-group" style="margin-top:20px">
			<p>
				<button class="btn btn-success">{User.lang(Controller.ids.user.childs.signup.title)}</button>
			</p>
			<p>
				<a href="/user/signin">{User.lang(Controller.ids.user.childs.signin.title)}</a>,
				<a href="/user/remind">{User.lang(Controller.ids.user.childs.remind.title)}</a>
			</p>
		</div>
	{:/form}
	{struser:}user
{form:}
	<style>
		#{div} .controls a {
			white-space: nowrap;
		}
	</style>
	<div class="row">
		<div class="col-md-8">
			<form 
			data-layerid="{id}"
			data-autosave="{autosavename}" 
			data-goal="{goal}" 
			data-global="{global}"
			data-recaptcha2="user"
			class="form-horizontal" action="/-user/api/{tplroot}?lang={User.lang()}&token={User.token()}" method="POST">
	{/form:}
			</form>
			<script type="module">
				import { Form } from '/vendor/akiyatkin/form/Form.js'
				import { View } from '/vendor/infrajs/view/View.js'
				let div = document.getElementById('{div}')
				let form = div.getElementsByTagName('form')[0]
				Form.fire('init', form)
				Form.after('submit', (f, ans) => {
					if (f !== form) return
					if (ans.token || ans.token === '') {
						View.setCOOKIE('token', ans.token)
					}
					
				})
				Form.done('submit', async (f, ans) => {
					if (f !== form) return
					if (!ans.result) return
					//Минимизируем связь сервера с интерфейсом.
					
					let { Crumb } = await import('/vendor/infrajs/controller/src/Crumb.js')
					let back = '/user'
					if (Crumb.get.back) {
						if (Crumb.get.back = 'ref') {
							back = Crumb.referrer
						} else {
							back = Crumb.get.back
						}
					}
					Crumb.go(back)
					
					let action = "{tplroot}";
					if (~['signin'].indexOf(action)) return;
					let { Popup } = await import('/vendor/infrajs/popup/Popup.js')
					await Popup.success(ans.msg)
					
				})
			</script>
		</div>
	</div>
{inp-email:}
	<div class="form-group">
		<label class="control-label" for="email">Email</label>
		<div class="controls">
			<input type="email" id="email" name="email" placeholder="" class="form-control input-lg">
			<p class="text-muted">{User.lang(:Please provide your E-mail)}</p>
		</div>
	</div>
{inp-password:}
	<div class="form-group">
		<label class="control-label" for="password">{User.lang(:Password)}</label>
		<div class="controls">
			<input type="password" id="password" name="password" placeholder="" class="form-control input-lg">
			<p class="text-muted">{User.lang(:Password should be at least 6 characters)}</p>
		</div>
	</div>
{inp-newpassword:}
	<div class="form-group">
		<label class="control-label" for="newpassword">{User.lang(:New password)}</label>
		<div class="controls">
			<input type="password" id="newpassword" name="newpassword" placeholder="" class="form-control input-lg">
			<p class="text-muted">{User.lang(:Password should be at least 6 characters)}</p>
		</div>
	</div>
{inp-oldpassword:}
	<div class="form-group">
		<label class="control-label" for="password">{User.lang(:Old password)}</label>
		<div class="controls">
			<input type="password" id="oldpassword" name="oldpassword" placeholder="" class="form-control input-lg">
			<p class="text-muted">{User.lang(:Specify your current password)}</p>
		</div>
	</div>
{inp-repeatnewpassword:}
	<div class="form-group">
		<label class="control-label" for="repeatnewpassword">{User.lang(:Repeat new password)}</label>
		<div class="controls">
			<input type="password" id="repeatnewpassword" name="repeatnewpassword" placeholder="" class="form-control input-lg">
			<p class="text-muted">{User.lang(:Repeat your password)}</p>
		</div>
	</div>
{inp-repeatpassword:}
	<div class="form-group">
		<label class="control-label" for="repeatpassword">{User.lang(:Repeat password)}</label>
		<div class="controls">
			<input type="password" id="repeatpassword" name="repeatpassword" placeholder="" class="form-control input-lg">
			<p class="text-muted">{User.lang(:Repeat your password)}</p>
		</div>
	</div>
{alert:}
	<div style="margin-top:20px;" class="alert alert-{..result?:success?:danger}">
		{.}
	</div>
{lang::}-user/i18n/{User.lang()}.tpl