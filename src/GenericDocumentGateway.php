<?php


namespace Combustion\Assets;


use Combustion\Assets\Contracts\AssetDocumentInterface;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;

/**
 * Class GenericDocumentGateway
 *
 * @package Combustion\Assets
 * @author  Luis A. Perez <lperez@combustiongroup.com>
 */
class GenericDocumentGateway extends DocumentsGateway
{
    /**
     * @var array
     */
    protected $config;
    /**
     * @var \Combustion\Assets\FileGateway
     */
    protected $fileGateway;
    /**
     * @var \Illuminate\Filesystem\FilesystemAdapter
     */
    protected $localDriver;
    /**
     * @var array
     */

    /**
     * Mime type will be empty since this gateway will upload any document
     */
    const   DOCUMENT_TYPE   = 'image',
            MIMES           =   [];



    public function __construct(array $config, FileGateway $fileGateway, FilesystemAdapter $localDriver,array $manipulators)
    {
        $this->fileGateway  = $fileGateway;
        $this->localDriver  = $localDriver;
        $this->config       = $this->validatesConfig($config);
        $this->manipulators = $manipulators;
    }


    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @param array                         $options
     *
     * @return \Combustion\Assets\Contracts\AssetDocumentInterface
     */
    public function create(UploadedFile $file, array $options = []) : AssetDocumentInterface
    {
        // get Document manipulators and pass the options
        $manipulator = $this->getManipulator($options);
        // manipulate document as needed
        $imageBag = $manipulator->manipulate($this->moveToLocalDisk($file),$options);
        foreach ($imageBag as $size => $imageData)
        {
            $image = new UploadedFile($imageData['folder'].'/'.$imageData['name'].'.'.$imageData['extension'],$imageData['name']);
            $imageBag[$size]['model'] = $this->fileGateway->createFile($image);
        }
        $imageModelData = [
            'title'      => $imageBag['original']['name'],
            'slug'       => time().$imageBag['original']['name'],
            'image_id'   => $imageBag['original']['model']->id,
            'large_id'   => $imageBag['large']['model']->id,
            'small_id'   => $imageBag['small']['model']->id,
            'medium_id'  => $imageBag['medium']['model']->id,
        ];
        return ImageModel::create($imageModelData);
    }
    /**
     * @param int $imageId
     *
     * @return \Combustion\Assets\Contracts\AssetDocumentInterface
     */
    public function getOrFail(int $imageId) :  AssetDocumentInterface
    {
        // TODO: Implement getOrFail() method.
    }

    /**
     * @return array
     */
    public function getConfig() :  array
    {
        return $this->config;
    }

    /**
     * @param array $config
     *
     * @return array
     * @throws \Combustion\Assets\Exceptions\ValidationFailed
     */
    public function validatesConfig(array $config) : array
    {
        $validationRules = [
            "mimes"     => "required|array",
            "mimes.*"   => "required|string",
            "manipulators" => "required|array",
            "default_manipulator" => "required|string",
        ];
        $validation = Validator::make($config,$validationRules);
        if($validation->fails())
        {
            throw new ValidationFailed("Validation for ImageGateway config array failed.");
        }
        return $config;
    }


}