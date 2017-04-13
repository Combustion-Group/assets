<?php


namespace Combustion\Assets\Models\Scopes;


use Combustion\Assets\Models\GenericDocument;
use Combustion\Assets\Models\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Class DocumentStructureScope
 *
 * @package Combustion\Assets\Models\Scopes
 * @author  Luis A. Perez <lperez@combustiongroup.com>
 */
class DocumentStructureScope implements Scope
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $model
     */
    public function apply(Builder $builder, Model $model)
    {
        if($model instanceof Image)
        {
            // refer to model to see scopeWithFilesData(Builder $query)
            $builder
                ->appendToSelect('images.*')
                ->WithFilesData()
                ->PullSelectInQuery();
        }

        if($model instanceof GenericDocument)
        {
            // refer to model to see scopeWithFilesData(Builder $query)
            $builder
                ->appendToSelect('microsoft_documents.*')
                ->WithFilesData()
                ->PullSelectInQuery();
        }
    }
}