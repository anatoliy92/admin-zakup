@extends('avl.default')

@section('js')
	<script src="{{ asset('vendor/adminzakup/js/index.js') }}" charset="utf-8"></script>
@endsection

@section('main')
	<div class="card">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> {{ $section->name_ru }}
			@can('create', $section)
				<div class="card-actions">
					<a href="{{ route('adminzakup::sections.zakup.create', ['id' => $id]) }}" class="w-100 pl-4 pr-4 bg-primary text-white" title="Добавить"><i class="fa fa-plus"></i></a>
				</div>
			@endcan
		</div>
		<div class="card-body">
			@if ($tenders)
				<div class="table-responsive">
					@php $iteration = 30 * ($tenders->currentPage() - 1); @endphp
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="50" class="text-center">#</th>
								<th class="text-center" width="20"></th>
								<th class="text-center">Наименование тэндера</th>
								<th class="text-center" width="50">Подано заявок</th>
								<th class="text-center" style="width: 160px">Дата публикации</th>
								<th class="text-center" style="width: 100px;">Действие</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($tenders as $tender)
								<tr class="position-relative" id="tender--item-{{ $tender->id }}">
									<td class="text-center">{{ ++$iteration }}</td>
									<td class="text-center">
										<a class="change--status" href="#" data-id="{{ $tender->id }}" data-model="Avl\AdminZakup\Models\Tender">
											<i class="fa @if ($tender->good){{ 'fa-eye' }}@else{{ 'fa-eye-slash' }}@endif"></i>
										</a>
									</td>
									<td><b>{{ $tender->title_ru }}</b><br/><span class="text-secondary">{{ str_limit(strip_tags($tender->short_ru), 300) }}</span></td>
									<td class="text-center">
										<a href="{{ route('adminzakup::sections.zakup.contractors', ['id' => $id, 'zakup_id' => $tender->id]) }}">{{ $tender->confirmed->count() }}</a>
									</td>
									<td class="text-center">
										{{ date('Y-m-d H:i', strtotime($tender->published_at)) }}
									</td>
									<td class="text-right">
										<div class="btn-group" role="group">
											@can('update', $section) <a href="{{ route('adminzakup::sections.zakup.edit', ['id' => $id, 'zakup_id' => $tender->id]) }}" class="btn btn btn-outline-success" title="Изменить"><i class="fa fa-edit"></i></a> @endcan
											@can('delete', $section) <a href="#" class="btn btn btn-outline-danger remove--record" title="Удалить"><i class="fa fa-trash"></i></a> @endcan
										</div>
										@can('delete', $section)
											<div class="remove-message">
												<span>Вы действительно желаете удалить запись?</span>
												<span class="remove--actions btn-group btn-group-sm">
														<button class="btn btn-outline-primary cancel"><i class="fa fa-times-circle"></i> Нет</button>
														<button class="btn btn-outline-danger remove--zakup" data-id="{{ $tender->id }}" data-section="{{ $id }}"><i class="fa fa-trash"></i> Да</button>
												</span>
											</div>
										 @endcan
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>

					<div class="d-flex justify-content-end">
						{{ $tenders->appends($_GET)->links('vendor.pagination.bootstrap-4') }}
					</div>
				</div>
			@endif
		</div>
	</div>
@endsection
