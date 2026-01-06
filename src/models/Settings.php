<?php

namespace roelvanhintum\sentry\models;

use craft\base\Model;

class Settings extends Model
{
    public $enabled = true;
    public $anonymous = false; // Determines to log user info or not
    public $clientDsn;
    public $clientKey;
    public $dataStorageLocation = 'US';
    public $excludedCodes = ['404'];
    public $release; // Release number/name used by sentry.
    public $reportJsErrors = false; // Client only option
    public $sampleRate = 1.0; // Client only option
    public $ignoreErrors = [];

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        return [
            [['enabled', 'anonymous', 'reportJsErrors'], 'boolean'],
            [['clientDsn', 'clientKey', 'excludedCodes', 'release', 'dataStorageLocation'], 'string'],
            [['ignoreErrors'], 'array'],
            [['clientDsn'], 'required'],
            [['sampleRate'], 'number', 'min' => 0, 'max' => 1],
            [['dataStorageLocation'], 'in', 'range' => ['US', 'EU'], 'message' => 'Data Storage Location must be either "US" or "EU".'],
        ];
    }
}
