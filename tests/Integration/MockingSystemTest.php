<?php

use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Exceptions\APIException;
use InterWorks\Tableau\Services\ServerInfoService;
use InterWorks\Tableau\TableauAPI;
use InterWorks\Tableau\Tests\Mocks\TableauMock;

describe('MockingSystemTests', function () {
    it('handles server info requests correctly', function () {
        TableauMock::init();
        TableauMock::mockServerInfo();

        $serverInfo = ServerInfoService::fetchServerInfo();
        expect($serverInfo['serverInfo']['productVersion']['value'])->toBe('2024.3.0');
        expect($serverInfo['serverInfo']['restApiVersion'])->toBe('3.24');
    });
});
