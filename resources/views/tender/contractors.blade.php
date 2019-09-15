@extends('avl.default')

@section('css')
	<link rel="stylesheet" href="/avl/js/jquery-ui/jquery-ui.min.css">
	<link rel="stylesheet" href="/avl/js/uploadifive/uploadifive.css">
	<link rel="stylesheet" href="/avl/js/jquery-ui/timepicker/jquery.ui.timepicker.css">
@endsection

@section('main')
		<div class="card">
			<div class="card-header">
				<i class="fa fa-align-justify"></i> Участники тендера : {{ str_limit($tender->title_ru, 100) }}
			</div>

			<div class="card-body">
				@if ($contractors)
					<div class="table-responsive">
						@php $iteration = 30 * ($contractors->currentPage() - 1); @endphp
						<table class="table table-bordered">
							<thead>
							<tr>
								<th width="50" class="text-center">#</th>
								<th class="text-center">Имя поставщика</th>
								<th class="text-center">ФИО контактного лица</th>
								<th class="text-center">Телефон</th>
								<th class="text-center">БИН/ИИН</th>
								<th class="text-center">Дата подачи заявки</th>
								<th class="text-center" style="width: 100px;">Действие</th>
							</tr>
							</thead>
							<tbody>
							@foreach ($contractors as $confirmed)
								@php $contractor = $confirmed->contractor @endphp
								<tr class="position-relative" id="contractor--item-{{ $contractor->id }}">
									<td class="text-center">{{ ++$iteration }}</td>
									<td class="text-center">{{ $contractor->name }}</td>
									<td class="text-center">{{ $contractor->contact_name }}</td>
									<td class="text-center">{{ $contractor->phone }}</td>
									<td class="text-center">{{ $contractor->bin }}</td>
									<td class="text-center">
										{{ date('Y-m-d H:i', strtotime($contractor->created_at)) }}
									</td>
									<td class="text-right">
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>

						<div class="d-flex justify-content-end">
							{{ $contractors->appends($_GET)->links('vendor.pagination.bootstrap-4') }}
						</div>
					</div>
				@endif
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
