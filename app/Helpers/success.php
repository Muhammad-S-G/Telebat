<?php

function success($data, $code = 200, $message = 'success')
{
    return response()->json([
        'status' => 'success',
        'message' => $message,
        'data' => $data
    ], $code);
}


function error($message = null, $code = 400, $errors = [])
{
    return response()->json([
        'status' => 'error',
        'message' => $message,
        'errors' => $errors
    ], $code);
}

function validationError($errors, $message = 'Validation errors')
{
    return response()->json([
        'status' => 'fail',
        'message' => $message,
        'errors' => $errors
    ], 422);
}

function message($message = 'success', $code = 200)
{
    return response()->json([
        'message' => $message,
    ], $code);
}
