<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\UserPaquete;

class UserPaquetesController extends Controller
{
    
    public function index(Request $request)
    { 
        $data=UserPaquete::all()->toArray();
        $user_id =Auth::id();
       return response()->json(["msg"=>"Users finded",
        "data: "=>UserPaquete::all(),],200);
    }



    public function store(Request $request)
    {
        $validate = Validator::make(
            $request->all(),[
                "user_id"=>"required|exists:user_id",
                "paquetes_id"=>"required"
            ]
        );

        if($validate->fails())
        {
            return response()->json(["msg"=>"Data failed",
            "data:"=>$validate->errors()],422);
        }

        $user = new UserPaquete();
        $user->user_id=$request->user_id;
        $user->paquete_id=$request->paquete_id;
        $user->save();
        return response()->json(["msg"=>"UserPaquete agregado correctamente"],201);
    }
}
