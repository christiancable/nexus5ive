<form method="POST" wire:submit.prevent="save">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link @if($composeActive) active @endif" id="compose-tab"  href="#postEdit" role="tab"
                aria-controls="home" aria-selected="true" wire:click="showCompose">Compose</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($previewActive) active @endif" id="profile-tab"  href="#postPreview" role="tab"
                aria-controls="profile" aria-selected="false" wire:click="showPreview">Preview</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        
        <div role="tabpanel" class="tab-pane @if($composeActive) active @endif" id="postEdit">
            <div class="form-group">
                <input class="form-control" placeholder="Subject" id="title" name="title" type="text" wire:model="title" />
            </div>

            <div class="form-group">
                <textarea class="form-control" id="text" name="text" cols="50" rows="10" wire:model="text"
                    ref="text"></textarea>
            </div>

            
        

            @error('text')
            <div class="alert alert-danger">
                <p>(╯°□°）╯︵ ┻━┻</p>
                <p>{!! __('nexus.validation.post.empty') !!}</p>
            </div>

            @enderror


        </div>


        <div role="tabpanel" class="tab-pane @if($previewActive) active @endif" id="postPreview">
            <div class="card mb-3">
                <div class="card-body">
                    <p class="card-text">
                        {!! $postPreview !!}
                    </p>
                </div>
            </div>

        </div>
    </div>

    <!-- buttons and help - medium screens and above -->
    <div class="d-none d-md-flex justify-content-between">
        <div class="form-group">
            <input dusk="addCommentBtn" class="btn btn-primary form-control" type="submit" value="Add Comment"
                @if (!$buttonActive) disabled @endif
                 />
        </div>

        <a tabindex="0" class="small text-muted" role="button" data-html="true" data-placement="left"
            data-toggle="popover" data-trigger="focus" title="Formating Help" data-content="{!! $help !!}">
            <u>Formatting Help</u>
        </a>
    </div>

    <!-- buttons and help - below medium screens -->
    <div class="d-md-none">
        <div class="form-group">
            <input class="btn btn-primary form-control" type="submit" value="Add Comment"
                @if (!$buttonActive) disabled @endif
                />
        </div>
    </div>

    <div class="d-md-none">
        <p>
            <a data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false"
                aria-controls="collapseExample">
                <x-heroicon-m-chevron-right class="icon_mini mr-1"/>
                Formatting Help
            </a>
        </p>
        <div class="collapse" id="collapseExample">
            <div class="card card-body mb-4">{!! $help !!}</div>
        </div>
    </div>
</form>
<script>
$('[data-toggle="popover"]').popover();
</script>