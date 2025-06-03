<?php

namespace InterWorks\Tableau\Data\Auth;

class PATAuthentication
{
    public function __construct(
        public readonly string $personalAccessTokenName,
        public readonly string $personalAccessTokenSecret,
        public readonly string $siteContentUrl = '',
        public readonly string $impersonateId = '',
    ){}
}
