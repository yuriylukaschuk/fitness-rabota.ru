#SCRIPT#
<div class="reg-block">
	<div class="left">
		<h3>Регистрация</h3>#SCRIPT#
		<h4>#USERNAME#, приглашаем Вас пройти первоначальное тестирование. Данная процедура позволит оценить Ваши реальные знания и предложить оптимальные варианты сотрудничества при первом собеседовании.</h4>
		<form action="#ACTION_FILE#" enctype="multipart/form-data" method="post" style="padding-top:21px">
			<div class="input_container">
				<input id="usermail" type="email" value="#EMAIL#" disabled readonly>
				<label for="usermail">E-mail</label>
			</div>
			<div class="input_container">
				<input id="password" type="password" name="password" value="#PASSWORD#" placeholder="">
				<label for="password">Пароль</label>
				<span id="show-password">
					<img id="img-password" src="/img/site/eyeopen.png" title="Показать пароль">
				</span>
			</div>
			<password></password>
			<p class="text-block">#CAPTCHA#</p>
			<div class="input_container">
				<input id="captchacode" type="text" name="captchacode" placeholder="" autocomplete="off">
				<label for="captchacode">Код без учета регистра</label>
			</div>
			<captchacode></captchacode>
			<div style="margin-top:21px;">
				<button class="btn btn-blue" name="signup">Зарегистрироваться</button>
			</div>
		</form>
	</div>
	<div class="right"></div>
</div>