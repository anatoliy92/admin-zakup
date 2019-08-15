<?php namespace Avl\AdminZakup\Controllers\Admin\Ajax;

use App\Http\Controllers\Avl\AvlController;
use App\Models\{Sections, Media, Langs};
use Avl\AdminZakup\Models\Tender;
use Illuminate\Support\Facades\Storage;
use Avl\AdminNpa\Models\Npa;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Carbon\Carbon;
use Image;
use Hash;

class MediaController extends AvlController
{

    /**
     * Загрузка изображений
     *
     * @param Request $request
     * @return JSON
     */
    public function zakupImages(Request $request)
    {
        return $this->images($request);
    }

    /**
     * Загрузка изображений
     *
     * @param Request $request
     * @return JSON
     */
    public function zakupHideImages(Request $request)
    {
        return $this->images($request, "hideImage");
    }

    /**
     * Загрузка файлов
     *
     * @param Request $request
     * @return JSON
     */
    public function zakupFiles(Request $request)
    {
        return $this->files($request);
    }

    /**
     * Загрузка файлов
     *
     * @param Request $request
     * @return JSON
     */
    public function zakupHideFiles(Request $request)
    {
        return $this->files($request, 'hideFile');
    }

    protected function files(Request $request, $type = "file")
    {
        $tender = Tender::where('section_id', $request->input('section_id'))->find($request->input('tender_id'));

        if ($tender) {
            $sind = $tender->media('file')->orderBy('sind', 'DESC')->first();
            $item = ($sind) ? ++$sind->sind : 1;

            $media = new Media();

            $media->model                                = 'Avl\AdminZakup\Models\Tender';
            $media->model_id                             = $tender->id;
            $media->good                                 = 1;
            $media->type                                 = $type;
            $media->sind                                 = $item;
            $media->lang                                 = $request->input('lang');
            $media->{'title_' . $request->input('lang')} = $request->Filedata->getClientOriginalName();
            $media->published_at                         = Carbon::now();

            if ($media->save()) {
                $path = $request->Filedata->store(config('adminzakup.path_to_file'));

                if ($path) {
                    $media->url = $path;

                    if ($media->save()) {
                        return [
                            'success' => true,
                            'file'    => $media->toArray()
                        ];
                    }

                    $media->delete();
                }
            }
        }

        return ['errors' => ['Ошибка загрузки, обратитесь к администратору.']];
    }
    
    protected function images(Request $request, $type = "image")
    {
        if ($request->Filedata->getSize() < config('adminzakup.max_file_size')) {

            if (in_array(strtolower($request->Filedata->extension()), config('adminzakup.valid_image_types'))) {

                $tender = Tender::where('section_id', $request->input('section_id'))->find($request->input('tender_id'));

                if ($tender) {
                    $sind = $tender->media()->orderBy('sind', 'DESC')->first();
                    $item = ($sind) ? ++$sind->sind : 1;

                    $picture               = new Media;
                    $picture->model        = 'Avl\AdminZakup\Models\Tender';
                    $picture->model_id     = $tender->id;
                    $picture->type         = $type;
                    $picture->sind         = $item;
                    $picture->title_ru     = $request->Filedata->getClientOriginalName();
                    $picture->published_at = Carbon::now();

                    if ($picture) {

                        /* Загружаем файл и получаем путь */
                        $path = $request->Filedata->store(config('adminzakup.path_to_image'));

                        $img = Image::make(Storage::get($path));
                        $img->resize(
                            1000,
                            1000,
                            function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            })->stream();

                        Storage::put($path, $img);

                        $picture->url = $path;

                        if ($picture->save()) {
                            return [
                                'success' => true,
                                'file'    => Media::find($picture->id)->toArray(),
                                'storage' => env('STORAGE_URL')
                            ];
                        }

                        $picture->delete();
                    }
                }

                return ['errors' => ['Ошибка загрузки, обратитесь к администратору.']];
            }

            return ['errors' => ['Ошибка загрузки, формат изображения не допустим для загрузки.']];
        }

        return ['errors' => ['Размер фотографии не более <b>12-х</b> мегабайт.']];
    }

}
