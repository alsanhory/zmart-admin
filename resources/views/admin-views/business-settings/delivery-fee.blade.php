@extends('layouts.admin.app')

@section('title', translate('delivery fee setup'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">

            @include('admin-views.business-settings.partial.business-settings-navmenu')
        </div>
        @php($config=\App\CentralLogics\Helpers::get_business_settings('maintenance_mode'))
        <div class="tab-content">
            <div class="tab-pane active" id="delivery-fee">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon">
                                <i class="tio-user"></i>
                            </span> <span>{{translate('Delivery Fee Setup')}}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.business-settings.store.delivery-setup-update')}}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                @php($config=\App\CentralLogics\Helpers::get_business_settings('delivery_management'))
                                <div class="col-lg-6 text-capitalize">
                                    <label class="form-label font-semibold">{{translate('Delivery Type')}}</label>

                                    <div class="d-flex flex-wrap align-items-center form-control border">
                                        <label class="form-check form--check mr-2 mr-md-4 mb-0">
                                            <input type="radio" name="shipping_status" value="0" {{$config['status']==0?'checked':''}} id="default_delivery_status" class="form-check-input">
                                            <span class="form-check-label">
                                                    {{translate('default_delivery_charge')}}
                                            </span>
                                        </label>
                                        <label class="form-check form--check mr-2 mr-md-4 mb-0">
                                            <input type="radio" name="shipping_status" value="1" {{$config['status']==1?'checked':''}} id="shipping_by_distance_status" class="form-check-input">
                                            <span class="form-check-label">
                                                    {{translate('delivery_charge_by_distance')}}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div>
                                                <label>{{translate('minimum_shipping_charge')}} <span>({{ \App\CentralLogics\Helpers::currency_symbol() }})</span></label><br>
                                                <input type="number" step=".01" class="form-control"
                                                       name="min_shipping_charge"
                                                       value="{{$config['min_shipping_charge']}}"
                                                       id="min_shipping_charge" {{ $config['status']==0?'disabled':'' }} >
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div>
                                                <label>{{translate('shipping_charge_per_km')}} <span>({{ \App\CentralLogics\Helpers::currency_symbol() }})</span></label><br>
                                                <input type="number" step=".01" class="form-control" name="shipping_per_km"
                                                       value="{{$config['shipping_per_km']}}"
                                                       id="shipping_per_km" {{ $config['status']==0?'disabled':'' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 mt-4">
                                    @php($delivery=\App\Model\BusinessSetting::where('key','delivery_charge')->first()->value)
                                    <label class="" for="exampleFormControlInput1">{{translate('default_delivery_charge')}} <span>({{ \App\CentralLogics\Helpers::currency_symbol() }})</span></label>
                                    <input type="number" min="0" step=".01" name="delivery_charge" value="{{$delivery}}" class="form-control" placeholder="EX: 100" required
                                           {{ $config['status']==1?'disabled':'' }} id="delivery_charge">
                                </div>
                            </div>
                            <div class="btn--container mt-4 justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn--primary">{{translate('submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script_2')
    <script>
        $('#shipping_by_distance_status').on('click', function () {
            $("#delivery_charge").prop('disabled', true);
            $('#min_shipping_charge').prop('disabled', false);
            $('#shipping_per_km').prop('disabled', false);
        });

        $('#default_delivery_status').on('click', function () {
            $("#delivery_charge").prop('disabled', false);
            $('#min_shipping_charge').prop('disabled', true);
            $('#shipping_per_km').prop('disabled', true);
        });
    </script>

@endpush
