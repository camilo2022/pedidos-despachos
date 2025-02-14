@if (session('danger') or isset($danger))
    <script>
        let danger = @json(session('danger') ?? $danger);
        document.addEventListener('DOMContentLoaded', function() {
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'ERROR',
                body: danger
            });
        });
    </script>
@endif

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($errors->all() as $error)
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'ERROR',
                    body: '{{ $error }}'
                });
            @endforeach
        });
    </script>
@endif

