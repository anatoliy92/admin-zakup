@extends('avl.default')

@section('js')
	<script src="/avl/js/jquery-ui/jquery-ui.min.js" charset="utf-8"></script>
	<script src="/avl/js/uploadifive/jquery.uploadifive.min.js" charset="utf-8"></script>

	<script src="{{ asset('vendor/adminzakup/js/edit.js') }}" charset="utf-8"></script>
	<script src="/avl/js/tinymce/tinymce.min.js" charset="utf-8"></script>

	<script src="/avl/js/jquery-ui/timepicker/jquery.ui.timepicker.js" charset="utf-8"></script>
@endsection

@section('css')
	<link rel="stylesheet" href="/avl/js/jquery-ui/jquery-ui.min.css">
	<link rel="stylesheet" href="/avl/js/uploadifive/uploadifive.css">
	<link rel="stylesheet" href="/avl/js/jquery-ui/timepicker/jquery.ui.timepicker.css">
@endsection

@section('main')
	<div class="card">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Добавление закупки
			<div class="card-actions">
				<a href="{{ route('adminzakup::sections.zakup.index', [ 'id' => $id, 'page' => session('page', '1') ]) }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
				<button type="submit" form="submit" name="button" value="add" class="btn btn-primary pl-3 pr-3" style="width: 70px;" title="Сохранить и добавить новую"><i class="fa fa-plus"></i></button>
				<button type="submit" form="submit" name="button" value="save" class="btn btn-success pl-3 pr-3" style="width: 70px;" title="Сохранить и перейти к списку"><i class="fa fa-floppy-o"></i></button>
				<button type="submit" form="submit" name="button" value="edit" class="btn btn-warning pl-3 pr-3" style="width: 70px;" title="Сохранить и изменить"><i class="fa fa-floppy-o"></i></button>
			</div>
		</div>

		<div class="card-body">
			<form action="{{ route('adminzakup::sections.zakup.store', ['id' => $id]) }}" method="post" id="submit">
				{!! csrf_field(); !!}
				<div class="row">
					<div class="col-12 col-sm-3">
						<div class="form-group">
							{{ Form::label(null, 'Дата публикации') }}
							{{ Form::text('zakup_published_at', date('Y-m-d'), ['class' => 'form-control datepicker', 'id' => '']) }}
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="form-group">
							{{ Form::label(null, 'Время публикации') }}
							{{ Form::text('zakup_published_time', date('H:i'), ['class' => 'form-control timepicker']) }}
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="form-group">
							{{ Form::label(null, 'Дата завершения тендера') }}
							<div class="controls">
								<div class="input-prepend input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">{{ Form::checkbox('zakup_until', 'on', false, ['class' => 'change--until-date']) }}</span>
									</div>
									{{ Form::text('zakup_until_date', null, ['class' => 'form-control datepicker until--date', 'disabled' => true ]) }}
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-sm-3">
						<div class="form-group">
							{{ Form::label(null, 'Время окончания публикации') }}
							{{ Form::text('zakup_until_time', null, ['class' => 'form-control timepicker until--date', 'disabled' => true]) }}
						</div>
					</div>
					@php $organizations = getManualItems('zakup-organizations'); @endphp
					@if ($organizations)
						@php $organizations = toSelectTransform($organizations->toArray()); @endphp
						<div class="col-4">
							<label>Выберите организатора</label>
							<div class="form-group">
								{!! Form::select('organization', $organizations, null , ['class' => 'form-control', 'placeholder' => 'Выберите организатора']) !!}
							</div>
						</div>
					@endif

					@php $methods = getManualItems('zakup-method'); @endphp
					@if (!is_null($methods))
						@php $methods = toSelectTransform($methods->toArray()); @endphp
						<div class="col-4">
							<div class="form-group">
								<label>Способ закупа</label>
								{!! Form::select('sposob', $methods, null , ['class' => 'form-control', 'placeholder' => 'Выберите способ закупа']) !!}
							</div>
						</div>
					@endif

					@php $statuses = getManualItems('zakup-statuses'); @endphp
					@if (!is_null($statuses))
						@php $statuses = toSelectTransform($statuses->toArray()); @endphp
						<div class="col-4">
							<div class="form-group">
								<label>Статус закупа</label>
								{!! Form::select('status', $statuses, null , ['class' => 'form-control', 'placeholder' => 'Выберите статус']) !!}
							</div>
						</div>
					@endif
				</div>

				<ul class="nav nav-tabs" role="tablist">
					@foreach($langs as $lang)
						<li class="nav-item">
							<a class="nav-link @if($lang->key == 'ru') active show @endif" href="#title_{{ $lang->key }}" data-toggle="tab">
								{{ $lang->name }}
							</a>
						</li>
					@endforeach
				</ul>
				<div class="tab-content">
					@foreach ($langs as $lang)
						<div class="tab-pane @if($lang->key == "ru") active show @endif"  id="title_{{$lang->key}}" role="tabpanel">
							<ul class="nav nav-tabs" role="tablist">
								<li class="nav-item"><a class="nav-link active show" href="#sub-tab_{{ $lang->key }}-index" data-toggle="tab">Основные</a></li>
								<li class="nav-item"><a class="nav-link" href="#sub-tab_{{ $lang->key }}-full" data-toggle="tab">Полный текст Закупки</a></li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane active show"  id="sub-tab_{{ $lang->key }}-index" role="tabpanel">
									<div class="row">
										<div class="col-12">
											<div class="form-group">
												{{ Form::label(null, 'Заголовок') }}
												{{ Form::text('zakup_title_' . $lang->key, null, ['class' => 'form-control']) }}
											</div>
										</div>
										<div class="col-12">
											{{ Form::textarea('zakup_short_' . $lang->key, null, ['class' => 'tinymce']) }}
										</div>
									</div>
								</div>
								<div class="tab-pane"  id="sub-tab_{{ $lang->key }}-full" role="tabpanel">
									{{ Form::textarea('zakup_full_' . $lang->key, null, ['class' => 'tinymce']) }}
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</form>
		</div>

		<div class="card-footer position-relative">
			<i class="fa fa-align-justify"></i> Добавление закупки
			<div class="card-actions">
				<a href="{{ route('adminzakup::sections.zakup.index', [ 'id' => $id, 'page' => session('page', '1') ]) }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
				<button type="submit" form="submit" name="button" value="add" class="btn btn-primary pl-3 pr-3" style="width: 70px;" title="Сохранить и добавить новую"><i class="fa fa-plus"></i></button>
				<button type="submit" form="submit" name="button" value="save" class="btn btn-success pl-3 pr-3" style="width: 70px;" title="Сохранить и перейти к списку"><i class="fa fa-floppy-o"></i></button>
				<button type="submit" form="submit" name="button" value="edit" class="btn btn-warning pl-3 pr-3" style="width: 70px;" title="Сохранить и изменить"><i class="fa fa-floppy-o"></i></button>
			</div>
		</div>
	</div>
@endsection
