@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'EXITO',
                body: '{{ session('success') }}'
            });
        });
    </script>
@endif
