<?php

/**
 * @author Jose Rodriguez <jrpcone@gmail.com>
 * @license MIT
 * @link https://github.com/jorodriguezpr/
 */

namespace Microrepairnet\ChatWidget\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AIProviderCredential extends Model
{
    protected $table = 'ai_provider_credentials';
    protected $fillable = ['provider', 'api_key', 'model', 'additional_config', 'is_active'];
    protected $hidden = ['api_key'];
    protected $casts = [
        'additional_config' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get API key (decrypt if needed)
     */
    public function getApiKeyAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Set API key (encrypt before saving)
     */
    public function setApiKeyAttribute($value)
    {
        try {
            $this->attributes['api_key'] = Crypt::encryptString($value);
        } catch (\Exception $e) {
            $this->attributes['api_key'] = $value;
        }
    }

    /**
     * Get active provider
     */
    public static function active()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Get provider by name
     */
    public static function forProvider($provider)
    {
        return self::where('provider', $provider)->first();
    }
}
