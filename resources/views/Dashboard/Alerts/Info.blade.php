@if (session('info') or isset($info))
    <script>
        let info = @json(session('info') ?? $info);
        document.addEventListener('DOMContentLoaded', function() {
            $(document).Toasts('create', {
                class: 'bg-info',
                title: 'INFORMACION',
                body: info
            });
        });
    </script>
@endif
