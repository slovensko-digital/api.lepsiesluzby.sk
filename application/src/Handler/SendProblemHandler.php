<?php

declare(strict_types=1);

namespace App\Handler;

use App\Model\AtlassianProblemModel;

final class SendProblemHandler extends IssueHandler
{
    protected string $issueClassName = AtlassianProblemModel::class;
}