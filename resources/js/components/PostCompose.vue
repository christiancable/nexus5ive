<template>
<form method="POST" action="" @submit.prevent="sendPost">
  <!-- Nav tabs -->


<ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="compose-tab" data-toggle="tab" href="#postEdit" role="tab" aria-controls="home" aria-selected="true">Compose</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#postPreview" role="tab" aria-controls="profile" aria-selected="false">Preview</a>
  </li>
</ul>

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

  <!-- buttons and help - medium screens and above -->
  <div class="d-none d-md-flex justify-content-between">
    <div class="form-group">
        <input class="btn btn-primary form-control" type="submit" value="Add Comment" :disabled="errors!=null">
    </div>

    <a tabindex="0" class="small text-muted" role="button" 
        data-html="true"
        data-placement="left"
        data-toggle="popover" data-trigger="focus" title="Formating Help" 
        :data-content=help>
        <u>Formatting Help</u>
    </a>  
  </div>

  <!-- buttons and help - below medium screens -->
  <div class="d-md-none">
    <div class="form-group">
      <input class="btn btn-primary form-control" type="submit" value="Add Comment" :disabled="errors!=null">
    </div>
  </div>

  <div class="d-md-none">
    <p>
      <a data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
        <span class="oi oi-chevron-right mr-1"></span>Formatting Help
      </a>
    </p>
    <div class="collapse" id="collapseExample">
      <div class="card card-body mb-4" v-html="help"></div>
    </div>
  </div>

</form>

</template>

<script>
export default {
  props: ["topic", "reply", "help"],

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

    if (this.reply) {
      this.post.text = this.quoteReply(this.reply) + this.post.text;
    }
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

          // reload the page without any query parms to avoid always replying
          window.location = window.location.href.split("?")[0];
        })
        .catch(error => {
          this.$refs.postText.disabled = false;
          this.errors = error.response.data;
        });
    },

    quoteReply(reply) {
      const regex = /(^)/gm;
      const subst = `> `;
      const quotedText = reply.text.replace(regex, subst) + "\n\n";

      let authorText = "";
      if (reply.username) {
        authorText = "@" + reply.username + "\n";
      }

      return authorText + quotedText;
    }
  }
};
</script>