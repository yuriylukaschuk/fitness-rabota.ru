#SCRIPT#
<div class="reg-block">
	<div class="left">
		<h3>Заполните, пожалуйста, анкету</h3>
		<form action="#ACTION_FILE#" enctype="multipart/form-data" method="post">
			<div class="input_container">
				<input id="fname" type="text" name="fname" value="#FNAME#" maxlength="64" placeholder="" autocomplete="off">
				<label for="fname">Ваше имя</label>
			</div>
			<fname></fname>
			<div class="select">
				<div class="label">Дата рождения</div>
				<div class="option">
					<select name="day" size="1">#DAY_OPTION#</select>
					<select name="month" size="1">#MONTH_OPTION#</select>
					<select name="year" size="1">#YEAR_OPTION#</select>
                </div>
			</div>
			<div class="input_container">
				<input id="phone" type="phone" name="phone" value="#PHONE#" maxlength="64" placeholder="" autocomplete="off">
				<label for="phone">Телефон для связи</label>
			</div>
			<phone></phone>
			<div class="input_container">
				<input id="username" type="email" name="username" value="#USERNAME#" maxlength="64" placeholder="" autocomplete="off">
				<label for="username">E-mail</label>
			</div>
			<username></username>
			<div class="select">
				<div class="label">Выберете удобный способ связи</div>
				<div class="option">
					<select name="connection_id" size="1">#CONNECTION_OPTION#</select>
                </div>
			</div>
			<div class="select">
				<div class="label">Образование</div>
				<div class="option">
					<select name="education_id" size="1">#EDUCATION_OPTION#</select>
                </div>
			</div>
			<div class="select">
				<div class="label">Опыт в профессии</div>
				<div class="option">
					<select name="lvl_id" size="1">#LVL_OPTION#</select>
                </div>
			</div>
			<div class="select">
				<div class="label">Стаж работы по профилю</div>
				<div class="option">
					<select name="experience_id" size="1">#EXPERIENCE_OPTION#</select>
                </div>
			</div>
			<div class="select">
				<div class="label">Ближайшая к вам станция метро</div>
				<div class="option">
					<select name="metro_id" size="1">#METRO_OPTION#</select>
                </div>
			</div>
			<div style="margin-top:21px;">
				<input type="hidden" name="program_id" value="#PROGRAM_ID#">
				<button name="send" class="btn btn-blue">Отправить анкету</button>
			</div>
		</form>
	</div>
	<div class="right"></div>
</div>

