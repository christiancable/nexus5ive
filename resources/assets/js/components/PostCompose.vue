<template>
<form method="POST" action="" @submit.prevent="sendPost">
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#postEdit" aria-controls="postEdit" role="tab" data-toggle="tab">Compose</a></li>
    <li role="presentation"><a href="#postPreview" aria-controls="postPreview" role="tab" data-toggle="tab" >Preview</a></li>
  </ul>
 <br/>
  <!-- Tab panes -->
  <div class="tab-content">

    <div role="tabpanel" class="tab-pane active" id="postEdit">
            <input name="topic_id" :value="topic.id" type="hidden">
            <div class="form-group">
            <input class="form-control" placeholder="Subject" name="title" type="text" v-model="post.title">
            </div>

            <div class="form-group">
                <textarea 
                    class="form-control" 
                    id="postText"
                    name="text" cols="50" rows="10" 
                    v-model="post.text" 
                    ref="postText"></textarea>
            </div>

            <div v-if="errors" class="alert alert-danger">
                 <p>(╯°□°）╯︵ ┻━┻</p>
                 <p>Only a monster would leave an <strong>empty comment!</strong></p>
            </div>           
    </div>

    <div role="tabpanel" class="tab-pane" id="postPreview">
        <post-preview :post="this.post"></post-preview>
    </div>

</div>

<div class="row">    
    <div class="col-md-2">
        <div class="form-group">
            <input class="btn btn-primary form-control" type="submit" value="Add Comment" :disabled="errors!=null">
        </div>
    </div>

    <div class="col-md-10">
        <a tabindex="0" class="pull-right small text-muted visible-md visible-lg" role="button" 
            data-html="true"
            data-placement="left"
            data-toggle="popover" data-trigger="focus" title="Formating Help" 
            :data-content=help>
            <u>Formatting Help</u>
        </a>
    
      <div class="visible-xs visible-sm small">
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="formattinghelp">  
              <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                <span class="glyphicon  glyphicon-triangle-right"></span> Formatting Help
              </a>
            </div>

            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="addNewTopic">
              <div class="panel-body">
                    <p v-html="help"></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
</form>
</template>

<script>
export default {
  props: ["topic", "help"],

  data() {
    return {
      post: {
        title: "",
        text: ""
      },
      errors: null
    };
  },

  computed: {},

  mounted() {
    $('[data-toggle="popover"]').popover();
    this.$refs.postText.focus();
  },

  methods: {
    sendPost() {
      const data = this.post;
      this.$refs.postText.disabled = true;
      data.topic_id = this.topic.id;

      // send post
      window.axios
        .post("/posts", data)
        .then(response => {
          // clear form
          this.post.title = "";
          this.post.text = "";

          // reload the page
          // @TODO inject this into the page when this is built from vue
          window.location.reload();
        })
        .catch(error => {
          this.$refs.postText.disabled = false;
          this.errors = error.response.data;
        });
    }
  }
};
</script>