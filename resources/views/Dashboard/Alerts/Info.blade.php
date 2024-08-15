@if (session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).Toasts('create', {
                class: 'bg-info',
                title: 'INFORMACION',
                body: '{{ session('info') }}'
            });
        });
    </script>
@endif
