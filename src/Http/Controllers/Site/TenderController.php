<?php namespace Avl\AdminZakup\Controllers\Site;

use App\Http\Controllers\Site\Sections\SectionsController;
use App\Models\Langs;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use File;
use Api;
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
        if ((is_null($this->section->rubric) || $this->section->rubric == 0) || $this->section->alias == 'zakup') {

            $template = 'site.templates.zakup.short.' . $this->getTemplateFileName(
                    $this->section->current_template->file_short);

            $records = $this->getQuery($this->section->tender(), $request);

            $records = $records->orderBy('published_at', 'DESC')->paginate($this->section->current_template->records);

            $rubrics = $this->section->rubrics()->where('good_' . $this->lang, 1)->orderBy(
                'title_' . $this->lang,
                'ASC')->get();

            $template = (View::exists($template)) ? $template : 'site.templates.zakup.short.default';

            return view(
                $template,
                [
                    'records'    => $records,
                    'rubrics'    => toSelectTransform($rubrics->toArray()),
                    'pagination' => $records->appends($_GET)->links(),
                    'request'    => $request
                ]);
        }

        return redirect()->route('site.zakup.rubrics', ['alias' => $this->section->alias]);
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
            return redirect()->back()->with('success', true);
        }

        return redirect()->back()->with('error', true);
    }

    public function show($alias, $id)
    {
        $template = 'site.templates.zakup.full.' . $this->getTemplateFileName(
                $this->section->current_template->file_full);

        $data = $this->section->tender()->where('good_' . $this->lang, 1)
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
            'alias' => $alias,
        ];

        $user = Auth::user();

        if (!empty($user) && $user->isContractor()) {
            $viewData['hideFiles'] = $data->media('hideFile')->where('lang', $this->lang)->where('good', 1)->orderBy(
                'sind',
                'DESC')->get();
            $viewData['isContractor'] = true;
        }

        return view(
            $template,
            $viewData);
    }

    /**
     * View all rubrics if instance on
     *
     * @param string $alias alias off section
     * @return to view all rubrics
     */
    public function rubrics($alias, Request $request)
    {
        $records = $this->section->rubrics()->where('good_' . $this->lang, 1)->orderBy('published_at', 'DESC');

        $template = 'site.templates.zakup.category.' . $this->getTemplateFileName(
                $this->section->current_template->file_category);

        $records = $records->paginate($this->section->current_template->records);

        return view(
            $template,
            [
                'records'    => $records,
                'pagination' => $records->appends($_GET)->links(),
                'byPage'     => $this->section->current_template->records
            ]);
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

        $result = $result->where('good_' . $this->lang, 1);

        // фильтр если приходит
        if ($request->input('rubric') && $request->input('rubric') > 0) {
            $result = $result->where('rubric_id', $request->input('rubric'))->whereHas(
                'rubric',
                function ($query) {
                    $query->where('good_' . $this->lang, 1);
                });
        }

        if ($request->input('date')) {
            $result = $result->whereDate('published_at', $request->input('date'));
        }

        $result = $result->where('until_date', '>=', Carbon::now());

        $result = $result->with('rubric');
        $result = $result->where('published_at', '<=', Carbon::now());

        return $result;
    }
}