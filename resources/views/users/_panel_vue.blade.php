<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js"></script>
<script src="https://unpkg.com/vue@2.1.3/dist/vue.js"></script>

<script>
Vue.component('user-panel', {
    props: ['user'],
    template: `
<a :href="'/users/' + user.username">
    <div class="panel panel-primary panel-user">
       <div class="panel-heading">
           <h3 class="panel-title clearfix">
               <span class="text-muted">@</span><strong>@{{user.username}}</strong> <em class="pull-right">@{{user.name}}</em>
           </h3>
       </div>
       <div class="panel-body">
         @{{user.popname}}
      </div>
      <div class="panel-footer clearfix" v-if="user.latestLogin != null">
          <span class="pull-right">Latest Visit @{{user.latestLogin | formatDate}}</span>
      </div>
  </div>
</a>
    `,
    filters: {
        formatDate: function(value) {
         if (window.moment !== undefined) {
          return moment(String(value)).format('LLLL');
        } else {
          let dateFormat = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'};
          let unformattedDate = Date.parse(value);
          let eventDate = new Date(unformattedDate);
          return eventDate.toLocaleDateString('en-GB', dateFormat);
        }
      }
    }
});

var app = new Vue({
    el: '#root',

    data: {
        users: {!! json_encode($users) !!},
        filter: '',
        currentLetter: '',
        previousLetter: '',
    },

    computed: {
        filterRegExp: function() {
            let filterString = this.filter;
            return RegExp(filterString, 'i');
      },
    },

     mounted() {
        /* hide all vue fall back markup */
        let elementsToHide = document.getElementsByClassName('hidden-from-vue');
        for (i = 0; i < elementsToHide.length; ++i) {
          elementsToHide[i].style.display = 'none';
        }
    },

});
</script>