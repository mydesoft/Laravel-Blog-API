<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use Auth;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\PostRepository;


class PostController extends Controller
{
    public function __construct(PostRepositoryInterface $postRepository){

        $this->postRepository = $postRepository;
    }

    public function index(){
        
        $posts = $this->postRepository->all();

        if (!$posts->count() > 0) {
            return response()->json([

                'message' => 'No Posts Yet'
            ], Response::HTTP_FOUND);
        }

        return response()->json([

            'data' =>  PostCollection::collection($posts)
        ]);
    }

    public function store(Request $request){
         $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);
     
        $post = $this->postRepository->create();

        if ($post) {
            return response()->json([
                'data' =>  new PostResource($post)
            ], Response::HTTP_CREATED);
        }

        else{

             return response()->json([
                'error' =>  'Post was not created succesfully'
            ]);   
        }
        
    }

    public function show($id){

        $post = $this->postRepository->findById($id);
        if(!$post){
            return response()->json([
                'error' =>  'No Post Found'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'data' =>  new PostResource($post)
        ], Response::HTTP_FOUND);
    }

    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $userId = $this->postRepository->findPostCreator($id);
        //Check Authourization
        if (Auth::user()->id !== $userId) {
            
            return response()->json([
                'error' => 'Unauthourized'
            ], Response::HTTP_UNAUTHORIZED);
        }
       

        $this->postRepository->update($id);

        $post = $this->postRepository->findById($id);
        
        return response()->json([
            'data' =>  new PostResource($post),
            'message' => 'Post Updated'
        ], Response::HTTP_CREATED);
    }

    public function destroy($id){

        $userId = $this->postRepository->findPostCreator($id);

        //Check Authorization
        if (Auth::user()->id !== $userId) {
            
            return response()->json([
                'error' => 'Unauthourized'
            ], Response::HTTP_UNAUTHORIZED);
        }
       
        $this->postRepository->delete($id);
        
        return response()->json([
            'message' => 'Post Deleted'
        ]);
    }


   
}
