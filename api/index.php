<?php
require 'vendor/autoload.php';

$app = new \Slim\Slim();

// default route
/*$app->get('/', function () use ($app) {
  $app->render('home.html');
});

$app->get('/about', function () use ($app) {
  $app->render('about.html');
});*/

// routes to user resources
$app->get('/users', 'getUsers');
$app->get('/users/:id', 'getUser');
// $app->get('/users/search/:query', 'findByName');
$app->post('/users', 'addUser');
$app->put('/users/:id', 'updateUser');
$app->delete('/users/:id', 'deleteUser');

// routes to server resources
$app->get('/servers', 'getServers');
$app->get('/servers/:id', 'getServer');
// $app->get('/servers/search/:query', 'findByName');
$app->post('/servers', 'addServer');
$app->put('/servers/:id', 'updateServer');
$app->delete('/servers/:id', 'deleteServer');

$app->run();


/*
 * All User related methods
 */

function getUsers() {
  $sql = "SELECT * FROM users ORDER BY id DESC";
  try {
    $db = getConnection();
    $stmt = $db->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo json_encode($users);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

function getUser($id) {
  $sql = "SELECT * FROM users WHERE id=:id";
  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("id", $id);
    $stmt->execute();
    $user = $stmt->fetchObject();
    $db = null;
    echo json_encode($user);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

function addUser() {
  error_log("addUser\n", 3, "/var/tmp/php.log");
  $request = Slim::getInstance()->request();
  $user = json_decode($request->getBody());

  //create random username
  // $username = 'loadavg_' . uniqid();

  //create random password
  // $password = md5(time());

  //generate api token
  $api_token = strtoupper(md5($user->username));

  $sql = "INSERT INTO users (username, password, api_token, created_at, updated_at)
          VALUES (:username, :password, :api_token, NOW(), NOW())";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    // $stmt->bindParam("name", $user->username);
    $stmt->bindParam("username", $user->username);
    $stmt->bindParam("password", $user->password);
    $stmt->bindParam("api_token", $api_token);
    $stmt->execute();
    $user->id = $db->lastInsertId();
    $db = null;
    echo json_encode($user);
  } catch(PDOException $e) {
    error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }

}

function updateUser($id) {
  # code...
}

function deleteUser($id) {
  # code...
}

/*
 * All Server related methods
 */

function getServers() {
  # code...
}

function getServer($id) {
  # code...
}

function addServer() {
  # code...
}

function updateServer($id) {
  # code...
}

function deleteServer($id) {
  # code...
}


function getConnection() {
  // Fetch DB settings.
  $settings = parse_ini_file("../config/settings.ini");

  $dbhost = $settings["db_host"];
  $dbuser = $settings["db_user"];
  $dbpass = $settings["db_pass"];
  $dbname = $settings["db_name"];

  $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $dbh;
}









