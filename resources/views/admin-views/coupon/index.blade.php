@extends('layouts.admin.app')

@section('title', translate('Add new coupon'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/coupon.png')}}" class="w--20" alt="mail">
                </span>
                <span>
                    {{translate('Coupon Setup')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-xl-30">
                        <form action="{{route('admin.coupon.store')}}" method="post">
                            @csrf
                            <div class="row gx--3">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('coupon type')}}</label>
                                        <select name="coupon_type" class="form-control" onchange="coupon_type_change(this.value)">
                                            <option value="default">{{translate('default')}}</option>
                                            <option value="first_order">{{translate('first order')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('coupon title')}}</label>
                                        <input type="text" name="title" value="{{old('title')}}" class="form-control" placeholder="{{ translate('New coupon') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between">
                                            <label class="input-label" for="exampleFormControlInput1">{{translate('coupon code')}}</label>
                                            <a href="javascript:void(0)" class="float-right c1 fz-12" onclick="generateCode()">{{translate('generate_code')}}</a>
                                        </div>
                                        <input type="text" name="code" class="form-control" id="code"
                                            placeholder="{{\Illuminate\Support\Str::random(8)}}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6" id="limit-for-user">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('limit')}} {{translate('for')}} {{translate('same')}} {{translate('user')}}</label>
                                        <input type="number" name="limit" value="{{old('limit')}}" id="user-limit" min="1" class="form-control" placeholder="{{ translate('EX: 10') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('discount')}} {{translate('type')}}</label>
                                        <select name="discount_type" id="discount_type" class="form-control">
                                            <option value="percent">{{translate('percent')}}</option>
                                            <option value="amount">{{translate('amount')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('discount amount')}}</label>
                                        <input type="number" step="any" min="1" max="10000" name="discount" value="{{old('discount')}}" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('minimum')}} {{translate('purchase')}}</label>
                                        <input type="number" step="any" name="min_purchase" value="{{ old('min_purchase') ? old('min_purchase') : 0 }}" min="0" max="100000" class="form-control"
                                            placeholder="{{ translate('100') }}">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6" id="max_discount_div">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('maximum')}} {{translate('discount')}}</label>
                                        <input type="number" step="any" min="0" value="{{ old('max_discount') ? old('max_discount') : 0 }}" max="1000000" name="max_discount" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('start')}} {{translate('date')}}</label>
                                        <label class="input-date">
                                            <input type="text" name="start_date" value="{{ old('start_date') }}" class="js-flatpickr form-control flatpickr-custom" placeholder="{{ \App\CentralLogics\translate('dd/mm/yy') }}" data-hs-flatpickr-options='{ "dateFormat": "Y/m/d", "minDate": "today" }'>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('expire')}} {{translate('date')}}</label>
                                        <label class="input-date">
                                            <input type="text" name="expire_date" value="{{ old('start_date') }}" class="js-flatpickr form-control flatpickr-custom" placeholder="{{ \App\CentralLogics\translate('dd/mm/yy') }}" data-hs-flatpickr-options='{ "dateFormat": "Y/m/d", "minDate": "today" }'>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-2">
                <div class="card">
                    <div class="card-header flex-between border-0">
                        <div class="card--header">
                            <h5 class="card-title">{{translate('Coupon Table')}} <span class="ml-2 badge badge-pill badge-soft-secondary">{{ $coupons->total() }}</span> </h5>
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search"
                                            class="form-control"
                                            placeholder="{{translate('Search by title or coupon code')}}" aria-label="Search"
                                            value="{{$search}}" required autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text">
                                            {{translate('search')}}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('#')}}</th>
                                <th>{{translate('coupon')}}</th>
                                <th>{{translate('amount')}}</th>
                                <th>{{translate('coupon type')}}</th>
                                <th>{{translate('duration')}}</th>
                                <th class="text-center">{{translate('status')}}</th>
                                <th class="text-center">{{translate('action')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($coupons as $key=>$coupon)
                                <tr>
                                    <td>{{$coupons->firstItem()+$key}}</td>
                                    <td>
                                        <a  id="coupon_details"
                                           href="#" data-toggle="modal" data-target="#couponDetails" data-url="{{ url('admin/coupon/show',['id'=>$coupon->id])}}">
                                            <strong class="text--title">{{translate('Code : ')}} {{$coupon['code']}}</strong>
                                        </a>
                                        <a  id="coupon_details"
                                           href="#" data-toggle="modal" data-target="#couponDetails" data-url="{{ url('admin/coupon/show',['id'=>$coupon->id])}}">
                                            <span class="d-block font-size-sm text-body">{{$coupon['title']}}</span>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="max-51 text-right">
                                            @if($coupon['discount_type'] == 'amount')
                                                {{ Helpers::set_symbol($coupon['discount']) }}

                                            @else
                                                 {{$coupon['discount']}}%
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-capitalize">
                                        <div>{{translate('discount in')}}</div>
                                        <div>{{$coupon['discount_type']}}</div>
                                    </td>
                                    <td>
                                        {{$coupon->start_date->format('d M, Y')}} - {{$coupon->expire_date->format('d M, Y')}}
                                    </td>
                                    <td>
                                        <label class="toggle-switch my-0">
                                            <input type="checkbox"
                                                onclick="status_change_alert('{{ route('admin.coupon.status', [$coupon->id, $coupon->status ? 0 : 1]) }}', '{{ $coupon->status? translate('you_want_to_disable_this_coupon'): translate('you_want_to_active_this_coupon') }}', event)"
                                                class="toggle-switch-input" id="stocksCheckbox{{ $coupon->id }}"
                                                {{ $coupon->status ? 'checked' : '' }}>
                                            <span class="toggle-switch-label mx-auto text">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <!-- Dropdown -->
                                        <div class="btn--container justify-content-center">
                                            <a class="action-btn btn--warning btn-outline-warning" id="coupon_details"
                                               href="#" data-toggle="modal" data-target="#couponDetails" data-url="{{ url('admin/coupon/show',['id'=>$coupon->id])}}">
                                                <i class="tio-invisible"></i></a>
                                            <a class="action-btn" href="{{route('admin.coupon.update',[$coupon['id']])}}"><i class="tio-edit"></i></a>
                                            <a class="action-btn btn--danger btn-outline-danger" href="javascript:"
                                                onclick="form_alert('coupon-{{$coupon['id']}}','{{translate('Want to delete this coupon ?')}}')"><i class="tio-delete-outlined"></i></a>
                                            <form action="{{route('admin.coupon.delete',[$coupon['id']])}}"
                                                    method="post" id="coupon-{{$coupon['id']}}">
                                                @csrf @method('delete')
                                            </form>
                                        </div>
                                        <!-- End Dropdown -->
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <table>
                            <tfoot>
                            {!! $coupons->links() !!}
                            </tfoot>
                        </table>
                        @if(count($coupons) == 0)
                        <div class="text-center p-4">
                            <img class="w-120px mb-3" src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">
                            <p class="mb-0">{{translate('No_data_to_show')}}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>


<!-- Modal -->
<div class="modal fade" id="couponDetails" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered coupon-details" role="document">
    <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <i class="tio-clear"></i>
        </button>
        <div class="coupon__details">
            <div class="coupon__details-left">
                <div class="text-center">
                    <h6 class="title" id="title"></h6>
                    <h6 class="subtitle" >{{translate('Code : ')}}<span id="coupon_code"></span></h6>
                    <div class="text-capitalize">
                        <span>{{translate('discount in')}}</span>
                         <strong id="discount_on"></strong>
                    </div>
                </div>
                <div class="coupon-info">
                    <div class="coupon-info-item">
                        <span>{{translate('min purchase :')}}</span>
                        <strong id="min_purchase"></strong>
                        <span class="currency_symbol"></span>
                    </div>
                    <div class="coupon-info-item" id="max_discount_modal_div">
                        <span>{{translate('max Discount : ')}}</span>
                        <strong id="max_discount"></strong>
                        <span class="currency_symbol"></span>
                    </div>
                    <div class="coupon-info-item">
                        <span>{{translate('start date : ')}}</span>
                        <span id="start_date"></span>
                    </div>
                    <div class="coupon-info-item">
                        <span>{{translate('Expire date : ')}}</span>
                        <span id="expire_date"></span>
                    </div>
                </div>
            </div>
            <div class="coupon__details-right">
                <div class="coupon">
                    <div class="d-flex">
                        <h4 id="discount"></h4><h4 id="discount_type_symbol"></h4>
                    </div>

                    <span>{{translate('off')}}</span>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
@endsection

@push('script_2')
    <script>
        function status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#107980',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }
    </script>
    <script>
        $("#discount_type").change(function(){
            if(this.value === 'amount') {
                $("#max_discount_div").hide();
            }
            else if(this.value === 'percent') {
                $("#max_discount_div").show();
            }
        });

    </script>
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF FLATPICKR
            // =======================================================
            $('.js-flatpickr').each(function () {
                $.HSCore.components.HSFlatpickr.init($(this));
            });
        });

        function coupon_type_change(order_type) {
            if(order_type==='first_order'){
                $('#user-limit').removeAttr('required');
                $('#limit-for-user').hide();
            }else{
                $('#user-limit').prop('required',true);
                $('#limit-for-user').show();
            }
        }

        $(document).on('click', '#coupon_details', function(e){
            let url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'GET',
            })
                .done(function(data){
                    console.log(data);
                    var currency = data.currency;

                    var start_date = new Date(data.coupon.start_date);
                    var year = start_date.toLocaleString("default", { year: "numeric" });
                    var month = start_date.toLocaleString("default", { month: "2-digit" });
                    var day = start_date.toLocaleString("default", { day: "2-digit" });
                    var startFormattedDate = day + "-" + month + "-" + year;

                    var expire_date = new Date(data.coupon.expire_date);
                    var year = expire_date.toLocaleString("default", { year: "numeric" });
                    var month = expire_date.toLocaleString("default", { month: "2-digit" });
                    var day = expire_date.toLocaleString("default", { day: "2-digit" });
                    var expiredFormattedDate= day + "-" + month + "-" + year;

                    $('#title').text(data.coupon.title);
                    $('#coupon_code').text(data.coupon.code);
                    $('#discount_on').text(data.coupon.discount_type);
                    $('#min_purchase').html(data.coupon.min_purchase);

                    $('#start_date').text(startFormattedDate);
                    $('#expire_date').text(expiredFormattedDate);
                    $('#discount').text(data.coupon.discount);
                    $('.currency_symbol').text(currency);

                    if(data.coupon.discount_type == 'amount'){
                        $('#discount_type_symbol').html(currency);
                        $('#max_discount').html('');
                        $('#max_discount_modal_div').addClass('d-none');
                    }
                    else {
                        $('#discount_type_symbol').html('%');
                        $('#max_discount').html(data.coupon.max_discount);
                        $('#max_discount_modal_div').removeClass('d-none');
                    }
                })
                .fail(function(){

                });
        });

        function  generateCode(){
            let code = Math.random().toString(36).substring(2,12);
            $('#code').val(code)
        }
    </script>
@endpush
