@extends('avl.default')

@section('js')
    <script src="{{ asset('vendor/adminzakup/js/index.js') }}" charset="utf-8"></script>
@endsection

@section('main')
    <div class="card">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Поставщики
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
                            <th class="text-center">Дата регистрации</th>
                            <th class="text-center" style="width: 100px;">Действие</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($contractors as $contractor)
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
                                    <div class="btn-group" role="group">
                                        @if ($contractor->block == 1)
                                            <a href="{{ route('adminzakup::tender.contractor.unblock', ['id' => $contractor->id]) }}" class="btn btn-outline-secondary" title="Разблокировать"><i class="fa fa-unlock"></i></a>
                                        @else
                                            <a href="{{ route('adminzakup::tender.contractor.block', ['id' => $contractor->id]) }}" class="btn btn-outline-secondary" title="Заблокировать"><i class="fa fa-lock"></i></a>
                                        @endif
                                        <a href="#" class="btn btn btn-outline-danger remove--record" title="Удалить"><i class="fa fa-trash"></i></a>
                                    </div>

                                        <div class="remove-message">
                                            <span>Вы действительно желаете удалить запись?</span>
                                            <span class="remove--actions btn-group btn-group-sm">
															<button class="btn btn-outline-primary cancel"><i class="fa fa-times-circle"></i> Нет</button>
															<a href="{{ route('adminzakup::tender.contractor.remove', ['id' => $contractor->id]) }}" class="btn btn-outline-secondary" title="Удалить"><i class="fa fa-trash"></i> Да</a>
													</span>
                                        </div>

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
