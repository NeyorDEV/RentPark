<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Middleware\JsonResponse; 
use App\DTO\CreateVehiculeDTO; 
use App\DTO\UpdateVehiculeDTO; 


class VehiculeController {
    private $vehiculeService;

    public function __construct() {
        // On récupère la connexion via ton usine existante
        $databaseFactory = require __DIR__ . '/../config/database.php';
        $conn = $databaseFactory();
        $repo = new VehiculeRepository($conn);
        $this->vehiculeService = new VehiculeService($repo);
    }

    // Méthode pour GET /api/vehicules
    public function list(Request $request, Response $response) {
        $nom = $request->getQueryParams()['nom'] ?? null;
        $vehicules = $this->vehiculeService->getAll($nom);
        
        $role = $_SERVER['USER']['role'] ?? 'guest';

        $result = array_map(function ($v) use ($role) {
            $v['_links'] = VehiculeLinks::build($v['NumSerie'], $role);
            return $v;
        }, $vehicules);

        return JsonResponse::success($result);
    }

    // Méthode pour GET /api/vehicules/{id}
    public function getOne(Request $request, Response $response, array $args) {
        $vehicule = $this->vehiculeService->getOne($args['id']);
        $role = $_SERVER['USER']['role'] ?? 'guest';
        $vehicule['_links'] = VehiculeLinks::build($vehicule['NumSerie'], $role);

        return JsonResponse::success($vehicule);
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
        // Note: assure-toi que ton VehiculeService a bien la méthode findUserByUsername
        $user = $this->vehiculeService->findUserByUsername($username);
    
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
        $this->vehiculeService->delete($id);
        return JsonResponse::success(["message" => "Véhicule supprimé"]);
    }
    
    public function update(Request $request, Response $response, array $args) {
        // 1. Sécurité Admin
        \App\Security\RoleMiddleware::requireRole('admin');
    
        $numSerie = $args['id']; 
        $data = $request->getParsedBody();
    
        try {
            $dto = UpdateVehiculeDTO::fromArray($numSerie, $data);
            
            $success = $this->vehiculeService->update($dto);
    
            if ($success) {
                return JsonResponse::success(["message" => "Véhicule $numSerie mis à jour (identité préservée)"]);
            }
            
            return JsonResponse::error("Échec de la mise à jour", 400);
    
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage(), 500);
        }
    }

    // VehiculeController.php
    public function create(Request $request, Response $response) {
        \App\Security\RoleMiddleware::requireRole('admin');
        // 1. Récupérer le tableau JSON envoyé par Guzzle
        $data = $request->getParsedBody();
    
        try {
            // 2. Transformer l'array en OBJET DTO via ta méthode statique
            $dto = CreateVehiculeDTO::fromArray($data);
    
            // 3. Appeler le service avec l'objet DTO (plus d'erreur de type !)
            $this->vehiculeService->create($dto);
    
            return JsonResponse::success(["message" => "Véhicule créé avec succès"], 201);
            
        } catch (\Exception $e) {
            // Capture les erreurs (ex: NumSerie manquant ou erreur BDD)
            return JsonResponse::error($e->getMessage(), 500);
        }
    }
}