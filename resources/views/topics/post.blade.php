<section>
    <ul>
        <li>From: <a href="{{ url("/users/{$post->author->user_name}") }}">{{$post->author->user_name}}</a> ({{$post->message_popname}})</li>
        <li>Date: {{ date('D, F jS Y - H:i', strtotime($post->message_time)) }}</li>
        @unless (str_is($post->message_title, ""))
            <li>Subject: {{$post->message_title}}</li>
        @endunless
    </ul>
    <p>{!! nl2br($post->message_text) !!}</p>
    {{-- <li>{{$post->message_html}}</li> --}}
    {{-- <li>{{$post->update_user_id}}</li> --}}
</section>
