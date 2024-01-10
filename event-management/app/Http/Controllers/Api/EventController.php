<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return EventResource::collection(Event::all()); // only the resource data is sent

        return EventResource::collection(Event::with('user')->paginate()); // adds user data to response for each resource
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
            'user_id' => 1
        ]);

        return new EventResource($event);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('user', 'attendees'); // adds user + attendee data to the single resource response
        return new EventResource($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request , Event $event)
    {
        $event->update($request->validate([
                'name' =>'sometimes|string|max:255',
                'description' =>'nullable|string',
                'start_time' =>'sometimes|date',
                'end_time' =>'sometimes|date|after:start_time',
            ]),
        );

        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        // return response()->json([
        //     'message' => 'message deleted successfully.'
        // ]);
        return response(status: 204);
    }
}
