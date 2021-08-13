<?php
namespace App\Repositories;


interface PostRepositoryInterface{

    public function all();

    public function create();

    public function findById($id);

    public function update($id);

    public function delete($id);

    public function findPostCreator($id);
}