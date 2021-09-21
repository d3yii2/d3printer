<?php

namespace d3yii2\d3printer\components;

use yii\base\Component;

class G500LabelBase extends Component
{

    /** @var int  */
    public $labelHeight = 80;

    /** @var int  */
    public $labelWidth = 80;

    /** @var int  */
    public $density = 10;

    /** @var int  */
    public $copies = 1;


}