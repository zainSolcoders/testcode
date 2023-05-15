<div class="tab-pane fade @if(($store->current_charge_id == null) || ($isBillingActive['billing'] == false)) show active @endif" id="plan" role="tabpanel" aria-labelledby="plan-tab">
    <div class="row my-3">
        @foreach ($plans as $plan)
            <div class="col-lg-3 col-md-3 mb-4">
                <div class="card h-100 shadow-lg">
                    <div class="card-body">
                        <div class="text-center p-3">
                            <h5 class="card-title">{{ $plan->name }}</h5>
                            <span class="h4">${{ $plan->price }}</span>/month
                            @if($store->trial_expiration_date == null && $plan->id == 1)
                                <hr>
                                    <p class="card-text mb-0">10 Days free trial</p>
                                <hr>
                            @endif
                            <hr class="mb-0">
                                <p class="card-text mb-0">@if($plan->products == 0 ) Unlimited Products @else Up to {{ $plan->products }} products @endif</p>
                            <hr class="mt-0">
                        </div>
                    </div>
                    <div class="card-body text-center">
                        @if ($store->plan_id  != $plan->id || $store->current_charge_id  ==  NULL || $isBillingActive['billing'] == false)
                            <button class="btn btn-outline-primary btn-lg change-plan" style="border-radius:30px" data-plan_id="{{ $plan->id }}" @if($store->trial_expiration_date == null && $plan->id != 1) data-is_trial="false" @else data-is_trial="1" @endif>Select</button>
                        @else
                            <button class="btn btn-outline-primary btn-lg" style="border-radius:30px" id="cancel_charge_id"> Cancel my plan</button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
