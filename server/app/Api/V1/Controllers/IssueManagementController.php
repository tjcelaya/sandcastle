<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\SubmitIssueRequest;
use App\Http\Requests\ResourceElectionRequest;
use App\Model\Issue;
use App\Model\Permission;
use App\Model\Resource;
use App\Model\ResourceEngagement;
use App\Model\Role;
use App\Model\User;
use Dingo\Api\Auth\Auth;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\SignUpRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IssueManagementController extends Controller
{
    public function __construct()
    {
    }

    public function assignedToUser(Auth $apiAuth)
    {
        /** @var User $user */
        $user = $apiAuth->user();

        return $user->issues()->paginate();
    }

    public function byUser(Auth $apiAuth)
    {
        if (!$apiAuth->user()) {
            abort(500);
        }

        $issueT = Issue::databaseTable();
        $userT = User::databaseTable();
        $userRoleT = (new User)->roles()->getTable();
        $userIssueT = (new User)->issues()->getTable();
        $report = User::query()
            ->join(
                $userRoleT,
                function (JoinClause $j) use ($userT, $userRoleT) {
                    $j->on("$userT.id", '=', "$userRoleT.role_name");
                })
            ->leftJoin(
                $userIssueT,
                function (JoinClause $j) use ($userT, $userIssueT) {
                    $j->on("$userT.id", '=', "$userIssueT.issue_id");
                })
            ->leftJoin(
                $issueT,
                function (JoinClause $j) use ($userIssueT, $issueT) {
                    $j->on("$userIssueT.issue_id", '=', "$issueT.id");
                })
            // TODO: remove in case other people can get issues assigned
            ->where("$userRoleT.role_name", '=', Role::PEER)
            ->groupBy("$userIssueT.user_id")
            ->select(
                "$userIssueT.user_id",
                DB::raw("COUNT($userIssueT.issue_id) AS " . report_json_field('issue_id', 'count')),
                DB::raw("GROUP_CONCAT($userIssueT.issue_id) AS " . report_json_field('issue_id', 'list'))
            );

        return [
            'meta' => [
                'discriminant' => 'user_id',
                'aggregate_field' => 'issue_id',
                'aggregate_type' => 'count',
                'aggregate_list_contents' => true
            ],
            'data' => $report->get()
        ];
    }

    public function electResource(Auth $apiAuth, ResourceElectionRequest $request)
    {
        $issue = Issue::find($request->get('issue_id'));
        $issue->resourceEngagement()->save(ResourceEngagement::create([
            'resource_id' => $request->get('resource_id'),
            'elected_by' => $apiAuth->user()->id
        ]));

        return response('', 201);
    }
}
