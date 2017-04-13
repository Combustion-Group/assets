<?php


namespace Combustion\Assets\Models\Scopes;


use Combustion\Assets\Models\GenericDocument;
use Combustion\Assets\Models\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class DocumentStructureScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if($model instanceof Image)
        {
            // refer to model to see scopeWithFilesData(Builder $query)
            $builder->WithFilesData();
        }

        if($model instanceof GenericDocument)
        {
            // refer to model to see scopeWithFilesData(Builder $query)
            $builder->WithFilesData();
        }
    }
}