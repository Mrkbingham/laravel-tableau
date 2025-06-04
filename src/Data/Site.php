<?php

namespace InterWorks\Tableau\Data;

class Site
{
    public function __construct(
        public readonly string $siteContentUrl = '',
        public readonly ?string $id = null,
    ){}
}
