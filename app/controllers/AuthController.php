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
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';
        $user = $this->UserModel->findByEmail($email);
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