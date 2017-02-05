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