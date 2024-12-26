@php
    $vehicles = \Tritiyo\Vehicle\Models\Vehicle::get();
@endphp
<fieldset class="pb-5">
    <div class="mb-3">
        <label>Vehicle Information</label>
        @if(isset($task_vehicle) && count($task_vehicle) > 0)
            <a style="float: right; display: block">
                <span style="cursor: pointer;" class="tag is-success" id="add_vehicle_row">
                    Add Breakdown &nbsp; <strong>+</strong>
                </span>
            </a>
        @endif
    </div>
    @php $veh_count = 0 @endphp
    @if(isset($task_vehicle) && count($task_vehicle)  > 0)
        @foreach($task_vehicle as $key => $veh)
            <div id="myVehicle">
                <div class="columns vs{{$key}}">
                    <div class="column is-1">
                        <label></label> <br/>
                        <a><span class="tag is-danger is-small ibtnDel">Delete</span></a>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            {{ Form::label('vehicle_id', 'Vehicle', array('class' => 'label')) }}
                            <div class="control">
                                <select name="vehicle[{{$veh_count = $key}}][vehicle_id]" id="vehicle_select"
                                        class="input is-small" required>
                                    <option></option>
                                    @foreach($vehicles as $vehicle)
                                        <option
                                            value="{{$vehicle->id}}" {{ $veh->vehicle_id == $vehicle->id ? 'selected' : '' }} >{{$vehicle->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="column is-2">
                        {{ Form::label('vehicle_rent', 'Vehicle Rent', array('class' => 'label')) }}
                        <input name="vehicle[{{$veh_count = $key}}][vehicle_rent]" type="number"
                               value="{{$veh->vehicle_rent}}" class="input is-small" required>
                    </div>
                    <div class="column is-6">
                        {{ Form::label('vehicle_note', 'Note', array('class' => 'label')) }}
                        <input name="vehicle[{{$veh_count = $key}}][vehicle_note]" type="text"
                               value="{{$veh->vehicle_note}}" class="input is-small" required>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div id="myVehicle">
            <div class="columns">
                <div class="column is-1">
                    <label></label> <br/>
                    <a><span class="tag is-success is-small " id="add_vehicle_row"> Add &nbsp; <strong>+</strong></span></a>
                </div>
                <div class="column is-3">
                    <label for="vehicle_id" class="label">Vehicle</label>
                    <select name="vehicle[0][vehicle_id]" id="vehicle_select" class="input is-small" required>
                        <option></option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{$vehicle->id}}">{{$vehicle->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="column is-2">
                    <label for="vehicle_rent" class="label">Vehicle Rent</label>
                    <input name="vehicle[0][vehicle_rent]" type="number" value="" class="input is-small" required>
                </div>
                <div class="column is-6">
                    <label for="vehicle_note" class="label">Note</label>
                    <input name="vehicle[0][vehicle_note]" type="text" value="" class="input is-small" required>
                </div>
            </div>
        </div>
    @endif
</fieldset>


<script>
    var veh_counter = "{{$veh_count +1 }}";

    $("#add_vehicle_row").on("click", function () {
        var cols = '<div class="columns r' + veh_counter + '">';
        cols += '<div class="column is-1">';
        cols += '<br/><a><span class="tag is-danger is-small ibtnDel">Delete</span></a>';
        cols += '</div>';
        cols += '<div class="column is-3">';
        cols += '<label for="vehicle_id" class="label">Vehicle</label>';
        cols += '<select name="vehicle[' + veh_counter + '][vehicle_id]" id="vehicle_select" class="input is-small" required>';
        cols += '<option></option>';
        cols += '<?php foreach($vehicles as $vehicle){?>';
        cols += '<option value="<?php echo $vehicle->id;?>"><?php echo $vehicle->name;?></option>';
        cols += '<?php } ?>';
        cols += '<select>';
        cols += '</div>';
        cols += '<div class="column is-2">';
        cols += '<label for="vehicle_rent" class="label">Vehicle Rent</label>';
        cols += '<input name="vehicle[' + veh_counter + '][vehicle_rent]" type="number" value="" class="input is-small" required>';
        cols += '</div>';
        cols += '<div class="column is-6">';
        cols += '<label for="vehicle_note" class="label">Note</label>';
        cols += '<input name="vehicle[' + veh_counter + '][vehicle_note]" type="text" value="" class="input is-small" required>';
        cols += '</div>';
        cols += '</div>';
        $("div#myVehicle").append(cols);
        vehicleSelectRefresh();
        veh_counter++;
    });


    $("div#myVehicle").on("click", ".ibtnDel", function (event) {
        $(this).closest("div.columns").remove();
        veh_counter -= 1
    });

</script>

<script>

    //Select 2
    function vehicleSelectRefresh() {
        $('select#vehicle_select').select2({
            placeholder: "Select Vehicle",
            allowClear: true
        });
    }

    vehicleSelectRefresh()
</script>
