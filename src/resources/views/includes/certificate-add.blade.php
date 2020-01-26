<form method="POST" action="{{ route('whtools.addCertificate') }}" id="addCertForm">
    <div class="modal fade" tabindex="-1" role="dialog" id="addCert">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add a New Certificate</h4>
                </div>
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                      <label for="certificateName">Certificate Name:</label>
                      <input type="text" class="form-control" name="certificateName" id="certificateName" />
                    </div>
                    <div class="form-group">
                      <label for="listofskills">Select Skills to Add to Certificate</label>
                      <select multiple class="form-control" size="6" style="width: 75%" id="listofskills">
                      </select>
                    </div>
                    <div class="form-group">
                        <div class="form-group pull-left">
                            <label class="control-label">Required Level</label>
                                <div>
                                @for ($i = 1; $i < 6; $i++)
                                    <label class="radio-inline">
                                        <input type="radio" name="reqLvlList" id="always" value="{{$i}}"@if($i == 1) checked="checked"@endif/>
                                    {{$i}}</label>
                                @endfor
                                </div>
                        </div>
                        <div class="form-group pull-right">
                            <label class="control-label">Grants Certificate Level</label>
                                <div>
                                    @for ($i = 1; $i < 6; $i++)
                                        <label class="radio-inline">
                                        <input type="radio" name="certLvlList" id="always" value="{{$i}}"@if($i == 1) checked="checked"@endif/>
                                        {{$i}}</label>
                                    @endfor
                                </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <br>
                    <div class="form-group">
                      <div class="btn-group text-center" role="group" style="margin: 0 auto; text-align: center; width: inherit; display: inline-block;">
                        <button type="button" class="btn btn-sm btn-success" id="addSkills">Add Skill(s)</button>
                        <button type="button" class="btn btn-sm btn-danger" id="removeSkills">Remove Skill(s)</button>
                      </div>
                    </div>
                    <div class="form-group">
                        <label for="selectedFits">Chosen Skills</label>
                        <select class="form-control" size="15" id="selectedSkills" name="selectedSkills[]" multiple="multiple">
                        </select>
                      <input type="hidden" name="certificateID" id="certificateID" value="0">
                    </div>
                <div class="modal-footer bg-primary">
                    <div class="text-left">
                        <div class="btn-group pull-right" role="group">
                            <button type="submit" class="btn btn-sm btn-success" id="saveDoctrine">Save Certificate</button>
                            <button type="button" class="btn btn-sm btn-default text-black" data-dismiss="modal" id="Cancel">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</form>
