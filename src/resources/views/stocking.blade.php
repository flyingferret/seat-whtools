@extends('web::layouts.grids.4-4-4')

@section('title', trans('whtools::seat.name'))
@section('page_header', trans('whtools::seat.name'))
@section('page_description', trans('whtools::seat.stocking'))

@section('left')
<div class="box box-primary box-solid">
        <div class="box-header">
           <h3 class="box-title">Stock Levels</h3>
           @if (auth()->user()->has('whtools.stockedit', false)) 
           <div class="box-tools pull-right">
               <button type="button" class="btn btn-xs btn-box-tool" id="addStocklvl" data-toggle="tooltip" data-placement="top" title="Add a new fitting">
                   <span class="fa fa-plus-square"></span>
               </button>
           </div>
           @endif
        </div>
        <div class="box-body">
        <table id='stockList' class="table table-hover" style="vertical-align: top">
            <thead>
            <tr>
                <th></th>
                <th>Ship</th>
                <th>Fit Name</th>
                <th>Min</th>
                <th>Stock</th>
                <th class="pull-right">Option</th>
             </tr>
             </thead>
             <tbody>
             @if (count($stock) > 0)
             @foreach($stock as $item)
             <tr id="stockid" data-id="{{$item['id']}}">
                 <td><img src='https://image.eveonline.com/Type/{{$item['typeID']}}_32.png' height='24' /></td>
                 <td>{{ $item['shiptype'] }}</td>
                 <td>{{ $item['fitname'] }}</td>
                 <td>{{ $item['minlvl'] }}</td>
                 <td>{{ $item['stock'] }}</td>
                 <td class="no-hover pull-right">
                     <!--<button type="button" id="viewfit" class="btn btn-xs btn-success" data-id="" data-toggle="tooltip" data-placement="top" title="View Fitting">
                         <span class="fa fa-eye text-white"></span>
                     </button>-->
                     @if (auth()->user()->has('whtools.stockedit', false)) 
                     <button type="button" id="editStock" class="btn btn-xs btn-warning" data-id="" data-toggle="tooltip" data-placement="top" title="Edit Stocking">
                         <span class="fa fa-pencil text-white"></span>
                     </button>
                     <button type="button" id="deletestock" class="btn btn-xs btn-danger" data-id="" data-toggle="tooltip" data-placement="top" title="Delete Stocking">
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

<div class="modal fade" tabindex="-1" role="dialog" id="addStocklvlModal">
       <div class="modal-dialog" role="document">
         <div class="modal-content">
           <div class="modal-header bg-primary">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
             <h4 class="modal-title">Which Fit to add to Stock List</h4>
           </div>
           <form role="form" action="{{ route('whtools.saveStocking') }}" method="post">
                
               <input type="hidden" id="stockSelection" name="stockSelection" value="0">
               <div class="modal-body">
                   <p>Select the fitting to add a stocking level for and enter the minimum stock level.</p>
                   {{ csrf_field() }}
                    <select name="selectedfit" id="selectedfit">
                        @if (count($fitlist) > 0)
                        @foreach($fitlist as $fit)
                        <option value="{{$fit['id']}}">{{$fit['fitname']}} {{$fit['shiptype']}}</option>
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

@endsection

@push('javascript')
<script type="application/javascript">
    $('#addStocklvl').on('click', function () {
            $('#addStocklvlModal').modal('show');
            $('#stockSelection').val('0');
            
        });
        
    $('#stockList').on('click', '#deletestock', function () {
        $('#stockConfirmModal').modal('show');
        $('#stockSelection').val($(this).data('id'));
    }).on('click', '#editstock', function () {
        id = $(this).data('id');
        $('#stockEditModal').modal('show');
        $('#stockSelection').val(id);
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
        $.ajax({
            headers: function () {
            },
            url: "/fitting/getskillsbyfitid/"+$(this).data('id'),
            type: "GET",
            dataType: 'json',
            timeout: 10000
        }).done( function (result) {
            if (result) {
                skills_informations = result;
                $('#skills-box').show();
                $('#skillbody').empty();
                if ($('#characterSpinner option').size() === 0) {
                    for (var toons in result.characters) {
                        $('#characterSpinner').append('<option value="'+result.characters[toons].id+'">'+result.characters[toons].name+'</option>');
                    }
                }
                fillSkills(result);
            }
        });
    });
    
</script>
@endpush