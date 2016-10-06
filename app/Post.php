<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = ['created_time','id','caption','message','attachments','user_id','name','description','picture','full_picture'];
    public $timestamps = false;


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function getAttachmentsAttribute()
    {
        return json_decode($this->attributes['attachments']);
    }

}
