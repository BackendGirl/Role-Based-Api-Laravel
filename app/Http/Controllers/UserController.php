<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //Register User
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6'
        ]);

        if($validator->fails()){
            return response()->json([
                'error'=>$validator->errors(),
                'status'=>400,
                'success'=>false
            ],400);
        }else{
            $user = User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
                'role'=>0
            ]);
            if($user){
                return response()->json([
                    'message'=>'User registered successfully',
                    'status'=>201,
                    'success'=>true
                ],201);
            }else{
                return response()->json([
                    'message'=>'something went wrong',
                    'status'=>400,
                    'success'=>false
                ],400);
            }
        }
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'error'=>$validator->errors(),
                'status'=>400,
                'success'=>false
            ],400);
        }else{
            if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
                $user = Auth::user();
                $token = $user->createToken('Personal Access token');
                return response()->json([
                    'message'=>'Logged In Successfully',
                    'token'=>$token->plainTextToken,
                    'status'=>200,
                    'success'=>true
                ],200);
            }else{
                return response()->json([
                    'error'=>'Unauthorized Credentials',
                    'status'=>401,
                    'success'=>false
                ],401);
            }
        }
    }

    public function updateProfile(Request $request){
        $user = Auth::user();
        if($user){
            if($request->name){
                $user->name = $request->name;
            }
            if($request->email){
                $user->email = $request->email;
            }
            $user->save();
            return response()->json([
                'message'=>'Updated Successfully',
                'status'=>200,
                'success'=>true
            ],200);
        }else{
            return response()->json([
                'error'=>'Unauthorized User',
                'status'=>401,
                'success'=>false
            ],401);
        }
    }

    public function forgetPassword(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
        ]);

        if($validator->fails()){
            return response()->json([
                'error'=>$validator->errors(),
                'status'=>400,
                'success'=>false
            ],400);
        }else{
            if(User::where('email',$request->email)->exists()){
                $user = User::where('email',$request->email)->first();
                $otp = rand(1000,9999);
                Otp::create([
                    'user_id'=>$user->id,
                    'otp'=>$otp
                ]);
                $data = [
                    'name'=>$user->name,
                    'otp'=>$otp
                ];
               try{
                $mail = Mail::send('otpMail',['data'=>$data],function($message) use($user){
                    $message->to($user->email)
                    ->subject('Your OTP Code');
                });
                if($mail){
                    return response()->json([
                        'message'=>'Mail sent Successfully',
                        'status'=>200,
                        'success'=>true
                    ],200);
                }else{
                    return response()->json([
                        'message'=>'something went wrong',
                        'status'=>400,
                        'success'=>false
                    ],400);
                }
               }catch(\Exception $e){
                return $e;
               }
               
            }else{
                return response()->json([
                    'error'=>'User not found',
                    'status'=>404,
                    'success'=>false
                ],404);
            }
        }
    }

    public function verifyOtp(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'otp'=>'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'error'=>$validator->errors(),
                'status'=>400,
                'success'=>false
            ],400);
        }else{
            $user = User::where('email',$request->email)->first();
            if($user){
                if(Otp::where('user_id',$user->id)->where('otp',$request->otp)->exists()){
                    $token = $user->createToken('Personal Access token');
                    return response()->json([
                        'message'=>'Otp verified Successfully',
                        'token'=>$token->plainTextToken,
                        'status'=>200,
                        'success'=>true
                    ],200);
                }else{
                    return response()->json([
                        'error'=>'Otp Not matched',
                        'status'=>401,
                        'success'=>false
                    ],401);
                }
            }else{
                return response()->json([
                    'error'=>'User not found.',
                    'status'=>404,
                    'success'=>false
                ],404);
            }
        }
    }

    public function updatePassword(Request $request){
        $validator = Validator::make($request->all(),[
            'password'=>'required|min:6',
            'confirm_password'=>'same:password'
        ]);

        if($validator->fails()){
            return response()->json([
                'error'=>$validator->errors(),
                'status'=>400,
                'success'=>false
            ],400);
        }else{
            $user = Auth::user();
            if($user){
                $update = User::where('id',$user->id)->update([
                    'password'=>Hash::make($request->password)
                ]);
                if($update){
                    return response()->json([
                        'message'=>'Password updated Successfully',
                        'status'=>200,
                        'success'=>true
                    ],200);
                }else{
                    return response()->json([
                        'error'=>'Something Went wrong',
                        'status'=>400,
                        'success'=>false
                    ],400);
                }
            }else{
                return response()->json([
                    'error'=>'User not found.',
                    'status'=>404,
                    'success'=>false
                ],404);
            }
        }
    }

    public function getProfile(Request $request){
        $user = Auth::user();
        if($user){
            return response()->json([
                'user'=>$user,
                'status'=>200,
                'success'=>true
            ],200);
        }else{
            return response()->json([
                'error'=>'Unauthorized User',
                'status'=>401,
                'success'=>false
            ],401);
        }
    }


}
