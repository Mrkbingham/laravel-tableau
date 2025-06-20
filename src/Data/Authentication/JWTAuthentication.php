<?php

namespace InterWorks\Tableau\Data\Authentication;

class JWTAuthentication
{
    public function __construct(
        public readonly string $jwt,
        public readonly string $siteContentUrl = '',
    ){}
}
