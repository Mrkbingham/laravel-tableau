<?php

namespace InterWorks\Tableau\Data\Auth;

class JWTAuthentication
{
    public function __construct(
        public readonly string $jwt,
        public readonly string $siteContentUrl = '',
    ){}
}
