<?php

namespace Combustion\Assets\Models;

use Combustion\Assets\Contracts\AssetDocumentInterface;
use Combustion\Assets\Models\Scopes\DocumentStructureScope;
use Combustion\Assets\Traits\IsDocument;
use Combustion\StandardLib\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;

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
    /*
     * RELATIONSHIPS
     */

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new DocumentStructureScope());
    }

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

    // Scopes to pull in data on the same level
    public function scopeWithFilesData(Builder $query)
    {
        $query->join('files as thumbnail_files_table',function(JoinClause $join){
            $join->on("thumbnail_files_table.id","microsoft_documents.thumbnail_id");
        });
        $query->join('files as document_files_table',function(JoinClause $join){
            $join->on("document_files_table.id","microsoft_documents.document_id");
        });
        // Thumbnail
        $this->appendToSelect("small_files_table.id as small_file_id");
        $this->appendToSelect("small_files_table.mime as small_file_mime");
        $this->appendToSelect("small_files_table.original_name as small_file_original_name");
        $this->appendToSelect("small_files_table.url as small_file_url");
        $this->appendToSelect("small_files_table.extension as small_file_extension");
        // Document
        $this->appendToSelect("medium_files_table.id as medium_file_id");
        $this->appendToSelect("medium_files_table.mime as medium_file_mime");
        $this->appendToSelect("medium_files_table.original_name as medium_file_original_name");
        $this->appendToSelect("medium_files_table.url as medium_file_url");
        $this->appendToSelect("medium_files_table.extension as medium_file_extension");
    }
}
