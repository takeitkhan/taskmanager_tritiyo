<br/>

@if(auth()->user()->isResource(auth()->user()->id))
    <div class="card tile is-child">
        <header class="card-header">
            <p class="card-header-title">
                <span class="icon"><i class="fas fa-tasks default"></i></span>
                Submit Proof
            </p>
        </header>

        <div class="card-content">
            <div class="card-data">
                @if(!empty($taskStatus) && $taskStatus->code == 'head_accepted' && auth()->user()->id == $taskStatus->action_performed_by)
                    <div class="notification is-success has-background-primary-dark " style="padding: 2px 10px;">
                        Task Accepted. Please submit your proof
                    </div>
                @endif
                {{ Form::open(array('url' => route('taskproof.store'), 'method' => 'POST', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}
                {{ Form::hidden('task_id', $task->id ?? '') }}

                <?php 
                $task_resources = DB::select('SELECT resource_id FROM `tasks_site` WHERE task_id = '. $task->id .' GROUP BY resource_id');
                $task_vehicle = \Tritiyo\Task\Models\TaskVehicle::where('task_id', $task->id)->get();
                $task_material = \Tritiyo\Task\Models\TaskMaterial::where('task_id', $task->id)->get();
                ?>
                @if(count($task_resources) > 0)
                    <div class="columns">
                        <div class="column is-2">Resource Proof
                        </div>
                        <div class="column is-1">:</div>
                        @if(auth()->user()->isResource(auth()->user()->id) )
                            <div class="column">
                                <div>
                                    <input style="display: inline-block" name="resource_proof[]" type="file" id="resource_proof" multiple required/>
                                    <span class="removeresource has-text-danger"></span>
                                </div>
                                <div id="resource_proof-wrap"></div>
                            </div>
                        @endif
                    </div>
                @endif
                
                @if(count($task_vehicle) > 0)
                    <div class="columns">
                        <div class="column is-2">Vehicle Proof</div>
                        <div class="column is-1">:</div>
                        @if(auth()->user()->isResource(auth()->user()->id) )
                            <div class="column">
                                <div>
                                    <input style="display: inline-block" name="vehicle_proof[]" type="file" id="vehicle_proof" multiple required/>
                                    <span class="removevehicle has-text-danger"></span>
                                </div>
                                <div id="vehicle_proof-wrap"></div>
                            </div>
                        @endif
                    </div>
                @endif

                @if(count($task_material) > 0)
                    <div class="columns">
                        <div class="column is-2">Material Proof</div>
                        <div class="column is-1">:</div>
                        @if(auth()->user()->isResource(auth()->user()->id) )
                            <div class="column">
                                <div>
                                    <input style="display: inline-block" name="material_proof[]" type="file" id="material_proof" multiple required/>
                                    <span class="removematerial has-text-danger"></span>
                                    <div id="material_proof-wrap"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                @if(!empty($task->anonymous_proof_details))
                    <div class="columns">
                        <div class="column is-2">Anonymous Proof</div>
                        <div class="column is-1">:</div>
                        @if(auth()->user()->isResource(auth()->user()->id) )
                            <div class="column">
                                <div>
                                    <input style="display: inline-block" name="anonymous_proof[]" type="file" id="anonymous_proof" multiple required/>
                                    <span class="removeanonymous has-text-danger"></span>
                                </div>
                                <div id="anonymous_proof-wrap"></div>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="columns">
                    <div class="column">
                        <div class="field is-grouped">
                            <div class="control">
                                <button class="button is-success is-small">Submit Proof</button>
                            </div>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endif



<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        function upload(arg, id, id2){
            var files = arg.target.files,
                filesLength = files.length;
            for (var i = 0; i < filesLength; i++) {
                var f = files[i]
                var fileReader = new FileReader();
                fileReader.onload = (function(e) {
                    var file = e.target;
                    //console.log(files[0]);
                    $('<span class="pip wrap'+id2+'">' +
                        "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + files[0].name + "\"/>" +
                        //'<br/><span class="remove remove'+id2+'">Remove image</span>' +
                        "</span>").insertAfter(id+'-wrap');

                    //$('span.remove'+id2).append('<span class="remove remove'+id2+'"><i class="fa fa-trash"></i></span>');

                    $(".remove.remove"+id2).click(function(){
                        $(this).parent(".pip").remove();
                        $(id).val("");
                        $('.wrap'+id2).empty();
                        $('span.remove'+id2).empty();
                        //alert(id);
                    });

                });
                fileReader.readAsDataURL(f);
            }
            $('span.remove'+id2).append('<span class="remove remove'+id2+'">Remove image</span>');
        }
        if (window.File && window.FileList && window.FileReader) {
            $("#resource_proof").on("change", function(e) {
               upload(e, '#resource_proof', 'resource')
            });
            $("#vehicle_proof").on("change", function(e) {
                upload(e, '#vehicle_proof', 'vehicle')
            });
            $("#material_proof").on("change", function(e) {
                upload(e, '#material_proof', 'material')
            });
            $("#anonymous_proof").on("change", function(e) {
                upload(e, '#anonymous_proof', 'anonymous')
            });
        } else {
            alert("Your browser doesn't support to File Upload")
        }
    });
</script>
<style>
    input[type="file"] {
        display: block;
    }
    .imageThumb {
        max-height: 75px;
        border: 2px solid;
        padding: 1px;
        cursor: pointer;
    }
    .pip {
        display: inline-block;
        margin: 10px 10px 0 0;
    }
    .remove {
        /* display: inlin-blockblock;
        background: #444;
        border: 1px solid black;
        color: white;
        text-align: center;
        cursor: pointer; */
    }
    .remove:hover {
        background: white;
        color: black;
    }
</style>

