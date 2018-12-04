<template>
    <div>
        <form>
            <div class="form-group" role="search">
                <input v-model="searchTerm" class="form-control" placeholder="Search for a user" autofocus>
            </div>
        </form>

    <div class="card-deck">
        <template v-if="matchedUsers.length !== 0">
            <template v-for="(user, index) in matchedUsers">
                
                    <template>
                        <div class="card text-center mb-3 bg-light">
                            <div class="card-header bg-dark text-white">
                                <a :href="'/users/' + user.username" class="d-block text-white"><h3 class="card-title mb-0">{{user.username}}</h3></a>
                            </div>

                            <div class="card-body">
                            
                                <p v-if="user.name" class="card-subtitle">{{user.name}}</p>
                            

                                <p class="card-text text-secondary">
                                    <q v-if="user.popname"><em>{{user.popname}}</em></q>
                                    <br v-else>
                                </p>

                                <div class="row text-secondary mb-3">
                                    <div class="col">
                                        <p class="h2 mb-0 text-dark">{{user.totalPosts}}</p>
                                        Posts 
                                    </div>
                                    <div class="col">
                                        <p class="h2 mb-0 text-dark">{{user.totalVisits}}</p>
                                        Visits
                                    </div>
                                </div>

                                <p><a :href="'/users/' + user.username" class="btn btn-primary">View Profile</a></p>

                            </div>

                            <div v-if="user.latestLogin" class="card-footer text-muted">
                                <small>Latest Visit {{user.latestLogin | formatDate }}</small>
                            </div>
                    
                        </div>

                        <div class="w-100 d-sm-block d-md-none"></div>
                        <div v-if="index % 2 == 0" class="w-100 d-none d-md-block d-lg-none"></div>
                        <div v-if="index % 3 == 0" class="w-100 d-none d-lg-block"></div>
                        
                    </template>
                
            </template>
        </template>

        <template v-else>
            <hr/>
            <div class="alert alert-info" role="alert">
                <p>No users found found for <strong>{{searchTerm}}</strong></p>
            </div>
        </template>
    </div>
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