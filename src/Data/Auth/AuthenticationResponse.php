<?php

namespace InterWorks\Tableau\Data\Auth;

class AuthenticationResponse
{
    public function __construct(
        public readonly string $token,
        public readonly string $siteId,
        public readonly string $siteContentUrl,
        public readonly string $userId,
        public readonly ?string $timeToExpiration = null,
    ){}
}
