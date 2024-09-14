<form method="POST" action="" wire:submit.prevent="sendPost">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="compose-tab" data-toggle="tab" href="#postEdit" role="tab"
                aria-controls="home" aria-selected="true">Compose</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#postPreview" role="tab"
                aria-controls="profile" aria-selected="false" wire:click.stop="updatePreview">Preview</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="postEdit">
            <input name="topic_id" :value="topic.id" type="hidden" />
            <div class="form-group">
                <input class="form-control" placeholder="Subject" name="title" type="text" wire:model="postTitle" />
            </div>

            <div class="form-group">
                <textarea class="form-control" id="postText" name="text" cols="50" rows="10" wire:model="postText"
                    ref="postText"></textarea>
            </div>

            <div v-if="errors" class="alert alert-danger">
                <p>(╯°□°）╯︵ ┻━┻</p>
                <p>Only a monster would leave an <strong>empty comment!</strong></p>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="postPreview">
            <div> {{ $postPreview }} </div>
        </div>
    </div>

    <!-- buttons and help - medium screens and above -->
    <div class="d-none d-md-flex justify-content-between">
        <div class="form-group">
            <input class="btn btn-primary form-control" type="submit" value="Add Comment"
                wire:click="sendPost"
                @if (empty($postText)) disabled @endif />
        </div>

        <a tabindex="0" class="small text-muted" role="button" data-html="true" data-placement="left"
            data-toggle="popover" data-trigger="focus" title="Formating Help" :data-content="help">
            <u>Formatting Help</u>
        </a>
    </div>

    <!-- buttons and help - below medium screens -->
    <div class="d-md-none">
        <div class="form-group">
            <input class="btn btn-primary form-control" type="submit" value="Add Comment"
                wire:click="sendPost"
                @if (empty($postText)) disabled @endif />
        </div>
    </div>

    <div class="d-md-none">
        <p>
            <a data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false"
                aria-controls="collapseExample">
                <span class="oi oi-chevron-right mr-1"></span>Formatting Help
            </a>
        </p>
        <div class="collapse" id="collapseExample">
            <div class="card card-body mb-4" v-html="help"></div>
        </div>
    </div>
</form>
