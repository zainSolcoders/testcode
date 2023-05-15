<div>
    <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation" border="1">
        <thead>
            <tr>
                <th scope="col">
                    <a wire:click.prevent="sortBy('id')" role="button" href="#">
                        Order ID
                        <em class="arrow-down fa-solid fa-caret-down"></em>
                        <em class="arrow-up fa-solid fa-caret-up"></em>
                    </a>
                </th>
                <th scope="col">
                    <a wire:click.prevent="sortBy('first_name')" role="button" href="#">
                        Date
                        <em class="arrow-down fa-solid fa-caret-down"></em>
                        <em class="arrow-up fa-solid fa-caret-up"></em>
                    </a>
                </th>
                <th scope="col">
                    <a wire:click.prevent="sortBy('email')" role="button" href="#">
                        Customer
                        <em class="arrow-down fa-solid fa-caret-down"></em>
                        <em class="arrow-up fa-solid fa-caret-up"></em>
                    </a>
                </th>
                <th scope="col">
                    <a wire:click.prevent="sortBy('details->contact')" role="button" href="#">
                        Total
                        <em class="arrow-down fa-solid fa-caret-down"></em>
                        <em class="arrow-up fa-solid fa-caret-up"></em>
                    </a>
                </th>
                <th scope="col">
                    <a wire:click.prevent="sortBy('created_at')" role="button" href="#">
                        Payment Status
                        <em class="arrow-down fa-solid fa-caret-down"></em>
                        <em class="arrow-up fa-solid fa-caret-up"></em>
                    </a>
                </th>
                <th scope="col">
                    <a wire:click.prevent="sortBy('status')" role="button" href="#">
                        Delivery Method
                        <em class="arrow-down fa-solid fa-caret-down"></em>
                        <em class="arrow-up fa-solid fa-caret-up"></em>
                    </a>
                </th>
                <th scope="col">
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->created_at }}</td>
                <td>{{ $order->billing_address?->name ?? "-" }}</td>
                <td>{{ $order->total_price }}</td>
                <td>{{ $order->financial_status }}</td>
                <td>{{ $order->shipping_lines?->title ?? "-" }}</td>

            </tr>
            @empty
            <tr>
                <td colspan="7">No Record Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination bg-gray2 w-full mt-6">
    @for ($page=1; $page<=$pages; $page++)
    <button wire:click="gotoPage({{ $page }})"> {{ $page }}
    </button>
    @endfor
</div>