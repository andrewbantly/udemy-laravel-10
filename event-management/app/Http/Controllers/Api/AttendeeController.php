<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationships;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Attendee;

class AttendeeController extends Controller
{
    use CanLoadRelationships;

    public function __construct() 
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'update']);
        $this->middleware('throttle:60,1')->only(['store', 'destroy']);
        $this->authorizeResource(Attendee::class, 'attendee');
    }

    private array $relations = ['user'];
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        // $attendees = $event->attendees()->latest();

        $attendees = $this->loadRelationships(
            $event->attendees()->latest()
        );

        return AttendeeResource::collection(
            $attendees->paginate() 
        );

    }

    public function store(Request $request, Event $event)
    {
        // $attendee = $event->attendees()->create([
        //     'user_id' => 1,
        // ]);
        $attendee = $this->loadRelationships(
            $event->attendees()->create(
                ['user_id' => 1]
            ));

        return new AttendeeResource($attendee);
    }
    public function show(Event $event, Attendee $attendee)
    {
        return new AttendeeResource(
            $this->loadRelationships($attendee)
        );
    }

    public function destroy(Event $event, Attendee $attendee)
    {
        $this->authorize('remove-attendee', [$event, $attendee]);
        $attendee->delete();
        return response(status:204);
    }
}
