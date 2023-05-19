<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'create_charge',
        'cancel_charge',
        'get_current_charge_status',
        'check_billing',
        'save_setting',
        'gdpr_view_customer',
        'gdpr_delete_customer',
        'gdpr_delete_shop',
        'uninstall',
        'get_variants',
        'get_settings',
        'fb_login',
        'fb_posts',
    ];
}
