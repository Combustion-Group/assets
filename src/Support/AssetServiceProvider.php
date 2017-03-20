<?php
namespace Combustion\Assets\Support;

use Combustion\Assets\AssetsGateway;
use Combustion\Assets\FileGateway;
use Combustion\Assets\ImageGateway;
use Combustion\Assets\Manipulators\ImageProfileManipulator;
use Combustion\Assets\Manipulators\BannerImageManipulator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;

/**
 * Class AssetServiceProvider
 *
 * @package Combustion\Assets\Support
 * @author Luis A. Perez <lperez@combustiongroup.com>
 */
class AssetServiceProvider extends ServiceProvider
{
    /**
     * Create the User Gateway as a singleton
     */
    public function register()
    {
        $this->app->singleton(AssetsGateway::class, function (Application $app, array $params = []) {
            $config = $app['config']['core.packages'][AssetsGateway::class];
            // build drivers array
            $drivers = array();
            foreach ($config['drivers'] as $driverName  => $driverInfo) {
                $drivers[$driverName] = $app->make($driverInfo['class']);
            }
            return new AssetsGateway(
                $config,
                $drivers
            );
        });
        $this->app->singleton(ImageGateway::class, function (Application $app, array $params = []) {
            $config = $app['config']['core.packages'][AssetsGateway::class]['drivers'][ImageGateway::DOCUMENT_TYPE]['config'];
            // build drivers array
            $manipulators = array();
            foreach ($config['manipulators'] as $manipulatorName  => $driverInfo) {
                $manipulators[$manipulatorName] = $app->make($driverInfo['class']);
            }
            return new ImageGateway(
                $config,
                $app->make(FileGateway::class),
                Storage::disk($app['config']['core.packages'][FileGateway::class]['local_driver']),
                $manipulators
            );
        });
        $this->app->singleton(FileGateway::class, function (Application $app, array $params = []) {
            $config = $app['config']['core.packages'][FileGateway::class];
            return new FileGateway(
                $config,
                Storage::disk($config['local_driver']),
                Storage::disk($config['cloud_driver'])
            );
        });
        $this->app->singleton(ImageProfileManipulator::class, function (Application $app, array $params = []) {
            $config = $app['config']['core.packages'][AssetsGateway::class]['drivers'][ImageGateway::DOCUMENT_TYPE]['config']['manipulators'][ImageProfileManipulator::MANUPULATOR_NAME];
            return new ImageProfileManipulator(
                $config
            );
        });
        $this->app->singleton(BannerImageManipulator::class, function (Application $app, array $params = []) {
            $config = $app['config']['core.packages'][AssetsGateway::class]['drivers'][ImageGateway::DOCUMENT_TYPE]['config']['manipulators'][BannerImageManipulator::MANUPULATOR_NAME];
            return new BannerImageManipulator(
                $config
            );
        });
    }
}