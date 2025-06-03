<?php

namespace InterWorks\Tableau\Data\Auth;

class UsernameAuthentication
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
        public readonly string $siteContentUrl = '',
        public readonly string $impersonateId = '',
    ){}
}
