@extends('base')

@section('css')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
@endsection

@section('main')

    <div class="container">
        <div class="my-4 text-center">
            <h2>{{ trans('registrations::translate.registrations.form-name') }}</h2>
        </div>

        @if(session('errorRegistration'))
            <div class="row">
                <div class="col-md-8 m-auto">
                    <div class="alert alert-danger" role="alert">
                        {{ trans('registrations::translate.registrations.error') }}
                    </div>
                </div>
            </div>
        @endif

        @if(session('successRegistration'))
            <div class="row">
                <div class="col-md-8 m-auto">
                    <div class="alert alert-success" role="alert">
                        {{ trans('registrations::translate.registrations.success') }}
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8 m-auto">

                {{ Form::open(['url' => route('site.zakup.registration.registration'), 'class' => 'needs-validation', 'novalidate' ]) }}
            @if(empty(\Illuminate\Support\Facades\Auth::user()))
                <div class="row">

                    <div class="col-md-4 mb-3">
                        {{ Form::label('surname', trans('registrations::translate.surname') . ' *') }}
                        {{ Form::text('surname', null, ['id' => 'surname', 'class' => 'form-control'. ($errors->has('surname') ? ' is-invalid' : '')]) }}
                        <div class="invalid-feedback">{{ $errors->first('surname') ?? 'Error filling the field' }}</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        {{ Form::label('name', trans('registrations::translate.name') . ' *') }}
                        {{ Form::text('name', null, ['id' => 'name', 'class' => 'form-control'. ($errors->has('name') ? ' is-invalid' : '')]) }}
                        <div class="invalid-feedback">{{ $errors->first('name') ?? 'Error filling the field' }}</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        {{ Form::label('patronymic', trans('registrations::translate.patronymic')) }}
                        {{ Form::text('patronymic', null, ['id' => 'patronymic', 'class' => 'form-control'. ($errors->has('patronymic') ? ' is-invalid' : '')]) }}
                        <div class="invalid-feedback">{{ $errors->first('patronymic') ?? 'Error filling the field' }}</div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        {{ Form::label('dob', trans('registrations::translate.dob')) }}
                        {{ Form::text('dob', null, ['id' => 'dob', 'class' => 'form-control'. ($errors->has('dob') ? ' is-invalid' : ''), 'placeholder' => '01.05.1990']) }}
                        <div class="invalid-feedback">{{ $errors->first('dob') ?? 'Error filling the field' }}</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        {{ Form::label('mobile', trans('registrations::translate.mobile') . ' *') }}
                        {{ Form::text('mobile', null, ['id' => 'mobile', 'class' => 'form-control'. ($errors->has('mobile') ? ' is-invalid' : ''), 'placeholder' => '+7 (777) 123-45-67']) }}
                        <div class="invalid-feedback">{{ $errors->first('mobile') ?? 'Error filling the field' }}</div>
                    </div>
                </div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        {{ Form::label('login', trans('registrations::translate.login') . ' *') }}
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">@</span></div>
                            {{ Form::text('login', null, ['id' => 'login', 'class' => 'form-control'. ($errors->has('login') ? ' is-invalid' : ''), 'placeholder' => 'Логин']) }}
                            <div class="invalid-feedback">{{ $errors->first('login') ?? 'Error filling the field' }}</div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        {{ Form::label('email', trans('registrations::translate.email') . ' *') }}
                        {{ Form::text('email', null, ['class' => 'form-control'. ($errors->has('email') ? ' is-invalid' : ''), 'id' => 'email', 'placeholder' => 'you@example.com']) }}
                        <div class="invalid-feedback">{{ $errors->first('email') ?? 'Error filling the field' }}</div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        {{ Form::label('password', trans('registrations::translate.password') . ' *') }}
                        {{ Form::password('password', ['id' => 'password', 'class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : '')]) }}
                        <div class="invalid-feedback">{{ $errors->first('password') ?? 'Error filling the field' }}</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        {{ Form::label('confirm', trans('registrations::translate.confirm') . ' *') }}
                        {{ Form::password('confirm', ['id' => 'confirm', 'class' => 'form-control' . ($errors->has('confirm') ? ' is-invalid' : '')]) }}
                        <div class="invalid-feedback">{{ $errors->first('confirm') ?? 'Error filling the field' }}</div>
                    </div>
                </div>
                @endif

                <div class="row">

                    <div class="col-md-6 mb-3">
                        {{ Form::label('contractor_name', 'Наименование юридического лица или ФИО физлица') }}
                        {{ Form::text('contractor_name', null, ['id' => 'contractor_name', 'class' => 'form-control'. ($errors->has('contractor_name') ? ' is-invalid' : '')]) }}
                        <div class="invalid-feedback">{{ $errors->first('contractor_name') ?? 'Error filling the field' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        {{ Form::label('contractor_bin', 'РНН/БИН') }}
                        {{ Form::text('contractor_bin', null, ['id' => 'contractor_bin', 'class' => 'form-control'. ($errors->has('contractor_bin') ? ' is-invalid' : '')]) }}
                        <div class="invalid-feedback">{{ $errors->first('contractor_name') ?? 'Error filling the field' }}</div>
                    </div>


                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        {{ Form::label('contaractor_contact_name',  "ФИО контактного лица")}}
                        {{ Form::text('contractor_contact_name', null, ['id' => 'contractor_contact_name', 'class' => 'form-control'. ($errors->has('contractor_contact_name') ? ' is-invalid' : '')]) }}
                        <div class="invalid-feedback">{{ $errors->first('contaractor_contact_name') ?? 'Error filling the field' }}</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        {{ Form::label('contractor_phone', "Номер телефона") }}
                        {{ Form::text('contractor_phone', null, ['id' => 'contractor_phone', 'class' => 'form-control'. ($errors->has('contractor_phone') ? ' is-invalid' : '')]) }}
                        <div class="invalid-feedback">{{ $errors->first('contractor_phone') ?? 'Error filling the field' }}</div>
                    </div>
                </div>

                <button class="btn btn-primary btn-lg btn-block" type="submit">{{ trans('registrations::translate.btn.register') }}</button>
                {{ Form::close() }}

            </div>
        </div>

        {{-- <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">&copy; 2017-2019 Company Name</p>
            <ul class="list-inline">
                <li class="list-inline-item"><a href="#">Privacy</a></li>
                <li class="list-inline-item"><a href="#">Terms</a></li>
                <li class="list-inline-item"><a href="#">Support</a></li>
            </ul>
        </footer> --}}
    </div>

@endsection
