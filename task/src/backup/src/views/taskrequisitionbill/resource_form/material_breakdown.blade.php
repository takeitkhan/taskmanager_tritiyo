@php
    $materials = \Tritiyo\Material\Models\Material::get();
@endphp
<fieldset class="pb-5">
    <div class="mb-3">
        <label>Material Information</label>
        @if(isset($task_material) && count($task_material) > 0)
            <a style="float: right; display: block">
                <span style="cursor: pointer;" class="tag is-success"
                      id="add_material_row">Add Breakdown &nbsp; <strong>+</strong></span>
            </a>
        @endif
    </div>
    @php $mat_count = 0 @endphp
    @if(isset($task_material) && count($task_material) > 0)
        @foreach($task_material as $key => $mat)
            <div id="myMaterial">
                <div class="columns m{{$key}}">
                    <div class="column is-1">
                        <label></label> <br/>
                        <a><span class="tag is-danger is-small ibtnDel">Delete</span></a>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            {{ Form::label('material_id', 'Material', array('class' => 'label')) }}
                            <div class="control">
                                <select name="material[{{$mat_count = $key}}][material_id]" id="material_select"
                                        class="input is-small"
                                        required>
                                    <option></option>
                                    @foreach($materials as $material)
                                        <option
                                            value="{{$material->id}}" {{ $mat->material_id == $material->id ? 'selected' : '' }} >{{$material->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="column is-2">
                        {{ Form::label('material_qty', 'Material Qty', array('class' => 'label')) }}
                        <input name="material[{{$mat_count = $key}}][material_qty]" type="number"
                               value="{{$mat->material_qty}}" class="input is-small" required>
                    </div>

                    <div class="column is-2">
                        {{ Form::label('material_amount', 'Amount', array('class' => 'label')) }}
                        <input name="material[{{$mat_count = $key}}][material_amount]" type="number"
                               value="{{$mat->material_amount}}" class="input is-small">
                    </div>
                    <div class="column is-5">
                        {{ Form::label('material_note', 'Note', array('class' => 'label')) }}
                        <input name="material[{{$mat_count = $key}}][material_note]" type="text"
                               value="{{$mat->material_note}}" class="input is-small">
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div id="myMaterial">
            <div class="columns">
                <div class="column is-1">
                    <label></label> <br/>
                    <a><span class="tag is-success is-small" id="add_material_row">Add &nbsp; <strong>+</strong></span></a>
                </div>
                <div class="column is-2">
                    <label for="material_id" class="label">Material</label>
                    <select name="material[0][material_id]" id="material_select" class="input is-small" required>
                        <option></option>
                        @foreach($materials as $material)
                            <option value="{{$material->id}}">{{$material->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="column is-2">
                    <label for="material_qty" class="label">Material Qty</label>
                    <input name="material[0][material_qty]" type="number" value="" class="input is-small" required>
                </div>
                <div class="column is-2">
                    <label for="material_amount" class="label">Material Amount</label>
                    <input name="material[0][material_amount]" type="number" value="" class="input is-small">
                </div>
                <div class="column is-5">
                    <label for="material_note" class="label">Note</label>
                    <input name="material[0][material_note]" type="text" value="" class="input is-small">
                </div>
            </div>
        </div>
    @endif
</fieldset>

<script>
    var mat_counter = "{{$mat_count + 1}}";

    $("#add_material_row").on("click", function () {
        //console.log(mat_counter)
        var cols = '<div class="columns m' + mat_counter + '">';
        cols += '<div class="column is-1">';
        cols += '<br/><a><span class="tag is-danger is-small ibtnDel">Delete</span></a>';
        cols += '</div>';
        cols += '<div class="column is-2">';
        cols += '<label for="material_id" class="label">Material</label>';
        cols += '<select name="material[' + mat_counter + '][material_id]" id="material_select" class="input is-small" required>';
        cols += '<?php foreach($materials as $material){?>';
        cols += '<option></option>'
        cols += '<option value="<?php echo $material->id;?>"><?php echo $material->name;?></option>';
        cols += '<?php } ?>';
        cols += '<select>';
        cols += '</div>';
        cols += '<div class="column is-2">';
        cols += '<label for="material_qty" class="label">Material Qty</label>';
        cols += '<input name="material[' + mat_counter + '][material_qty]" type="number" value="" class="input is-small" required>';
        cols += '</div>';
        cols += '<div class="column is-2">';
        cols += '<label for="material_amount" class="label">Material Amount</label>';
        cols += '<input name="material[' + mat_counter + '][material_amount]" type="number" value="" class="input is-small">';
        cols += '</div>';
        cols += '<div class="column is-5">';
        cols += '<label for="material_note" class="label">Note</label>';
        cols += '<input name="material[' + mat_counter + '][material_note]" type="text" value="" class="input is-small">';
        cols += '</div>';
        cols += '</div>';

        $("div#myMaterial").append(cols);
        mat_counter++;
        materialSelectRefresh();
    });

    $("div#myMaterial").on("click", ".ibtnDel", function (event) {
        $(this).closest("div.columns").remove();
        mat_counter -= 1
    });
</script>

<script>

    //Select 2
    function materialSelectRefresh() {
        $('select#material_select').select2({
            placeholder: "Select Material",
            allowClear: true
        });
    }
    materialSelectRefresh();
</script>

