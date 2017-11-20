<?php

namespace Combustion\Assets\Models;


use Combustion\StandardLib\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class File
 *
 * @package Combustion\Assets\Models
 * @author Luis A. Perez <lperez@combustiongroup.com>
 */
class File extends Model
{
    use SoftDeletes;
    /**
     * @var string
     */
    protected $table = 'files';

    /**
     * @var array
     */
    protected $fillable = [
        'mime',
        'size',
        'original_name',
        'extension',
        'url'
    ];

    /**
     * @return array
     */
    public function validationRules(): array
    {
        return [
            'mime' => 'required|string',
            'size' => 'required|numeric',
            'original_name' => 'required|string',
            'extension' => 'required|string',
            'url' => 'required|string',
        ];
    }
}
