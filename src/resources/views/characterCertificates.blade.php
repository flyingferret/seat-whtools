<div class="col-md-8">
    <div class="box box-primary box-solid">
        <div class="box-header ">
            <h3 class="box-title">{{trans('whtools::whtools.certificateskills')}} </h3>
            @can('whtools.certManager')
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-xs btn-box-tool" id="newCert" data-toggle="modal"
                            data-toggle="tooltip" data-target="#addCert" data-placement="top"
                            title="{{trans('whtools::whtools.createcertificate')}}">
                        <span class="fa fa-plus-square"></span>
                    </button>
                </div>
            @endcan
        </div>
        <div class="box-body">
            <div class="input-group">
                <select id="certSpinner" class="form-control">
                    <option value="0">{{trans('whtools::whtools.choosecertificate')}}</option>
                    @foreach ($certificates as $cert)
                        <option value="{{ $cert['certID'] }}">{{ $cert['name'] }}</option>
                    @endforeach
                </select>
                <div class="input-group-btn">
                    @can('whtools.certManager')

                        <button type="button" id="editCert" class="btn btn-warning" disabled="disabled" data-id=""
                                data-toggle="modal" data-target="#addCert" data-toggle="tooltip" data-placement="top"
                                title="{{trans('whtools::whtools.editcert')}}" inactive>
                            <span class="fa fa-pencil text-white"></span>
                        </button>
                        <button type="button" id="deleteCert" class="btn btn-danger" disabled="disabled" data-id=""
                                data-toggle="tooltip" data-placement="top" title="{{trans('whtools::whtools.deletecert')}}">
                            <span class="fa fa-trash text-white"></span>
                        </button>
                    @endcan
                </div>
            </div>
            <br>
            <br>
            <div class="flex-row">
                <div class="col-md-8"></div>
                <div class="col-md-2">
                    <label id="selectRankLabel" class="form-control">&nbsp;&nbsp; {{trans('whtools::whtools.showrank')}}</label>
                </div>
                <div class="col-md-2">
                    <select id="selectRank" class="form-control">
                        <option value="0">{{trans('web::seat.all')}}</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="box-body">

            <table id='skilllist' class="table table-hover" style="vertical-align: top">
                <thead>
                <tr>
                    <th></th>
                    <th>{{trans('web::seat.skill')}}</th>
                    <th>{{trans('whtools::whtools.requiredlevel')}}</th>
                    <th>{{trans('whtools::whtools.characterlevel')}}</th>
                    <th>{{trans('whtools::whtools.certificaterank')}}</th>
                    <th>{{trans('web::seat.status')}}</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>


<div class="col-md-4" id="skills-box">
    <div class="box box-primary box-solid">
        <div class="box-header form-group"><h3 class="box-title" id="skill-title">
                My @can('whtools.certchecker') Corporation Members @endcan Certificates</h3>
        </div>
        <div class="box-body">
            <div id="certificate-window">
                <select id="characterSpinner" class="form-control" style="width: 100%"></select>
                <br>
                <br>
                <table id="certificateTable" style="width: 100%" class="table table-condensed table-striped">
                    <thead>
                    <tr>
                        <th>{{trans('whtools::whtools.certificatename')}}</th>
                        <th style="width: 80px">{{trans('whtools::whtools.rank')}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@include('whtools::includes.certificate-add')
@include('whtools::includes.certificate-confirm-delete')

@push('javascript')
    <script type="application/javascript">
        var selectRank = $('#selectRank');
        var selectRankLabel = $('#selectRankLabel');

        var rankQuery = 0;

        var certTable = $('#skilllist').DataTable({
            "oSearch": {"sSearch": "Missing"},
            "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
            dom: "<'row'<'col-sm-5'l><'col-sm-7'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",

        });

        var characterCertTable = $('#certificateTable').DataTable();

        populateCharacterCertificates({{auth()->user()->main_character->character_id}});

        //rest spinner to default
        $('#certSpinner').val(0);

        $('#newCert').on('click', function () {
            $.ajax({
                headers: function () {
                },
                url: "/whtools/skilllist",
                type: "GET",
                datatype: 'json',
                timeout: 10000
            }).done(function (result) {
                $('#listofskills').empty();
                $('#certificateID').val(0);
                $.each(result, function (key, value) {
                    $('#listofskills').append($("<option></option>").attr("value", value.typeID).text(value.typeName));
                });
            }).fail(function (xmlHttpRequest, textStatus, errorThrown) {
            });
        });
        $('#listofskills').select2();
        $('#addSkills').on('click', function () {
            $("#listofskills option:selected").each(function () {

                var reqLvl = $("input[name='reqLvlList']:checked").val();
                var certRank = $("input[name='certLvlList']:checked").val();
                var skillCode = $(this).val() + reqLvl + certRank;
                $('#selectedSkills').append($("<option></option>").attr("value", skillCode).text($(this).text() + '     Lvl :' + reqLvl + '   Cert. Rank:' + certRank));
            });
        });

        $('#removeSkills').on('click', function () {
            $("#selectedSkills option:selected").each(function () {
                $('#selectedSkills option[value="' + $(this).val() + '"]').remove();
            });
        });

        $('#addCertForm').submit(function (event) {
            $('#selectedSkills').find("option").each(function () {
                $(this).prop('selected', true);
            });
        });

        $('#certSpinner').change(updateCertificateSkillList);

        $('#deleteCert').on('click', function () {
            $('#certConfirmModal').modal('show');
            $('#delSelection').val($(this).data('id'));
        });
        $('#deleteCertConfirm').on('click', function () {
            id = $('#certSpinner').find(":selected").val();

            $.ajax({
                headers: function () {
                },
                url: "/whtools/delcert/" + id,
                type: "GET",
                datatype: 'json',
                timeout: 10000
            }).done(function (result) {
                $('#certSpinner option[value=' + id + ']').remove();
                $('#skilllist').find("tbody").empty();
            }).fail(function (xmlHttpRequest, textStatus, errorThrown) {
            });
        });

        //ensure lvl number is last character in cell used in comparitor
        function drawStars(lvl, withColor = false) {

            var stars = '';
            lvl = parseInt(lvl);
            if (withColor) {
                switch (lvl) {
                    case 0:
                        stars = stars + '<span style="color: Tomato">';
                        stars = stars + '<i class="fa fa-star-o"></i>';
                        break;
                    case 1:
                        stars = stars + '<span style="color: SkyBlue">';
                        break;
                    case 2:
                        stars = stars + '<span style="color: SteelBlue">';
                        break;
                    case 3:
                        stars = stars + '<span style="color: SteelBlue">';
                        break;
                    case 4:
                        stars = stars + '<span  class="text-green">';
                        break;
                    case 5:
                        stars = stars + '<span class="text-yellow">';
                        break;
                }
            } else {
                stars = stars + '<span>';
            }
            if (lvl > 0) {
                for (var i = 1; i <= lvl; i++) {
                    stars = stars + '<i class="fa fa-star"></i>';
                }
            }

            stars = stars + '| ' + lvl.toString();
            stars.concat('</span>');

            return stars;
        }

        $('#editCert').on('click', function () {
            var id = $('#certSpinner').find(":selected").val();

            $.ajax({
                headers: function () {
                },
                url: "/whtools/getcertedit/" + id,
                type: "GET",
                datatype: 'json',
                timeout: 10000
            }).done(function (result) {
                $('#listofskills').empty();
                $.each(result['allSkills'], function (key, value) {
                    $('#listofskills').append($("<option></option>").attr("value", value.typeID).text(value.typeName));
                });
                $('#selectedSkills').empty();
                $.each(result['certSkills'], function (key, value) {
                    $('#selectedSkills').append($("<option></option>").attr("value", value.skillID + String(value.requiredLvl) + String(value.certRank)).text(value.skillName + '     Lvl :' + value.requiredLvl + '   Cert. Rank:' + value.certRank));
                });
                $('#certificateID').val(result['cert']['certID']);
                $('#certificateName').val(result['cert']['name']);
            }).fail(function (xmlHttpRequest, textStatus, errorThrown) {
            });

        });

        function updateCertificateSkillList() {
            var id = $('#certSpinner').find(":selected").val();
            if (id > 0) {
                $('button#editCert').prop('disabled', false);
                $('button#deleteCert').prop('disabled', false);

                $.ajax({
                    headers: function () {
                    },
                    url: "/whtools/getcertbyid/" + id,
                    type: "GET",
                    dataType: 'json',
                    timeout: 10000
                }).done(function (result) {
                    if (result) {
                        certTable.destroy();
                        $('#skilllist').find("tbody").empty();
                        rowNum = 1;
                        for (var skill in result) {

                            row = "<tr id='row" + rowNum + "'><td><img src='https://image.eveonline.com/Type/2403_32.png' height='24' /></td>";
                            row = row + "<td id='skillNameCell'>" + result[skill].skillName + "</td>";
                            row = row + "<td id='reqLvlCell' class='text-right'>" + drawStars(result[skill].reqLvl) + "</td>";
                            row = row + "<td id='charSkillCell' class='charSkill" + result[skill].skillID + " text-right'>Not Injected</td>";
                            row = row + "<td id='certRankCell' class='text-right'>" + drawStars(result[skill].certRank) + "</td>";
                            row = row + "<td id='statusCell'>Status</td>";
                            row = row + "</tr>";
                            $('#skilllist').find("tbody").append(row);
                            rowNum++;
                        }
                        updateCharacterTrained($('#characterSpinner').val());

                    }

                });
            } else {
                $('button#editCert').prop('disabled', true);
                $('button#deleteCert').prop('disabled', true);
            }
        }

        function updateCharacterTrained(characterID) {
            $.ajax({
                headers: function () {
                },
                url: "/whtools/getcharskills/" + characterID,
                type: "GET",
                dataType: 'json',
                timeout: 10000
            }).done(function (result) {
                $.each(result, function (key, value) {
                    $('td.charSkill' + value.type.typeID).html(drawStars(value.trained_skill_level));
                })
                $('#skilllist > tbody > tr').each(function (index, tr) {
                    currentRow = $(this);
                    reqLvlText = currentRow.find('#reqLvlCell').text();
                    reqLvl = parseInt(reqLvlText.substr(reqLvlText.length - 1)) || 0;
                    charSkillText = currentRow.find('#charSkillCell').text();
                    charSkill = parseInt(charSkillText.substr(charSkillText.length - 1)) || 0;
                    if (reqLvl <= charSkill) {
                        currentRow.find('#statusCell').html("Trained");
                    } else {
                        currentRow.find('#statusCell').html("Missing");
                        currentRow.addClass('bg-danger');
                    }
                });
                certTable = $('#skilllist').DataTable({
                    //default to missing skills
                    "oSearch": {"sSearch": "Missing"},
                    "lengthMenu": [[25, 50, -1], [25, 50, "All"]]
                });
            });
        }

        function populateCharacterCertificates(characterID) {
            $.ajax({
                headers: function () {
                },
                url: "/whtools/getcharcert/" + characterID,
                type: "GET",
                dataType: 'json',
                timeout: 10000
            }).done(function (result) {
                if (result) {
                    characterCertTable.destroy();
                    $('#certificateTable').find("tbody").empty();
                    for (var certificate in result) {
                        if (typeof (result[certificate]['characterCert']) !== "undefined") {
                            row = "<tr><td class='text-left'>" + result[certificate]['characterCert'].name + "</td>";
                            row = row + "<td class='text-right'>" + drawStars(result[certificate].certRank, true) + "</td>";
                            row = row + "<td class='no-hover pull-right'><button id='displayCert' class='btn btn-xs btn-success' type='button' data-id='" + result[certificate]['characterCert'].certID + "' data-toggle='tooltip' data-placement='top' data-original-title='View Certificate'>" +
                                "<span class='fa fa-eye text-white'></span></button></td>";
                            row = row + "</tr>";
                            $('#certificateTable').find("tbody").append(row);
                        } else if ($('#characterSpinner option').size() === 0) {
                            for (var toons in result[certificate].characters) {
                                $('#characterSpinner').append('<option value="' + result[certificate].characters[toons].character_id + '">' + result[certificate].characters[toons].name + '</option>');
                            }
                            $('#characterSpinner').select2();
                            //make sure original character sent to populate function is selected
                            $('#characterSpinner').val(characterID);
                            $('#characterSpinner').select2().trigger('change');
                        }
                    }
                }
                characterCertTable = $('#certificateTable').DataTable({
                    "lengthMenu": [[25, 50, -1], [25, 50, "All"]]
                });
            });
        }

        $('#characterSpinner').change(function () {
            populateCharacterCertificates($('#characterSpinner').val());
            updateCertificateSkillList();
        });

        //view button click update certSkillList to display selected cert
        $(document).on('click', '#displayCert', function () {
            $('#certSpinner').val($(this).data('id'));
            $('#characterSpinner').trigger('change');
        });

        //Add rank Filter
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'skilllist') {
                    return true;
                }
                var rank = parseFloat(data[4].substr(data[4].length - 1)) || 0; // use data for the age column


                if ((isNaN(rankQuery)) || //If invalid query
                    (rankQuery == 0) || //if all is selected
                    (rankQuery == rank)) {
                    return true;
                }
                return false;
            }
        );

        //add event listeners to the rank select and redraw table
        selectRank.change(function () {
            rankQuery = parseInt(selectRank.val());
            certTable.draw();
            populateCharacterCertificates($('#characterSpinner').val());
        });
    </script>
@endpush
