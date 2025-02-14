@if (session('warning') or isset($warning))
    <script>
        let warning = @json(session('warning') ?? $warning);
        document.addEventListener('DOMContentLoaded', function() {
            $(document).Toasts('create', {
                class: 'bg-warning',
                title: 'ADVERTENCIA',
                body: warning
            });
        });
    </script>
@endif
