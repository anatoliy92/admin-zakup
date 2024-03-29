@extends('avl.default')

@section('css')
	<link rel="stylesheet" href="/avl/js/jquery-ui/jquery-ui.min.css">
	<link rel="stylesheet" href="/avl/js/uploadifive/uploadifive.css">
	<link rel="stylesheet" href="/avl/js/jquery-ui/timepicker/jquery.ui.timepicker.css">
@endsection

@section('main')
		<div class="card">
			<div class="card-header">
				<i class="fa fa-align-justify"></i> Редактирование : {{ str_limit($tender->title_ru, 100) }}
				<div class="card-actions">
					<a href="{{ route('adminzakup::sections.zakup.index', [ 'id' => $id, 'page' => session('page', '1') ]) }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
					<button type="submit" form="submit" name="button" value="save" class="btn btn-success pl-3 pr-3" style="width: 70px;" title="Сохранить изменения"><i class="fa fa-floppy-o"></i></button>
				</div>
			</div>

			<div class="card-body">
				<form action="{{ route('adminzakup::sections.zakup.update', ['id' => $id, 'zakup' => $tender->id]) }}" method="post" id="submit">
					{!! csrf_field(); !!}
					{{ method_field('PUT') }}
					<input id="section_id" type="hidden" name="section_id" value="{{ $tender->section_id }}">
					<input id="model-name" type="hidden" value="Avl\AdminZakup\Models\Tender">
					<input id="model-id" type="hidden" name="zakup_id" value="{{ $tender->id }}">

					<div class="row">
						<div class="col-12 col-sm-3">
							<div class="form-group">
								{{ Form::label(null, 'Дата публикации') }}
								{{ Form::text('zakup_published_at', date('Y-m-d', strtotime($tender->published_at)), ['class' => 'form-control datepicker', 'id' => '']) }}
							</div>
						</div>
						<div class="col-12 col-sm-3">
							<div class="form-group">
								{{ Form::label(null, 'Время публикации') }}
								{{ Form::text('zakup_published_time', date('H:i', strtotime($tender->published_at)), ['class' => 'form-control timepicker']) }}
							</div>
						</div>
						<div class="col-12 col-sm-3">
							<div class="form-group">
								{{ Form::label(null, 'Дата завершения тендера') }}
								<div class="controls">
									<div class="input-prepend input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">{{ Form::checkbox('zakup_until', 'on', ($tender->until_date) ? true : false, ['class' => 'change--until-date']) }}</span>
										</div>
										{{ Form::text('zakup_until_date', $tender->until_date ? date('Y-m-d', strtotime($tender->until_date)) : null, ['class' => 'form-control datepicker until--date', 'disabled' => !$tender->until_date ? true : false]) }}
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-3">
							<div class="form-group">
								{{ Form::label(null, 'Время окончания публикации') }}
								{{ Form::text('zakup_until_time', $tender->until_date ? date('H:i', strtotime($tender->until_date)) : null, ['class' => 'form-control timepicker until--date', 'disabled' => !$tender->until_date ? true : false]) }}
							</div>
						</div>
						@php $organizations = getManualItems('zakup-organizations'); @endphp
						@if ($organizations)
							@php $organizations = toSelectTransform($organizations->toArray()); @endphp
							<div class="col-4">
								<label>Выберите организатора</label>
								<div class="form-group">
									{!! Form::select('organization', $organizations, $tender->organization ?? null, ['class' => 'form-control', 'placeholder' => 'Выберите организатора']) !!}
								</div>
							</div>
						@endif

						@php $methods = getManualItems('zakup-method'); @endphp
						@if (!is_null($methods))
							@php $methods = toSelectTransform($methods->toArray()); @endphp
							<div class="col-4">
								<div class="form-group">
									<label>Способ закупа</label>
									{!! Form::select('sposob', $methods, $tender->sposob ?? null , ['class' => 'form-control', 'placeholder' => 'Выберите способ закупа']) !!}
								</div>
							</div>
						@endif

						@php $statuses = getManualItems('zakup-statuses'); @endphp
						@if (!is_null($statuses))
							@php $statuses = toSelectTransform($statuses->toArray()); @endphp
							<div class="col-4">
								<div class="form-group">
									<label>Статус закупа</label>
									{!! Form::select('status', $statuses, $tender->status ?? null , ['class' => 'form-control', 'placeholder' => 'Выберите статус']) !!}
								</div>
							</div>
						@endif

					</div>

					<ul class="nav nav-tabs" role="tablist">
						@foreach($langs as $lang)
							<li class="nav-item">
								<a class="nav-link @if($lang->key == 'ru') active show @endif" href="#title_{{ $lang->key }}" data-toggle="tab">{{ $lang->name }}</a>
							</li>
						@endforeach
						@if (array_key_exists('file', $section->modules ?? []))
							<li class="nav-item"><a class="nav-link" href="#file" data-toggle="tab">Документы</a></li>
							{{-- <li class="nav-item"><a class="nav-link" href="#hideFile" data-toggle="tab">Скрытые файлы</a></li> --}}
						@endif
					</ul>
					<div class="tab-content">
						@foreach ($langs as $lang)
							<div class="tab-pane @if($lang->key == "ru") active show @endif"  id="title_{{$lang->key}}" role="tabpanel">
								<ul class="nav nav-tabs" role="tablist">
									<li class="nav-item"><a class="nav-link active show" href="#sub-tab_{{ $lang->key }}-index" data-toggle="tab">Основные</a></li>
									<li class="nav-item"><a class="nav-link" href="#sub-tab_{{ $lang->key }}-full" data-toggle="tab">Полный текст Закупа</a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active show"  id="sub-tab_{{ $lang->key }}-index" role="tabpanel">
										<div class="row">
											<div class="col-12">
												<div class="form-group">
													{{ Form::label(null, 'Заголовок') }}
													{{ Form::text('zakup_title_' . $lang->key, $tender->{'title_' . $lang->key} ?? null, ['class' => 'form-control']) }}
												</div>
											</div>
											<div class="col-12">
												{{ Form::textarea('zakup_short_' . $lang->key, $tender->{'short_' . $lang->key} ?? null, ['class' => 'tinymce']) }}
											</div>
										</div>
									</div>
									<div class="tab-pane"  id="sub-tab_{{ $lang->key }}-full" role="tabpanel">
										{{ Form::textarea('zakup_full_' . $lang->key, $tender->{'full_' . $lang->key} ?? null, ['class' => 'tinymce']) }}
									</div>
								</div>
							</div>
						@endforeach

						@if (array_key_exists('file', $section->modules ?? []))
							<div class="tab-pane" id="file" role="tabpanel">
								<div class="block--file-upload block--file-upload-zakup position-relative">
									<div class="form-group">
										<select class="form-control" id="select--language-file">
											@foreach($langs as $lang)
												<option value="{{ $lang->key }}">{{ $lang->key }}</option>
											@endforeach
										</select>
									</div>
									<input id="upload-files" name="upload" type="file" />
								</div>
								<div class="row files--zakup">
									<div class="col-md-12">
										<ul id="sortable-files" class="list-group">
											@foreach ($files as $file)
													<li class="col-md-12 list-group-item files--item" id="mediaSortable_{{ $file['id'] }}">
														<div class="img-thumbnail">
															<div class="input-group">
																<div class="input-group-prepend">
																	<span class="input-group-text"><a href="" class="change--lang" data-id="{{ $file['id'] }}"><img src="/avl/img/icons/flags/{{ $file['lang'] ?? 'null' }}--16.png"></a></span>
																	<span class="input-group-text"><a href="" class="change--type" data-id="{{ $file['id'] }}"><i class="fa {{ $file['type'] == 'file' ? 'fa-unlock' : 'fa-lock' }}"></i></a></span>
																	<span class="input-group-text"><a href="#" class="change--status" data-model="App\Models\Media" data-id="{{ $file['id'] }}"><i class="fa @if($file['good'] == 1){{ 'fa-eye' }}@else{{ 'fa-eye-slash' }}@endif"></i></a></span>
																	<span class="input-group-text"><a href="/file/download/{{ $file['id'] }}" target="_blank"><i class="fa fa-download"></i></a></span>
																	<span class="input-group-text"><a href="#" class="deleteMedia" data-id="{{ $file['id'] }}"><i class="fa fa-trash-o"></i></a></span>
																</div>
																<input type="text" id="title--{{ $file['id'] }}" class="form-control" value="{{ $file['title_' . $file['lang'] ] }}">
																{{ Form::text('', $file['published_at'] ? date('Y-m-d', strtotime($file['published_at'])) : null, ['class' => 'form-control datepicker', 'id' => 'file-published-at-' . $file['id']]) }}
																{{ Form::text('', $file['published_at'] ? date('H:i', strtotime($file['published_at'])) : null, ['class' => 'form-control timepicker', 'id' => 'file-published-time-at-' . $file['id']]) }}
																<div class="input-group-append">
																	<a href="#" class="input-group-text save--file" data-id="{{ $file['id'] }}"><i class="fa fa-floppy-o"></i></a>
																</div>
															</div>
														</div>
													</li>
											@endforeach
										</ul>
										<small class="d-block border-top pt-2 mt-2 text-muted">
											<i class="fa fa-lock"></i> - для пользователей которые подали заявку на участие <br/>
											<i class="fa fa-unlock"></i> - для всех пользователей
										</small>
									</div>
								</div>
							</div>

							{{-- <div class="tab-pane" id="hideFile" role="tabpanel">
								<div class="block--file-upload block--file-upload-zakup position-relative">
									<div class="form-group">
										<select class="form-control" id="select--language-hide-file">
											@foreach($langs as $lang)
												<option value="{{ $lang->key }}">{{ $lang->key }}</option>
											@endforeach
										</select>
									</div>
									<input id="upload-hide-files" name="upload" type="file" />
								</div>
								<div class="row files--zakup">
									<div class="col-md-12">
										<ul id="sortable-hide-files" class="list-group">
											@foreach ($hideFiles as $file)
													<li class="col-md-12 list-group-item files--item" id="mediaSortable_{{ $file['id'] }}">
														<div class="img-thumbnail">
															<div class="input-group">
																<div class="input-group-prepend">
																	<span class="input-group-text"><a href="" class="change--lang" data-id="{{ $file['id'] }}"><img src="/avl/img/icons/flags/{{ $file['lang'] ?? 'null' }}--16.png"></a></span>
																	<span class="input-group-text file-move" style="cursor: move;"><i class="fa fa-arrows"></i></span>
																	<span class="input-group-text"><a href="#" class="change--status" data-model="App\Models\Media" data-id="{{ $file['id'] }}"><i class="fa @if($file['good'] == 1){{ 'fa-eye' }}@else{{ 'fa-eye-slash' }}@endif"></i></a></span>
																	<span class="input-group-text"><a href="/file/download/{{ $file['id'] }}" target="_blank"><i class="fa fa-download"></i></a></span>
																	<span class="input-group-text"><a href="#" class="deleteMedia" data-id="{{ $file['id'] }}"><i class="fa fa-trash-o"></i></a></span>
																</div>
																<input type="text" id="title--{{ $file['id'] }}" class="form-control" value="{{ $file['title_' . $file['lang'] ] }}">
																<div class="input-group-append">
																	<a href="#" class="input-group-text save--file-name" data-id="{{ $file['id'] }}"><i class="fa fa-floppy-o"></i></a>
																</div>
															</div>
														</div>
													</li>
											@endforeach
										</ul>
									</div>
								</div>
							</div> --}}
						@endif
					</div>
				</form>
			</div>

			<div class="card-footer position-relative">
					<i class="fa fa-align-justify"></i> Редактирование : {{ str_limit($tender->title_ru, 100) }}
					<div class="card-actions">
						<a href="{{ route('adminzakup::sections.zakup.index', [ 'id' => $id, 'page' => session('page', 1) ]) }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
						<button type="submit" form="submit" name="button" value="save" class="btn btn-success pl-3 pr-3" style="width: 70px;" title="Сохранить изменения"><i class="fa fa-floppy-o"></i></button>
					</div>
			</div>
		</div>
@endsection

@section('js')
	<script src="/avl/js/jquery-ui/jquery-ui.min.js" charset="utf-8"></script>
	<script src="/avl/js/uploadifive/jquery.uploadifive.min.js" charset="utf-8"></script>

	<script src="/avl/js/tinymce/tinymce.min.js" charset="utf-8"></script>
	<script src="/avl/js/jquery-ui/timepicker/jquery.ui.timepicker.js" charset="utf-8"></script>
	<script src="{{ asset('vendor/adminzakup/js/edit.js') }}" charset="utf-8"></script>
@endsection
