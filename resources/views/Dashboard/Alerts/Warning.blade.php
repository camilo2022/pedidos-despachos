@if (session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).Toasts('create', {
                class: 'bg-warning',
                title: 'ADVERTENCIA',
                body: '{{ session('warning') }}'
            });
        });
    </script>
@endif
