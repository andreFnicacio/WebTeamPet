
@php
    $messages = Session::get('messages');
@endphp
@if($messages)
    @section('scripts')
        @parent
        <script type="text/javascript">
            let steps = [];
            @foreach($messages as $message)
                @if($message['style'] !== 'toast')
                    steps.push({
                        type: "{{ $message['type'] }}",
                        title: "{!! $message['title'] !!}",
                        html: "{!! str_replace("\n", "", nl2br($message['text'])) !!}",
                    });
                @else
                    M.toast({
                        html: "{!! $message['title'] !!}"
                    });
                @endif
            @endforeach
            if(steps.length > 0) {
                swal.queue(steps);
            }
        </script>
    @endsection
@endif


{{ session()->forget('message') }}
