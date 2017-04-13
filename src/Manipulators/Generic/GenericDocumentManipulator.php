<?php


namespace Combustion\Assets\Manipulators\Generic;


use Combustion\Assets\Contracts\Manipulator;
use Combustion\Assets\Exceptions\ValidationFailed;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

/**
 * Class GenericDocumentManipulator
 *
 * @package Combustion\Assets\Manipulators\Generic
 * @author  Luis A. Perez <lperez@combustiongroup.com>
 */
class GenericDocumentManipulator implements Manipulator
{
    /**
     * @var array
     */
    protected $config;
    /**
     *
     */
    const MANIPULATOR_NAME = 'GenericDocuments';


    /**
     * ImageProfileManipulator constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $this->validatesConfig($config);
    }


    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @param array                         $options
     *
     * @return array
     */
    public function manipulate(UploadedFile $file, array $options = []): array
    {
        // get mime and thumbnail
        $mime = $file->getMimeType();
        $thumbnail = $this->getThumbnailFor($mime);
        // check if thumbnail is in files table already if not imported in
        $filesBag = [];
        if(is_null($thumbnail['id'])) // upload thumbnail for this asset
        {
            $localImage = storage_path('app/documents').'/'.md5('temp'.time()).'.png';
            // move file from url to local
            file_put_contents($localImage, file_get_contents($thumbnail['url']));
            // use UploadedFile object to get file data
            $thumbnailFile = new UploadedFile($localImage,'temp');
            // add thumbnail to $filesBag
            $filesBag['thumbnail']=
                ['file' => $thumbnailFile, 'id'   => null];
        }
        else
        {
            $filesBag['thumbnail']=['file'=>null,"id"=>$thumbnail['id']];
        }
        // add to filesBag
        $filesBag['document']=['file'=>$file,"id"=>null];
        return $filesBag;
    }

    /**
     * @param $mimeType
     *
     * @return array
     */
    private function getThumbnailFor($mimeType) : array
    {
        foreach ($this->config['mimes'] as $documentType => $mimes)
        {
            if(in_array($mimeType,$mimes))
            {
                return $this->config['thumbnails'][$documentType];
            }
        }
    }



    /**
     * @param array $config
     *
     * @return array
     * @throws \Combustion\Assets\Exceptions\ValidationFailed
     */
    public function validatesConfig(array $config): array
    {
        $validationRules = [
            "thumbnails" => "required|array",
            "thumbnails.*" => "required|array",
            "thumbnails.*.id" => "required|numeric|nullable",
            "thumbnails.*.url" => "required|string",
        ];
        $messages = [
            "thumbnails"=>"Thumbnails need to be configured",
            "thumbnails.*.url"=>"The url to an image is required",
        ];
        $validation = Validator::make($config, $validationRules,$messages);
        if ($validation->fails()) {
            throw new ValidationFailed("Validation for GenericDocumentManipulator config array failed.");
        }
        return $config;
    }
}