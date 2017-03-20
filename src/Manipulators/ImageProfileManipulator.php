<?php
namespace Combustion\Assets\Manipulators;

use Combustion\Assets\Contracts\Manipulator;
use Combustion\Assets\Exceptions\InvalidAspectRatio;
use Combustion\Assets\Exceptions\ValidationFailed;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;


/**
 * Class ImageProfileManipulator
 *
 * @package Combustion\StandardLib\src\Services\Assets\Manipulators
 * @author  Luis A. Perez <lperez@combustiongroup.com>
 */
class ImageProfileManipulator implements Manipulator
{
    /**
     * @var array
     */
    protected $config;
    /**
     *
     */
    const MANUPULATOR_NAME = 'ImageProfiles';

    /**
     * ImageGateway constructor.
     *
     * @param array                                               $config
     * @param \Combustion\Assets\FileGateway $fileGateway
     * @param \Illuminate\Filesystem\FilesystemAdapter            $localDriver
     */
    public function __construct(array $config)
    {
        $this->config  = $this->validatesConfig($config);
    }


    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @param array                         $options
     *
     * @return array
     */
    public function manipulate(UploadedFile $file, array $options=[]) : array
    {
        $dimensions = $this->checkForDimessions($options);
        // get name
        $name = $file->getClientOriginalName();
        $path = $file->getPath();
        $extension = $file->getExtension();
        // create image bag and add original data
        $imageBag = [
            'original' => ['folder' => $path,'name' => $name,'extension' => $extension]
        ];
        $image = Image::make($path.'/'.$name.'.'.$extension);
        if(is_array($dimensions))
        {
            $image->crop($dimensions['width'],$dimensions['height'],$dimensions['x'],$dimensions['y'])->save($path.'/'.$name.'.'.$extension);
        }
        foreach ($this->config['sizes'] as $size => $imageSize)
        {
            // get name
            $sizeName = md5(time().$size.'-'.$file->getClientOriginalName());
            // append size to the name
            $imagePath = $path.'/'.$sizeName.'.'.$extension;
            // make data for array
            $imageData = [$size => ['folder' => $path,'name' => $sizeName,'extension' => $extension]];
            // push data in
            $imageBag = array_merge($imageBag,$imageData);
            // manipulate image
            $image->fit($imageSize['x'],$imageSize['y'], function (Constraint $constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
                // save once done
            })->orientate()->save($imagePath);
        }
        return $imageBag;
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
            "sizes"     => "required|array",
            "sizes.*"   => "required|array",
            "sizes.*.y"   => "required|numeric|nullable",
            "sizes.*.x"   => "required|numeric|nullable",
        ];
        $validation = Validator::make($config,$validationRules);
        if($validation->fails())
        {
            throw new ValidationFailed("Validation for ImageGateway config array failed.");
        }
        return $config;
    }

    /**
     * @return string
     */
    public function getManipulator()
    {
        return ImageProfileManipulator::MANUPULATOR_NAME;
    }

    /**
     * @param array $options
     *
     * @return array
     * @throws \Combustion\Assets\Exceptions\ImageDimensionsAreInvalid
     */
    private function checkForDimessions(array $options)
    {
        $data=[
            'width'=>isset($options['width'])?$options['width']:0,
            'height'=>isset($options['height'])?$options['height']:0,
            'x'=>isset($options['x'])?$options['x']:0,
            'y'=>isset($options['y'])?$options['y']:0,
        ];
        foreach ($data as $coordinates=>$value) if($value===0) return false;
        if((int)$data['width']!=(int)$data['height']) throw new InvalidAspectRatio("Height adn Width given are not 4:4 aspect ratio");
        return $data;
    }

}