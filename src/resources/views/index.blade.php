@section('title', trans('whtools::seat.certificates'))
@section('page_header', trans('whtools::seat.name'))
@section('page_description',trans('whtools::seat.certificates'))
{{--TOD Add trans for Description and titles --}}

@extends('web::layouts.grids.8-4')

@section('content')
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#characterCertificats"
                                  data-toggle="tab">{{trans('whtools::whtools.charactercertificates')}}</a></li>
            @can ('whtools.certchecker')
                <li><a href="#corporationCertificates"
                       data-toggle="tab">{{trans('whtools::whtools.corporationcertificates')}}</a></li>
            @endcan
        </ul>
        <div class="tab-content ">
            <div class="tab-pane active" id="characterCertificats">
                <div class="row">
                    <div class="col-md-12">
                        <h3>{{trans('whtools::whtools.charactercertificates')}}</h3>
                        <p>{{trans('whtools::whtools.charactercertificatesdescription')}}</p>
                    </div>
                </div>

                <div class="row">
                    @include('whtools::characterCertificates')
                </div>
            </div>
            <!-- /.tab-pane -->
            @can('whtools.certchecker')
                <div class="tab-pane" id="corporationCertificates">
                    <div class="row">

                        <div class="col-md-12">
                            <h3>{{trans('whtools::whtools.corporationcertificates')}}</h3>
                            <p>{{trans('whtools::whtools.corporationcertificatesdescription')}}</p>
                        </div>

                    </div>
                    <div class="row">
                        @include('whtools::corporationCertificates')
                    </div>
                </div>
            @endcan
        <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
    </div>
@endsection

@push('javascript')
    <script type="application/javascript">
        $(function () {
            $('#characterCertificats').tab('show');
            @if(!empty(session('activeTab')))
            $('.nav-tabs a[href="#' + '{{session('activeTab')}}' + '"]').tab('show');
            @endif
        })
    </script>
@endpush