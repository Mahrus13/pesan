<div class="pesan-wrapper">
    <ul class="pesan">
        @foreach($pesan as $data)
        <li class="pesan clearfix">
            {{--if message from id is equal to auth id then it is sent by logged in user--}}
            <div class="{{($data->from == Auth::id()) ? 'sent' : 'received'}}">
                <p>{{$data->pesan}}</p>
                <p class="date">{{date('d M y, h:i a', strtotime($data->created_at))}}</p>
             </div>
        </li>
       @endforeach
    </ul>
</div>
            
<div class="input-text">
    <input type="text" name="pesan" class="submit">
</div>