@extends('web::layouts.grids.4-4-4')

@section('title', trans('whtools::seat.name'))
@section('page_header', trans('whtools::seat.name'))
@section('page_description', trans('whtools::seat.stocking'))

@section('left')
<div class="box box-primary box-solid">
        <div class="box-header">
           <h3 class="box-title">Stock Levels for </h3>
           <p class="text text-center"><span class="id-to-name"
data-id="{{auth()->user()->character->corporation_id}}">{{ trans('web::seat.unknown') }}</span>
           @if (auth()->user()->has('whtools.stockedit', false)) 
           <div class="box-tools pull-right">
               <button type="button" class="btn btn-xs btn-box-tool" id="addStocklvl" data-toggle="tooltip" data-placement="top" title="Add a new fitting">
                   <span class="fa fa-plus-square"></span>
               </button>
           </div>
           @endif
        </div>
        <div class="box-body">
        <table id='stocklist' class="table table-hover" style="vertical-align: top">
            <thead>
            <tr>
                <th></th>
                <th>Ship</th>
                <th>Fit Name</th>
                <th>Min</th>
                <th>Stock</th>
                <th>Contract Title</th>
                <th class="pull-right">Option</th>
             </tr>
             </thead>
             <tbody>
             @if (count($stock) > 0)
             @foreach($stock as $item)
             <tr id="stockid" data-id="{{$item['id']}}">
                 <td><img src='https://image.eveonline.com/Type/{{$item['typeID']}}_32.png' height='24' /></td>
                 <td>{{ $item['shiptype'] }}</td>
                 <td>{{ $item['fitname'] }}
                 <button type="button" id="viewfit" class="btn btn-xs btn-success no-hover pull-right" data-id="{{$item['fitting_id']}}" data-toggle="tooltip" data-placement="top" title="View Fitting">
                         <span class="fa fa-eye text-white"></span>
                     </button>
                 </td>
                 <td>{{ $item['minlvl'] }}</td>
                 <td>{{ $item['stock'] }}</td>
                 <th>{{ $item['shiptype'] }} {{ $item['fitname'] }}</th>
                 <td class="no-hover pull-right">
                     
                     @if (auth()->user()->has('whtools.stockedit', false)) 
                     <button type="button" id="editStock" class="btn btn-xs btn-warning" data-id="{{$item['id']}}" data-toggle="tooltip" data-placement="top" title="Edit Stocking">
                         <span class="fa fa-pencil text-white"></span>
                     </button>
                     <button type="button" id="deletestock" class="btn btn-xs btn-danger" data-id="{{$item['id']}}" data-toggle="tooltip" data-placement="top" title="Delete Stocking">
                         <span class="fa fa-trash text-white"></span>
                     </button>
                     @endif
                 </td>
             </tr>
             @endforeach
             @endif
             </tbody>
        </table>
        </div>
    </div>

<div class="modal fade" tabindex="-1" role="dialog" id="editStocklvlModal">
       <div class="modal-dialog" role="document">
         <div class="modal-content">
           <div class="modal-header bg-primary">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
             <h4 class="modal-title">Which Fit to add to Stock List</h4>
           </div>
           <form role="form" action="{{ route('whtools.saveStocking') }}" method="post">
                
               <input type="hidden" id="stockSelection" name="stockSelection">
               <div class="modal-body">
                   <p id="pedittext">Select the fitting to add a stocking level for and enter the minimum stock level.</p>
                   {{ csrf_field() }}
                    <select name="selectedfit" id="selectedfit">
                        @if (count($fitlist) > 0)
                        @foreach($fitlist as $fit)
                        <option id="selectfit{{$fit['id']}}" value="{{$fit['id']}}">{{$fit['fitname']}} {{$fit['shiptype']}}</option>
                        @endforeach
                        @endif
                        <input type="number" name='minlvl' min='1' max='50' value="1">
                    </select>
               </div>
               <div class="modal-footer">
                   <div class="btn-group pull-right" role="group">
                       <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                       <input type="submit" class="btn btn-primary" id="addstock" value="Submit Fitting" />
                   </div>
              </div>
           </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" tabindex="-1" role="dialog" id="stockConfirmModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Are you sure?</h4>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete this stocking?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="deleteConfirm" data-dismiss="modal">Delete Fitting</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="box box-primary box-solid" id='eftexport'>
        <div class="box-header">
           <h3 class="box-title">EFT Fitting</h3>
        </div>
        <div class="box-body">
            <textarea name="showeft" id="showeft" rows="15" style="width: 100%" onclick="this.focus();this.select()" readonly="readonly"></textarea>
        </div>
</div>

@endsection

@section('center')
    <div class="box box-primary box-solid" id="fitting-box">
        <div class="box-header"><h3 class="box-title" id='middle-header'></h3></div>
        <input type="hidden" id="fittingId" value=""\>
        <div class="box-body">
            <div id="fitting-window">
                 <table class="table table-condensed table-striped" id="lowSlots">
                     <thead>
                         <tr>
                             <th>Low Slot Module</th>
                         </tr>
                     </thead>
                     <tbody></tbody>
                 </table>
                 <table class="table table-condensed table-striped" id="midSlots">
                     <thead>
                         <tr>
                             <th>Mid Slot Module</th>
                         </tr>
                     </thead>
                     <tbody></tbody>
                 </table>
                 <table class="table table-condensed table-striped" id="highSlots">
                     <thead>
                         <tr>
                             <th>High Slot Module</th>
                         </tr>
                     </thead>
                     <tbody></tbody>
                 </table>
                 <table class="table table-condensed table-striped" id="rigs">
                     <thead>
                         <tr>
                             <th>Rigs</th>
                         </tr>
                     </thead>
                     <tbody></tbody>
                  <table class="table table-condensed table-striped" id="subSlots">
                     <thead>
                         <tr>
                             <th>Subsystems</th>
                         </tr>
                     </thead>
                     <tbody></tbody>
                 </table>
                 </table>
                 <table id="drones" class="table table-condensed table-striped">
                     <thead>
                         <tr>
                             <th class="col-md-10">Drone Bay</th>
                             <th class="col-md-2">Number</th>
                         </tr>
                     </thead>
                     <tbody></tbody>
                 </table>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
@include('web::includes.javascript.id-to-name');
<script type="application/javascript">
    $('#fitting-box').hide();
    $('#eftexport').hide();
    $('#showeft').val('');
    
    $('#addStocklvl').on('click', function () {
            document.getElementById('selectedfit').style.visibility = 'visible';
            document.getElementById('pedittext').innerHTML = "Select the fitting to add a stocking level for and enter the minimum stock level. ";
            $('#editStocklvlModal').modal('show');
            $('#stockSelection').val('0');
            
        });
        
    $('#stocklist').on('click', '#deletestock', function () {
        $('#stockConfirmModal').modal('show');
        $('#stockSelection').val($(this).data('id'));
    }).on('click', '#editStock', function () {
        id = $(this).data('id');
        
        $('#stockSelection').val(id);
        document.getElementById('pedittext').innerHTML = "Enter the new minimum stock level. ";
        document.getElementById('selectedfit').style.visibility = 'hidden';
        $('#editStocklvlModal').modal('show');
        $.ajax({
            headers: function () {
            },
            url: "/fitting/geteftfittingbyid/" + id,
            type: "GET",
            datatype: 'string',
            timeout: 10000
        }).done( function (result) {
          $('textarea#eftfitting').val(result);
        }).fail( function(xmlHttpRequest, textStatus, errorThrown) {
        });
    }).on('click', '#viewfit', function () {
        uri = "['id' => " + $(this).data('id') +"]";
        $('#highSlots, #midSlots, #lowSlots, #rigs, #cargo, #drones, #subSlots')
            .find('tbody')
            .empty();
        $('#fittingId').text($(this).data('id'));
        $.ajax({
            headers: function () {
            },
            url: "/fitting/getfittingbyid/"+$(this).data('id'),
            type: "GET",
            dataType: 'json',
            timeout: 10000
        }).done( function (result) {
            $('#highSlots, #midSlots, #lowSlots, #rigs, #cargo, #drones, #subSlots')
                .find('tbody')
                .empty();
            $('#showeft').val('');
            $('#fitting-box').show();
            fillFittingWindow(result);
        });

    });


    $('#deleteConfirm').on('click', function () {
       id = $('#stockSelection').val();
        $('#stocklist #stockid[data-id="'+id+'"]').remove();
        $.ajax({
            headers: function () {
            },
            url: "/whtools/delstockingbyid/" + id,
            type: "GET",
                datatype: 'json',
            timeout: 10000
        }).done( function (result) {
            $('#stocklist #stockid[data-id="'+id+'"]').remove();
        }).fail( function(xmlHttpRequest, textStatus, errorThrown) {
        });
    });
    
        function fillFittingWindow (result) {
        if (result) {
            $('#fitting-window').show();
            $('#middle-header').text(result.shipname + ', ' + result.fitname);
            $('#showeft').val(result.eft);
            $('#eftexport').show();
            for (slot in result) {
                if (slot.indexOf('HiSlot') >= 0)
                    $('#highSlots').find('tbody').append(
                        "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='24' /> " + result[slot].name + "</td></tr>");
                if (slot.indexOf('MedSlot') >= 0)
                    $('#midSlots').find('tbody').append(
                        "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='24' /> " + result[slot].name + "</td></tr>");
                if (slot.indexOf('LoSlot') >= 0)
                    $('#lowSlots').find('tbody').append(
                        "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='24' /> " + result[slot].name + "</td></tr>");
                if (slot.indexOf('RigSlot') >= 0)
                    $('#rigs').find('tbody').append(
                        "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='24' /> " + result[slot].name + "</td></tr>");
                if (slot.indexOf('SubSlot') >= 0)
                    $('#subSlots').find('tbody').append(
                        "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='24' /> " + result[slot].name + "</td></tr>");
                if (slot.indexOf('dronebay') >= 0) {
                    for (item in result[slot])
                        $('#drones').find('tbody').append(
                        "<tr><td><img src='https://image.eveonline.com/Type/" + item + "_32.png' height='24' /> " + result[slot][item].name + "</td><td>" + result[slot][item].qty + "</td></tr>");
                    }
            }
        }
    }

</script>
@endpush