<?php

use InterWorks\Tableau\Services\VersionService;

describe('VersionServiceTest', function() {
    it('returns the correct API version for given Tableau versions', function () {
        $testCases = [
            ['8.3', '2.0'],
            ['9.0', '2.0'],
            ['9.1', '2.0'],
            ['9.2', '2.1'],
            ['9.3', '2.2'],
            ['10.0', '2.3'],
            ['10.1', '2.4'],
            ['10.2', '2.5'],
            ['10.3', '2.6'],
            ['10.4', '2.7'],
            ['10.5', '2.8'],
            ['2018.1', '3.0'],
            ['2018.2', '3.1'],
            ['2018.3', '3.2'],
            ['2019.1', '3.3'],
            ['2019.2', '3.4'],
            ['2019.3', '3.5'],
            ['2019.4', '3.6'],
            ['2020.1', '3.7'],
            ['2020.2', '3.8'],
            ['2020.3', '3.9'],
            ['2020.4', '3.10'],
            ['2021.1', '3.11'],
            ['2021.2', '3.12'],
            ['2021.3', '3.13'],
            ['2021.4', '3.14'],
            ['2022.1', '3.15'],
            ['2022.2', '3.16'],
            ['2022.3', '3.17'],
            ['2022.4', '3.18'],
            ['2023.1', '3.19'],
            ['2023.2', '3.20'],
            ['2023.3', '3.21'],
            ['2024.1', '3.22'],
            ['2024.2', '3.23'],
            ['2024.3', '3.24'],
        ];

        foreach ($testCases as [$tableauVersion, $expectedVersion]) {
            Config::set('tableau.product_version', $tableauVersion);
            $apiVersion = VersionService::getAPIVersion();
            expect($apiVersion)->toBe($expectedVersion);
        }
    });

    it('throws an exception for an unknown Tableau version', function () {
        Config::set('tableau.product_version', 'unknown-version');
        VersionService::getAPIVersion();
    })->throws(Exception::class, 'Unknown Tableau version: unknown-version');
});
