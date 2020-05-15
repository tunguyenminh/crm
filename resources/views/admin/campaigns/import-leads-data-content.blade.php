<div class="row" style="overflow-x: scroll;">
    <div class="col-12">
        <div class="col-sm-4 pull-left">
            <b><span id="unmatchedCount">{{ $unMatchCsvColumnCount }}</span> unmatched columns</b> · <a href="javascript:void(0);" onclick="skipall()">Skip All</a>
        </div>
        <div class="col-sm-4 pull-right">
            <div class="form-group">
                <div class="pull-right">
                    <div class="ckbox ckbox-default">
                        <input type="checkbox" id="showSkipped" checked="checked">
                        <label for="showSkipped">Show Skipped Columns</label>
                    </div></div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <table>
            <tbody>
            <tr>
                @foreach($headerFields as $headerFieldKey => $headerField)
                    <td>
                        <div class="row leadBox {{ ($csvMatchedColumnsDetail[$headerFieldKey] == -1) ? "unmatched" : "matched" }}" id="box_{{ $headerFieldKey }}">
                            <div class="leadOptions">
                                <div class="row selectColumnNameBox" id="selectColumnNameBox_{{ $headerFieldKey }}" style="display:none;">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label">Column Name</label>
                                            <div id="selectOptionList_{{ $headerFieldKey }}">
                                                @if($csvMatchedColumns[$headerFieldKey] == TRUE)
                                                    <select id="columnName_{{ $headerFieldKey }}" class="form-control input-sm mb15">
                                                        <option value="{{ $csvMatchedColumnsDetail[$headerFieldKey] }}">{{ $campaignFormDetailsByID[$csvMatchedColumnsDetail[$headerFieldKey]] }}</option>
                                                    </select>
                                                @else
                                                    <select id="columnName_{{ $headerFieldKey }}" class="form-control input-sm mb15">
                                                        <option value="-1">Select a Column...</option>
                                                    </select>
                                                @endif
                                            </div>
                                        </div>
                                    </div><!-- col-sm-12 -->
                                    <div class="col-sm-12">
                                        <p>
                                            <button onclick="goBack({{ $headerFieldKey }})" class="btn btn-info btn-sm" type="button">Back</button>
                                            <button onclick="saveColumnBox({{ $headerFieldKey }})" class="btn btn-white btn-sm" type="button">Save</button>
                                            <a onclick="skipColumnBox({{ $headerFieldKey }})">Skip</a>
                                        </p>
                                    </div><!-- col-sm-12 -->
                                </div>

                                <div class="row columnDescriptionBox" id="columnDescriptionBox_{{ $headerFieldKey }}">
                                    <div class="col-sm-12">
                                        <h4 id="columnDescriptionBoxColumnName_{{ $headerFieldKey }}">{{ $headerField }}</h4>
                                        <p id="columnDescriptionBoxText_{{ $headerFieldKey }}">
                                            @if($csvMatchedColumns[$headerFieldKey] == TRUE)
                                                {{ $campaignFormDetailsByID[$csvMatchedColumnsDetail[$headerFieldKey]] }}
                                            @else
                                                <span class="unmatchedWarning" id="unmatchedWarning_{{$headerFieldKey}}">(unmatched column)</span>
                                            @endif
                                        </p>
                                        <p class="alert alert-warning notimported" id="columnSkipBox_{{ $headerFieldKey }}" style="display:none;">will not be Imported</p>
                                    </div><!-- col-sm-12 -->
                                </div>

                                <div class="row editAndSkipBox" id="editAndSkipBox_{{ $headerFieldKey }}">
                                    <div class="col-sm-12">
                                        <a href="javascript:void(0);" onclick="showColumnBox({{ $headerFieldKey }})">Edit</a>&nbsp;
                                        <a href="javascript:void(0);" onclick="skipColumnBox({{ $headerFieldKey }})" id="skipButton_{{ $headerFieldKey }}">Skip</a>
                                    </div><!-- col-sm-12 -->
                                </div>
                            </div>

                            <div class="leadSamples">
                                <p class="sampleHeading">{{ $headerField }}</p>
                                @foreach($headerFieldsData[$headerFieldKey] as $headerFieldsDataKey => $headerFieldsDataValue)

                                    <p class="sample">{{ $headerFieldsDataValue }}</p>

                                @endforeach
                            </div>
                        </div>
                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">

    // Current column being edited
    var columnID = 0;

    //    var matchedColumnsDetailArray = [];

    // Fields associated with this lead
    var jsColumnArray = {!! json_encode($campaignFormFields) !!};

    var currentLeadColumnID = jsColumnArray[0].id; // By default column 0 is selected

    // Array to store matched columns. ith element tells that Column i of the CSV matches with which field
    // of the lead. Initially each columns is matched serially with columns in the CSV
    var jsMatchedColumnArray = {!! json_encode($csvMatchedColumnsDetail) !!};

    // Array to indicate which of the leads columns have been matched
    var leadsMatchedColumns = {!! json_encode($formMatchedColumns) !!};

    $(document).ready(function() {
        // Show first column box for editing
        var unmatched = getUnMatched();
        $("#unmatchedCount").html(unmatched);

        if(getUnMatched() == 0) {
            $("#getUnMatchedSuccess").show();
            $("#submit").removeAttr("disabled");
        }
        else {
            showColumnBox(columnID);
        }
    });

    // Generate the select control for this column box
    function generateSelectList(columnID)
    {
//        var selectedColumnID = $('#columnName_'+columnID+' option:selected').val();

        // So that we can select column if user edits it
        var selectedColumnID = jsMatchedColumnArray[columnID];

        var text = '<select id="columnName_'+columnID+'" class="form-control input-sm mb15">' +
            '<option value="-1">Select a Column...</option>';

        for(var i=0; i < jsColumnArray.length; i++)
        {
            var id = jsColumnArray[i]['id'];
            var name = jsColumnArray[i]['name'];


            if(leadsMatchedColumns[id] != undefined && leadsMatchedColumns[id] != -1 && selectedColumnID != id ) {
                // This means this column is matched. We should not show this column
                continue;
            }

            if(selectedColumnID == id)
            {
                text += '<option value="'+id+'" selected>'+name+'</option>';
            }else{
                text += '<option value="'+id+'">'+name+'</option>';
            }

        }

        text += "</select>";

        return text;
    }

</script>
<script type="text/javascript">

    function showColumnBox(columnID)
    {
        // Hide all other edit boxes
        $(".selectColumnNameBox").hide();
        $(".editAndSkipBox").show();
        $(".columnDescriptionBox").show();

        // Show hide for this column
        $('#skipButton_'+columnID).show();
        $('#columnSkipBox_'+columnID).hide();
        $('#editAndSkipBox_'+columnID).hide();
        $('#columnDescriptionBox_'+columnID).hide();
        $('#selectColumnNameBox_'+columnID).show();

        // Hide back button for first column
        if(columnID == 0) {
            $("#selectColumnNameBox_"+columnID+" .btn-info").hide();
        }

        var selectedOption = $('#columnName_'+columnID+' option:selected');
        var selectedColumnID = selectedOption.val();
        currentLeadColumnID = selectedColumnID;
        var columnName = selectedOption.text();

        var selectListText = generateSelectList(columnID);

        $('#selectOptionList_'+columnID).html(selectListText);
        console.log(jsMatchedColumnArray);
        console.log(leadsMatchedColumns);
    }

    function hideColumnBox(columnID) {
        // Show hide for this column
        $('#skipButton_'+columnID).show();
        $('#columnSkipBox_'+columnID).hide();
        $('#editAndSkipBox_'+columnID).hide();
        $('#columnDescriptionBox_'+columnID).hide();
        $('#selectColumnNameBox_'+columnID).show();

        if(jsMatchedColumnArray[columnID] == -2) {
            $('#columnSkipBox_'+columnID).show();
        }

    }

    function goBack(columnID)
    {
        $('#skipButton_'+columnID).show();
        $('#columnSkipBox_'+columnID).hide();
        $('#selectColumnNameBox_'+columnID).hide();
        $('#editAndSkipBox_'+columnID).show();
        $('#columnDescriptionBox_'+columnID).show();

        if(jsMatchedColumnArray[columnID] == -2) {
            $('#columnSkipBox_'+columnID).show();
        }

        while(jsMatchedColumnArray[--columnID] == -2);

        showColumnBox(columnID);
    }

    function saveColumnBox(columnID)
    {
        var selectedOption = $('#columnName_'+columnID+' option:selected');
        var selectedColumnID = selectedOption.val();

        if(selectedColumnID == "-1") {
            window.alert("Please select a column or click on skip");
        }
        else{
            var columnName = selectedOption.text();

            // Now this column is matched. So we can save it in leadsMatchedColumns array
            leadsMatchedColumns[selectedColumnID] = 1;
            jsMatchedColumnArray[columnID] = selectedColumnID;

            $('#skipButton_'+columnID).show();

            $('#columnSkipBox_'+columnID).hide();
            $('#columnDescriptionBoxText_'+columnID).html(columnName);
            $('#selectColumnNameBox_'+columnID).hide();
            $('#columnDescriptionBox_'+columnID).show();
            $('#columnDescriptionBoxText_'+columnID).show();
            $('#editAndSkipBox_'+columnID).show();
            $('#unmatchedWarning_'+columnID).hide();

            $('#box_'+columnID).removeClass('unchanged unmatched').addClass('matched');

            // Skip skipped columns
            while(jsMatchedColumnArray[++columnID] == -2);
            var unmatched = getUnMatched();
            $("#unmatchedCount").html(unmatched);

            if(unmatched == 0) {
                $("#getUnMatchedSuccess").show();
                $("#submit").removeAttr("disabled");
            }
            else {
                showColumnBox(columnID);
            }
        }

    }

    function skipColumnBox(columnID)
    {
        var selectedOption = $('#columnName_'+columnID+' option:selected');
        var selectedColumnID = selectedOption.val();
        var columnName = selectedOption.text();

        if (currentLeadColumnID == -1) {
            leadsMatchedColumns[jsMatchedColumnArray[columnID]] = -1;
        } else {
            leadsMatchedColumns[currentLeadColumnID] = -1;
            jsMatchedColumnArray[columnID] = -2;
        }

        $('#selectOptionList_'+columnID).html("");

        $('#columnDescriptionBox_'+columnID).show();
        $('#selectColumnNameBox_'+columnID).hide();
        $('#columnDescriptionBoxText_'+columnID).hide();
        $('#skipButton_'+columnID).hide();

        $('#columnSkipBox_'+columnID).show();
        $('#editAndSkipBox_'+columnID).show();
        $('#unmatchedWarning_'+columnID).hide();


        $('#box_'+columnID).removeClass('matched unchanged').addClass('unmatched');

        // Skip skipped columns
        while(jsMatchedColumnArray[++columnID] == -2);
        var unmatched = getUnMatched();
        $("#unmatchedCount").html(unmatched);

        if(unmatched == 0) {
            $("#getUnMatchedSuccess").show();
            $("#submit").removeAttr("disabled");
        }
        else {
            showColumnBox(columnID);
        }

    }

    $("#showSkipped").click(function (e) {
        if(this.checked) {
            $(".unmatched").show();
        }
        else {
            $(".unmatched").hide();
        }
    });

    function submit() {

        var newData = JSON.stringify(jsMatchedColumnArray);

        $('#beforeSubmitting').hide();
        $('#afterSubmitting').show();
        $('#processingBarStatus').width('1%');

        var pingTimer = 0;

        $.ajax({
            type: "POST",
            url: "",
            data: { "sorting": newData}
        }).done(
            function( response ) {
                clearInterval(pingTimer);
                var obj = jQuery.parseJSON(response);

                if(obj.success == "success")
                {
                    $('#processingBarStatus').width('100%');
                    location.href = "";
                }
            }).fail(function (response) {
            $("#progressError").html('<div class="alert alert-danger"><strong>Oh snap!</strong> Error occurred while importing. ' +
                'Please go back to <a href="">Create Campaign</a> page and try again. If problem persists, please contact support.').show();
            clearInterval(pingTimer);
        });


        var pingTimer = setInterval(function()
        {
            $.ajax({
                type: "POST",
                url: "",
                data: {}
            }).done(
                function( response ) {
                    $('#processingBarStatus').width(response+'%');
                    $("#progressAmount").html(Math.ceil(response) + "% Completed");
                });
        }, 5000);


    }

    function getUnMatched() {
        var matched = 0;
        for(var i=0; i< jsMatchedColumnArray.length; i++) {
            if(jsMatchedColumnArray[i] == -1) {
                matched++;
            }
        }
        return matched;
    }

    function skipall() {
        for(var i=0; i< jsMatchedColumnArray.length; i++) {
            if(jsMatchedColumnArray[i] == -1) {
                skipColumnBox(i);
            }
        }
    }

    function cancleImportData()
    {
        var answer = confirm("Are you Really want to Cancel?");

        if(answer)
        {
            $.ajax({
                type: "POST",
                url: "",
                data: {}
            }).done(
                function( response ) {
                    var obj = jQuery.parseJSON(response);

                    if(obj.success == 'success')
                    {
                        location.href = "";
                    }
                });
        }
    }

</script>