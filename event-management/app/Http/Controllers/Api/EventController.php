<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    use CanLoadRelationships;

    private array $relations = ['user', 'attendees', 'attendees.user']; // automatically sends relations to CanLoadRelationships

    // PROTECT THE ROUTES USING USER AUTH TOKEN
    public function __construct() 
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return EventResource::collection(Event::all()); // only the resource data is sent
        // $relations = ['user', 'attendees', 'attendees.user'];
        $query = $this->loadRelationships(Event::query());
        
        // $query = Event::query();

        // THIS IS NOW INSIDE THE CanLoadRelationships Trait
        // foreach ($relations as $relation) {
        //     $query->when(
        //         $this->shouldIncludeRelation($relation), 
        //         fn($q) => $q->with($relation)
        //     );
        // };

        return EventResource::collection(
            $query->latest()->paginate()
        );

        // return EventResource::collection(Event::with('user')->paginate()); // adds user data to response for each resource
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $event = Event::create([
            ... $request->validate([
                'name' =>'required|string|max:255',
                'description' =>'nullable|string',
                'start_time' =>'required|date',
                'end_time' =>'required|date|after:start_time',
            ]),
            'user_id' => $request->user()->id,
        ]);

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('user', 'attendees'); // adds user + attendee data to the single resource response
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request , Event $event)
    {
        // if (Gate::denies('update-event', $event)) {
        //     abort(403, 'You are not authorized to update this event.');
        // }
        $this->authorize('update-event', $event);

        $event->update($request->validate([
                'name' =>'sometimes|string|max:255',
                'description' =>'nullable|string',
                'start_time' =>'sometimes|date',
                'end_time' =>'sometimes|date|after:start_time',
            ]),
        );

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $this->authorize('destroy-event', $event);
        $event->delete();

        // return response()->json([
        //     'message' => 'message deleted successfully.'
        // ]);
        return response(status: 204);
    }
}
