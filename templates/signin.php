<div class="reg-block">
	<div class="left">
		<h2>Авторизация</h2>
		<h3>Для входа введите адрес<br>электронной почты и пароль</h3>
		#SCRIPT#
		<form action="#ACTION_FILE#" enctype="multipart/form-data" method="post" style="padding-top:20px">
			<div class="input_container">
				<input id="username" type="email" name="username" value="#USERNAME#" maxlength="64" placeholder="" autocomplete="off">
				<label for="username">E-mail</label>
			</div>
			<username></username>
			<div class="input_container">
				<input id="password" type="password" name="password" value="#PASSWORD#" placeholder="">
				<label for="password">Пароль</label>
				<span id="show-password">
					<img id="img-password" src="/img/site/eyeopen.png" title="Показать пароль">
				</span>
			</div>
			<password></password>
			<div style="padding-top:20px">
				<button name="signin" class="btn btn-blue">Войти</button>
				<a href="/remember.php" class="forgot">Забыли пароль?</a>
			</div>
		</form>
	</div>
	<div class="right"></div>
</div>