<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Model: ProductModel
 * 
 * Automatically generated via CLI.
 */
class ProductModel extends Model {
    protected $table = 'products';
    protected $primary_key = 'id';
    protected $fillable = [
        'product_name',
        'description',
        'price',
        'quantity'
    ];
    protected $guarded = ['id'];

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll(){
        return $this->all();
    }

    public function getById($id){
        return $this->find($id);
    }

    public function createProduct($data){
        return $this->insert($data);
    }

    public function updateProduct($id, $data){
        return $this->update($id, $data);
    }

    public function deleteProduct($id){
        return $this->delete($id);
    }
}