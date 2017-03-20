<?php
namespace Combustion\Assets\Models;

use Combustion\Assets\Contracts\AssetDocumentInterface;
use Combustion\Assets\Traits\IsDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Image
 *
 * @package Combustion\Assets\Models
 * @author Luis A. Perez <lperez@combustiongroup.com>
 */
class Image extends Model implements AssetDocumentInterface
{
    use IsDocument,SoftDeletes;
    /**
     * @var array
     */
    protected $fillable = [
        'image_id',
        'small_id',
        'medium_id',
        'large_id',
        'title',
    ];

    /**
     * @var array
     */
    protected $with = ['image_file'];
    /*
     * RELATIONSHIPS
     */

    /**
     * @return int
     */
    public function getId() : int
    {
        return (int)$this->id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function image_file()
    {
        return $this->hasOne(File::class,'id','image_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function small_file()
    {
        return $this->hasOne(File::class,'id','small_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function medium_file()
    {
        return $this->hasOne(File::class,'id','medium_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function large_file()
    {
        return $this->hasOne(File::class,'id','large_id');
    }

}
