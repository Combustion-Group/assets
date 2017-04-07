<?php

namespace Combustion\Assets\Models;

use Combustion\Assets\Contracts\AssetDocumentInterface;
use Combustion\Assets\Traits\IsDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GenericDocument extends Model implements AssetDocumentInterface
{
    use IsDocument,SoftDeletes;

    protected $table = 'microsoft_documents';
    /**
     * @var array
     */
    protected $fillable = [
        'thumbnail_id',
        'document_id',
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
    public function thumbnail_file()
    {
        return $this->hasOne(File::class,'id','thumbnail_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function document_file()
    {
        return $this->hasOne(File::class,'id','document_id');
    }
}
