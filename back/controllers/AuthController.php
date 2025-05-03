<?php

class AuthController {
    private $usersFile = __DIR__ . '/../data/users.json';

    private function loadUsers() {
        if (!file_exists($this->usersFile)) {
            http_response_code(500);
            return [];
        }
        $json = file_get_contents($this->usersFile);
        return json_decode($json, true);
    }

    private function saveUsers($users) {
        file_put_contents($this->usersFile, json_encode($users, JSON_PRETTY_PRINT));
    }

    public function register($data) {
        $username = trim($data['username']);
        $password = $data['password'];
    
        // ✅ Rôle par défaut
        $defaultRoles = ['Cuisinier'];
    
        if (!$username || !$password) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Champs manquants'];
        }
    
        $users = $this->loadUsers();
    
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                http_response_code(409); // Conflit
                return ['success' => false, 'message' => 'Nom d\'utilisateur déjà pris'];
            }
        }
    
        $newUser = [
            'username' => $username,
            'password' => $password,
            'roles' => $defaultRoles,
            'demandeRoles' => [],
            'likes' => [],
            'photos' => [],
            'comments' => []
        ];
    
        $users[] = $newUser;
    
        $this->saveUsers($users);
        http_response_code(201);
    
        // ✅ Retourner l'objet utilisateur pour le front (sessionStorage)
        return ['success' => true, 'message' => 'Inscription réussie', 'user' => $newUser];
    }
    
    public function login($data) {
        $username = trim($data['username']);
        $password = $data['password'];

        $users = $this->loadUsers();

        foreach ($users as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                http_response_code(200);
                return ['success' => true, 'user' => $user];
            }
        }

        http_response_code(401);
        return ['success' => false, 'message' => 'Identifiants incorrects'];
    }

    public function toggleLike($data) {
        if (!isset($data['username'], $data['recipe'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Données manquantes'];
        }
    
        $users = $this->loadUsers();
        foreach ($users as &$user) {
            if ($user['username'] === $data['username']) {
                if (!isset($user['likes'])) $user['likes'] = [];
    
                if (in_array($data['recipe'], $user['likes'])) {
                    $user['likes'] = array_values(array_filter($user['likes'], fn($r) => $r !== $data['recipe']));
                    $this->saveUsers($users);
                    http_response_code(200);
                    return ['success' => true, 'message' => '💔 Like retiré'];
                } else {
                    $user['likes'][] = $data['recipe'];
                    $this->saveUsers($users);
                    http_response_code(200);
                    return ['success' => true, 'message' => '❤️ Recette likée'];
                }
            }
        }

        http_response_code(404);
        return ['success' => false, 'message' => 'Utilisateur non trouvé'];
    }

    public function countLikes($data) {
        if (!isset($data['name'])) {
            http_response_code(400);
            return ['count' => 0];
        }

        $users = $this->loadUsers();
        $count = 0;
        foreach ($users as $user) {
            if (in_array($data['name'], $user['likes'] ?? [])) $count++;
        }

        http_response_code(200);
        return ['count' => $count];
    }
}
