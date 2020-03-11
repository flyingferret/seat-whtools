@section('title', 'Certificates')
@section('page_header', 'Certificates')
@section('page_description','Certificates')
{{--TOD Add trans for Description and titles --}}

@extends('web::layouts.grids.8-4')

@section('content')
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#characterCertificats" data-toggle="tab">Character Certificates</a></li>
            @if (auth()->user()->has('whtools.certchecker', false))
                <li><a href="#corporationCertificates" data-toggle="tab">Corporation Certificates</a></li>
            @endif
        </ul>
        <div class="tab-content ">
            <div class="tab-pane active" id="characterCertificats">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Character Certificates</h3>
                        <p>Character Certificates Description</p>
                    </div>
                </div>

                <div class="row">
                    @include('whtools::characterCertificates')
                </div>
            </div>
            <!-- /.tab-pane -->
            @if (auth()->user()->has('whtools.certchecker', false))
                <div class="tab-pane" id="corporationCertificates">
                    <div class="row">

                        <div class="col-md-12">
                            <h3>Corporation Certificates</h3>
                            <p>Corporation Certificates Description</p>
                        </div>

                    </div>
                    <div class="row">
                        @include('whtools::corporationCertificates')
                    </div>
                </div>
        @endif
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