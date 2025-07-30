$(function(){
	$('#show-password').click(function() {
		if ($('#password').attr('type') == 'password'){
			$('#password').attr('type','text');
			$('#img-password').attr('src','../img/site/eyeclose.png');
			$('#img-password').attr('title','Скрыть пароль');
		} else {
			$('#password').attr('type','password');
			$('#img-password').attr('src','../img/site/eyeopen.png');
			$('#img-password').attr('title','Показать пароль');
		}
	})
	$('#phone').mask('+7 (999) 999-99-99');
})