<?php namespace Avl\AdminZakup\Controllers\Admin;

use App\Http\Controllers\Avl\AvlController;
use App\Models\{Langs, Rubrics, Sections};

use App\Traits\MediaTrait;
use Avl\AdminZakup\Models\Contractor;
use Avl\AdminZakup\Models\Tender;
use Illuminate\Http\Request;
use Auth;
use File;

class ContractorController extends AvlController
{

    use MediaTrait;

    protected $langs = null;

    public function __construct(Request $request)
    {

        parent::__construct($request);

        $this->langs = Langs::get();
    }

    /**
     * Страница вывода списка поставщиков
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->session()->put('page', $request->input('page') ?? 1);

        return view(
            'adminzakup::contractor.index',
            [
                'request' => $request,
                'langs'   => $this->langs,
                'contractors' => Contractor::paginate(30),
            ]);
    }

    public function block($id, Request $request)
    {
        $contrator = Contractor::whereId($id)->firstOrFail();

        $contrator->block = 1;

        if ($contrator->save()) {
            return redirect()->back()->with(['success' => ['Пользователь заблокирован']]);
        }

        return redirect()->back()->with(['error' => ['Произошла ошибка']]);
    }

    public function remove($id, Request $request)
    {
        $contrator = Contractor::whereId($id)->firstOrFail();

        if ($contrator->delete()) {
            return redirect()->back()->with(['success' => ['Пользователь удален']]);
        }

        return redirect()->back()->with(['error' => ['Произошла ошибка']]);
    }

    public function unblock($id, Request $request)
    {
        $contrator = Contractor::whereId($id)->firstOrFail();

        $contrator->block = 0;

        if ($contrator->save()) {
            return redirect()->back()->with(['success' => ['Пользователь разблокирован']]);
        }

        return redirect()->back()->with(['error' => ['Произошла ошибка']]);
    }

    /**
     * Функция для формирования фильтра в списке записей
     *
     * @param query   $query Eloquent
     * @param request $request
     * @return query
     */
    private function getQuery($query, $request)
    {
       return $query;
    }
}
