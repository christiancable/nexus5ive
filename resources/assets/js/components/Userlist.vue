<template>
    <div>
        <form>
            <div class="form-group" role="search">
                <input v-model="searchTerm" class="form-control" placeholder="Search for a user">
            </div>
        </form>

        <template v-if="matchedUsers.length !== 0">
            <template v-for="(user, index) in matchedUsers">
                <section>

                    <template v-if="index === 0">
                        <h2 class="bg-info"><span>{{user.username[0].toUpperCase() }}</span></h2>
                        <hr/>
                    </template>
                    <template v-else>
                        <template v-if="user.username[0].toLowerCase() != matchedUsers[index - 1].username[0].toLowerCase()">
                            <h2 class="bg-info"><span>{{user.username[0].toUpperCase() }}</span></h2>
                            <hr/>
                        </template>   
                    </template>


                    <template>
                        <a :href="'/users/' + user.username">
                            <div class="panel panel-primary panel-user">
                                <div class="panel-heading">
                                    <h3 class="panel-title clearfix">
                                        <span class="text-muted">@</span><strong>{{user.username}}</strong> <em class="pull-right">{{user.name}}</em>
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    {{user.popname}}
                                </div>
                                <div class="panel-footer clearfix" v-if="user.latestLogin != null">
                                    <span class="pull-right">Latest Visit {{user.latestLogin | formatDate}}</span>
                                </div>
                            </div>
                        </a>
                    </template>

                </section>
            </template>
        </template>

        <template v-else>
            <hr/>
            <div class="alert alert-info" role="alert">
                <p>No users found found for <strong>{{searchTerm}}</strong></p>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        props: ['users'],

        filters: {
            formatDate: function(value) {
                let dateFormat = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'};
                let unformattedDate = Date.parse(value);
                let eventDate = new Date(unformattedDate);
                return eventDate.toLocaleDateString('en-GB', dateFormat);
            }
        },

        data() {
            return {
                searchTerm: '',
            }
        },

        computed: {
            matchedUsers: function() {
                var context = {searchTerm:this.searchTerm};
                return this.users.filter(function (user) {
                    return (user.username + user.name).toLowerCase().indexOf(this.searchTerm.toLowerCase()) !== -1;
                }, context)
            }
        },

        mounted() {
            /* hide all vue fall back markup */
            let elementsToHide = document.getElementsByClassName('replace-with-vue');
            for (let i = 0; i < elementsToHide.length; ++i) {
                elementsToHide[i].style.display = 'none';
            }

        }
    }
</script>