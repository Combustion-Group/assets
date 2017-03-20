<?php

namespace Combustion\Assets\Contracts;


use Combustion\Assets\Models\Asset;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Interface HasAssetsInterface
 *
 * @package Combustion\Assets\Contracts
 * @author Luis A. Perez <lperez@combustiongroup.com>
 */
interface HasAssetsInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function assets() : MorphToMany;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function primaryAsset() : MorphToMany;

    /**
     * @return bool
     */
    public function takeOutExistingPrimaryAsset() : bool;

    /**
     * @return bool
     */
    public function bringPrimaryAssetUrlToTopLevelOfModel() : bool;

    /**
     * @param \Combustion\Assets\Models\Asset $asset
     * @param bool                                                 $primary
     *
     * @return \Combustion\Assets\Contracts\HasAssetsInterface
     */
    public function attachAsset(Asset $asset, bool $primary = false) : HasAssetsInterface;
}