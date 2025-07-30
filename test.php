<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="robots" content="index, follow, noyaca" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Комбинации цветов сайта</title>
</head>
<body>
<div align="center">
<table width="1040">
<tr><td valign="top">
    <table width="150" border="1">
    <tr height="50">
        <td colspan="2" align="center" valign="middle"><b>Возможные цвета фона</b></td>
    </tr>
    <tr height="50">
        <td align="center" valign="middle" style="background-color:#FFFAFA">Snow</td>
    </tr>
    <tr height="50">
        <td align="center" valign="middle" style="background-color:#FFFFF0">Ivory</td>
    </tr>
    <tr height="50">
        <td align="center" valign="middle" style="background-color:#FFF8DC">Cornsilk</td>
    </tr>
    <tr height="50">
        <td align="center" valign="middle" style="background-color:#FFF0F5">LavenderBlush</td>
    </tr>
<!--
    <tr height="50">
        <td align="center" valign="middle">Возможный цвет текста</td>
    </tr>
    <tr height="50">
        <td align="center" valign="middle" style="background-color:#000; color:#fff">Т1</td>
    </tr>
-->
    <tr height="50">
        <td align="center" valign="middle"><b>Возможные цвета кнопок</b></td>
    </tr>
    <tr height="50">
        <td align="center" valign="middle" style="background-color:#FF0000">Red</td>
    </tr>
    <tr height="50">
        <td align="center" valign="middle" style="background-color:#FF8C00">DarkOrange</td>
    </tr>
    <tr height="50">
        <td align="center" valign="middle" style="background-color:#808000">Olive</td>
    </tr>
    <tr height="50">
        <td align="center" valign="middle" style="background-color:#32CD32">LimeGreen</td>
    </tr>
    <tr height="50">
        <td align="center" valign="middle" style="background-color:#008000">Green</td>
    </tr>
	</table>
</td>
<td width="40">&nbsp;</td>
<td valign="top">
    <table width="850" border="1">
    <tr height="50">
        <td colspan="5" align="center" valign="middle"><b>Полные комбинации</b></td>
    </tr>
    <tr height="50" align="center" valign="middle">
        <td width="100">№ п/п</td>
        <td width="250">Цвет фона</td>
        <td width="250">Цвет кнопки</td>
        <td width="250">Текст при наведении мышки на кнопку</td>
    </tr>
<?php
$npp = 0;
$fon = array(
	'Snow' => '#FFFAFA',
	'Ivory' => '#FFFFF0',
	'Cornsilk' => '#FFF8DC',
	'LavenderBlush' => '#FFF0F5'
);
$btn = array(
	'Red' => '#FF0000',
	'DarkOrange' => '#FF8C00',
	'Olive' => '#808000',
	'LimeGreen' => '#32CD32',
	'Green' => '#008000'
);
$btnclick = array(
	'Red' => '#FF0000',
	'DarkOrange' => '#FF8C00',
	'Olive' => '#808000',
	'LimeGreen' => '#32CD32',
	'Green' => '#008000'
);
$tbl = '';
foreach ($fon as $fkey => $fval){
	foreach ($btn as $bkey => $bval){
		foreach ($btnclick as $bckey => $bcval){
			$npp++;
			$tbl = '<tr height="50" align="center" valign="middle">
				<td align="center" valign="middle">'.$npp.'</td>
				<td align="center" valign="middle" style="background-color:'.$fval.'">'.$fkey.'</td>
				<td align="center" valign="middle" style="background-color:'.$bval.'">'.$bkey.'</td>
				<td align="center" valign="middle" style="background-color:'.$bcval.'">'.$bckey.'</td>
			</tr>';
/*
			$tbl = '<tr height="50" align="center" valign="middle">
				<td align="center" valign="middle">'.$npp.'</td>
				<td align="center" valign="middle" style="background-color:'.$fval.'; color:#000">Текст на сайте</td>
				<td align="center" valign="middle" style="background-color:'.$bval.'; color:#000">Текст на кнопке</td>
				<td align="center" valign="middle" style="background-color:'.$bcval.'; color:#000">Текст при наведении мышки на кнопку</td>
			</tr>';
*/
			echo $tbl;
		}
	}
}
?>
	</table>
</td></tr>
</table>
</div>
</body>
</html>
