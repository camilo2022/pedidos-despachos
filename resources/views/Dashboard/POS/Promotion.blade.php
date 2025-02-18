<div class="modal fade" id="PromotionPOSModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="text-center w-100" style="background: white;">
                    <label style="font-size:20px;font-weight:bold;">APLICAR PROMOCIONES</label>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table table-bordered w-100">
                    <thead class="bg-dark">
                        <tr>
                            <th scope="col">Promocion</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Descripcion</th>
                            <th scope="col">Datos</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($promotions as $promotion)
                        <tr>
                            <td>{{ $promotion->name }}</td>
                            <td>
                                @if ($promotion->apply_quantity and !$promotion->apply_percentage)
                                    <h5><span class="badge badge-success"><i class="fas fa-dollar-sign mr-2"></i>Cantidad</span></h5>
                                @elseif(!$promotion->apply_quantity and $promotion->apply_percentage)
                                    <h5><span class="badge badge-primary"><i class="fas fa-percent mr-2"></i>Porcentaje</span></h5>
                                @elseif($promotion->apply_quantity and $promotion->apply_percentage)
                                    <h5><span class="badge badge-success"><i class="fas fa-dollar-sign mr-2"></i>Cantidad</span></h5>
                                    <h5><span class="badge badge-primary"><i class="fas fa-percent mr-2"></i>Porcentaje</span></h5>
                                @endif
                            </td>
                            <td>
                                @if ($promotion->apply_quantity and !$promotion->apply_percentage)
                                    {{ $promotion->settings->quantity }} unds por {{ number_format($promotion->settings->value, 0) }}.
                                @elseif(!$promotion->apply_quantity and $promotion->apply_percentage)
                                    Mayor o Igual a {{ $promotion->settings->quantity }} unds obtienes un {{ $promotion->settings->percentage }} % de descuento.
                                @elseif($promotion->apply_quantity and $promotion->apply_percentage)
                                    Mayor o Igual a {{ $promotion->settings->quantity }} unds obtienes un {{ $promotion->settings->percentage }} % de descuento.
                                @endif
                            </td>
                            <td>
                                <table class="table table-bordered">
                                    <tbody>
                                        @if($promotion->apply_trademark or ($promotion->apply_quantity and $promotion->apply_percentage))
                                        <tr>
                                            <td>Marca</td>
                                            <td>
                                                @forelse ($promotion->settings->trademarks as $trademark)
                                                    <h5><span class="badge badge-info">{{ $trademark }}</span></h5>
                                                @empty
                                                    @if (!$promotion->apply_trademark and !$promotion->apply_category and !$promotion->apply_product and $promotion->apply_quantity and $promotion->apply_percentage)
                                                        <h5><span class="badge badge-info">APLICA TODO</span></h5>
                                                    @endif
                                                @endforelse
                                            </td>
                                        </tr>
                                        @endif
                                        @if($promotion->apply_category or ($promotion->apply_quantity and $promotion->apply_percentage))
                                        <tr>
                                            <td>Categoria</td>
                                            <td>
                                                @forelse ($promotion->settings->categories as $category)
                                                    <h5><span class="badge badge-info">{{ $category }}</span></h5>
                                                @empty
                                                    @if (!$promotion->apply_trademark and !$promotion->apply_category and !$promotion->apply_product and $promotion->apply_quantity and $promotion->apply_percentage)
                                                        <h5><span class="badge badge-info">APLICA TODO</span></h5>
                                                    @endif
                                                @endforelse
                                            </td>
                                        </tr>
                                        @endif
                                        @if($promotion->apply_product or ($promotion->apply_quantity and $promotion->apply_percentage))
                                        <tr>
                                            <td>Producto</td>
                                            <td>
                                                @forelse ($promotion->settings->products as $product)
                                                    <h5><span class="badge badge-info">{{ $product }}</span></h5>
                                                @empty
                                                    @if (!$promotion->apply_trademark and !$promotion->apply_category and !$promotion->apply_product and $promotion->apply_quantity and $promotion->apply_percentage)
                                                        <h5><span class="badge badge-info">APLICA TODO</span></h5>
                                                    @endif
                                                @endforelse
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                <a type="button" class="btn btn-outline-dark btn-sm PromotionsButton" title="Aplicar promocion." data-id="{{ $promotion->id }}">
                                    <i class="fas fa-tag text-dark"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Cerrar ventana.">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        </div>
    </div>
</div>
