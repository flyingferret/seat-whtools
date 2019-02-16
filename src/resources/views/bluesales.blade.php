@extends('web::layouts.grids.12')

@section('title', trans('whtools::seat.name'))
@section('page_header', trans('whtools::seat.name'))
@section('page_description', trans('whtools::seat.stocking'))

@section('content')
<div class="box box-primary box-solid">
        <div class="box-header">
           <h3 class="box-title">Blue Loot Sales for </h3>
           <p class="text text-center"><span class="id-to-name"
data-id="{{auth()->user()->character->corporation_id}}">{{ trans('web::seat.unknown') }}</span>

        </div>
        <div class="box-body">
        <table id='saleslist' class="table table-hover" style="vertical-align: top">
            <thead>
            <tr>
                <th></th>
                <th>Pilot Main</th>
                <th>Corporation</th>
                <th>Transaction Character</th>
                <th>Date</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
             </tr>
             </thead>
             <tbody>
             @if (count($bluesales) > 0)
             @foreach($bluesales as $sale)
             <tr id="taxSaleid" data-id="{{ $sale['transaction_id'] }}">
                 <td><img src='https://image.eveonline.com/Type/{{$sale['itemID']}}_32.png' height='24' /></td>
                 <td>{{$sale['maincharacter'] }}</td>
                 <td><span class="id-to-name" data-id="{{ $sale['maincorpID'] }}">{{ $sale['maincorpID'] }}</span>  </td>
                 <td><span class="id-to-name" data-id="{{ $sale['transcharacterID'] }}">{{ $sale['transcharacterID'] }}</span></td>
                 <td>{{ $sale['date'] }}</td>
                 <th><span class="id-to-name" data-id="{{ $sale['itemID'] }}">{{ $sale['itemID'] }}</span></th>
                 <th>{{ $sale['quantity'] }}</th>
                 <th>{{ $sale['unitprice'] }}</th>
                 <th>{{ $sale['total'] }}</th>
             </tr>
             @endforeach
             @endif
            </tbody>
            <tfoot>
                <th></th>
                <th></th>
                <th>Total (All)</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tfoot>
             
        </table>
        </div>
    </div>



@endsection



@push('javascript')
@include('web::includes.javascript.id-to-name');
<script type="application/javascript">
$('#saleslist').dataTable();

</script>
@endpush