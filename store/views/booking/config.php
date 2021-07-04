<?php

	require_once(dirname(__FILE__).'/lib/dhtmlxScheduler/connector/db_pdo.php');

    $dsn =  Yii::$app->getDb()->dsn;
    $username = Yii::$app->getDb()->username;
    $password = Yii::$app->getDb()->password;


	$dbtype = "PDO";
	$res = new PDO("$dsn", "$username", "$password");

?>