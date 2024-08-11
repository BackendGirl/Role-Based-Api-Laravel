<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //

    public function getAllUsers(){
        return response()->json([
            'users'=>User::where('role',0)->get(),
            'status'=>200,
            'success'=>true
        ],200);
    }

    public function getUser($id){
        $user = User::where('id',$id)->where('role',0)->first();
        if($user){
            return response()->json([
                'user'=>$user,
                'status'=>200,
                'success'=>true
            ],200);
        }else{
            return response()->json([
                'error'=>'User not found',
                'status'=>404,
                'success'=>false
            ],404);
        }
    }
}
