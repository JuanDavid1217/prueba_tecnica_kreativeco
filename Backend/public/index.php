<?php
    require_once __DIR__."/../vendor/autoload.php";

    use Dotenv\Dotenv;
    use App\Controllers\UserController;
    use App\Controllers\LoginController;
    use App\Controllers\PublicationController;
    use App\Services\UserService;
    use App\Services\LoginService;
    use App\Services\PublicationService;
    use App\Services\AuthService;
    use App\Dao\UserDao;
    use App\Dao\PublicationDao;
    use App\Utils\HttpException;
    use App\Utils\Token;
    use App\Utils\AccessLevel;
    use App\Router;
    use App\Database;

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Content-Type: application/json; charset=utf-8');

    $dotenv = Dotenv::createImmutable(__DIR__.'/..');
    $dotenv->load();

    $db=Database::getInstance();
    
    //DAOs
    $user_dao = new UserDao($db);
    $publication_dao = new PublicationDao($db);

    //Services
    $user_service = new UserService($user_dao);
    $publication_service = new PublicationService($publication_dao, $user_dao);
    $login_service = new LoginService($user_dao);
    $auth_service = new AuthService($user_dao);

    //controllers
    $user_controller = new UserController($user_service);
    $publication_controller = new PublicationController($publication_service);
    $login_controller = new LoginController($login_service);
    
    $router = new Router();

    $router->get('/', function() use ($auth_service){
        $data = $auth_service->isAuthorized();
        $name = $data['name']??null;
        $message = "Hola, Bienvenido a mi API";
        if (!is_null($name)){
            $message = "Hola ".$name.", Bienvenido a mi API";
        }
        http_response_code(200);
        echo json_encode(["message"=>$message]);
    });

    $router->post('/login', function() use ($login_controller){
        $login_controller->login();
    });

    $router->post('/users', function() use ($user_controller, $auth_service){
        $data = $auth_service->isAuthorized();
        AccessLevel::validateRole(3, $data);
        $user_controller->createUser();
    });

    $router->get('/users', function() use ($user_controller, $auth_service){
        $data = $auth_service->isAuthorized();
        AccessLevel::validateRole(2, $data);
        $user_controller->findAll();
    });

    $router->get('/users/{id}', function(int $id) use ($user_controller, $auth_service){
        $data = $auth_service->isAuthorized();
        AccessLevel::validateRole(2, $data);
        $user_controller->findById($id);
    });

    $router->delete('/users/{id}', function(int $id) use ($user_controller, $auth_service){
        $data = $auth_service->isAuthorized();
        AccessLevel::validateRole(5, $data);
        $user_controller->delete($id);
    });

    $router->put('/users/{id}', function(int $id) use ($user_controller, $auth_service){
        $data = $auth_service->isAuthorized();
        AccessLevel::validateRole(4, $data);
        $user_controller->update($id);
    });

    $router->post('/publications', function() use ($publication_controller, $auth_service){
        $data = $auth_service->isAuthorized();
        AccessLevel::validateRole(3, $data);
        $publication_controller->createPublication($data['id']);
    });

    $router->get('/publications', function() use ($publication_controller, $auth_service){
        $data = $auth_service->isAuthorized();
        AccessLevel::validateRole(2, $data);
        $publication_controller->findAll();
    });

    $router->get('/publications/{id}', function(int $id) use ($publication_controller, $auth_service){
        $data = $auth_service->isAuthorized();
        AccessLevel::validateRole(2, $data);
        $publication_controller->findById($id);
    });

    $router->delete('/publications/{id}', function(int $id) use ($publication_controller, $auth_service){
        $data = $auth_service->isAuthorized();
        AccessLevel::validateRole(5, $data);
        $publication_controller->delete($id);
    });

    $router->put('/publications/{id}', function(int $id) use ($publication_controller, $auth_service){
        $data = $auth_service->isAuthorized();
        AccessLevel::validateRole(4, $data);
        $publication_controller->update($id);
    });

    $router->run();
    
?>