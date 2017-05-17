<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\SubmitIssueRequest;
use App\Model\Issue;
use Config;
use App\Model\User;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\SignUpRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IssueSubmissionController extends Controller
{
    public function __invoke(SubmitIssueRequest $request)
    {
        $d = $request->except('contact');
        $issue = new Issue($d);

        if (!$issue->save()) {
            throw new HttpException(500);
        }

        $contact = $issue->contact()->create($request->get('contact'));

        return response()->json($issue->toArray(), 201);
    }
}
