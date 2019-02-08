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
        <table id='fitlist' class="table table-hover" style="vertical-align: top">
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
             @if (count($fitlist) > 0)
             @foreach($fitlist as $fit)
             <tr id="fitid" data-id="{{ $fit['id'] }}">
                 <td><img src='https://image.eveonline.com/Type/{{ $fit['typeID'] }}_32.png' height='24' /></td>
                 <td>{{ $fit['shiptype'] }}</td>
                 <td>{{ $fit['fitname'] }}</td>
                 <td>1</td>
                 <td>0</td>
                 <td class="no-hover pull-right">
                     <button type="button" id="viewfit" class="btn btn-xs btn-success" data-id="{{ $fit['id'] }}" data-toggle="tooltip" data-placement="top" title="View Fitting">
                         <span class="fa fa-eye text-white"></span>
                     </button>
                     @if (auth()->user()->has('fitting.create', false)) 
                     <button type="button" id="editfit" class="btn btn-xs btn-warning" data-id="{{ $fit['id'] }}" data-toggle="tooltip" data-placement="top" title="Edit ">
                         <span class="fa fa-pencil text-white"></span>
                     </button>
                     <button type="button" id="deletefit" class="btn btn-xs btn-danger" data-id="{{ $fit['id'] }}" data-toggle="tooltip" data-placement="top" title="Delete Fitting">
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
           <form role="form" action="{{ route('fitting.saveFitting') }}" method="post">
                
               <input type="hidden" id="fitSelection" name="fitSelection" value="0">
               <div class="modal-body">
                   <p>Cut and Paste EFT fitting in the box below</p>
                    <select name="selectedfit" id="selectedfit">
                        @if (count($fitlist) > 0)
                        @foreach($fitlist as $fit)
                        <option value="{{$fit['id']}}">{{$fit['fitname']}} {{$fit['shiptype']}}</option>
                        @endforeach
                        @endif
                        <input type="number" name='minstock' min='1' max='50'>
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
          
@endsection

@push('javascript')
<script type="application/javascript">
    $('#addStocklvl').on('click', function () {
            $('#addStocklvlModal').modal('show');
            //$('#fitSelection').val('0');
            //$('textarea#eftfitting').val('');
        });
</script>
@endpush