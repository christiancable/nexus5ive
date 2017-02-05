@extends('layouts.master')

@section('breadcrumbs')
@include('_breadcrumbs', $breadcrumbs)
@endsection 

@section('meta')
<title>View Users</title>
@endsection

@section('content')
        

<div class="container">
    <h1>Users</h1>
    <span class="lead">"I fight for the Users"</span>
</div>
<hr>
<div class="container" id="users-list">

<div id="root" v-cloak>
    <form>
        <div class="form-group" role="search">
        <input v-model="filter" class="form-control" placeholder="Search for a user">
        </div>
    </form>

    <template v-for="(user, index) in users">
        <section v-if="(user.username + user.name).search(filterRegExp) !==-1">
        <template v-if="index != 0">
            <template v-if="user.username[0].toLowerCase() != users[index - 1].username[0].toLowerCase()">
             <h2 class="bg-info"><span>@{{user.username[0].toUpperCase() }}</span></h2>
            <hr/>
            </template>   
        </template>
        <template v-else>
            <h2 class="bg-info"><span>@{{user.username[0].toUpperCase() }}</span></h2>
            <hr/>
        </template>
            <user-panel v-bind:user="user" ></user-panel>
        </section>
    </template>
</div>

<div class="hidden-from-vue">
<?php
$previousLetter = '';
$currentLetter = '';
?>
                @foreach ($users as $user) 
                <?php
                $currentLetter = strtoupper($user->username[0]);
                ?>
                @if ($currentLetter !== $previousLetter) 
                    <h2 class="bg-info"><span>{{ $currentLetter }}</span></h2>
                    <hr/>
                @endif 
                     @include('users._panel', $user)
                <?php
                $previousLetter = $currentLetter;
                ?>
                @endforeach
              
                </ul>
            </div>
</div>        
@endsection


@section('javascript')
    <script src="https://unpkg.com/vue@2.1.3/dist/vue.js"></script>
    
<script>
Vue.component('user-panel', {
    props: ['user'],
    template: `
<a :href="'./' + user.username">
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
            let dateFormat = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'};
            let unformattedDate = Date.parse(value);
            let eventDate = new Date(unformattedDate);
            return eventDate.toLocaleDateString('en-GB', dateFormat);
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
        for (el of document.getElementsByClassName('hidden-from-vue')) {
          el.style.display = 'none';
        }
    },

});
</script>

@endsection