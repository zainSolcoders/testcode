<div>
    <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation" border="1">
        <thead>
            <tr>
               
                <th scope="col">
                    <a  role="button" href="#">
                        Title
                        <em class="arrow-down fa-solid fa-caret-down"></em>
                        <em class="arrow-up fa-solid fa-caret-up"></em>
                    </a>
                </th>
               
            </tr>
        </thead>
        <tbody>
            @forelse ($collections as $collection)
            <tr>
                
                <td>{{ $collection->title }}</td>
            </tr>
            @empty
            <tr>
                <td>No Record Found</td>
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