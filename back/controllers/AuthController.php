<?php

class AuthController
{
	private string $filePath;

	public function __construct(string $filePath)
	{
		$this->filePath = $filePath;
	}

	
	public function handleRegister(): void
	{
		header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["username"], $data["email"], $data["password"])) {
        http_response_code(400);
        echo json_encode(["error" => "Champs requis manquants"]);
        return;
    }

    $name = filter_var($data["username"], FILTER_SANITIZE_STRING);
    $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
    $password = $data["password"];

    if (!$email) {
        http_response_code(400);
        echo json_encode(["error" => "Format d'email invalide"]);
        return;
    }

    if (strlen($password) < 8 || strlen($password) > 32) {
        http_response_code(400);
        echo json_encode(["error" => "Mot de passe entre 8 et 32 caractères requis"]);
        return;
    }

    $users = $this->getAllUsers();
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            http_response_code(409);
            echo json_encode(["error" => "Email déjà utilisé"]);
            return;
        }
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $newUser = [
        "user_id" => uniqid(),
        "username" => $name,
        "email" => $email,
        "password" => $hashedPassword,
        "role" => "cuisinier", // rôle par défaut
        "roleAsked" => null
    ];
    $users[] = $newUser;
    file_put_contents($this->filePath, json_encode($users, JSON_PRETTY_PRINT));
    http_response_code(201);
    echo json_encode(["message" => "Inscription réussie"]);
}
	
		// If any error occurs, return an error message with the appropriate HTTP status code
		// Make sure to set the Content-Type header to 'application/json' in the response
		// You can use the json_encode function to encode an array as JSON
		// You can use the http_response_code function to set the HTTP status code
	

	// Implement the handleLogin method
	public function handleLogin(): void
	{
		header('Content-Type: application/json');

		// 1.Check if the request Content-Type is 'application/x-www-form-urlencoded'
		if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
			http_response_code(400);
			echo json_encode(['error' => 'Content-Type must be application/json']);
			return;
		}
	
		// 2. Get the email and password from the POST data
		$input = json_decode(file_get_contents("php://input"), true);
		$email = $input['email'] ?? '';
		$password = $input['password'] ?? '';
	
		// 3. Validate the email and password
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			http_response_code(400);
			echo json_encode(['error' => 'Email invalide']);
			return;
		}
	
		if (strlen($password) < 8 || strlen($password) > 32) {
			http_response_code(400);
			echo json_encode(['error' => 'Mot de passe invalide']);
			return;
		}
	
		// 4. Check if the user exists and the password is correct
		$users = file_exists($this->filePath) ? json_decode(file_get_contents($this->filePath), true) : [];
		foreach ($users as $id => $user) {
			if ($user['email'] === $email && password_verify($password, $user['password'])) {
				// 5. Store the user session
				$_SESSION['user'] = $id;
	
				http_response_code(200);// 6. Return a success message with HTTP status code 200
				echo json_encode([
					'message' => 'Connexion réussie',
					'user_id' => $id,
					'role' => $user['role'],
					'name' => $user['name']
				]);
				return;
			}
		}
	
		// 如果用户不存在或密码不匹配
		http_response_code(401);
		echo json_encode(['error' => 'Identifiants incorrects']);
		// Additional hints:
		// If any error occurs, return an error message with the appropriate HTTP status code
		// Make sure to set the Content-Type header to 'application/json' in the response
		// You can use the getAllUsers method to get the list of registered users
		// You can use the password_verify function to verify the password
		// You can use the $_SESSION superglobal to store the user session
		// You can use the json_encode function to encode an array as JSON
		// You can use the http_response_code function to set the HTTP status code
	}

	public function handleLogout(): void
	{
		session_destroy(); // Clear session
		http_response_code(200);
		echo json_encode(['message' => 'Logged out successfully']);
	}

	public function validateAuth(): ?string
	{
		return $_SESSION['user'] ?? null;
	}

	private function getAllUsers(): array
	{
		return file_exists($this->filePath) ? json_decode(file_get_contents($this->filePath), true) ?? [] : [];
	}
}
