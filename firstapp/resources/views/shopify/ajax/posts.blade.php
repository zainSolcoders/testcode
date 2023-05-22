<div class="main-container">
        <div class="main-content">
          
        @forelse ($posts as $post)
        
            <span> {{ $post->message }} </span>
            @empty
            No Post Found
            @endforelse
        </div>
        <hr>
        <form action="/create_post" method="POST">
        @csrf
                        <input type="hidden" name="shop" value="{{ $store->shop_url }}">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="product_feature_enable">Post</label><br>
                                <textarea name="comment" class="form-control" style="height:200px"></textarea> 
                            </div>
                        </div>
                        
           
                        <button type="submit" class="btn btn-success">Create Post</button>
                    </form>
    </div>