@if (session('success') or isset($success))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'EXITO',
                body: '{{ $success }}'
            });
        });
    </script>
@endif
