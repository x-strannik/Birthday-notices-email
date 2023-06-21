<?php 
/*
Plugin Name: Birthday notices email
Plugin URI: https://kodabra.unchikov.ru/birthday-notices-email/
Description: Уведомления о днях рождения на email.
Author: Elena Unchikova
Version: 1.0.0
Author URI: https://kodabra.unchikov.ru/
*/ 

// Выход при прямом доступе.
     if ( ! defined( 'ABSPATH' ) ) { exit; }
 
// *******************************************************	 
function birthday_notices_email() {

$my_posts = new WP_Query;
$myposts = $my_posts->query( [
	'post_type' => 'member', // тип записи персон, имеющих поле "дата рождения" (здесь 'born')
	'posts_per_page'=> '-1'	
] );
date_default_timezone_set('Asia/Yakutsk'); // ваша временная зона
$tmes = date('m-d'); // текущая дата в формате мм-дд
$month_list = array(
	1  => 'января',
	2  => 'февраля',
	3  => 'марта',
	4  => 'апреля',
	5  => 'мая', 
	6  => 'июня',
	7  => 'июля',
	8  => 'августа',
	9  => 'сентября',
	10 => 'октября',
	11 => 'ноября',
	12 => 'декабря'
);   
$soob2 = '';
foreach( $myposts as $pst ){
    $drojd = get_post_meta( $pst->ID, 'born', true ); // получаем дату рождения (здесь даты в поле 'born' записаны в формате гггг-мм-дд)
    $pstdr = mb_substr($drojd, 5, 5); // оставляем последние 5 знаков
  if($pstdr==$tmes){ // проверяем на совпадение с текущей датой
    $perurl = get_permalink($pst); // ссылка на персону 
    $postper_title = get_the_title( $pst->ID ); // название записи (имя персоны)
    $soob2 .= '<li><a href="' . $perurl . '"> ' . $postper_title . ' (' .  $drojd . ')</a></li>';
} 
}
$soob = $soob2;
if($soob){
$soob1 = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body><h3>' .date('d') . ' ' . $month_list[date('n')] . ' родились</h3><ul>' . $soob . '</ul></body></html>';

  $headers = array();
  $headers[] = 'Content-Type: text/html; charset="UTF-8"'; 
wp_mail('ваша_эл_почта', 'День рождения', $soob1, implode("\r\n", $headers));
}
wp_reset_postdata();
}
// *******************************************************

// Добавим крон-задачу при активации плагина
register_activation_hook( __FILE__, 'my_activation' );

// Удалим крон задачу при де-активации плагина
register_deactivation_hook( __FILE__, 'my_deactivation');

// Функция, которая будет выполнятся при наступлении крон-события
add_action('birthday_notices_email_hook', 'birthday_notices_email');

function my_activation() {

	// удалим на всякий случай все такие же задачи cron.
	wp_clear_scheduled_hook( 'birthday_notices_email_hook' );

	// добавим новую ежедневную cron задачу
	wp_schedule_event( time(), 'daily', 'birthday_notices_email_hook');
}

function my_deactivation() {
	wp_clear_scheduled_hook( 'birthday_notices_email_hook' );
}
