@if (session('question'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).Toasts('create', {
                class: '',
                title: 'INTERROGANTE',
                body: '{{ session('question') }}'
            });
        });
    </script>
@endif
