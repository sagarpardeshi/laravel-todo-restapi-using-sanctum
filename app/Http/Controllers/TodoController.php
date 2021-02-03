<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Todo;

class TodoController extends Controller
{
    public function getTodoList(Request $request)
    {

        $user = Auth::user();

        if( !is_null($user) )
        {
            $per_page = $request->input('per_page');

            if( !$per_page )
            {
                $per_page = 10;
            }

            $todos          = Todo::where("user_id", $user->id)->paginate($per_page);

            $record_count   = Todo::where("user_id", $user->id)->count();

            if( count($todos) > 0 )
            {
                return response()->json(
                    [
                        "status"        => "success", 
                        "record_count"  => $record_count, 
                        "data"          => $todos
                    ],
                    200
                );
            } else {

                return response()->json(
                    [
                        "status"        => "failed", 
                        "record_count"  => count($todos), 
                        "message"       => "Todos not found."
                    ], 
                    404
                );
            }
        }
    }

    public function createTodo(Request $request)
    {
        $user = Auth::user();

        if( !is_null($user) )
        {
            $validator = Validator::make($request->all(), [
                "title"       => "required",
                "description" => "required",
                'thumbnail'   => 'mimes:png,jpg|max:2048'
            ]);

            if( $validator->fails() )
            {
                return response()->json(["status" => "failed", "validation_errors" => $validator->errors()]);
            }

            $todoInput            = $request->all();
            $todoInput['user_id'] = $user->id;

            $thumbnail = $request->file('thumbnail');

            if( $thumbnail )
            {
                $thumbnail_name = 'thumbnail_' . time() . '.' . $thumbnail->getClientOriginalExtension();
                $thumbnail_path = $thumbnail->storeAs("public/todo/media", $thumbnail_name);
                $todoInput['thumbnail'] = $thumbnail_path;
            }

            $todo = Todo::create($todoInput);

            if( !is_null($todo) )
            {
                return response()->json([
                    "status" => "success", 
                    "message" => 
                    "Success! Todo created", 
                    "data" => $todo
                ]);

            } else {

                return response()->json(["status" => "failed", "message" => "Todo cannot be created."]);
            }
        }
    }

    public function getTodo($id)
    {
        $user = Auth::user();

        if( !is_null($user) )
        {
            $todo = Todo::where("user_id", $user->id)->where("id", $id)->first();

            if( !is_null($todo) )
            {
                return response()->json(["status" => "success", "data" => $todo], 200);

            } else {

                return response()->json(["status" => "failed", "message" => "Todo not found"], 404);
            }

        } else {

            return response()->json(["status" => "failed", "message" => "Authentication Failed."], 403);
        }
    }

    public function updateTodo($id, Request $request)
    {
        $input  = $request->all();

        $user   = Auth::user();

        if( !is_null($user) )
        {
            $validator = Validator::make($request->all(), [
                "title"       => "required",
                "description" => "required",
                'thumbnail'   => 'mimes:png,jpg|max:2048'

            ]);

            if( $validator->fails() )
            {
                return response()->json(["status" => "failed", "validation_errors" => $validator->errors()],405);
            }

            $todo = Todo::where("user_id", $user->id)->where("id", $id)->first();

            if( !is_null($todo) )
            {

                if( $todo['user_id'] != $user->id )
                {
                    return response()->json(["status" => "failed", "message" => "Not allowed."], 405);
                }

                $todoUpdate = [];

                $thumbnail = $request->file('thumbnail');

                if( $thumbnail )
                {
                    $thumbnail_name = 'thumbnail_' . time() . '.' . $thumbnail->getClientOriginalExtension();
                    $thumbnail_path = $thumbnail->storeAs("public/todo/media", $thumbnail_name);

                    $todoUpdate['thumbnail'] = $thumbnail_path;
                    $todoUpdate['title'] = $input['title'];
                    $todoUpdate['description'] = $input['description'];
                } else {
                    $todoUpdate['title'] = $input['title'];
                    $todoUpdate['description'] = $input['description'];
                }

                $update = $todo->update($todoUpdate);

                return response()->json(["status" => "success", "message" => "Todo modified.", "data" => $todo], 200);

            } else {

                return response()->json(["status" => "failed", "message" => "Todo not found"], 404);
            }

        } else {

            return response()->json(["status" => "failed", "message" => "Authentication Failed."], 403);
        }
    }

    public function deleteTodo($id)
    {
        $user = Auth::user();
        
        if( !is_null($user) )
        {
            $todo = Todo::where("user_id", $user->id)->where("id", $id)->first();

            if( !is_null($todo) )
            {
                if( $todo['user_id'] != $user->id )
                {
                    return response()->json(["status" => "failed", "message" => "Not allowed."], 405);
                }

                $todo = Todo::where("id", $todo['id'])->where("user_id", $user->id)->delete();

                return response()->json(["status" => "success", "message" => "Todo deleted."], 200);

            } else {

                return response()->json(["status" => "failed", "message" => "Todo not found"], 404);
            }

        } else {

            return response()->json(["status" => "failed", "message" => "Authentication Failed."], 403);
        }
    }

    public function changeTodoStatus($id, Request $request)
    {

        $user = Auth::user();

        if( !is_null($user) )
        {
            $validator = Validator::make($request->all(), [
                "is_completed" => "required"
            ]);

            if( $validator->fails() )
            {
                return response()->json(["status" => "failed", "validation_errors" => $validator->errors()],405);
            }

            $is_completed = $request->input('is_completed');

            $todo = Todo::where("user_id", $user->id)->where("id", $id)->first();

            if( !is_null($todo) )
            {

                if( $is_completed == 1 )
                {

                    $completed_at = date('Y-m-d H:i:s');

                    $update = Todo::where("user_id", $user->id)
                    ->where("id", $id)
                    ->update(['is_completed' => 1, 'completed_at'=> $completed_at]);

                    $todo = Todo::where("user_id", $user->id)->where("id", $id)->first();

                    return response()->json(["status" => "success", "message" => "Todo marked complete.", "data" => $todo], 200);
                }

                if( $is_completed == 0 )
                {
                    $completed_at = null;
                    
                    $update = Todo::where("user_id", $user->id)
                    ->where("id", $id)
                    ->update(['is_completed' => 0, 'completed_at'=> $completed_at]);

                    $todo = Todo::where("user_id", $user->id)->where("id", $id)->first();

                    return response()->json(["status" => "success", "message" => "Todo marked incomplete.", "data" => $todo], 200);
                }
            } else {

                return response()->json(["status" => "failed", "message" => "Todo not found."],404);
            }

        } else {

            return response()->json(["status" => "failed", "message" => "Authentication Failed."], 403);
        }
    }
}
