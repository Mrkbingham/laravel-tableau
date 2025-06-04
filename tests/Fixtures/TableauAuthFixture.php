<?php

namespace InterWorks\Tableau\Tests\Fixtures;

use Saloon\Http\Faking\Fixture;

use function Pest\Faker\fake;

class TableauAuthFixture extends Fixture
{
    protected function defineName(): string
    {
        return 'tableau';
    }

    protected function defineSensitiveHeaders(): array
    {
        return [
            'Authorization' => 'REDACTED',
        ];
    }

    protected function defineSensitiveJsonParameters(): array
    {
        return [
            'token' => 'REDACTED',
            'id' => fake()->uuid,
        ];
    }

    protected function defineSensitiveRegexPatterns(): array
    {
        return [
            '/@[a-z0-9_]{0,100}/' => 'REDACTED-TWITTER-HANDLE',
        ];
    }
}
