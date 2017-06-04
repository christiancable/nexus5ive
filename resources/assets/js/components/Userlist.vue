<template>
    <div>
        <form>
            <div class="form-group" role="search">
                <input v-model="searchTerm" class="form-control" placeholder="Search for a user">
            </div>
        </form>

        <template v-for="(user, index) in matchedUsers">
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

            <template>
            <p>{{user.username}}</p>
            </template>
            <!-- <user-panel v-bind:user="user" ></user-panel> -->
        </section>
    </template>

        
    </div>
</template>

<script>
    export default {
        props: ['users'],

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
            // console.log('hello');
        }
    }
</script>