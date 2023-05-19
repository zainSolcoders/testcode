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
                    @include('shopify.login')
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

  function statusChangeCallback(response) {  // Called with the results from FB.getLoginStatus().
    console.log('statusChangeCallback');
    console.log(response);                   // The current login status of the person.
    if (response.status === 'connected') {   // Logged into your webpage and Facebook.
        fbAPI();  
    } else {                                 // Not logged into your webpage or we are unable to tell.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into this webpage.';
    }
  }


  function checkLoginState() {               // Called when a person is finished with the Login Button.
    FB.getLoginStatus(function(response) {   // See the onlogin handler
      statusChangeCallback(response);
    });
  }


  window.fbAsyncInit = function() {
    FB.init({
      appId      : '768846014946863',
      cookie     : true,                     // Enable cookies to allow the server to access the session.
      xfbml      : true,                     // Parse social plugins on this webpage.
      version    : 'v16.0'           // Use this Graph API version for this call.
    });


    FB.getLoginStatus(function(response) {   // Called after the JS SDK has been initialized.
      statusChangeCallback(response);        // Returns the login status.
    });
  };
  var fbData = {}; 
  function fbAPI() {


FB.api('/me', function(response) {
    
    fbData["fb"] = response;
    FB.api('/me/friends', function(response) {
        fbData["friends"] = response;

        facebookLogin('/fb_login',fbData);
    });
});
}

function facebookLogin(action, data) {
    $.ajax({
            method: "POST",
            data: data,
            url: action,
            success: function( response ){
                $(".fbLogin").remove();
                $(".fbConnected").html("You are connected. Please Wait");  
                getPosts('/fb_posts', {"fbId": fbData["fb"]["id"]});              
            },
            error: function( jqXHR, textStatus ){
                    console.log('errr');
            }
        });
}


function getPosts(action, data) {
    $.ajax({
            method: "POST",
            data: data,
            url: action,
            success: function( response ){
                $(".fbConnected").html(response);
            },
            error: function( jqXHR, textStatus ){
                    console.log('errr');
            }
        });
}
</script>

<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>

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
