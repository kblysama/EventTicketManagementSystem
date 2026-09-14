<?php

namespace App\Http\Controllers\Api\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckInRequest;
use App\Models\Event;
use App\Services\CheckInService;
use Illuminate\Http\JsonResponse;

class CheckInController extends Controller
{
    public function store(CheckInRequest $request, Event $event, CheckInService $checkIn): JsonResponse
    {
        $result = $checkIn->verify($event, $request->string('code')->toString(), $request->user());

        return response()->json([
            'reused' => $result['reused'],
            'message' => $result['message'],
            'ticket' => [
                'code' => $result['ticket']->code,
                'status' => $result['ticket']->status->value,
            ],
        ], $result['reused'] ? 409 : 200);
    }
}
