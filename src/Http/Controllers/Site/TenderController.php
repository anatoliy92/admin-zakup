<?php namespace Avl\AdminZakup\Controllers\Site;

use App\Http\Controllers\Site\Sections\SectionsController;
use App\Models\Langs;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use File;
use Api;
use View;
use Illuminate\Http\Request;

class TenderController extends SectionsController
{
    protected $langs = null;

    public function __construct(Request $request)
    {

        parent::__construct($request);

        $this->langs = Langs::get();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $template = 'site.templates.zakup.short.' . $this->getTemplateFileName($this->section->current_template->file_short);

        $records = $this->getQuery($this->section->tender(), $request);

        $records = $records->orderBy('published_at', 'DESC')->paginate($this->section->current_template->records);

        $template = (View::exists($template)) ? $template : 'site.templates.zakup.short.default';

        return view($template, [
            'records'    => $records,
            'pagination' => $records->appends($_GET)->links(),
            'request'    => $request
        ]);
    }

    public function confirm($alias, $id, Request $request)
    {
        $user = Auth::user();
        $data = $this->section->tender()->where('until_date', '>=', Carbon::now())->findOrFail($id);

        if (empty($user)) {
            return redirect()->back();
        }

        $response = Api::request('POST', 'api/tender/' . $data->id . '/confirm', [
            "contract_id" => $user->id,
        ]);


        if (isset($response->success) && $response->success == 'ok') {
            return redirect()->back()->with('success', ['Вы стали участником тендера']);
        }

        return redirect()->back()->with('error', true);
    }

    public function show($alias, $id)
    {
        $template = 'site.templates.zakup.full.' . $this->getTemplateFileName(
                $this->section->current_template->file_full);

        $data = $this->section->tender()->where('good', 1)
            ->where('until_date', '>=', Carbon::now())->findOrFail($id);

        $data->timestamps = false;  // отключаем обновление даты

        $viewData = [
            'data'     => $data,
            'images'   => $data->media()->where('good', 1)->orderBy('main', 'DESC')->orderBy('sind', 'DESC')->get(),
            'files'    => $data->media('file')->where('lang', $this->lang)->where('good', 1)->orderBy(
                'sind',
                'DESC')->get(),
            'print'    => true,
            'isContractor' => false,
            'isBlock' => false,
            'alias' => $alias,
        ];

        $user = Auth::user();

        if (!empty($user) && $user->isContractor()) {
            $viewData['hideFiles'] = $data->hideFiles()->where('lang', $this->lang)->where('good', 1)->orderBy(
                'sind',
                'DESC')->get();
            $viewData['isContractor'] = true;
            $viewData['isBlock'] = $user->contractor->block == 1;
        }

        return view($template, $viewData);
    }


    public function rubricsShow($alias, $rubric = null, Request $request)
    {
        $template = 'site.templates.zakup.short.' . $this->getTemplateFileName(
                $this->section->current_template->file_short);

        $records = $this->getQuery($this->section->tender(), $request);

        $records = $records->where('rubric_id', $rubric)->orderBy('published_at', 'DESC')->paginate(
            $this->section->current_template->records);

        return view(
            $template,
            [
                'records'    => $records,
                'rubrics'    => $this->section->rubrics()->orderBy('published_at', 'desc')->get(),
                'rubricOne'  => $this->section->rubrics()->find($rubric),
                'pagination' => $records->appends($_GET)->links(),
                'request'    => $request
            ]);
    }


    public function getQuery($result, $request)
    {
        $result = $result->where('good', 1);

        $result = $result->where('until_date', '>=', Carbon::now());

        return $result->where('published_at', '<=', Carbon::now());
    }
}
