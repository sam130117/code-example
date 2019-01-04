<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\MailRequest;
use App\Jobs\SendMailJob;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;


class MailController extends Controller
{
    public function send(MailRequest $request): JsonResponse
    {
        $data = $request->only(['name', 'email', 'message']);
        $job = (new SendMailJob($data))->delay(Carbon::now()->addSeconds(5));
        dispatch($job);

        return response()->json(null, Response::HTTP_OK);
    }
}
