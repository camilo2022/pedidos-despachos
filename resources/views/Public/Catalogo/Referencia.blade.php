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

	<title>Marca: {{ $product->trademark }} | Ref. {{ $product->code }}</title>
</head>
<body class="body-catalogo4">

	<div class="container-fluid bcontent log logo">
	    <nav class="navbar navbar-expand-sm">
	        <a class="navbar-brand">
				<img src="{{ asset('images/logo-bless.svg') }}" style="width: 200px !important; height: auto !important;"/>
            </a>
	    </nav>
	</div>

	<section>
		<div class="container-fluid  single-product">

            <div class="text-center">
                <p>
                    <b>REF:</b> {{ $product->code }}</br>
                    <b>MARCA:</b> {{ $product->trademark }}</br>
                    <b>DESCRIPCION:</b> {{ str_replace($product->trademark, "", $product->description) }}
                </p>
            </div>
			<div class="row justify-content-center">
				<p class="imglist">
					@if ($product->files->whereIn('type', ['IMAGEN', 'VIDEO'])->isEmpty())
						@php($file = $product->files->where('type', 'PORTADA')->first())
						@if ($file)
						<div class="col-12 col-md-6 col-lg-4 imgprod">
							<a class="one" href="{{ asset("storage/$file->path") }}" data-fancybox="galeria">
								<img src="{{ asset("storage/$file->path") }}" width="100%">
							</a>
						</div>
						@endif
					@endif
                    @foreach ($product->files->where('type', 'IMAGEN') as $image)
                        <div class="col-12 col-md-6 col-lg-4 imgprod">
                            <a class="one" href="{{ asset("storage/$image->path") }}" data-fancybox="galeria">
                                <img src="{{ asset("storage/$image->path") }}" width="100%">
                            </a>
                        </div>
                    @endforeach
                    @foreach ($product->files->where('type', 'VIDEO') as $video)
                        <div class="col-12 col-md-6 col-lg-4 imgprod">
                            <a class="one" href="{{ asset("storage/$video->path") }}" data-fancybox="galeria">
                                <video controls width="100%">
                                    <source src="{{ asset("storage/$video->path") }}" type="video/mp4">
                                    Tu navegador no soporta el elemento de video.
                                </video>
                            </a>
                        </div>
                    @endforeach
				</p>
			</div>
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
</html>

