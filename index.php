<?php
require_once 'Database.php';

//$users = Database::getInstance()->query("SELECT * FROM users WHERE status = ?", [online]);
$users = Database::getInstance()->get('users',['user_name', '/=', 'Саша']);

var_dump($users->error()); die;

if($users->error()){
	echo 'у вас ошибка';
} else {
	foreach ($users->results() as $user) {
	echo $user->user_name . '<br>';
	}
}

