{{-- this is for moderators to edit sub sections --}}
<div class="panel panel-primary">
    <div class="well">
        <?php $formName = 'subsection'.$subSection->id ?>
        {!! Form::open(
            array(
                'route'     => ['section.update', $subSection->id],
                'class'     => 'form',
                'method'    => 'PATCH',
                'name'      => $formName,
                )
                ) !!}
                {!! Form::hidden('id', $subSection->id) !!}

                <div class="form-group">
                    {!! Form::text("form[$formName][title]", $subSection->title, ['class'=> 'form-control', 'placeholder'=>'Subject']) !!}
                </div>

                <div class="form-group">
                    {!! Form::textarea("form[$formName][text]", $subSection->intro, ['class'=> 'form-control']) !!}
                </div>
        <?php
            $submitLabel = 'Save Changes';
            $submitIcon = 'glyphicon-pencil';
            $submitType = 'btn-info';
        ?>

 <div class="row form-inline">

   

        <div class="col-md-7 form-group">     
        {{-- 
        @if(isset($moderatedSections))
            <label>Section {!! Form::select('section_id', $moderatedSections, null, ['class' => 'form-control'])!!} 
            </label>
        @endif
        --}}
        <label>Order {!! Form::selectRange('weight', 0, 10, $subSection->weight, ['class' => 'form-control'])!!} </label>
        <?php
            $destinationSections = array();
            foreach ($destinations as $destination) {
                $destinationSections[$destination->id] = $destination->title;
            }
        ?>
        <label>Parent {!! Form::select('parent', $destinationSections, $subSection->parent->id, ['class' => 'form-control'])!!} </label>
        </div>



   
</div>




                <div class="row">    
                    <div class="col-sm-12">
                        <div class="form-group">          
                            {!! Form::button("<span class='glyphicon glyphicon-pencil'></span>&nbsp;&nbsp;Save Changes",
                                array(
                                    'type'  => 'submit',
                                    'class' => "btn pull-right btn-info col-xs-12 col-sm-3", 
                                    'value' => $formName
                                    )
                                    ) !!}
                                </div>
                            </div>
                        </div>




                        {!! Form::close() !!}
{{-- 

    {{-- the only error we have is if the user tries to leave a blank comment --}}

{{-- 
 @if (Session::get('postForm') == $post->id)
    @if ($errors->any())
        <p class="alert alert-danger">
            Comments cannot be empty. Please delete the comment instead. 
        </p>
    @endif 
@endif
--}}
</div>
</div>

