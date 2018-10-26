<?php

namespace App;

use App\Email\CustomPasswordResetEmail;
use App\Models\DungeonRoute;
use App\Models\GameServerRegion;
use App\Models\PatreonData;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;

/**
 * @property int $id
 * @property int $game_server_region_id
 * @property string $timezone
 * @property string $name
 * @property string $email
 * @property string $password
 * @property PatreonData $patreondata
 * @property GameServerRegion $gameserverregion
 */
class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;

    /**
     * @var string Have to specify connection explicitly so that Tracker still works (has its own DB)
     */
    protected $connection = 'mysql';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'email', 'password', 'remember_token', 'patreon_data_id', 'raw_patreon_response_data', 'created_at', 'updated_at'
    ];

    /**
     * @return string Make the binding for profile/{user} resolve a username rather than an ID. The IDs are private.
     */
    public function getRouteKeyName()
    {
        return 'name';
    }

    /**
     * Checks if this user has paid for a certain tier one way or the other.
     *
     * @param $name
     * @return bool
     */
    function hasPaidTier($name)
    {
        // True for all admins
        $result = $this->hasRole('admin');

        // If we weren't an admin, check patreon data
        if (!$result && $this->patreonData !== null) {
            foreach ($this->patreonData->paidtiers as $tier) {
                if ($tier->name === $name) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Checks if this user can create a dungeon route or not (based on free account limits)
     */
    function canCreateDungeonRoute()
    {
        return DungeonRoute::where('author_id', $this->id)->count() < config('keystoneguru.registered_user_dungeonroute_limit') ||
            $this->hasPaidTier('unlimited-dungeonroutes');
    }

    /**
     * Get the amount of routes a user may still create.
     *
     * NOTE: Will be inaccurate if the user is a Patron. Just don't call this function then.
     * @return mixed
     */
    function getRemainingRouteCount()
    {
        return max(0,
            config('keystoneguru.registered_user_dungeonroute_limit') - \App\Models\DungeonRoute::where('author_id', $this->id)->count()
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function dungeonroutes()
    {
        return $this->hasMany('App\Models\DungeonRoute', 'author_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function reports()
    {
        return $this->hasMany('App\Models\UserReports', 'author_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    function patreondata()
    {
        return $this->hasOne('App\Models\PatreonData');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function gameserverregion()
    {
        // Don't know why it won't work without the foreign key specified..
        return $this->belongsTo('App\Models\GameServerRegion', 'game_server_region_id');
    }

    /**
     * Sends the password reset notification.
     *
     * @param  string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomPasswordResetEmail($token));
    }

    public function getIsAdminAttribute()
    {
        return $this->hasRole('admin');
    }
}
