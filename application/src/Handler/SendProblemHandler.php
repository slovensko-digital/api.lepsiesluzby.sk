<?php

declare(strict_types=1);

namespace App\Handler;

use App\Model\AtlassianProblemModel;

final class SendProblemHandler extends IssueHandler
{
    private string $issueClassName = AtlassianProblemModel::class;
}