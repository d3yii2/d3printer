<?php

namespace d3yii2\d3printer\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

class Panel extends Model
{
    public ?string $printerName = null;
    public ?string $printerAccessUrl = null;
    public ?string $status = null;
    public ?string $cartridge = null;
    public ?string $ip = null;

    /** @var array  printer config panel data for displaying in panel */
    public array $config = [];

    public function rules(): array
    {
        return [
            [['printerName', 'printerAccessUrl', 'status', 'cartridge','ip'], 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'printerName' => Yii::t('d3printer', 'Printer name'),
            'printerAccessUrl' => Yii::t('d3printer', 'Printer access url'),
            'status' => Yii::t('d3printer', 'Status'),
            'cartridge' => Yii::t('d3printer', 'Cartridge'),
            'ip' => 'IP',
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public function loadData(array $data): void
    {
        foreach ($this->config as $settingName => $settingDefinition) {
            if (!property_exists($this, $settingName)) {
                throw new InvalidConfigException('Invalid setting name: ' . $settingName);
            }
            if (is_string($settingDefinition)) {
                $this->{$settingName} = $data[$settingName] ?? $settingDefinition;
                continue;
            }
            $settingValue = $data;
            foreach ($settingDefinition as $subSettingKey => $subSettingDefinition) {
                if ($subSettingKey === 'sprintf_format') {
                    $settingValue = sprintf($subSettingDefinition, $settingValue);
                    break;
                }
                $settingValue = $settingValue[$subSettingDefinition] ?? null;
            }
            $this->{$settingName} = $settingValue;
        }
    }

    public function displayAttributes(): array
    {
        $config = $this->config;
        unset($config['printerName'], $config['printerAccessUrl']);
        return array_keys($config);
    }
}
