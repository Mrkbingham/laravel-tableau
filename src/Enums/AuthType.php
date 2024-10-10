<?php declare(strict_types=1);

namespace InterWorks\Tableau\Enums;

enum AuthType: string {
    case PAT = 'pat';
    case USERNAME = 'username';
}
