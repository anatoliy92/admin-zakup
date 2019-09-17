@extends('avl.default')

@section('js')
    <script src="{{ asset('vendor/adminzakup/js/index.js') }}" charset="utf-8"></script>
@endsection

@section('main')
    <div class="card">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Поставщик {{ $contractor->name }}
        </div>
        <div class="card-body">
            <div>
                <b>Имя поставщика: </b> {{ $contractor->name }}
            </div>
            <div>
                <b>ФИО контактного лица: </b> {{ $contractor->contact_name }}
            </div>
            <div>
                <b>Номер телефона: </b> {{ $contractor->phone }}
            </div>
            <div>
                <b>БИН/ИИН: </b> {{ $contractor->bin }}
            </div>
            <div>
                <b>Дата регистрации: </b> {{ date('Y-m-d H:i', strtotime($contractor->created_at)) }}
            </div>
        </div>
    </div>
@endsection
