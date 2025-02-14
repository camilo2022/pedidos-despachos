@if (session('question') or isset($question))
    <script>
        let question = @json(session('question') ?? $question);
        document.addEventListener('DOMContentLoaded', function() {
            $(document).Toasts('create', {
                class: '',
                title: 'INTERROGANTE',
                body: question
            });
        });
    </script>
@endif
