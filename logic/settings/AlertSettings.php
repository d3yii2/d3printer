<?php

namespace d3yii2\d3printer\logic\settings;

use d3yii2\d3printer\models\AlertSettings as AlertSettingsModel;
use Yii;

/**
 * Class AlertSettings
 * @package d3yii2\d3printer\logic\settings
 */
class AlertSettings
{
    protected $model;

    /**
     * AlertSettings constructor.
     * @param string $addKey
     */
    public function __construct(string $addKey)
    {
        $this->model = new AlertSettingsModel();
        $this->model->prepare($addKey);
    }
    
    /**
     * @return string
     */
    public function getCartridgeMinValue(): string
    {
        return $this->model->cartridge_remain_min;
    }
    
    /**
     * @return string
     */
    public function getDrumMinValue(): string
    {
        return $this->model->drum_remain_min;
    }
    
    /**
     * @return string
     */
    public function getEmailFrom(): string
    {
        return $this->model->email_from;
    }

    /**
     * @return array
     */
    public function getEmailTo(): array
    {
        $return = [];
        foreach (explode('|', $this->model->email_to) as $email) {
            if ($email = trim($email)) {
                /**
                 * added for solving issue with email address truncating first character
                 */
                if (strpos($email, 'ihards') === 0) {
                    Yii::error([
                        'message' => 'Email address starts with ihards: ',
                        'extra' => [
                            'email' => $email,
                            'model' => $this->model->attributes,
                        ]
                    ]);
                    continue;
                }
                $return[] = $email;
            }
        }
        return $return;
    }
    
    /**
     * @return string
     */
    public function getEmailSubject(): string
    {
        return $this->model->email_subject;
    }
}
