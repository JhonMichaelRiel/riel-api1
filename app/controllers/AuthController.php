<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Controller: AuthController
 * 
 * Automatically generated via CLI.
 */
class AuthController extends Controller {
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('UserModel');
        $this->call->library('api');
    }

    public function login(){
        $body = $this->api->body();
        $login = $body['email'] ?? $body['username'] ?? '';
        $password = $body['password'] ?? '';
        $user = $login !== '' ? $this->UserModel->findByLogin($login) : null;
        if (!$user || !password_verify($password, $user['password'])){
            return $this->api->respond_error('Invalid email or password', 401);
        }
        if ($user['is_active'] == 0){
            return $this->api->respond_error('User account is not active', 403);
        }
        $tokens = $this->api->issue_tokens([
            'id' => $user['id'],
            'role' => $user['role']
        ]);
        return $this->api->respond([
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
            'tokens' => $tokens
        ]);
    }

    public function signup(){
        $body = $this->api->body();
        $username = $body['username'] ?? '';
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';

        if (!is_string($username) || !is_string($email) || !is_string($password)
            || $username === '' || strlen($username) > 100
            || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 191
            || strlen($password) < 8) {
            return $this->api->respond_error(
                'Enter a username, a valid email, and a password with at least 8 characters.',
                422
            );
        }

        if ($this->UserModel->findByLogin($email)
            || $this->UserModel->findByLogin($username)) {
            return $this->api->respond_error(
                'That email or username is already in use.',
                409
            );
        }

        $this->UserModel->_query()->insert([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user',
            'is_active' => 1,
        ]);

        return $this->api->respond([
            'message' => 'Account created. You can now sign in.',
        ], 201);
    }

    public function logout(){
        $body = $this->api->body();
        $refresh_token = $body['refresh_token'] ?? '';
        if(empty($refresh_token)){
            return $this->api->respond_error('Refresh token is required', 400);
        }
        $this->api->revoke_refresh_token($refresh_token);
        return $this->api->respond([
            'message' => 'Logout successful'
        ]);
    }
}