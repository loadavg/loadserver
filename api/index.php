<?php
require 'vendor/autoload.php';

$app = new \Slim\Slim();


// routes to user resources
$app->get('/users', 'getUsers');
$app->get('/users/:id', 'getUser');
$app->get('/users/:token/data', 'getUserByToken');
$app->get('/users/:id/servers', 'getUserServers');
$app->get('/users/:id/serverCount', 'getServerCount');
// $app->get('/users/search/:query', 'findByName');
$app->post('/users', 'addUser');
$app->post('/users/:id/servers', 'addServer');
$app->put('/users/:id', 'updateUser');
$app->delete('/users/:id', 'deleteUser');

// routes to server resources
$app->get('/servers', 'getServers');
$app->get('/servers/:id', 'getServer');
$app->get('/servers/:token/t', 'getServerByToken'); // depreciated ?
$app->get('/servers/:id/data', 'getServerData');


//validation rountines
$app->get('/users/:token/va', 'validateUserApiKey'); // was /data getUserByToken
$app->get('/servers/:token/vs', 'validateServerToken');  //was /t getServerByToken above
$app->get('/servers/:s_token/:a_key/v', 'validateUserApiKeyAndToken');


//used for the client to push data up to the server
//we need to add UserApiKey and Token to this to validate on push on server
$app->post('/servers/:id/data', 'addServerData');

// $app->get('/servers/search/:query', 'findByName');
$app->put('/servers/:id', 'updateServer');
$app->delete('/servers/:id', 'deleteServer');


header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
$app->run();


// TODO: Implement method to secure API
function verifyKey(\Slim\Route $route) {
  return false;
}


/*
 * All User related methods
 */

// Return a list of all the users
function getUsers() {
  $sql = "SELECT users.* FROM users ORDER BY id DESC";

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

// Get user with the requested ID
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

// Get user with the requested API token
function getUserByToken($token) {
  $sql = "SELECT * FROM users WHERE api_token=:token";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("token", $token);
    $stmt->execute();
    $user = $stmt->fetchObject();
    $db = null;
    echo json_encode($user);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

// Return a list of all servers assigned to a user
function getUserServers($id) {
  echo "You should see all servers for user with ID: {$id} here...";
}

// Return a count of all the servers belonging to a user
function getServerCount($userId) {
  $sql = "SELECT COUNT(*) AS server_count FROM servers
          WHERE user_id=:user_id";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("user_id", $userId);
    $stmt->execute();
    $server_count = $stmt->fetchObject();
    $db = null;
    echo json_encode($server_count);
  } catch (PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

// Add a new user
function addUser() {

  $request = \Slim\Slim::getInstance()->request();
  $user = json_decode($request->getBody());

  //create random username
  // $username = 'loadavg_' . uniqid();

  //generate api token
  $api_token = md5($user->username);
  $hashed_pwd = hash_password($user->password);

  $sql = "INSERT INTO users (first_name, last_name, email, username, password, api_token, created_at, updated_at)
          VALUES (:first_name, :last_name, :email, :username, :password, :api_token, NOW(), NOW())";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("first_name", $user->first_name);
    $stmt->bindParam("last_name", $user->last_name);
    $stmt->bindParam("email", $user->email);
    $stmt->bindParam("username", $user->username);
    $stmt->bindParam("password", $hashed_pwd);
    $stmt->bindParam("api_token", $api_token);
    $stmt->execute();
    $user->id = $db->lastInsertId();
    $db = null;
    echo json_encode($user);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }

}

// Update user with specified ID
function updateUser($id) {
  $request = \Slim\Slim::getInstance()->request();
  $user = json_decode($request->getBody());

  $hashed_pwd = hash_password($user->password);
  $sql = "UPDATE users SET first_name=:first_name, last_name=:last_name, email=:email,
          password=:password, updated_at=NOW() WHERE id=:id";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("first_name", $user->first_name);
    $stmt->bindParam("last_name", $user->last_name);
    $stmt->bindParam("email", $user->email);
    $stmt->bindParam("password", $hashed_pwd);
    $stmt->bindParam("id", $id);
    $stmt->execute();
    $db = null;
    echo json_encode($user);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

// Delete user with specified ID
function deleteUser($id) {
  $sql = "DELETE FROM users WHERE id=:id";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("id", $id);
    $status->status = $stmt->execute();
    $db = null;
    echo json_encode($status);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

/*
 * All Server related methods
 */

// Return a list of all the servers
function getServers() {
  $sql = "SELECT * FROM servers ORDER BY id DESC";

  try {
    $db = getConnection();
    $stmt = $db->query($sql);
    $servers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo json_encode($servers);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

// Get the server with the requested ID
function getServer($id) {
  $sql = "SELECT servers.*, users.id AS uid, users.first_name, users.last_name
          FROM servers
          INNER JOIN users ON servers.user_id = users.id
          WHERE servers.id=:id";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("id", $id);
    $stmt->execute();
    $server = $stmt->fetchObject();
    $db = null;
    echo json_encode($server);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

// Get the server with the requested ID
function getServerByToken($server_token) {
  $sql = "SELECT servers.*, users.id AS uid, users.first_name, users.last_name
          FROM servers
          INNER JOIN users ON servers.user_id = users.id
          WHERE servers.server_token=:server_token";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("server_token", $server_token);
    $stmt->execute();
    $server = $stmt->fetchObject();
    $db = null;
    echo json_encode($server);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

/*
 *
 * BEGIN Validation Routines
 *
 */

// Validate the Users API Key
function validateUserApiKey($token) {
  $sql = "SELECT * FROM users WHERE api_token=:token";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("token", $token);
    $stmt->execute();
    $user = $stmt->fetchObject();
    $db = null;

    if ($user) {
      echo "true";
    } else {
      echo "false";
    }
    //echo json_encode($user);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

// Validate the server token
function validateServerToken($server_token) {
  $sql = "SELECT servers.*, users.id AS uid, users.first_name, users.last_name
          FROM servers
          INNER JOIN users ON servers.user_id = users.id
          WHERE servers.server_token=:server_token";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("server_token", $server_token);
    $stmt->execute();
    $server = $stmt->fetchObject();
    $db = null;

    if ($server) {
      echo "true";
    } else {
      echo "false";
    }
    //echo json_encode($server);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

// Validate the server token against users api key
function validateUserApiKeyAndToken($server_token, $api_token) {

  $sql = "SELECT servers.*, users.*
          FROM servers
          INNER JOIN users ON servers.user_id = users.id
          WHERE servers.server_token=:server_token AND users.api_token=:api_token";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("server_token", $server_token);
    $stmt->bindParam("api_token", $api_token);
    $stmt->execute();
    $server = $stmt->fetchObject();
    $db = null;

    if ($server) {
      echo "true";
    } else {
      echo "false";
    }
    // echo json_encode($server);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

/*
 * END Validation Routines
 */

// Get the server data with the requested ID
function getServerData($id) {
  $sql = "SELECT server_data.* FROM server_data
          INNER JOIN servers ON server_data.server_id = servers.id
          WHERE server_data.server_id=:id";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("id", $id);
    $stmt->execute();
    $server_data = $stmt->fetchAll(PDO::FETCH_OBJ);
    // print_r($server_data);
    $db = null;
    echo json_encode($server_data);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

// Add a new server for a selected user
function addServer($userId) {
  $request = \Slim\Slim::getInstance()->request();
  $server = json_decode($request->getBody());

  $server_token = md5($server->server_name);

  $sql = "INSERT INTO servers (user_id, server_name, server_token, created_at, updated_at)
          VALUES (:user_id, :server_name, :server_token, NOW(), NOW())";

  $sql2 = "SELECT COUNT(*) AS server_count FROM servers WHERE user_id=:user_id";

  $sql3 = "UPDATE users SET server_count=:server_count WHERE id=:user_id";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt2 = $db->prepare($sql2);
    $stmt3 = $db->prepare($sql3);
    $stmt->bindParam("user_id", $userId);
    $stmt->bindParam("server_name", $server->server_name);
    $stmt->bindParam("server_token", $server_token);
    $stmt->execute();
    $server->id = $db->lastInsertId();
    $stmt2->bindParam("user_id", $userId);
    $stmt2->execute();
    $sCount = $stmt2->fetchObject();
    $stmt3->bindParam("server_count", $sCount->server_count);
    $stmt3->bindParam("user_id", $userId);
    $stmt3->execute();
    $db = null;
    echo json_encode($server);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

// Add a new user
function addServerData($serverId) {
  // global $app;
  $request = \Slim\Slim::getInstance()->request();
  $server_data = $request->params('data');

  $sql = "INSERT INTO server_data (server_id, data, created_at, updated_at)
          VALUES (:server_id, :data, NOW(), NOW())";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("server_id", $serverId);
    $stmt->bindParam("data", $server_data);
    $stmt->execute();
    $server_data->id = $db->lastInsertId();
    $db = null;
    echo json_encode($server_data);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }

}

// Update server with specified ID
function updateServer($id) {
  $request = \Slim\Slim::getInstance()->request();
  $server = json_decode($request->getBody());

  $sql = "UPDATE servers SET user_id=:user_id, server_name=:server_name, updated_at=NOW() WHERE id=:id";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("user_id", $server->user_id);
    $stmt->bindParam("server_name", $server->server_name);
    $stmt->bindParam("id", $id);
    $stmt->execute();
    $db = null;
    echo json_encode($server);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}

// Delete server with specified ID
function deleteServer($id) {
  $sql = "DELETE FROM servers WHERE id=:id";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("id", $id);
    $status->status = $stmt->execute();
    $db = null;
    echo json_encode($status);
  } catch(PDOException $e) {
    echo '{"error":{"message":'. $e->getMessage() .'}}';
  }
}


// Hash password using PHPs new built-in method.
function hash_password($password) {
  // Configs for password_hash method
  $options = [
    'cost' => 12,
  ];

  $new_password = password_hash($password, PASSWORD_BCRYPT, $options);

  return $new_password;
}

// Database connection method.
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









