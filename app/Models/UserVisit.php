<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVisit extends Model
{
    protected $table = 'user_visits';

    protected $fillable = [
        'ip_address',
        'user_agent',
        'referer',
        'full_url',
        'device_type',
        'browser',
        'browser_version',
        'platform',
        'platform_version',
        'country_code',
        'country_name',
        'fbclid',
        'gclid',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
