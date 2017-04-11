<?php

namespace Combustion\Assets\Models;

use Combustion\Assets\Contracts\AssetDocumentInterface;
use Combustion\Assets\Traits\IsDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GenericDocument extends Model implements AssetDocumentInterface
{
    use IsDocument,SoftDeletes;

    const   WORD        = "word",
            EXCEL       = "excel",
            GENERIC     = "generic",
            WORD_MIMES  =   [
                                "application/msword",
                                "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                                "application/vnd.openxmlformats-officedocument.wordprocessingml.template",
                                "application/vnd.ms-word.document.macroEnabled.12",
                                "application/vnd.ms-word.template.macroEnabled.12",
                            ],
            EXCEL_MIMES =   [
                                "application/vnd.ms-excel",
                                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                                "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
                                "application/vnd.ms-excel.sheet.macroEnabled.12",
                                "application/vnd.ms-excel.template.macroEnabled.12",
                                "application/vnd.ms-excel.addin.macroEnabled.12",
                                "application/vnd.ms-excel.sheet.binary.macroEnabled.12",
                            ];
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
