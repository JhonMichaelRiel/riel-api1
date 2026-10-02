<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Controller: ProductController
 * 
 * Automatically generated via CLI.
 */
class ProductController extends Controller {
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('ProductModel');
        $this->call->library('api');
    }

    public function index(){
        $this->api->require_jwt();
        $products = $this->ProductModel->getAll();
        return $this->api->respond($products);
    }

    public function show($id){
        $this->api->require_jwt();
        $product = $this->ProductModel->getById($id);
        if(!$product){
            return $this->api->respond_error('Product not found', 404);
        }
        return $this->api->respond($product);
    }

    public function store(){
        $this->api->require_jwt();
        $body = $this->api->body();
        $data = [
            'product_name' => $body['product_name'] ?? '',
            'description' => $body['description'] ?? '',
            'price' => $body['price'] ?? 0,
            'quantity' => $body['quantity'] ?? 0
        ];

        $id = $this->ProductModel->createProduct($data);
        return $this->api->respond([
            'message' => 'Product created successfully',
            'product_id' => $id
        ], 201);
    }

    public function update($id){
        $this->api->require_jwt();
        $product = $this->ProductModel->getById($id);

        if (!$product) {
            return $this->api->respond_error('Product not found', 404);
        }
        $body = $this->api->body();
        $data = [
            'product_name' => $body['product_name'] ?? '',
            'description' => $body['description'] ?? '',
            'price' => $body['price'] ?? 0,
            'quantity' => $body['quantity'] ?? 0
        ];

        $this->ProductModel->updateProduct($id, $data);
        return $this->api->respond([
            'message' => 'Product updated successfully',
            'product_id' => $id
        ]);
    }

    public function delete($id){
        $this->api->require_jwt();
        $product = $this->ProductModel->getById($id);

        if (!$product) {
            return $this->api->respond_error('Product not found', 404);
        }
        $this->ProductModel->deleteProduct($id);
        return $this->api->respond([
            'message' => 'Product deleted successfully',
            'product_id' => $id
        ]);
    }
}