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
	<link rel="stylesheet" href="{{ asset('css/public/owl.carousel.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/public/owl.theme.default.min.css') }}">
	<title>CATALOGO BLESS</title>
</head>

<body class="body-catalogo4">

	<div class="container-fluid bcontent log logo">
	    <nav class="navbar navbar-expand-sm">
	        <a class="navbar-brand">
				<img src="{{ asset('images/logo-bless.svg') }}" style="width: 200px !important; height: auto !important;"/>
            </a>
	    </nav>
	    
	</div>
	<a id="back-to-top" href="#" class="back-to-top" role="button"><i class="icon-expand_less"></i></a>		<!-- Slider -->

	<div class="contenedor">
		
		<header>
			<form>
			<input type="text" class="barra-busqueda" id="barra-busqueda" placeholder="Buscar Referencia">
			</form>
		</header>

		
		<section class="container section-products grid" id="grid">
		    
            @foreach($products as $referencia)
			<div class="item col-12 col-md-4 col-lg-3" data-etiquetas="{{ $referencia->code }}" data-descripcion="JEAN {{ $referencia->description }}">
				<div class="item-contenido single-product">
					<a href="{{ route('Public.Catalogo.Referencia', $referencia->code) }}">
					<div class="image-wrapper">
						<a class="one" href="{{ route('Public.Catalogo.Referencia', $referencia->code) }}">
                            @php($path = $referencia->files->where('type', 'PORTADA')->first()->path)
							<img src="{{ "storage/$path" }}" width="100%">
						</a>
					</div>
					<div class="btnproduct item-title-referencia1">
						<a href="{{ route('Public.Catalogo.Referencia', $referencia->code) }}" class="btn">{{$referencia->code}}</a>
					</div>
					</a>
				</div>
			</div>
			@endforeach
		</section>
	</div>



	<footer class="container-fluid bcontent">

			<div class="col-12 row redess">
				<div class="logg col-12 col-lg-12 ">

					<p class="cr">ORGANIZACION BLESS © Todos los derechos reservados.</p>
					<p class="mi">Hecho en Colombia</p><img class="banderac" src="{{ asset('images/colombia.png') }}">
				</div>
			</div>


	</footer>


	</div>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	<script src="https://unpkg.com/muuri@0.8.0/dist/muuri.min.js"></script>
    <script src="{{ asset('js/public/filter.js') }}"></script>
    <script src="{{ asset('js/public/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/public/main.js') }}"></script>
</body>
</html>