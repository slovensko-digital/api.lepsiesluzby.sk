<?php

declare(strict_types=1);

namespace App\Handler;

use App\Model\AtlassianIdeaModel;

final class SendIdeaHandler extends IssueHandler
{
    protected string $issueClassName = AtlassianIdeaModel::class;
}
