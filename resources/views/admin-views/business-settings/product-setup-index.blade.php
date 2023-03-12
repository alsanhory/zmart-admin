@extends('layouts.admin.app')

@section('title', translate('Product Setup'))


@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    @include('admin-views.business-settings.partial.business-settings-navmenu')

    @php($config=\App\CentralLogics\Helpers::get_business_settings('maintenance_mode'))
    <div class="tab-content">
        <div class="tab-pane fade show active" id="business-setting">
            <div class="card">

                <div class="card-body">
                    <form action="{{route('admin.business-settings.store.product-setup-update')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            @php($stock_limit=\App\Model\BusinessSetting::where('key','minimum_stock_limit')->first()->value)
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="input-label" for="minimum_stock_limit">{{translate('minimum stock limit')}}</label>
                                    <input type="number" min="1" value="{{$stock_limit}}"
                                           name="minimum_stock_limit" class="form-control" placeholder=""
                                           required>
                                </div>
                            </div>

                        </div>

                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                            <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn--primary">{{translate('save')}}</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('script_2')

@endpush
