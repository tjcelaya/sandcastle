<?php

namespace App\Model;

class Plan extends Base
{
    protected $table = 'plan';

    public $timestamps = true;

    public $visible = [
        'name',
        'description',
        'requirements',
        'created_at',
        'updated_at',
    ];


    public function requirements()
    {
        return $this->belongsToMany(
            ResourceClassification::class,
            'plan_template_requirements',
            'plan_template_id',
            'classification_id');
    }
}
