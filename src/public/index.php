<?php
header("Access-Control-Allow-Origin: *");
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app = new \Slim\App(['settings' => $config]);
$container = $app->getContainer();
$container['logger'] = function($c) {
  $logger = new \Monolog\Logger('my_logger');
  $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
  $logger->pushHandler($file_handler);
  return $logger;
};

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
$this->logger->addInfo('Something interesting happened');
    return $response;
});

$app->get('/{tableName}/{rowName}', function (Request $request, Response $response, array $args) {
  $tableName = $args['tableName'];
  $rowName = $args['rowName'];
  try {
    $db = new PDO('pgsql:dbname=forest_bd;host=192.168.20.78', 'forest', ' ');
    $forest = $db->query("SELECT * FROM forest.$tableName");
  }
  catch (PDOException $e) {
    $error='error'.$e->getMessage();
  }
  while ($row = $forest->fetch(PDO::FETCH_ASSOC)) {
    $table.=$row[$rowName].'splitPlace';
  }
  $response->getBody()->write($table);
  return $response;
});

$app->run();