{root:}
	<div id="userbreadcrumbs"></div>
	<div id="userbody"></div>
{breadcrumbs:}
	<ol class="breadcrumb">
	  <li><a href="?">Home</a></li>
	  {crumb.child?:blong?:bshort}
	</ol>
	{bshort:}
		<li class="active">User {data.email}</li>
	{blong:}
		<li><a href="?user">User {data.email}</a></li>
		<li class="active">{parent.divs.userbody.0.childs[crumb.child.name].title}</li>
{user:}
	{:hat}
	{userbody:}
	<div class="row">
		<div class="col-md-6">
			{data.email?:userauth?:userguest}
			
		</div>
	</div>
	{userguest:}
		<p>
			Welcome to your personal account. At the moment you are unregistered user and functions are available with limitations.
		</p>
		<p>
			After registration you will have access to more features, you will also be able to choose a suitable package.
		</p>
		<p>
			<a href="?user/signup">{infrajs.unicks.user.childs.signup.title}</a>,
			<a href="?user/signin">{infrajs.unicks.user.childs.signin.title}</a>,
			<a href="?user/remind">{infrajs.unicks.user.childs.remind.title}</a>
		</p>
	{userauth:}
		<p>
			Welcome to your personal account. You are logged in as <b>{data.email}</b>.
		</p>
		{data.verify?:verified?:notverified}
		<p>
			<a href="?user/change">{infrajs.unicks.user.childs.change.title}</a>,
			<a href="?user/logout">{infrajs.unicks.user.childs.logout.title}</a>
		</p>
		{notverified:}
			<p>
				Your email has not been confirmed! <a href="?{crumb}/confirm">Confirm</a>.
			</p>
		{verified:}
			<p>
				Your email is confirmed!
			</p>
	{strsignup:}signup
	{strsignin:}signin
	{strremind:}remind
	{strlogout:}logout
	{strchange:}change
	{struser:}user
	{blink:}<li><a href="?user/{~key}">{title}</a></li>
{remind:}
	{:hat}
	{remindbody:}
		<p>To recover your password please fill out the form.</p>
		<p>{data.time?:alreadysent?:firstsentremind}</p>
		{:form}
			{:inp-email}
			<div class="control-group" style="margin-top:20px">
				<div class="controls">
					<button class="btn btn-success">Remind</button>
					<span style="margin-left:10px">
						<a href="?user/signup">{infrajs.unicks.user.childs.signup.title}</a>,
						<a href="?user/signin">{infrajs.unicks.user.childs.signin.title}</a>
					</span>
				</div>
			</div>
		{:/form}
	{firstsentremind:}
	<p>Will be sent an email with a link to recover your password.</p>
{remindkey:}
	{:hat}
	{remindkeybody:}
		<p>Specify a new password.</p>
		{:form}
		<input type="hidden" value="{crumb.child.name}" name="email">
		<input type="hidden" value="{crumb.child.child.name}" name="key">
		{:inp-password}
		{:inp-repeatpassword}
		<div class="control-group" style="margin-top:20px">
			<button class="btn btn-success">Submit</button>
		</div>
		{:/form}
{logout:}
	{:hat}
	{logoutbody:}
	{:form}
		<div class="control-group" style="margin-top:20px">
			<button class="btn btn-danger">Logout Now</button>
		</div>
	{:/form}
{confirm:}
	{:hat}
	{confirmbody:}
	{:form}
		{data.time?:alreadysent?:firstsent}
		
		<div class="control-group" style="margin-top:20px">
			<button class="btn btn-success">Send a letter to confirm</button>
		</div>
	{:/form}
	{alreadysent:}
		<p>Еmail was sent on {~date(:r,data.time)}</p>
	{firstsent:}
		<p>Will be sent an email with a link to confirm your address.</p>
{confirmkey:}
	<h1>{title}</h1>
	{data.msg:alert}
{change:}
	{:hat}
	{changebody:}
		<p>Complete the form to change your password.</p>
		{:form}
			{:inp-oldpassword}
			{:inp-newpassword}
			{:inp-repeatnewpassword}
			<div class="control-group" style="margin-top:20px">
				<!-- Button -->
				<div class="controls">
					<button class="btn btn-success">Change</button>
				</div>
			</div>
		{:/form}
{hat:}
	<h1>{title}</h1>
	{data.result?:{tplroot}body?data.msg:alert}
{statename:}tplroot
{signin:}
	{:hat}
	{signinbody:}
	<p>The entry to the personal account.</p>
	{:form}
		{:inp-email}
		{:inp-password}
		<div class="control-group" style="margin-top:20px">
			<!-- Button -->
			<div class="controls">
				<button class="btn btn-success">Sign In</button>
				<span style="margin-left:10px">
					<a href="?user/signup">{infrajs.unicks.user.childs.signup.title}</a>,
					<a href="?user/remind">{infrajs.unicks.user.childs.remind.title}</a>
				</span>
			</div>
		</div>
	{:/form}
{signup:}
	{:hat}
	{signupbody:}
	<p>Get started with a Free Account. Sign up in 10 seconds. No credit card or phone required.</p>
	{:form}
		{:inp-email}
		{:inp-password}
		{:inp-repeatpassword}
		<div class="control-group">
			<div class="checkbox">
				<label>
					<input name="terms" autosave="0" type="checkbox">I have read and agree to the <a href="?terms">terms of service</a>
				</label>
			</div>
        </div>
		<div class="control-group" style="margin-top:20px">
			<button class="btn btn-success">Sign Up</button>
			<span style="margin-left:10px">
				<a href="?user/signin">{unicks.user.childs.signin.title}</a>,
				<a href="?user/remind">{unicks.user.childs.remind.title}</a>
			</span>
		</div>
	{:/form}
{form:}
<div class="row">
			<div class="col-md-6">
	<form class="form-horizontal" action="infra/plugins/infra/theme.php?*user/user.php?type={tplroot}&submit=1" method="POST">		
		{/form:}
<div>
	{config.ans.msg:alert}
</div>
	</form>
	</div> 
	</div>
{inp-email:}
	<div class="control-group">
		<label class="control-label" for="email">E-mail</label>
		<div class="controls">
			<input type="email" id="email" name="email" placeholder="" class="form-control input-lg">
			<p class="help-block">Please provide your E-mail</p>
		</div>
	</div>
{inp-password:}
	<div class="control-group">
		<label class="control-label" for="password">Password</label>
		<div class="controls">
			<input type="password" id="password" name="password" placeholder="" class="form-control input-lg">
			<p class="help-block">Password should be at least 6 characters</p>
		</div>
	</div>
{inp-newpassword:}
	<div class="control-group">
		<label class="control-label" for="newpassword">New password</label>
		<div class="controls">
			<input type="password" id="newpassword" name="newpassword" placeholder="" class="form-control input-lg">
			<p class="help-block">Password should be at least 6 characters</p>
		</div>
	</div>
{inp-oldpassword:}
	<div class="control-group">
		<label class="control-label" for="password">Old password</label>
		<div class="controls">
			<input type="password" id="oldpassword" name="oldpassword" placeholder="" class="form-control input-lg">
			<p class="help-block">Specify your current password</p>
		</div>
	</div>
{inp-repeatnewpassword:}
	<div class="control-group">
		<label class="control-label" for="repeatnewpassword">Repeat new password</label>
		<div class="controls">
			<input type="password" id="repeatnewpassword" name="repeatnewpassword" placeholder="" class="form-control input-lg">
			<p class="help-block">Repeat your password</p>
		</div>
	</div>
{inp-repeatpassword:}
	<div class="control-group">
		<label class="control-label" for="repeatpassword">Repeat password</label>
		<div class="controls">
			<input type="password" id="repeatpassword" name="repeatpassword" placeholder="" class="form-control input-lg">
			<p class="help-block">Repeat your password</p>
		</div>
	</div>
{alert:}
	<div style="margin-top:20px" class="alert alert-{..result?:success?:danger}">
		{.}
	</div>