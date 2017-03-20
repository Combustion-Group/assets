<?php

namespace Combustion\Assets\Contracts;


use Combustion\Assets\Models\Asset;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface AssetDocumentInterface
 *
 * @package Combustion\Assets\Contracts
 * @author Luis A. Perez <lperez@combustiongroup.com>
 */
interface AssetDocumentInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function asset() : MorphMany;

    /**
     * @return int
     */
    public function getId() : int;

    /**
     * @param \Combustion\Assets\Models\Asset $asset
     *
     * @return \Combustion\Assets\Models\Asset
     */
    public function attachToAsset(Asset $asset) : Asset;
}