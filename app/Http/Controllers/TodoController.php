<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Todo;

class TodoController extends Controller
{
  
    public function index()
    {
        $userId = Auth::user()->id;
        $todos = Todo::where(['user_id' => $userId])->get();
        return view('todo.list', ['todos' => $todos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('todo.add');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|min:10',
            'description' => 'required|min:25',
        ]);

        $userId = Auth::user()->id;
        $input = $request->input();
        $input['user_id'] = $userId;
        $todoStatus = Todo::create($input);
        if ($todoStatus) {
            $request->session()->flash('success', 'Todo successfully added');
        } else {
            $request->session()->flash('error', 'Oops something went wrong, Todo not saved');
        }
        return redirect('todo');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
{
    $userId = Auth::user()->id;
    $todo = Todo::where(['user_id' => $userId, 'id' => $id])->first();
    if (!$todo) {
        return redirect('todo')->with('error', 'Todo not found');
    }
    return view('todo.view', ['todo' => $todo]);
}

    public function edit($id)
    {
        $userId = Auth::user()->id;
        $todo = Todo::where(['user_id' => $userId, 'id' => $id])->first();
        if ($todo) {
            return view('todo.edit', [ 'todo' => $todo ]);
        } else {
            return redirect('todo')->with('error', 'Todo not found');
        }
    }

   
    public function update(Request $request, $id)
    {
        $input = $request->input();

        $userId = Auth::user()->id;
        $todo = Todo::find($id);
        if (!$todo) {
            return redirect('todo')->with('error', 'Todo not found.');
        }
        
        $input['user_id'] = $userId;
        $todoStatus = $todo->update($input);
        if ($todoStatus) {
            return redirect('todo')->with('success', 'Todo successfully updated.');
        } else {
            return redirect('todo')->with('error', 'Oops something went wrong. Todo not updated');
        }
    }

    public function destroy($id)
    {
        $userId = Auth::user()->id;
        $todo = Todo::where(['user_id' => $userId, 'id' => $id])->first();
        $respStatus = $respMsg = '';
        if (!$todo) {
            $respStatus = 'error';
            $respMsg = 'Todo not found';
        }
        $todoDelStatus = $todo->delete();
        if ($todoDelStatus) {
            $respStatus = 'success';
            $respMsg = 'Todo deleted successfully';
        } else {
            $respStatus = 'error';
            $respMsg = 'Oops something went wrong. Todo not deleted successfully';
        }
        return redirect('todo')->with($respStatus, $respMsg);
    }
    
}
