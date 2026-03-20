<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'type',
        'mode',
        'test_key',
        'test_secret',
        'live_key',
        'live_secret',
        'config',
        'active',
    ];

    /**
     * Auto-encrypt/decrypt credential columns.
     */
    protected $casts = [
        'test_key'    => 'encrypted',
        'test_secret' => 'encrypted',
        'live_key'    => 'encrypted',
        'live_secret' => 'encrypted',
        'config'      => 'encrypted',
        'active'      => 'boolean',
    ];

    /**
     * Get the active credentials based on the current mode.
     */
    public function getActiveCredentials(): array
    {
        $isLive = $this->mode === 'live';

        return [
            'key'    => $isLive ? $this->live_key : $this->test_key,
            'secret' => $isLive ? $this->live_secret : $this->test_secret,
            'mode'   => $this->mode,
        ];
    }

    /**
     * Get the decoded config JSON data.
     */
    public function getConfigData(): array
    {
        if (!$this->config) return [];
        $decoded = json_decode($this->config, true);
        return is_array($decoded) ? $decoded : [];
    }
}
