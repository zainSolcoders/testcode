<ul class="nav nav-tabs" id="myTab" role="tablist">


<li class="nav-item" role="presentation">
        <button class="nav-link" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="false">Social Login</button>
    </li>

<li class="nav-item" role="presentation">
        <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="false">Orders</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="collections-tab" data-bs-toggle="tab" data-bs-target="#collections" type="button" role="tab" aria-controls="products" aria-selected="false">Collections</button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab" aria-controls="products" aria-selected="false">Products</button>
    </li>


    @if($isBillingActive['billing'])
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Settings</button>
        </li>
    @endif
    <li class="nav-item" role="presentation">
        <button class="nav-link @if(($store->current_charge_id == null) || ($isBillingActive['billing'] == false)) active @endif" id="plan-tab" data-bs-toggle="tab" data-bs-target="#plan" type="button" role="tab" aria-controls="plan" aria-selected="false">Plan</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="howtouse-tab" data-bs-toggle="tab" data-bs-target="#howtouse" type="button" role="tab" aria-controls="howtouse" aria-selected="false">How to Use</button>
    </li>
</ul>
