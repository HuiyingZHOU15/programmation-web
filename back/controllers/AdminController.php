<?php

class AdminController {
    private $file = __DIR__ . '/../data/users.json';

    private function loadUsers() {
        if (!file_exists($this->file)) {
            http_response_code(500);
            return [];
        }
        $json = file_get_contents($this->file);
        return json_decode($json, true);
    }

    private function saveUsers($users) {
        file_put_contents($this->file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function listUsers() {
        http_response_code(200);
        return $this->loadUsers();
    }

    public function validateRole($data) {
        if (!isset($data['username'], $data['role'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Données manquantes'];
        }

        $users = $this->loadUsers();
        foreach ($users as &$user) {
            if ($user['username'] === $data['username']) {
                if (!in_array($data['role'], $user['roles'])) {
                    $user['roles'][] = $data['role'];
                }
                if (($key = array_search($data['role'], $user['demandeRoles'])) !== false) {
                    unset($user['demandeRoles'][$key]);
                }

                $user['demandeRoles'] = array_values($user['demandeRoles']);
                $this->saveUsers($users);
                http_response_code(200);
                return ['success' => true, 'message' => "✅ Rôle validé pour {$data['username']}"];
            }
        }

        http_response_code(404);
        return ['success' => false, 'message' => 'Utilisateur non trouvé'];
    }

    public function requestRole($data) {
        if (!isset($data['username'], $data['role'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Données incomplètes'];
        }

        $users = $this->loadUsers();
        foreach ($users as &$user) {
            if ($user['username'] === $data['username']) {
                if (!in_array($data['role'], $user['roles']) &&
                    !in_array($data['role'], $user['demandeRoles'] ?? [])) {
                    $user['demandeRoles'][] = $data['role'];
                    $this->saveUsers($users);
                    http_response_code(201);
                    return ['success' => true, 'message' => "✅ Demande envoyée pour le rôle {$data['role']}"];
                } else {
                    http_response_code(409); // Conflit
                    return ['success' => false, 'message' => "⚠️ Rôle déjà attribué ou demandé"];
                }
            }
        }

        http_response_code(404);
        return ['success' => false, 'message' => 'Utilisateur non trouvé'];
    }

    public function validateRecipe($data) {
        if (!isset($data['name'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Nom manquant'];
        }

        $file = __DIR__ . '/../data/recipes.json';
        $recipes = json_decode(file_get_contents($file), true);

        foreach ($recipes as &$r) {
            if ($r['name'] === $data['name']) {
                $r['validated'] = true;
                file_put_contents($file, json_encode($recipes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                http_response_code(200);
                return ['success' => true, 'message' => "✅ Recette validée : {$r['name']}"];
            }
        }

        http_response_code(404);
        return ['success' => false, 'message' => 'Recette non trouvée'];
    }

    public function rejectRecipe($data) {
        if (!isset($data['name'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Nom manquant'];
        }

        $file = __DIR__ . '/../data/recipes.json';
        $recipes = json_decode(file_get_contents($file), true);
        $recipes = array_filter($recipes, fn($r) => $r['name'] !== $data['name']);
        file_put_contents($file, json_encode(array_values($recipes), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        http_response_code(200);
        return ['success' => true, 'message' => "❌ Recette supprimée"];
    }
}
