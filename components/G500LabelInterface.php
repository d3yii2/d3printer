<?php

namespace d3yii2\d3printer\components;

use Dbr\Ezpl\Builder;

interface G500LabelInterface
{
    public function createCommand(): Builder;

}