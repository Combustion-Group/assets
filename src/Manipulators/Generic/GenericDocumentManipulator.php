<?php


namespace Combustion\Assets\Manipulators\Generic;


use Combustion\Assets\Contracts\Manipulator;
use Combustion\Assets\Exceptions\ValidationFailed;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;

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
        //TODO finish the document manipulator
        if(is_null($thumbnail['id']))
        {
            $thumbnail;
        }
        // create image bag and add original data
//        $imageBag = [
//            'original' => ['folder' => $path, 'name' => $name, 'extension' => $extension]
//        ];
//        $image = Image::make($path . '/' . $name . '.' . $extension);
//        if (is_array($dimensions)) {
//            $image->crop($dimensions['width'], $dimensions['height'], $dimensions['x'], $dimensions['y'])->save($path . '/' . $name . '.' . $extension);
//        }
//        foreach ($this->config['sizes'] as $size => $imageSize) {
//            // get name
//            $sizeName = md5(time() . $size . '-' . $file->getClientOriginalName());
//            // append size to the name
//            $imagePath = $path . '/' . $sizeName . '.' . $extension;
//            // make data for array
//            $imageData = [$size => ['folder' => $path, 'name' => $sizeName, 'extension' => $extension]];
//            // push data in
//            $imageBag = array_merge($imageBag, $imageData);
//            // manipulate image
//            $image->fit($imageSize['x'], $imageSize['y'], function (Constraint $constraint) {
//                $constraint->aspectRatio();
//                $constraint->upsize();
//                // save once done
//            })->orientate()->save($imagePath);
//        }
//        return $imageBag;
    }

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
            "thumbnails.*.url" => "required|numeric",
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