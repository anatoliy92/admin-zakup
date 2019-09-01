<?php namespace Avl\AdminZakup\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;
use LaravelLocalization;
use App\Models\Media;

class Tender extends Model
{
    use ModelTrait;

    protected $table     = 'tender';

    protected $modelName = __CLASS__;

    protected $fillable  = ['title_ru'];

    protected $lang      = null;

    public function __construct()
    {
        $this->lang = LaravelLocalization::getCurrentLocale();
    }

    public function section()
    {
        return $this->belongsTo('App\Models\Sections', 'section_id', 'id');
    }

    public function rubric()
    {
        return $this->belongsTo('App\Models\Rubrics', 'rubric_id', 'id');
    }

    public function media($type = 'image')
    {
        return Media::whereModel('Avl\AdminZakup\Models\Tender')->where('model_id', $this->id)->where('type', $type);
    }

    public function files()
    {
        return Media::whereModel('Avl\AdminZakup\Models\Tender')->where('model_id', $this->id)->where('type', 'file');
    }

    public function hideMedia($type = 'hideImage')
    {
        return Media::whereModel('Avl\AdminZakup\Models\Tender')->where('model_id', $this->id)->where('type', $type);
    }

    public function hideFiles()
    {
        return Media::whereModel('Avl\AdminZakup\Models\Tender')->where('model_id', $this->id)->where('type', 'hideFile');
    }

    public function confirmed()
    {
        return $this->hasMany('Avl\AdminZakup\Models\ConfirmTender', 'tender_id', 'id');
    }

    public function getUpdatedAtAttribute($value)
    {
        return (!is_null($this->updated_date)) ? $this->updated_date : $value;
    }

    public function getGoodAttribute($value, $lang = null)
    {
        $good = (!is_null($lang)) ? $lang : $this->lang;

        return ($this->{'good_' . $good}) ? $this->{'good_' . $good} : $this->good_ru;
    }

    public function getTitleAttribute($value, $lang = null)
    {
        $title = (!is_null($lang)) ? $lang : $this->lang;

        return ($this->{'title_' . $title}) ? $this->{'title_' . $title} : null;
    }

    public function getShortAttribute($value, $lang = null)
    {
        $short = (!is_null($lang)) ? $lang : $this->lang;

        return ($this->{'short_' . $short}) ? $this->{'short_' . $short} : $this->short_ru;
    }

    public function getFullAttribute($value, $lang = null)
    {
        $full = (!is_null($lang)) ? $lang : $this->lang;

        return ($this->{'full_' . $full}) ? $this->{'full_' . $full} : $this->full_ru;
    }

    public function getUrlAttribute($value, $lang = null)
    {
        return '/' . $this->lang . '/' . $this->section->type . '/' . $this->section->alias . '/' . $this->id;
    }

    public function isOld()
    {
        return $this->until_date < Carbon::now();
    }

    public function isConfirmed($user) {
        if (empty($user)) {
            return false;
        }

        $confirmed = $this->confirmed->where('contract_id', $user->id)->where('confirm', 1)->all();

        return !empty($confirmed);
    }

    public function isAllowConfirm()
    {
        return Carbon::now()->add('-1', 'day')->greaterThan($this->until_date);
    }
}
