{root:}
	<div id="userbreadcrumbs"></div>
	<div id="userbody"></div>
{breadcrumbs:}
	<ol class="breadcrumb" style="float:right;">
		<li class="active">
			<a style="opacity:0.5; font-weight:{User.lang()=:ru?:bold}" href="?-env=:lang=ru">RU</a>
			<a style="opacity:0.5; font-weight:{User.lang()=:en?:bold}" href="?-env=:lang=en">EN</a>
		</li>
	</ol>
	<ol class="breadcrumb">
	  <li><a href="/">{User.lang(:Home)}</a></li>
	  {crumb.child?:blong?:bshort}
	</ol>
	{bshort:}
		<li class="active">{User.lang(:User)} {data.email}</li>
	{blong:}
		<li><a href="/user">{User.lang(:User)} {data.email}</a></li>
		<li class="active">{User.lang(Controller.ids.user.childs[crumb.child.name].title)}</li>
{user:}
	{:hat}
	{userbody:}
	<div class="row">
		<div class="col-md-6">
			{data.email?:userauth?:userguest}
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
		{data.verify?:verified?:notverified}
		<p>
			<a href="/user/change">{User.lang(Controller.ids.user.childs.change.title)}</a>,
			<a href="/user/logout">{User.lang(Controller.ids.user.childs.logout.title)}</a>
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
			<div class="control-group" style="margin-top:20px">
				<div class="controls">
					<button class="btn btn-success">{User.lang(:Remind)}</button>
					<span style="margin-left:10px">
						<a href="/user/signup">{User.lang(Controller.ids.user.childs.signup.title)}</a>,
						<a href="/user/signin">{User.lang(Controller.ids.user.childs.signin.title)}</a>
					</span>
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
		<div class="control-group" style="margin-top:20px">
			<button class="btn btn-success">{User.lang(:Submit)}</button>
		</div>
		{:/form}
{logout:}
	{:hat}
	{logoutbody:}
	{:form}
		<div class="control-group" style="margin-top:20px">
			<button class="btn btn-danger">{User.lang(:Logout Now)}</button>
		</div>
	{:/form}
{confirm:}
	{:hat}
	{confirmbody:}
	{:form}
		{data.time?:alreadysent?:firstsent}

		<div class="control-group" style="margin-top:20px">
			<button class="btn btn-success">{User.lang(:Send a letter to confirm)}</button>
		</div>
	{:/form}
	{alreadysent:}
		<p>{User.lang(:Еmail was sent on)} {~date(:Y-m-d H:i:s,data.time)}</p>
	{firstsent:}
		{:lang.descr-confirm}
{confirmkey:}
	<h1>{title}</h1>
	{data.msg:alert}
{change:}
	{:hat}
	{changebody:}
		{:lang.welcome-change}
		{:form}
			{:inp-oldpassword}
			{:inp-newpassword}
			{:inp-repeatnewpassword}
			<div class="control-group" style="margin-top:20px">
				<!-- Button -->
				<div class="controls">
					<button class="btn btn-success">{User.lang(:Change)}</button>
				</div>
			</div>
		{:/form}
{hat:}
	<h1>{User.lang(title)}</h1>
	{data.msg?data.msg:alert?:{tplroot}body}
{statename:}tplroot
{signin:}
	{:hat}
	{signinbody:}
	{:lang.welcome-signin}
	{:form}
		{:inp-email}
		{:inp-password}
		<div class="control-group" style="margin-top:20px">
			<!-- Button -->
			<div class="controls">
				<button class="btn btn-success">{User.lang(Controller.ids.user.childs.signin.title)}</button>
				<span style="margin-left:10px">
					<a href="/user/signup">{User.lang(Controller.ids.user.childs.signup.title)}</a>,
					<a href="/user/remind">{User.lang(Controller.ids.user.childs.remind.title)}</a>
				</span>
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
		<div class="control-group">
			<div class="checkbox">
				<label>
					<input name="terms" autosave="0" type="checkbox">{User.lang(:I have read and agree to the)} <a href="{~path(Config.get(:struser).terms)}">{User.lang(:terms of service)}</a>
				</label>
			</div>
	    </div>
		<div class="control-group" style="margin-top:20px">
			<button class="btn btn-success">{User.lang(Controller.ids.user.childs.signup.title)}</button>
			<span style="margin-left:10px">
				<a href="/user/signin">{User.lang(Controller.ids.user.childs.signin.title)}</a>,
				<a href="/user/remind">{User.lang(Controller.ids.user.childs.remind.title)}</a>
			</span>
		</div>
	{:/form}
	{struser:}user
{form:}
	<div class="row">
		<div class="col-md-6">
			<form class="form-horizontal" action="/-user/get.php?type={tplroot}&submit=1" method="POST">
	{/form:}
				<div>
					{config.ans.msg:alert}
				</div>
			</form>
		</div>
	</div>
{inp-email:}
	<div class="control-group">
		<label class="control-label" for="email">Email</label>
		<div class="controls">
			<input type="email" id="email" name="email" placeholder="" class="form-control input-lg">
			<p class="help-block">{User.lang(:Please provide your E-mail)}</p>
		</div>
	</div>
{inp-password:}
	<div class="control-group">
		<label class="control-label" for="password">{User.lang(:Password)}</label>
		<div class="controls">
			<input type="password" id="password" name="password" placeholder="" class="form-control input-lg">
			<p class="help-block">{User.lang(:Password should be at least 6 characters)}</p>
		</div>
	</div>
{inp-newpassword:}
	<div class="control-group">
		<label class="control-label" for="newpassword">{User.lang(:New password)}</label>
		<div class="controls">
			<input type="password" id="newpassword" name="newpassword" placeholder="" class="form-control input-lg">
			<p class="help-block">{User.lang(:Password should be at least 6 characters)}</p>
		</div>
	</div>
{inp-oldpassword:}
	<div class="control-group">
		<label class="control-label" for="password">{User.lang(:Old password)}</label>
		<div class="controls">
			<input type="password" id="oldpassword" name="oldpassword" placeholder="" class="form-control input-lg">
			<p class="help-block">{User.lang(:Specify your current password)}</p>
		</div>
	</div>
{inp-repeatnewpassword:}
	<div class="control-group">
		<label class="control-label" for="repeatnewpassword">{User.lang(:Repeat new password)}</label>
		<div class="controls">
			<input type="password" id="repeatnewpassword" name="repeatnewpassword" placeholder="" class="form-control input-lg">
			<p class="help-block">{User.lang(:Repeat your password)}</p>
		</div>
	</div>
{inp-repeatpassword:}
	<div class="control-group">
		<label class="control-label" for="repeatpassword">{User.lang(:Repeat password)}</label>
		<div class="controls">
			<input type="password" id="repeatpassword" name="repeatpassword" placeholder="" class="form-control input-lg">
			<p class="help-block">{User.lang(:Repeat your password)}</p>
		</div>
	</div>
{alert:}
	<div style="margin-top:20px;" class="alert alert-{..result?:success?:danger}">
		{.}
	</div>
{lang::}-user/i18n/{Lang.name(:struser)}.tpl