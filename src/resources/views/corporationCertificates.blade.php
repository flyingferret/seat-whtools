<div class="col-md-8">
    <div class="box box-primary box-solid">
        <div class="box-header">
            <h3 class="box-title">Corporation Certificates</h3>
        </div>
        <div class="box-body">
            <table id="corpCertTable" class="table table-hover" style="vertical-align: top">
                <thead>
                    <tr>
                    <th>Character</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('javascript')
    <script type="application/javascript">
        var corpCertTable = $('#corpCertTable').DataTable();
        $(function () {
            populateCorporationCertificates('98560621');

        });

        function populateCorporationCertificates(characterID) {
            $.ajax({
                headers: function () {
                },
                url: "/whtools/getcorpcert/" + characterID,
                type: "GET",
                dataType: 'json',
                timeout: 10000
            }).done(function (result) {
                if (result) {
                    $('#corpCertTable').find("tbody").empty();
                    if(corpCertTable){
                        corpCertTable.destroy();
                    };
                    headerPopulated = false;
                    for (var certificate in result) {
                        row ="<tr>"
                        row = row +"<td>" + result[certificate]['data']['0']['Character'].name + "</td>";
                        certs = result[certificate]['data']['1']['CharacterCerts'];
                        // minus 1 from length to ignore character information passed by pop charactercert
                        for(var i =0; i < certs.length -1;i++) {
                            row = row + "<td>" + drawStars(certs[i].certRank) + "</td>";
                            row = row + "<td >" + (certs[i].certRank >4? 1:0) + "</td>";
                            if (!headerPopulated){
                                $('#corpCertTable').find("thead").find("tr").append( "<th>"+certs[i]['characterCert'].name+"</th>");
                                $('#corpCertTable').find("thead").find("tr").append( "<th></th>");
                                $('#corpCertTable').find("tfoot").find("tr").append( "<td></td>");
                                $('#corpCertTable').find("tfoot").find("tr").append( "<td></td>");
                            }
                        }
                        headerPopulated = true;
                        row = row + "</tr>"
                        $('#corpCertTable').find("tbody").append(row);
                    }
                }

                corpCertTable = $('#corpCertTable').DataTable( {
                    "footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api(), data;

                        // Remove the formatting to get integer data for summation
                        var intVal = function ( i ) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?
                                    i : 0;
                        };
                        var populateFooters = function(col) {
                            // Total over all pages
                            total = api
                                .column(col)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);

                            // Total over this page
                            pageTotal = api
                                .column(col, {page: 'current'})
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);

                            // Update footer
                            $(api.column(col -1).footer()).html(
                                pageTotal + ' (' + total + ' total)'
                            );
                        }

                        for(i = 0; i < parseInt($('#corpCertTable thead th').length+2); i++) {
                            if (i > 0 && (i % 2 == 0)) {
                                populateFooters(i);
                                api.column(i).visible(false);
                            }
                        }
                    }
                } );
            });
        }
    </script>
@endpush