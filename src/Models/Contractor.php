<?php namespace Avl\AdminZakup\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;
use LaravelLocalization;
use App\Models\Media;

class Contractor extends Model
{
    use ModelTrait;

    protected $table     = 'contractor';

    protected $modelName = __CLASS__;

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

}
