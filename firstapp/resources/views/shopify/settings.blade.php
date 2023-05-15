@if($isBillingActive['billing'])
    <div class="tab-pane fade show active" id="settings" role="tabpanel" aria-labelledby="settings-tab">
        <div class="container my-4">
            <div class="row">
                <div class="col-md-12">
                    @if(isset($_REQUEST['error']))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <strong>{{ $_REQUEST['error'] }}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(isset($_REQUEST['success']))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <strong>Settings has been updated</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <form action="/save_setting" method="POST">
                        <input type="hidden" name="shop" value="{{ $store->shop_url }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="product_feature_enable">Enable This Feature :</label><br>
                                <label class="switch"><input type="checkbox" @if($settings) @if(!empty($settings->product_feature_enable) && $settings->product_feature_enable == 1) checked @endif  @else checked @endif name="product_feature_enable"  value="1"><span class="slider round" id="product_feature_enable"></span></label>
                            </div>
                            <div class="col-md-9 products"  @if(!empty($settings->product_feature_enable) && $settings->product_feature_enable == 2 )  style="display:none" @endif>
                                <label for="include_products"><strong>Products:</strong></label><br>
                                <select class="form-control w-100" id="include_products" name="include_products[]" multiple>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="image_feature_enable">Show Image Against Title :</label><br>
                                <label class="switch"><input type="checkbox" @if($settings) @if(!empty($settings->image_feature_enable) && $settings->image_feature_enable == 1) checked @endif  @else checked @endif name="image_feature_enable"  value="1"><span class="slider round" id="image_feature_enable"></span></label>
                            </div>
                        </div>
                        <hr>
                        <input type="hidden" value="{{ $host }}" name="host">
                        <button type="submit" class="btn btn-success">Update Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
