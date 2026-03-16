<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Middleware\JsonResponse; 
use App\DTO\CreateUserDTO; 
use App\DTO\UpdateUserDTO; 


class UserController {
    private $userService;

    public function __construct() {
        // On récupère la connexion via ton usine existante
        $databaseFactory = require __DIR__ . '/../config/database.php';
        $conn = $databaseFactory();
        $repo = new UserRepository($conn);
        $this->userService = new UserService($repo);
    }

    public function getAll(Request $request, Response $response) {
        $nom = $request->getQueryParams()['nom'] ?? null;
        $users = $this->userService->getAll($nom);
        
        $role = $_SERVER['USER']['role'] ?? 'guest';

        $result = array_map(function ($v) use ($role) {
            $v['_links'] = UserLinks::build($v['id'], $role);
            return $v;
        }, $users   );

        return JsonResponse::success($result);
    }


    public function getOne(Request $request, Response $response, array $args) {
        $user = $this->userService->getOne($args['id']);
        $role = $_SERVER['USER']['role'] ?? 'guest';
        $user['_links'] = UserLinks::build($user['Id'], $role);

        return JsonResponse::success($user);
    }

    // Méthode pour POST /login
    public function login(Request $request, Response $response) {
        // 1. Récupération propre du JSON envoyé par le client
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
    
        // 2. Validation basique
        if (empty($username) || empty($password)) {
            return JsonResponse::error("Identifiants requis", 400);
        }
    
        // 3. Recherche de l'utilisateur via le service (qui appelle le repo)
        // Note: assure-toi que ton UserService a bien la méthode findUserByUsername
        $user = $this->userService->findUserByUsername($username);
    
        if (!$user) {
            return JsonResponse::error("Utilisateur inconnu", 401);
        }
    
        // 4. Vérification du mot de passe haché
        if (password_verify($password, $user['password'])) {
            
            // 5. Création du Payload pour le JWT (le "contenu" du badge)
            $payload = [
                "id"       => $user['id'],
                "username" => $user['username'],
                "role"     => $user['role'] // 'admin', 'user', etc.
            ];
            
            // 6. Génération du token via ton JwtService
            $token = \App\Security\JwtService::generate($payload);
    
            return JsonResponse::success([
                "message"  => "Connexion réussie",
                "token"    => $token,
                "user"     => [
                    "username" => $user['username'],
                    "role"     => $user['role']
                ]
            ]);
        }
    
        // Si le mot de passe ne correspond pas
        return JsonResponse::error("Mot de passe incorrect", 401);
    }

    public function delete(Request $request, Response $response, array $args) {
        \App\Security\RoleMiddleware::requireRole('admin');
        $id = $args['id']; // Récupère le {id} de l'URL
        $this->userService->delete($id);
        return JsonResponse::success(["message" => "Utilisateur supprimé"]);
    }
    
    public function update(Request $request, Response $response, array $args) {
        // 1. Sécurité Admin
        \App\Security\RoleMiddleware::requireRole('admin');
    
        $id = $args['id']; 
        $data = $request->getParsedBody();
    
        try {
            $dto = UpdateUserDTO::fromArray($id, $data);
            
            $success = $this->userService->update($dto);
    
            if ($success) {
                return JsonResponse::success(["message" => "Utilisateur $id mis à jour (identité préservée)"]);
            }
            
            return JsonResponse::error("Échec de la mise à jour", 400);
    
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage(), 500);
        }
    }

    public function create(Request $request, Response $response) {
        \App\Security\RoleMiddleware::requireRole('admin');
        // 1. Récupérer le tableau JSON envoyé par Guzzle
        $data = $request->getParsedBody();
    
        try {
            // 2. Transformer l'array en OBJET DTO via ta méthode statique
            $dto = CreateUserDTO::fromArray($data);
    
            // 3. Appeler le service avec l'objet DTO (plus d'erreur de type !)
            $this->userService->create($dto);
    
            return JsonResponse::success(["message" => "Utilisateur créé avec succès"], 201);
            
        } catch (\Exception $e) {
            // Capture les erreurs (ex: NumSerie manquant ou erreur BDD)
            return JsonResponse::error($e->getMessage(), 500);
        }
    }
}