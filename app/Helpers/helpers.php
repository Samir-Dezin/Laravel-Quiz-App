<?php

function result($message,$statusCode,$array=null){
return response()->json([
    'message'=>$message,
    'status_code'=>$statusCode,
    'data'=>$array,
],$statusCode);
}

 function respondWithToken($token)
    {
        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ];
    }