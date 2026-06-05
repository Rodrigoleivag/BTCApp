<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $table = 'users';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pin_hash',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'must_change_pin' => 'boolean',
        'pin_failed_attempts' => 'integer',
        'pin_locked_at' => 'datetime',
    ];

    /**
     * Check if the user's PIN is locked.
     *
     * @return bool
     */
    public function isPinLocked()
    {
        if (!$this->pin_locked_at) {
            return false;
        }

        // Desbloquear después de 15 minutos
        $lockDuration = 15 * 60; // 15 minutos en segundos
        $lockedAt = strtotime($this->pin_locked_at);
        $now = time();

        return ($now - $lockedAt) < $lockDuration;
    }

    /**
     * Reset PIN failed attempts and unlock if locked.
     *
     * @return void
     */
    public function resetPinAttempts()
    {
        $this->pin_failed_attempts = 0;
        $this->pin_locked_at = null;
        $this->save();
    }

    /**
     * Record a failed PIN attempt.
     *
     * @return bool Returns true if account is now locked
     */
    public function recordFailedPinAttempt()
    {
        $this->pin_failed_attempts = ($this->pin_failed_attempts ?? 0) + 1;
        
        // Bloquear después de 5 intentos fallidos
        if ($this->pin_failed_attempts >= 5) {
            $this->pin_locked_at = now();
            $this->save();
            return true;
        }
        
        $this->save();
        return false;
    }

    public function deposit()
    {
        return $this->hasMany('App\Model\Deposit', 'user_id');
    }

    


}
