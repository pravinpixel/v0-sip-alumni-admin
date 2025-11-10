<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $fillable = [
        'email', 'token', 'expires_on', 'send_on', 'retry_count', 'last_try'
    ];

    protected $casts = [
        'retry_count' => 'integer',
    ];

    public function canSend()
    {
        if (!$this->send_on) {
            return true;
        }

        $lastTry = strtotime($this->send_on);

        if (($lastTry + (1.5 * 60)) > time()) {
            return false;
        }

        return true;
    }

    public function generateCode()
    {

        $this->token = rand(1000, 9999);
        $this->send_on = date("Y-m-d H:i:s", time());
        $this->expires_on = date("Y-m-d H:i:s", time() + (10 * 60));
        $this->last_try = null;
        $this->retry_count = 0;
    }

    public function validateOtp($code)
    {
        if ($this->isExpired()) {
            throw new \Exception("Code Expired");
        }

        if ($this->retry_count > 5) {
            $lastTry = strtotime($this->last_try);

            if (($lastTry + (15 * 60)) > time()) {
                throw new \Exception("Maximum retry count reached. Please try again after some time.");
            }

            $this->retry_count = -1;
        }

        if ($this->token != $code) {
            $this->last_try = date("Y-m-d H:i:s", time());
            $this->retry_count++;

            throw new \Exception("Invalid Code.");
        }

        return true;
    }

    public function isExpired()
    {
        $expiresOn = strtotime($this->expires_on);

        if ($expiresOn > time()) {
            return false;
        }

        return true;
    }
}




