@if (session('question') or isset($question))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).Toasts('create', {
                class: '',
                title: 'INTERROGANTE',
                body: '{{ $question }}'
            });
        });
    </script>
@endif
