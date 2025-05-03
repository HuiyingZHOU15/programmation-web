<?php

class RecipeController {
    private $file = __DIR__ . '/../data/recipes.json';

    private function loadRecipes() {
        if (!file_exists($this->file)) {
            http_response_code(500);
            return [];
        }
        $json = file_get_contents($this->file);
        return json_decode($json, true);
    }

    private function saveRecipes($recipes) {
        file_put_contents($this->file, json_encode($recipes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function getUser($username) {
        $usersFile = __DIR__ . '/../data/users.json';
        if (!file_exists($usersFile)) return null;
        $users = json_decode(file_get_contents($usersFile), true);
        foreach ($users as $user) {
            if ($user['username'] === $username) return $user;
        }
        return null;
    }

    public function addRecipe($data) {
        // ✅ Vérification d'identité
        if (!isset($data['username'])) {
            http_response_code(401);
            return ['success' => false, 'message' => 'Utilisateur non connecté'];
        }

        $user = $this->getUser($data['username']);
        if (!$user || !in_array('Chef', array_map('strtolower', $user['roles']))) {
            http_response_code(403);
            return ['success' => false, 'message' => 'Seuls les chefs peuvent ajouter des recettes'];
        }

        // ✅ Vérification minimale des champs
        if (!isset($data['name']) || !isset($data['ingredients']) || !isset($data['steps'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Champs obligatoires manquants'];
        }

        $recipes = $this->loadRecipes();

        $recipes[] = [
            'name' => $data['name'],
            'Author' => $data['Author'] ?? $data['username'],
            'imageURL' => $data['imageURL'] ?? '',
            'ingredients' => $data['ingredients'],
            'steps' => $data['steps'],
            'validated' => false
        ];

        $this->saveRecipes($recipes);
        http_response_code(201);
        return ['success' => true, 'message' => 'Recette ajoutée avec succès'];
    }

    public function translateRecipe($data) {
        if (!isset($data['username'])) {
            http_response_code(401);
            return ['success' => false, 'message' => 'Utilisateur non connecté'];
        }

        $user = $this->getUser($data['username']);
        if (!$user || !in_array('Traducteur', array_map('strtolower', $user['roles']))) {
            http_response_code(403);
            return ['success' => false, 'message' => 'Seuls les traducteurs peuvent modifier la traduction'];
        }

        if (!isset($data['name'], $data['nameFR'], $data['ingredientsFR'], $data['stepsFR'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Données incomplètes'];
        }

        $recipes = $this->loadRecipes();

        foreach ($recipes as &$recipe) {
            if ($recipe['name'] === $data['name']) {
                $recipe['nameFR'] = $data['nameFR'];
                $recipe['ingredientsFR'] = $data['ingredientsFR'];
                $recipe['stepsFR'] = $data['stepsFR'];
                $this->saveRecipes($recipes);
                http_response_code(200);
                return ['success' => true, 'message' => 'Traduction enregistrée'];
            }
        }

        http_response_code(404);
        return ['success' => false, 'message' => 'Recette introuvable'];
    }

    public function addComment($data) {
        if (!isset($data['recipe'], $data['author'], $data['text'])) {
            http_response_code(400);
            return ['success' => false, 'message' => 'Champs manquants'];
        }

        $user = $this->getUser($data['author']);
        if (!$user) {
            http_response_code(403);
            return ['success' => false, 'message' => 'Auteur invalide'];
        }

        $recipes = $this->loadRecipes();

        foreach ($recipes as &$recipe) {
            if ($recipe['name'] === $data['recipe']) {
                if (!isset($recipe['comments'])) $recipe['comments'] = [];

                $recipe['comments'][] = [
                    'author' => $data['author'],
                    'text' => $data['text'],
                    'date' => date("Y-m-d H:i")
                ];

                $this->saveRecipes($recipes);
                http_response_code(201);
                return ['success' => true, 'message' => '💬 Commentaire ajouté'];
            }
        }

        http_response_code(404);
        return ['success' => false, 'message' => 'Recette non trouvée'];
    }
}
