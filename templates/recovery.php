<div class="reg-block">
	<div class="left">
		<h2>Восстановление пароля</h2>
		<h4>Придумайте пароль длиной от 5 до 20 символов.</h4>
		#SCRIPT#
		<form action="#ACTION_FILE#" enctype="multipart/form-data" method="post" style="padding-top:20px">
			<div class="input_container">
				<input id="username" type="email" name="username" value="#USERNAME#" readonly>
				<label for="username">E-mail</label>
			</div>
			<username></username>
			<div class="input_container">
				<input id="password" type="password" name="password" value="" placeholder="">
				<label for="password">Пароль</label>
				<span id="show-password">
					<img id="img-password" src="/img/site/eyeopen.png" title="Показать пароль">
				</span>
			</div>
			<password></password>
			<div style="padding-top:20px">
				<input type="hidden" name="username" value="#USERNAME#">
				<button class="btn btn-blue" name="savepassword">Сохранить</button>
			</div>
		</form>
	</div>
	<div class="right"></div>
</div>