<div class="reg-block">
	<div class="left">
		<h2>Восстановление пароля</h2>
		<p style="padding-bottom:21px">Укажите адрес электронной почты. На него будет выслана ссылка для восстановления</p>
		#SCRIPT#
		<form action="#ACTION_FILE#" enctype="multipart/form-data" method="post">
			<div class="input_container">
				<input id="username" type="email" name="username" value="#USERNAME#" maxlength="64" placeholder="" autocomplete="off">
				<label for="username">E-mail</label>
			</div>
			<username></username>
			<p class="blue"><span class="text-block">#CAPTCHA#</span></p>
			<div class="input_container">
				<input id="captchacode" type="text" name="captchacode" placeholder="" autocomplete="off">
				<label for="captchacode">Код без учета регистра</label>
			</div>
			<captchacode></captchacode>
			<div style="padding-top:20px">
				<button class="btn btn-blue" name="remember">Отправить</button>
			</div>
		</form>
	</div>
	<div class="right"></div>
</div>