<div class="central">
	<h2 style="padding-bottom:10px">Тест на знание материала по программе &laquo;#PROGRAM#&raquo;</h4>
	<h3>Вопрос № #NPP# (из #QUESTION_COUNT#). #QUESTION#</h3>
	#MSG#
</div>
<div class="testing">
	<form action="#ACTION_FILE#" enctype="multipart/form-data" method="post">
		#REPLY#
		<div class="text-center" style="padding-top:21px;">
			<input type="hidden" name="question_id" value="#QUESTION_ID#">
			<input type="hidden" name="form_id" value="#FORM_ID#">
			<button name="save" class="btn btn-blue">Отправить</button>
		</div>
	</form>
</div>