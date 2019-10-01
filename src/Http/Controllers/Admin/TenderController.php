<?php namespace Avl\AdminZakup\Controllers\Admin;

use App\Http\Controllers\Avl\AvlController;
use App\Models\{
    Media, Langs, Rubrics, Sections
};

use App\Traits\MediaTrait;
use App\Traits\SectionsTrait;
use Avl\AdminZakup\Models\Tender;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use File;

class TenderController extends AvlController
{

    use MediaTrait;

    protected $langs = null;

    public function __construct(Request $request)
    {

        parent::__construct($request);

        $this->langs = Langs::get();
    }

    /**
     * Страница вывода списка тендеров к определенному разделу
     *
     * @param int     $id номер раздела
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index($id, Request $request)
    {
        // Запоминаем номер страницы на которой находимся
        $request->session()->put('page', $request->input('page') ?? 1);

        $section = Sections::whereId($id)->firstOrFail();

        $this->authorize('view', $section);

        return view('adminzakup::tender.index', [
            'id'      => $id,
            'section' => $section,
            'request' => $request,
            'langs'   => $this->langs,
            'tenders' => $this->getQuery($section->tender(), $request)->paginate(30)
        ]);
    }

    /**
     * Вывод формы на добавление тендера
     *
     * @param int $id Номер раздела
     * @return [type]     [description]
     */
    public function create($id)
    {
        $section = Sections::whereId($id)->firstOrFail();

        $this->authorize('create', $section);

        return view(
            'adminzakup::tender.create',
            [
                'langs'   => $this->langs,
                'section' => $section,
                'rubrics' => $section->rubrics()->orderBy('published_at', 'DESC')->get(),
                'id'      => $id
            ]);
    }

    /**
     * Метод для добавления новой записи в базу
     *
     * @param Request $request
     * @param int     $id номер раздела
     * @return redirect to index or create method
     */
    public function store(Request $request, $id)
    {
        $this->authorize('create', Sections::findOrFail($id));

        $post = $request->input();

        $this->validate(request(), [
            'button'                 => 'required|in:add,save,edit',
            'zakup_rubric_id'          => 'sometimes',
            'zakup_short_ru'           => '',
            'zakup_full_ru'            => '',
            'zakup_title_ru'           => 'max:255',
            'zakup_published_at'       => 'required|date_format:"Y-m-d"',
            'zakup_published_time'     => 'required|date_format:"H:i"',
            'zakup_until_date'         => 'date_format:"Y-m-d"',
            'zakup_until_time'         => 'date_format:"H:i"',
        ]);

        $record               = new Tender();
        $record->section_id   = $id;
        $record->created_user = Auth::user()->id;
        $record->published_at = $post['zakup_published_at'] . ' ' . $post['zakup_published_time'];

        foreach ($this->langs as $lang) {
            $record->{'title_' . $lang->key} = $post['zakup_title_' . $lang->key];
            $record->{'short_' . $lang->key} = $post['zakup_short_' . $lang->key];
            $record->{'full_' . $lang->key}  = $post['zakup_full_' . $lang->key];
        }

        if (isset($post['zakup_until'])) {
            $record->until_date = $post['zakup_until_date'] . ' ' . $post['zakup_until_time'];
        }

        if (isset($post['zakup_rubric_id']) && ($post['zakup_rubric_id'] > 0)) {
            $record->rubric_id = $post['zakup_rubric_id'];    // проставляему рубрику если ее выбрали
        }

        if (isset($post['organization']) && ($post['organization'] > 0)) {
            $record->organization = $post['organization'];
        }
        if (isset($post['sposob']) && ($post['sposob'] > 0)) {
            $record->sposob = $post['sposob'];
        }
        if (isset($post['status']) && ($post['status'] > 0)) {
            $record->status = $post['status'];
        }

        if ($record->save()) {
            switch ($post['button']) {
                case 'add': { return redirect()->route('adminzakup::sections.zakup.create', ['id' => $id])->with(['success' => ['Сохранение прошло успешно!']]); }
                case 'edit': { return redirect()->route('adminzakup::sections.zakup.edit', ['id' => $id, 'zakup_id' => $record->id])->with(['success' => ['Сохранение прошло успешно!']]); }
                default: { return redirect()->route('adminzakup::sections.zakup.index', ['id' => $id])->with(['success' => ['Сохранение прошло успешно!']]); }
            }
        }

        return redirect()->route('adminzakup::sections.zakup.create', ['id' => $id])->with(['errors' => ['Что-то пошло не так.']]);
    }

    /**
     * Отобразить запись на просмотр
     *
     * @param int $id Номер раздела
     * @param int $zakup_id Номер записи
     * @return \Illuminate\Http\Response
     */
    public function show($id, $zakup_id)
    {
        $this->authorize('view', Sections::findOrFail($id));

        return view(
            'adminzakup::tender.show',
            [
                'langs' => $this->langs,
                'tender'   => Tender::findOrFail($zakup_id),
                'id'    => $id
            ]);
    }

    public function contractors($id, $zakup_id)
    {
        $section = Sections::whereId($id)->firstOrFail();
        $tender = Tender::findOrFail($zakup_id);

        return view(
            'adminzakup::tender.contractors',
            [
                'langs' => $this->langs,
                'tender' => $tender,
                'contractors' => $tender->confirmed()->paginate(30),
                'id'    => $id
            ]);
    }

    /**
     * Форма открытия записи на редактирование
     *
     * @param int $id Номер раздела
     * @param int $zakup_id Номер записи
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $zakup_id)
    {
        $section = Sections::whereId($id)->firstOrFail();

        $this->authorize('update', $section);

        $tender = $section->tender()->findOrFail($zakup_id);

        return view(
            'adminzakup::tender.edit',
            [
                'tender'     => $tender,
                'id'      => $id,
                'section' => $section,
                // 'images'  => $tender->media('image')->orderBy('sind', 'DESC')->get(),
                'files'   => $tender->media('file')->orWhere('type', 'hideFile')->orderBy('sind', 'DESC')->get(),
                // 'hideImages'  => $tender->media('hideImage')->orderBy('sind', 'DESC')->get(),
                // 'hideFiles'   => $tender->media('hideFile')->orderBy('sind', 'DESC')->get(),
                'langs'   => $this->langs,
            ]);
    }

    /**
     * Метод для обновления определенной записи
     *
     * @param Request $request
     * @param int     $id Номер раздела
     * @param int     $zakup_id Номер записи
     * @return redirect to index method
     */
    public function update(Request $request, $id, $zakup_id)
    {
        $this->authorize('update', Sections::findOrFail($id));

        $post = $request->input();

        $this->validate(
            request(),
            [
                'button'             => 'required|in:add,save',
                'zakup_rubric_id'      => 'sometimes',
                'zakup_title_ru'       => 'max:255',
                'zakup_short_ru'       => '',
                'zakup_full_ru'        => '',
                'zakup_published_at'   => 'required|date_format:"Y-m-d"',
                'zakup_published_time' => 'required|date_format:"H:i"',
            ]);

        $tender = Tender::findOrFail($zakup_id);

        $tender->published_at = $post['zakup_published_at'] . ' ' . $post['zakup_published_time'];
        $tender->update_user  = Auth::user()->id;

        foreach ($this->langs as $lang) {
            $tender->{'title_' . $lang->key} = $post['zakup_title_' . $lang->key];
            $tender->{'short_' . $lang->key} = $post['zakup_short_' . $lang->key];
            $tender->{'full_' . $lang->key}  = $post['zakup_full_' . $lang->key];
        }

        if (isset($post['zakup_until'])) {
            $tender->until_date = $post['zakup_until_date'] . ' ' . $post['zakup_until_time'];
        } else {
            $tender->until_date = null;
        }

        if (isset($post['zakup_rubric_id']) && ($post['zakup_rubric_id'] > 0)) {
            $tender->rubric_id = $post['zakup_rubric_id'];
        } else {
            $tender->rubric_id = null;
        }

        if (isset($post['organization']) && ($post['organization'] > 0)) {
            $tender->organization = $post['organization'];
        } else {
            $tender->organization = null;
        }

        if (isset($post['sposob']) && ($post['sposob'] > 0)) {
            $tender->sposob = $post['sposob'];
        } else {
            $tender->sposob = null;
        }

        if (isset($post['status']) && ($post['status'] > 0)) {
            $tender->status = $post['status'];
        } else {
            $tender->status = null;
        }

        if ($tender->save()) {
            return redirect()->route(
                'adminzakup::sections.zakup.index',
                ['id' => $id, 'page' => $request->session()->get('page', '1')])
                ->with(['success' => ['Сохранение прошло успешно!']]);
        }

        return redirect()->back()->with(['errors' => ['Что-то пошло не так.']]);
    }

    /**
     * Форма для переноса документа в другой раздел
     *
     * @param int     $id Номер раздела
     * @param int     $zakup_id Номер записи
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function move($id, $zakup_id, Request $request)
    {
        $this->authorize('update', Sections::findOrFail($id));

        return view(
            'adminzakup::tender.move',
            [
                'tender' => Tender::findOrFail($zakup_id),
                'id'  => $id
            ]);
    }

    /**
     * Метод для обновления привязки записи к разделу
     *
     * @param int     $id Номер раздела
     * @param int     $zakup_id Номер записи
     * @param Request $request
     * @return redirect to index method
     */
    public function moveSave($id, $zakup_id, Request $request)
    {
        $this->authorize('update', Sections::findOrFail($id));

        $this->validate(
            $request,
            [
                'new_section' => 'required|exists:sections,id,type,zakup'
            ]);

        if ($request->input('new_section') > 0) {
            $data = Tender::findOrFail($zakup_id);

            $data->section_id = $request->input('new_section');

            if ($data->save()) {
                return redirect()->route('adminzakup::sections.zakup.index', ['id' => $id]);
            }
        }

        return redirect()->back()->with(['errors' => ['Выберите раздел']]);
    }

    /**
     * Удаление записи и всех медиа файлов
     *
     * @param int $id Номер раздела
     * @param int $zakup_id Номер записи
     * @return json
     */
    public function destroy($id, $zakup_id, Request $request)
    {
        $this->authorize('delete', Sections::findOrFail($id));

        $record = Tender::find($zakup_id);
        if (!is_null($record)) {

            /* Удаляем все изображения */
            if ($record->media('image')->count() > 0) {
                foreach ($record->media('image')->get() as $image) {
                    $this->deleteMedia($image->id, $request);
                }
            }

            if ($record->media('hideImage')->count() > 0) {
                foreach ($record->media('hideImage')->get() as $image) {
                    $this->deleteMedia($image->id, $request);
                }
            }

            /* Удаляем все файлы */
            if ($record->media('file')->count() > 0) {
                foreach ($record->media('file')->get() as $file) {
                    $this->deleteMedia($file->id, $request);
                }
            }

            if ($record->media('hideFile')->count() > 0) {
                foreach ($record->media('hideFile')->get() as $file) {
                    $this->deleteMedia($file->id, $request);
                }
            }

            if ($record->delete()) {
                return ['success' => ['Тендер удален']];
            }
        }

        return ['errors' => ['Ошибка удаления.']];
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
        if (!is_null($request->input('rubric'))) {
            if ($request->input('rubric') == 0) {
                $query = $query->whereNull('rubric_id');
            } else {
                $query = $query->where('rubric_id', $request->input('rubric'));
            }
        }

        return $query->orderBy('published_at', 'DESC');
    }
}
