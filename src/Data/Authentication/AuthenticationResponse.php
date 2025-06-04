<?php

namespace InterWorks\Tableau\Data\Authentication;

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
