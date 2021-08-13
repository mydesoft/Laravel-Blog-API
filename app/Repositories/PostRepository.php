<?php
namespace App\Repositories;
use App\Models\Post;
use Auth;

class PostRepository implements PostRepositoryInterface{

    public function all(){

        return Post::orderBy('created_at', 'desc')->get();
    }

    public function create(){
        $data = [
            'title' => request()->title,
            'body' => request()->body,
            'user_id' => auth()->id()
        ];
        return Post::create($data);
    }

    public function findById($postId){

        $post = Post::findOrFail($postId);

        return $post;
    }

    public function update($postId){
        $data = [
            'title' => request()->title,
            'body' => request()->body,
        ];

        $post = Post::findOrFail($postId);

        return $post->update($data);
        

    }

    public function delete($postId){
        
        Post::findOrFail($postId)->delete();

    }

    public function findPostCreator($postId){

        $post = Post::findOrFail($postId);

        return $post->user_id;
    }


}