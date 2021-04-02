<?php


namespace App\Traits;


trait ResponseHandler
{
    public function sendSuccessResponse($payload = [],$message = 'Success',$status = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $payload,
            'status' => $status
        ]);
    }

    public function sendErrorResponse($message = 'Error',$status = 500,$errors = [])
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
            'status' => $status
        ],500);
    }

}
