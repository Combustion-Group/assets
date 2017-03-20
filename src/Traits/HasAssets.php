<?php
namespace Combustion\Assets\Traits;


use Combustion\Assets\Contracts\HasAssetsInterface;
use Combustion\Assets\Models\Asset;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Class HasAssets
 *
 * @package Combustion\Assets\Traits
 * @author Luis A. Perez <lperez@combustiongroup.com>
 */
trait HasAssets
{
    /**
     * Get all of the assets for the post.
     */
    public function assets() : MorphToMany
    {
        return $this->morphToMany(Asset::class,'resource','resource_asset','resource_id','asset_id')->withPivot('primary','resource_type')->withTimestamps();
    }

    /**
     * @return mixed
     */
    public function primaryAsset() : MorphToMany
    {
        return $this->assets()->wherePivot('primary',1);
    }

    /**
     * Allows you to attach an asset to any model and if the asset
     * becomes primary it will change the flag for the previous
     * primary asset and set the new one as primary leaving
     * the previous one in the assets array. Also triggers
     * the event listener to bring asset url to the top
     * level of the model
     *
     * @param Asset $asset
     * @param bool $primary
     * @return HasAssetsInterface
     */
    public function attachAsset(Asset $asset,bool $primary = false) : HasAssetsInterface
    {
        // otherwise attach the asset to the resource
        if($primary)
        {
            // check if the current document can be adder as a primary asset
            $this->takeOutExistingPrimaryAsset();
            // take out existing primary asset if any and save new asset at primary
            $this->assets()->save($asset,['primary' => true]);
            // once save as primary we can trigger the listener
            $this->bringPrimaryAssetUrlToTopLevelOfModel();
        }
        else
        {
            $this->assets()->save($asset);
        }
        return $this;
    }

    /**
     *
     */
    public function takeOutExistingPrimaryAsset() : bool
    {
        // fetch the primary asset
        $primary_asset = $this->primaryAsset()->first();
        // if its found
        if($primary_asset)
        {
            // take out primary
            $primary_asset->pivot->primary = false;
            // save
            $primary_asset->pivot->save();
        }
        return true;
    }

    /**
     * Grab the url of the primary asset that is
     * buried under four layers of data and
     * brings it to the to p level of the
     * model
     */
    public function bringPrimaryAssetUrlToTopLevelOfModel() : bool
    {
        // if the model implementing hasAssetsInterface has primaryAssetsField
        if(isset($this->primaryAssetsField))
        {
            $fieldName=$this->primaryAssetsField;
            // put all urls on the top level
            $this->$fieldName = [
                'original' => $this->primaryAsset()->get()->first()->document->image_file->url,
                'small' => $this->primaryAsset()->get()->first()->document->small_file->url,
                'large' => $this->primaryAsset()->get()->first()->document->large_file->url,
                'medium' => $this->primaryAsset()->get()->first()->document->medium_file->url,
            ];
        }
        // if the object has primary assetField
        if(isset($this->primaryAssetField))
        {
            $fieldName=$this->primaryAssetField;
            // just place the original image
            $this->$fieldName = $this->primaryAsset()->get()->first()->document->image_file->url;
        }
        // save changes
        $this->save();
        return true;
        // this method can be overwritten inside of the model implementing hasAssetsInterface
    }
}