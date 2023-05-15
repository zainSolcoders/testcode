<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport"  content="width=device-width, initial-scale=1">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('public/css/style.css') }}" rel="stylesheet" />
    @livewireStyles
</head>
<body class="mt-4 mb-4 container">
    @include('shopify.app_bridge')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @include('shopify.tabs')
                <div class="tab-content" id="myTabContent">
                    @include('shopify.livewire.orders')
                    @include('shopify.livewire.collections')
                    @include('shopify.livewire.products')
                    @include('shopify.settings')
                    @include('shopify.plan')
                    @include('shopify.how_to_use')
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
<script>
    $(document).ready(function() {

        $('#include_products').attr('multiple',true);
        $('#include_products').select2({
            maximumSelectionLength: {{ $getPlan->products }},
            dropdownAutoWidth : true
        });
        $('#product_feature_enable').click(function(){
            if($('.products').is(':visible')){
                $('.products').hide();

                $('#check_product').attr('required',false);
            }
            else{
                $('.products').show();
                $('#check_product').attr('required',true);
            }
        });
        @if(isset($settings->product_feature_enable) && $settings->product_feature_enable == 1 )
            @if(!empty($settings->include_products))
                var includeProducts = @json($settings->include_products);
                $('#include_products').attr('multiple',true);
                $('#include_products').select2({
                    maximumSelectionLength: {{ $getPlan->products }},
                    dropdownAutoWidth : true
                });
                $('#include_products').val(includeProducts);
                $('#include_products').trigger('change');
            @endif
        @endif
    });
    $("#cancel_charge_id").click(function(){
        var shop = "{{ $store->shop_url }}";
        $.ajax({
            url: "cancel_charge",
            type: 'POST',
            data: {
                "shop": shop,
            },
            success: function (response){
                window.parent.location.href = response;
            }
        });
    });

    $(".change-plan").click(function(){
        let changePlanBtn = $(this);
        let planId  = $(this).attr('data-plan_id');
        let is_trial  = $(this).attr('data-is_trial');
        createCharge( planId , changePlanBtn,is_trial );
    });

    function createCharge( planId, planBtn,is_trial = false ){
        const params = new Proxy(new URLSearchParams(window.location.search), {
            get: (searchParams, prop) => searchParams.get(prop),
        });
        let shop_url_param = "?shop="+params.shop;

        $.ajax({
            method: "GET",
            url: '/create_charge/'+planId+shop_url_param+'&is_trial='+is_trial,
            success: function( response ){
                //   planBtn.next('.change-plan').prop('href',response.response.recurring_application_charge.confirmation_url);
                window.top.location.href = response.response.recurring_application_charge.confirmation_url;
            },
            error: function( jqXHR, textStatus ){
                    console.log('errr');
            }
        });
    }
</script>
@livewireScripts
</body>
</html>
