<?php
// project root dir. you can move public directory to safe place
const __ROOT__ = __DIR__;
require_once __ROOT__.'/vendor/autoload.php';
try {
// path to you .env config dir. if you use svn. then i recommended move it to invisible place
$dotenv = Dotenv\Dotenv::create(__ROOT__);
$dotenv->load();
$path = explode('/', $_GET['q']);

$token = $path[0] ?? null;

	if ($_ENV['MODE'] == 'debug') {
		file_put_contents(__ROOT__ . '/logs/' . date('Y:m:d') . '.debug.log', date('H-i-s') . json_encode($_POST). "\n");
	}
	else {
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {

			http_response_code(404);
			exit();
		}
	}
$appsTokens = json_decode($_ENV['APPS_TOKENS'], true);
	$request = null;
if (!$appId = $appsTokens[$token]) {
    http_response_code(404);
}

	if ($_ENV['MODE'] == 'debug' && empty($_POST)) {
		$request = [
			'user' => ['id' => 1, 'name' => 'r'],
			'payload' => '',
			'input' => '',
		];
	}
// mysql
	ORM::configure('mysql:host=' . $_ENV['MYSQL_HOST'] . ';dbname=' . $_ENV['MYSQL_USER']);
	ORM::configure('username', $_ENV['MYSQL_USER']);
	ORM::configure('password', $_ENV['MYSQL_PASS']);
	ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'));

 // ok. run
$matryoshka = new Matryoshka\Matryoshka($request);
$handlers = glob(__DIR__.'/Handlers/app'.$appId.'/**.php');
foreach ($handlers as $handler) {
    $filename = $handler;
    $filename = basename($filename);
    $class = str_replace('.php', '', $filename);
    $class = '\\Matryoshka\\Handlers\\app'.$appId.'\\'.$class;
	if (class_exists($class)) {
        $matryoshka->getHandlerManager()->addHandler($class);
    }
    else {
        throw new Exception('Class not found: '.$class);
    }
}


    if ($res = $matryoshka->start()) {
        header('Content-Type: text/json');
        echo $res;
        exit;
    }
}
catch (\Exception $exception) {
    $message = date('H:i:s') . ': '.$exception->getMessage(). "\n". $exception->getTraceAsString();
    if ($_ENV['MODE'] == 'debug') {
        echo "<pre>{$message}</pre>";
    }
    file_put_contents(__ROOT__.'/logs/'.date('Y-m-d').'.log', $message, FILE_APPEND);
}


