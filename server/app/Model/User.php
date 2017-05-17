<?php

namespace App\Model;

<<<<<<< Updated upstream
use App\Model\Contact;
use App\Model\Issue;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as IlluminateAuthUser;
use Illuminate\Notifications\Notifiable;
=======
use App\Model\AuthenticatableBase;
use Illuminate\Database\Eloquent\SoftDeletes;
>>>>>>> Stashed changes
use Zizaco\Entrust\Traits\EntrustUserTrait;

/**
 * Class User
 * @package App\Model
 *
 * @property string id
 * @property string email
 *
 * @method create(array $attrs) static
 * @method static find($id) static
 */
class User extends IlluminateAuthUser
{
    use Notifiable;
    use AnswersTableStatically;
    use SoftDeletes {
        restore as private plainRestore;
    }
    use EntrustUserTrait {
        restore as private cacheCleaningParentCall;
    }

    public static $descriptions = [];
    protected $table = 'user';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

<<<<<<< Updated upstream
    public function getAvailableRelations()
    {
        return [
            'issues' => [
                'class' => Issue::class,
                'cardinality' => 'many',
                'autoload' => false,
            ],
            'contacts' => [
                'class' => Contact::class,
                'cardinality' => 'many',
                'autoload' => false,
            ],
        ];
    }

    public function issues()
    {
        return $this->belongsToMany(Issue::class, 'issue_user', 'user_id', 'issue_id');
    }

    public function contact()
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function restore()
    {
        $this->cacheCleaningParentCall();

        $this->plainRestore();
=======
    protected static $sudo = false;

    public function hasSudo() {
        return $this->sudo;
    }

    public static $availableRelations = [
        // 'thing' => [
        //     'class' => Thing::class,
        //     'cardinality' => 'many',
        //     'autoload' => false,
        // ],
    ];

    use SoftDeletes, EntrustUserTrait {
        SoftDeletes::restore as untrash;
        EntrustUserTrait::restore as euRestore;
    }

    public function restore() {
        $this->untrash();
        $this->euRestore();
>>>>>>> Stashed changes
    }
}
