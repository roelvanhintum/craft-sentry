<?php

namespace roelvanhintum\sentry\models;

use craft\base\Model;

class Settings extends Model
{
    public $enabled = true;
    public $anonymous = false; // Determines to log user info or not
    public string $clientDsn;
    public string $clientKey;
    public $dataStorageLocation = 'US';
    public $excludedCodes = ['404'];
    public string $release; // Release number/name used by sentry.
    public $reportJsErrors = false; // Client only option
    public $sampleRate = 1.0; // Client only option
    public $ignoreErrors = [];
    public $allowUrls = [];

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        return [
            [['enabled', 'anonymous', 'reportJsErrors'], 'boolean'],
            [['clientDsn', 'clientKey', 'release', 'dataStorageLocation'], 'string'],
            [['excludedCodes', 'ignoreErrors', 'allowUrls'], 'each', 'rule' => ['string']],
            [['clientDsn'], 'required'],
            [['sampleRate'], 'number', 'min' => 0, 'max' => 1],
            [['dataStorageLocation'], 'in', 'range' => ['US', 'EU'], 'message' => 'Data Storage Location must be either "US" or "EU".'],
        ];
    }
}
