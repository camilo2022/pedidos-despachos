<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="description" content="Productos del catalogo">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon.png') }}">
        <link rel="stylesheet" href="{{ asset('css/public/main.css') }}">
        <link rel="stylesheet" href="{{ asset('css/public/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/public/fancybox.css') }}">
        <link rel="stylesheet" href="{{ asset('css/dist/css/adminlte.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">

        <title>Marca: {{ $product->trademark }} | Ref. {{ $product->code }}</title>
    </head>
    <body class="body-catalogo4">

        <div class="container-fluid bcontent log logo">
            <nav class="navbar navbar-expand-sm">
                <a class="navbar-brand" href="{{ route('Public.Catalogo.Index') }}">
                    <img src="{{ asset('images/logo-bless.svg') }}" style="width: 200px !important; height: auto !important;"/>
                </a>
            </nav>
        </div>

        <section>
            <div class="container-fluid single-product">
                <div class="text-center">
                    <p>
                        <b>REF:</b> {{ $product->code }}</br>
                        <b>MARCA:</b> {{ $product->trademark }}</br>
                        <b>DESCRIPCION:</b> {{ str_replace($product->trademark, "", $product->description) }}
                    </p>
                </div>

                <div class="row justify-content-center">

                    <div class="col-12 imgprod text-center">
                        @php($file = $product->files->where('type', 'PORTADA')->first())
                        @php($file = $file ?? $product->files->where('type', 'IMAGEN')->first())
                        <div class="imgprod">
                            <a class="one" data-fancybox="galeria" id="element">
                                <img src="{{ isset($file->path) ? asset("storage/$file->path") : asset("images/image-not-found.jpg") }}" class="product-image" style="height: 500px; width: auto;"/>
                            </a>
                        </div>
                    </div>

                    <div class="product-image-thumbs text-center">
                        @foreach ($product->files as $item)
                            @if(in_array($item->type, ['PORTADA', 'IMAGEN']))
                                <div class="product-image-thumb {{ $item->path == $file->path ? 'active' : '' }}" onclick="change(this)">
                                    <img src="{{ asset("storage/$item->path") }}" alt="Imagen">
                                </div>
                            @elseif(in_array($item->type, ['VIDEO']))
                                <div class="product-image-thumb {{ $item->path == $file->path ? 'active' : '' }}" onclick="change(this)">
                                    <video controls width="100%">
                                        <source src="{{ asset("storage/$item->path") }}" type="video/mp4"/>
                                        Tu navegador no soporta el elemento de video.
                                    </video>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- <p class="imglist">
                        @php($file = $product->files->where('type', 'PORTADA')->first())
                        @if ($file)
                        <div class="col-12 col-md-6 col-lg-4 imgprod">
                            <a class="one" data-fancybox="galeria">
                                <img src="{{ asset("storage/$file->path") }}" width="100%"/>
                            </a>
                        </div>
                        @endif
                        @foreach ($product->files->where('type', 'IMAGEN') as $image)
                            <div class="col-12 col-md-6 col-lg-4 imgprod">
                                <a class="one" data-fancybox="galeria">
                                    <img src="{{ asset("storage/$image->path") }}" width="100%"/>
                                </a>
                            </div>
                        @endforeach
                        @foreach ($product->files->where('type', 'VIDEO') as $video)
                            <div class="col-12 col-md-6 col-lg-4 imgprod">
                                <a class="one" data-fancybox="galeria">
                                    <video controls width="100%">
                                        <source src="{{ asset("storage/$video->path") }}" type="video/mp4"/>
                                        Tu navegador no soporta el elemento de video.
                                    </video>
                                </a>
                            </div>
                        @endforeach
                    </p> --}}
                </div>

                @if($inventory->to_transit->count() > 0)
                <div class="row justify-content-center">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th colspan="{{ $referenciaSizes->count() + 1 }}">EN PROCESO</th>
                            </tr>
                            <tr>
                                <th>COLOR</th>
                            @foreach($referenciaSizes as $size)
                                <th>{{ $size->code }}</th>
                            @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventory->to_transit as $item)
                            <tr>
                                <td>{{ $item->COLOR }}</td>
                                @foreach($referenciaSizes as $size)
                                <td>{{ $item->{"T$size->code"} }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($inventory->to_discount->count() > 0)
                <div class="row justify-content-center">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th colspan="{{ $referenciaSizes->count() + 1 }}">DESPACHO INMEDIATO</th>
                            </tr>
                            <tr>
                                <th>COLOR</th>
                            @foreach($referenciaSizes as $size)
                                <th>{{ $size->code }}</th>
                            @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventory->to_discount as $item)
                            <tr>
                                <td>{{ $item->COLOR }}</td>
                                @foreach($referenciaSizes as $size)
                                @php($cantidad = $item->{"T$size->code"} - $inventory->to_transit->where('COLOR', $item->COLOR)->pluck("T$size->code")->sum())
                                <td>{{ $cantidad >= 0 ? $cantidad : 0 }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </section>

        <footer class="container-fluid bcontent">
            <div class="col-12 row redess">
                <div class="logg col-12 col-lg-12 ">
                    <p class="cr">ORGANIZACION  BLESS © Todos los derechos reservados.</p>
                    <p class="mi">Hecho en Colombia</p><img class="banderac" src="{{ asset('images/colombia.png') }}">
                </div>
            </div>
        </footer>
    </body>
    <script src="https://unpkg.com/muuri@0.8.0/dist/muuri.min.js"></script>
    <script src="{{ asset('js/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('js/public/filter.js') }}"></script>

    @if (isset($file->path))
        <script>
            Toast.fire({
                icon: 'success',
                title: 'Imagenes de la referencia cargadas exitosamente.'
            })
        </script>
    @else
        <script>
            Toast.fire({
                icon: 'warning',
                title: 'No hay imagenes de muestra cargadas a esta referencia.'
            })
        </script>
    @endif
</html>

