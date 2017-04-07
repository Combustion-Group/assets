<?php


namespace Combustion\Assets\Manipulators;


use Combustion\Assets\Contracts\Manipulator;
use Combustion\Assets\Exceptions\InvalidAspectRatio;
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
    const MANIPULATOR_NAME = 'ImageProfiles';


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
        $dimensions = $this->checkForDimensions($options);
        // get name
        $name = $file->getClientOriginalName();
        $path = $file->getPath();
        $extension = $file->getExtension();
        // create image bag and add original data
        $imageBag = [
            'original' => ['folder' => $path, 'name' => $name, 'extension' => $extension]
        ];
        $image = Image::make($path . '/' . $name . '.' . $extension);
        if (is_array($dimensions)) {
            $image->crop($dimensions['width'], $dimensions['height'], $dimensions['x'], $dimensions['y'])->save($path . '/' . $name . '.' . $extension);
        }
        foreach ($this->config['sizes'] as $size => $imageSize) {
            // get name
            $sizeName = md5(time() . $size . '-' . $file->getClientOriginalName());
            // append size to the name
            $imagePath = $path . '/' . $sizeName . '.' . $extension;
            // make data for array
            $imageData = [$size => ['folder' => $path, 'name' => $sizeName, 'extension' => $extension]];
            // push data in
            $imageBag = array_merge($imageBag, $imageData);
            // manipulate image
            $image->fit($imageSize['x'], $imageSize['y'], function (Constraint $constraint) {
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
    public function validatesConfig(array $config): array
    {
        $validationRules = [
            "sizes" => "required|array",
            "sizes.*" => "required|array",
            "sizes.*.y" => "required|numeric|nullable",
            "sizes.*.x" => "required|numeric|nullable",
        ];
        $validation = Validator::make($config, $validationRules);
        if ($validation->fails()) {
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

}