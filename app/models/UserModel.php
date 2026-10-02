<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Model: UserModel
 * 
 * Automatically generated via CLI.
 */
class UserModel extends Model {
    protected $table = 'users';
    protected $primary_key = 'id';
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'is_active'
    ];
    protected $guarded = ['id'];

    public function __construct()
    {
        parent::__construct();
    }

    public function findByEmail($email){
        return $this->db->table('users')->where('email', $email)->get();
    }
}