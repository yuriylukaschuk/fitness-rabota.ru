<?php
$id_projects = 'fitness-rabota.ru';
define('SCHEME','https://');
define('DS', '/'); // разделитель в адресной строке
define('NS', '_'); // разделитель в названии файла
define('DOCS', '/docs/'); // путь к временным загруженным файлам
define('IMG', '/img/'); // путь к временным загруженным файлам
define('DE', '='); // разделитель элементов
define('DL', '&'); // разделитель строк

$dbhostname = 'localhost';
$dbusername = 'alfa';
$dbpassword = '';
$database = 'alfa';
$devshm = '/dev/shm/p'.$id_projects.'/';
$clubImgPath = '/var/www/p'.$id_projects.'/img/club/';
$usersImgPath = '/var/www/p'.$id_projects.'/img/staff/';
$localFilePath = '/var/www/p'.$id_projects.'/files/';
$reportPath = '/var/www/p'.$id_projects.'/reports/';
// Глобальные переменные
$secInDay = 86400;
$PeriodAction = 7; // 14 дней действие акции с дня перехода по ссылке
$maxselref = 5; // максимальное количество регистраций с одного компьютера
$menu = ''; // Основное меню
$rules = array(); // Правила расчета разделов
$params_ddc = array(); // Глобальная переменная для рассчета ДДС
$after = array(
	1 => 'с 09:00 до 10:59',
	2 => 'с 11:00 до 12:59',
	3 => 'с 13:00 до 14:59',
	4 => 'с 15:00 до 16:59',
	5 => 'с 17:00 до 18:59',
	6 => 'с 19:00 до 21:00'
);
$ready = array(
	1 => 'Хочу пройти обучение',
	2 => 'Не хочу проходить обучение'
);

$Recipients = array('nikaleonmanager@gmail.com','lukaschuk@praktika.fit');
$excelRange = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF');		//Создание объекта класса библиотеки
$mon = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
$mons = array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
$day = array ('ПН','ВТ','СР','ЧТ','ПТ','СБ','ВС');
$official_words = array('BLOB','BOTH','CASE','CHAR','DESC','DROP','ELSE','FROM','INTO','JOIN','KEYS','KILL','LEFT','LIKE','LOAD','LOCK','LONG','NULL','READ','REAL','SHOW','THEN','WHEN','WITH','ALTER','CROSS','FLOAT','GRANT','GROUP','INDEX','INNER','LIMIT','LINES','MATCH','ORDER','OUTER','PURGE','RIGHT','RLIKE','TABLE','UNION','USAGE','USING','WHERE','WRITE');
$abc_array = array(
	'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh',
	'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
	'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
	'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', ' ' => '-'
);
// Настройки почтового ящика для рассылки
$mail_server = array(
	'Host' => 'smtp.yandex.ru',
	'SMTPAuth' => true,
	'Username' => 'support@praktika.fit',
	'Password' => ''
);
$images = array(
	'big' => array(
		'size' => 900, // максимальный размер в px
		'weight' => 5 // вес в Мб, больше которого нельзя загрузить фотографию
	),
	'middle' => array(
		'size' => 300, // максимальный размер в px (для логотипов)
		'weight' => 5 // вес в Мб, больше которого нельзя загрузить фотографию
	),
	'small' => array(
		'size' => 200, // максимальный размер в px
		'weight' => 5 // вес в Мб, больше которого нельзя загрузить фотографию
	)
);
$logo_big = 227; // размер большого логотипа
$logo_small = 100; // размер большого логотипа
$postminlenght = 50; // минимальная длина сообщения
$err = $script = $password = $repeat = $phone = $username = '';

$replace_simbols = array(' ','-','(',')','.');
$imgType = array(
	'image/gif' => '.gif',
	'image/jpeg' => '.jpg',
	'image/jpg' => '.jpg',
	'image/png' => '.png',
	'image/bmp' => '.bmp',
	'image/tiff' => '.tif'
);
$docsType = array(
	'text/plain' => 'txt',
	'application/pdf' => 'pdf',
	'application/msword' => 'doc',
	'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
	'application/vnd.ms-excel' => 'xls',
	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx'
);
$errs = array(
	'' => '',
	'successreguser' => 'Новый пользователь зарегистрирован в базе',
	'successchange' => 'Изменения сохранены в базе',
	'successregistered' => 'Поздравляем! Вы зарегистрированы на сайте',
	'accessdenied' => 'Доступ запрещен',
	'notusername' => 'E-mail нужен обязательно',
	'badusername' => 'Неверно указан адрес электронной почты',
	'usernameexist' => 'Указанный E-mail уже зарегистрирован',
	'usernamenotexist' => 'Указанный Вами E-mail не зарегистрирован в базе',
	'badusernamelenght' => 'Длина E-mail не должна превышать 64 символа',
	'userexist' => 'Извините, указанное Вами Имя для входа уже занято',
	'badusernameorpassword' => 'Неправильное имя пользователя либо пароль',
	'lnameisempty' => 'Укажите Вашу фамилию',
	'fnameisempty' => 'Укажите, пожалуйста, Ваше имя',
	'phoneisempty' => 'Укажите, пожалуйста, телефон для связи с Вами',
	'phonexist' => 'Номер телефона уже зарегистрирован',
	'phonebad' => 'Номер телефона указан не правильно',
	'falseusername' => 'Для Вас пароль не может быть восстановлен',
	'passisempty' => 'Пароль не может быть пустым',
	'passnotmatch' => 'Пароли не совпадают',
	'badpasswordlenght' => 'Длина пароля должна составлять 5-20 символов',
	'badpassword' => 'Пароль содержит недопустимые символы или пробел',
	'notlname' => 'Фамилия обучающегося должна быть указана обязательно',
	'notfname' => 'Имя обучающегося должно быть указана обязательно',
	'successcreatepass' => 'Новый пароль зарегистрирован',
	'successsuchangepass' => 'Пароль успешно изменен',
	'successfulentry' => 'Добро пожаловать',
	'badcaptchacode' => 'Неверно указан код подтверждения',
	'notcookie' => 'В обозревателе должны быть включены Cookie',
	'postbesended' => 'Ваше обращение отправлено в службу поддержки',
	'postnotsendunregistered' => 'Извините, только зарегистрированные пользователи могут оставлять сообщения',
	'postnotbeempty' => 'Сообщение не может быть пустым',
	'postlegthless' => 'Длина сообщения не должна быть менее '.$postminlenght.' символов',
	'intervalnotselected' => 'Ни в одном из интервалов не указано количество получателей СМС-рассылки',
	'nameneed' => 'Необходимо указать имя Вашего друга, родственника либо знакомого',
	'fileuploadnoexists' => 'Не выбран файл для загрузки',
	'imageformat' => 'Неправильный формат файла. Изображение должно быть в формате JPEG, PNG, BMP, GIF либо TIF',
	'imageovermaxweight' => 'Вес фотографии не должен превышать '.$images['middle']['weight'].' Мб',
	'imagelessminsize' => 'Изображение должно быть по ширине и высоте не менее '.$images['small']['size'].'px',
	'numbernotspecified' => 'Курс должен иметь номер. По номеру определяется порядок их прохождения',
	'coursenotname' => 'Не указано название курса',
	'coursenotroll' => 'Видеоролик к курсу является обязательным',
	'rollnotexists' => 'По указанному адресу видеоролик не существует',
	'rollnotavailability' => 'Видеоролик недоступен для просмотра',
	'notnumber' => 'Не указан номер по порядку',
	'numbernotnumeric' => 'Номер должен быть числом',
	'emptyquestion' => 'Вопрос не может быть пустым',
	'emptyreply' => 'Ответ не может быть пустым',
	'notregusers' => 'В клубе нет зарегистрированных слушателей',
	'notassignedcoach' => 'Клубу не назначен сотрудник, ответственный за обучение. Сообщите, пожалуйста, в службу поддержки по телефону, указанному на сайте',
	'notaccessaddusers' => 'У вас отсуствуют полномочия на управление слушателями клуба',
	'vacanciesover' => 'Вакансии на новых учащихся нет'
);
?>