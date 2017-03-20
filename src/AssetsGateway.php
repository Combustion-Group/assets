<?php
namespace Combustion\Assets;

use Combustion\Assets\Contracts\HasAssetsInterface;
use Combustion\Assets\Exceptions\AssetDriverNotFound;
use Combustion\Assets\Exceptions\ValidationFailed;
use Combustion\Assets\Models\Asset;
use Illuminate\Http\UploadedFile;

/**
 * Class AssetsGateway
 *
 * @package Combustion\Assets
 * @author Luis A. Perez <lperez@combustiongroup.com>
 */
class AssetsGateway
{
    /**
     * @var array
     */
    protected $config;
    /**
     * @var DocumentGatewayInterface[]
     */
    protected $drivers;

    /**
     * AssetsGateway constructor.
     *
     * @param array $config
     * @param array $drivers
     */
    public function __construct(array $config, array $drivers)
    {
        $this->config  = $this->validatesConfig($config);
        $this->drivers = $this->validateDrivers($drivers);
    }

    /**
     * @param \Illuminate\Http\UploadedFile $file
     *
     * @return \Combustion\Assets\Models\Asset
     */
    public function createAsset(UploadedFile $file,array $options=[]) : Asset
    {
        // what type of asset is it
        $driver = $this->getDriver($file);
        // call create on gateway for whatever
        $document = $driver->create($file,$options);
        // get fresh asset
        $asset = $this->newAsset();
        // attach document to asset
        $document->asset()->save($asset);
        return $asset;
    }


    /**
     * @param \Combustion\Assets\Contracts\HasAssetsInterface $model
     * @param \Illuminate\Http\UploadedFile                                        $file
     * @param array                                                                $options
     *
     * @return \Combustion\Assets\Contracts\HasAssetsInterface
     */
    public function attachPrimaryAssetTo(HasAssetsInterface $model, UploadedFile $file, array $options=[]) : HasAssetsInterface
    {
        $options['model']=$model;
        $asset = $this->createAsset($file,$options);
        $model->attachAsset($asset,true);
        return $model;
    }

    /**
     * @param array $attributes
     *
     * @return \Combustion\Assets\Models\Asset
     */
    private function newAsset(array $attributes = []) : Asset
    {
        return Asset::create($attributes);
    }

    /**
     * @param \Illuminate\Http\UploadedFile $file
     *
     * @return \Combustion\Assets\Contracts\DocumentGatewayInterface
     * @throws \Combustion\Assets\Exceptions\AssetDriverNotFound
     */
    private function getDriver(UploadedFile $file) : DocumentsGateway
    {
        $mimeType = $file->getMimeType();
        foreach ($this->drivers as $driver)
        {
            if(in_array($mimeType,$driver->getConfig()['mimes']))return $driver;
        }
        throw new AssetDriverNotFound("Driver for mime type $mimeType was not found.");
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public function validatesConfig(array $config) : array
    {
        // no need to validate for now
        return $config;
    }


    /**
     * @param array $drivers
     *
     * @return array
     * @throws \Combustion\Assets\Exceptions\ValidationFailed
     */
    public function validateDrivers(array $drivers) : array
    {
        foreach ($drivers as $driverName => $driver)
        {
            if(!$driver instanceof DocumentsGateway)
            {
                throw new ValidationFailed("Driver $driverName needs to extend the DocumentsGateway");
            }
        }
        return $drivers;
    }
}