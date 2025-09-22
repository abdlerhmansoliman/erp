<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WebhookEventController extends Controller
{
    public function index()
    {
        return response()->json(Cache::get('webhook_events', []));
    }

    public function store(array $event)
    {
        $events = Cache::get('webhook_events', []);
        array_unshift($events, [
            'id' => uniqid(),
            'type' => $event['type'],
            'data' => $event['data'],
            'created_at' => now()
        ]);

        // Keep only last 10 events
        $events = array_slice($events, 0, 10);
        
        Cache::put('webhook_events', $events, now()->addDay());
    }
}