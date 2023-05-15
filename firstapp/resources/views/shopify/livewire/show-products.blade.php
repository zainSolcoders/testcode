<div>
    <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation" border="1">
        <thead>
            <tr>
                <th scope="col">
                    <a  role="button" href="#">
                        Product Image
                        <em class="arrow-down fa-solid fa-caret-down"></em>
                        <em class="arrow-up fa-solid fa-caret-up"></em>
                    </a>
                </th>
                <th scope="col">
                    <a  role="button" href="#">
                        Title
                        <em class="arrow-down fa-solid fa-caret-down"></em>
                        <em class="arrow-up fa-solid fa-caret-up"></em>
                    </a>
                </th>
                <th scope="col">
                    <a  role="button" href="#">
                        Status
                        <em class="arrow-down fa-solid fa-caret-down"></em>
                        <em class="arrow-up fa-solid fa-caret-up"></em>
                    </a>
                </th>
                <th scope="col">
                    <a  role="button" href="#">
                        Type
                        <em class="arrow-down fa-solid fa-caret-down"></em>
                        <em class="arrow-up fa-solid fa-caret-up"></em>
                    </a>
                </th>
                <th scope="col">
                    <a  role="button" href="#">
                        Vendor
                        <em class="arrow-down fa-solid fa-caret-down"></em>
                        <em class="arrow-up fa-solid fa-caret-up"></em>
                    </a>
                </th>
              
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
            <tr>
                <td><img src="{{ $product->image?->src ?? '/assets/blank.jpg' }}" style="width:100px" /></td>
                <td>{{ $product->title }}</td>
                <td>{{ $product->status }}</td>
                <td>{{ $product->product_type }}</td>
                <td>{{ $product->vendor }}</td>

            </tr>
            @empty
            <tr>
                <td colspan="5">No Record Found</td>
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