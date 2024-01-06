@extends('layouts.app')

@section('title', 'List of tasks')

@section('content')
    <div>
        <a href="{{route('tasks.create')}}">Add task</a>
    </div>
    @if (count($tasks))

        @foreach ($tasks as $task)
        <div>
            <a href="{{route('tasks.show', ['task' => $task->id])}}">{{$task->title}}</a>
        </div>
        @endforeach
    
    @else
        <div>There are no tasks.</div>        
    @endif
    @if ($tasks->count())
    <nav>
        {{$tasks->links()}}
    </nav>
    @endif
@endsection