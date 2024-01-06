<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use \App\Models\Task;
use \App\Http\Requests\TaskRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
  return redirect()->route('tasks.index');
});

Route::get('/tasks', function () {
    return view('index', [
        'tasks' => Task::latest()->get()
    ]);
})->name('tasks.index');

Route::view('/tasks/create', 'create')->name('tasks.create');

Route::get('/tasks/{task}/edit', function (Task $task) {
  return view('edit', [
    'task' => $task
  ]);
})->name('tasks.edit');

Route::get('/tasks/{task}', function (Task $task) {
  return view('show', [
    'task' => $task
  ]);
})->name('tasks.show');

Route::post('/tasks', function (TaskRequest $request) {
// dd($request->all());
//   $data = $request->validate([
//     'title' => 'required | max:255',
//     'description' => 'required',
//     'long_description' => 'required',
//  ]);
//  $task = new Task;
//  $task->title = $data['title'];
//  $task->description = $data['description'];
//  $task->long_description = $data['long_description'];
//  $task->save();

$data = $request->validated(); // created a TaskRequest class to validate data instead of doing it for each route
$task = Task::create($data);
 
 return redirect()->route('tasks.show', ['task' => $task->id])->with('success','Task created successfully.');
})->name('tasks.store');

Route::put('/tasks/{task}', function (Task $task, TaskRequest $request) {
    
  $data = $request->validated();
  $task->update($data);

 return redirect()->route('tasks.show', ['task' => $task->id])->with('success','Task updated successfully.');
})->name('tasks.update');

Route::delete('/tasks/{task}', function (Task $task) {
  $task->delete();
  return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
})->name('tasks.destroy');

// Route::get('/hello', function () {
//     return "Hello";
// }) ->name('hello'); // can name the route 

// Route::get('/hallo', function () {
//     return redirect()-> route('hello');
// });

// Route::get('/greet/{name}', function ($name) {
//     return 'Hello ' . $name . '!';
// });

Route::fallback(function() {
    return '404 not found route';
});