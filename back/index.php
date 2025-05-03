<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './controllers/AuthController.php';
require_once './controllers/RecipeController.php';
require_once './controllers/AdminController.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$action = $_GET['action'] ?? null;
$data = json_decode(file_get_contents('php://input'), true);

$response = ['success' => false, 'message' => 'Action inconnue'];
http_response_code(404); // par défaut

if ($action) {
    try {
        switch ($action) {
            // 🔐 Auth
            case 'login':
                $controller = new AuthController();
                $response = $controller->login($data);
                break;

            case 'register':
                $controller = new AuthController();
                $response = $controller->register($data);
                break;

            case 'toggle_like':
                $controller = new AuthController();
                $response = $controller->toggleLike($data);
                break;

            case 'count_likes':
                $controller = new AuthController();
                $response = $controller->countLikes($data);
                break;

            // 🧑‍🍳 Recipes
            case 'add_recipe':
                $controller = new RecipeController();
                $response = $controller->addRecipe($data);
                break;

            case 'translate_recipe':
                $controller = new RecipeController();
                $response = $controller->translateRecipe($data);
                break;

            case 'add_comment':
                $controller = new RecipeController();
                $response = $controller->addComment($data);
                break;

            // 👑 Admin
            case 'list_users':
                $controller = new AdminController();
                $response = $controller->listUsers();
                break;

            case 'validate_role':
                $controller = new AdminController();
                $response = $controller->validateRole($data);
                break;

            case 'request_role':
                $controller = new AdminController();
                $response = $controller->requestRole($data);
                break;

            case 'validate_recipe':
                $controller = new AdminController();
                $response = $controller->validateRecipe($data);
                break;

            case 'reject_recipe':
                $controller = new AdminController();
                $response = $controller->rejectRecipe($data);
                break;

            default:
                http_response_code(400);
                $response = ['success' => false, 'message' => 'Action non supportée'];
        }
    } catch (Exception $e) {
        http_response_code(500);
        $response = ['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()];
    }
}

echo json_encode($response);
