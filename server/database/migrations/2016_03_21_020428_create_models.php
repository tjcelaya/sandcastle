<?php

use App\Model\Contact;
use App\Model\Issue;
use App\Model\Location;
use App\Model\Plan;
use App\Model\Resource;
use App\Model\ResourceClassification;
use App\Model\ResourceEngagement;
use App\Model\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModels extends Migration
{
    protected function relate(
        Blueprint $t,
        $otherTable,
        $foreignCol = 'id',
        $nullable = false,
        $foreignKey = true
    )
    {
        $localCol = $otherTable . '_id';
        $colSpec = $t->bigInteger($localCol)->unsigned();

        if ($nullable) {
            $colSpec->nullable();
        }

        if ($foreignKey) {
            $t->foreign($localCol)
                ->references($foreignCol)
                ->on($otherTable)
                ->onUpdate('cascade')
                ->onDelete('cascade');
        }
    }

    /**
     * Because sometimes a migration fails.
     */
    protected function forceCreate($table, $cb)
    {
        if (Schema::hasTable($table))
            Schema::drop($table);

        Schema::create($table, $cb);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo "models up " . PHP_EOL;

        $this->forceCreate('seeds', function (Blueprint $t) {
            $t->string('seed')->unique();
        });

        $this->forceCreate('password_resets', function (Blueprint $t) {
            $t->string('email')->index();
            $t->string('token')->index();
            $t->timestamps();
        });

        $this->forceCreate(Location::databaseTable(), function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->bigInteger('locatable_id');
            $t->string('locatable_type');
            $t->decimal('latitude', 12, 8);
            $t->decimal('longitude', 12, 8);
            $t->boolean('confirmed');
        });

        $this->forceCreate(Contact::databaseTable(), function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->text('fields');
            // will be automagically json_{en,de}coded if the model declares
            // protected $casts = [
            //     'fields' => 'array',
            // ];

            $t->text('image_url')->nullable();
            $t->bigInteger('contactable_id')->nullable();
            $t->string('contactable_type');

            $t->timestamps();
            $t->softDeletes();
        });

        $this->forceCreate(User::databaseTable(), function (Blueprint $t) {
            $t->bigIncrements('id');

            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');

            $t->rememberToken();
            $t->timestamps();
            $t->softDeletes();
        });

        $this->forceCreate(Issue::databaseTable(), function (Blueprint $t) {
            $t->bigIncrements('id');

            $t->string('name');
            $t->text('notes')->nullable();

            $this->relate($t, Contact::databaseTable(), 'id', true);

            $t->timestamps();
            $t->softDeletes();
        });

        $this->forceCreate(Plan::databaseTable(), function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('name');
            $t->string('description');
            $t->timestamps();
            $t->softDeletes();
        });

        $this->forceCreate(Resource::databaseTable(), function (Blueprint $t) {
            $t->bigIncrements('id');

            $t->string('name');

            $t->timestamps();
            $t->softDeletes();
        });

        $this->forceCreate(ResourceClassification::databaseTable(), function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('name');
            $t->string('description');
            $t->timestamps();
            $t->softDeletes();
        });

        $this->forceCreate(ResourceEngagement::databaseTable(), function (Blueprint $t) {
            $this->relate($t, Issue::databaseTable());
            $this->relate($t, Resource::databaseTable());
            $t->timestamp('elected_at');
            $t->timestamp('cancelled_at')->nullable();
            $t->timestamp('completed_at')->nullable();
        });

        foreach ([
                     [Issue::databaseTable(), Resource::databaseTable(), 'issue_resource',],
                     [Issue::databaseTable(), User::databaseTable(), 'issue_user',],
                     [User::databaseTable(), Contact::databaseTable(), 'user_contact',],
                     [Resource::databaseTable(), Contact::databaseTable(), 'resource_contact',],
                     [Plan::databaseTable(), ResourceClassification::databaseTable(), 'plan_resource_requirements',],
                 ] as $tables) {
            $joinTable = array_get($tables, 2, implode('_', $tables));
            $this->forceCreate($joinTable,
                function (Blueprint $t) use ($tables) {
                    $this->relate($t, $tables[0]);
                    $this->relate($t, $tables[1]);
                    // TODO: $t->boolean('current') ?
                });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        echo "models down " . PHP_EOL;
        foreach (array_reverse([
            'seeds',
            'password_resets',
            Location::databaseTable(),
            Contact::databaseTable(),
            User::databaseTable(),
            Issue::databaseTable(),
            Plan::databaseTable(),
            Resource::databaseTable(),
            ResourceClassification::databaseTable(),
            ResourceEngagement::databaseTable(),
            'issue_resource',
            'issue_user',
            'user_contact',
            'resource_contact',
            'plan_resource_requirements',
        ]) as $t) {
            Schema::dropIfExists($t);
        }
    }
}
