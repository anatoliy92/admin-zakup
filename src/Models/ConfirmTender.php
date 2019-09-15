<?php namespace Avl\AdminZakup\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;
use LaravelLocalization;
use App\Models\Media;

class ConfirmTender extends Model
{
    use ModelTrait;

    protected $table     = 'confirm_tender';

    protected $modelName = __CLASS__;

    public function contractor()
    {
        return $this->belongsTo('Avl\AdminZakup\Models\Contractor', 'contract_id', 'id');
    }
}
