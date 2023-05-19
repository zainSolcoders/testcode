<div class="main-container">
        <div class="main-content">
          
        @forelse ($posts as $post)
        
            <span> {{ $post->message }} </span>
            @empty
            No Record Found
            @endforelse
        </div>
    </div>