<template>
    <div class="container">
        <div class="text-center" style="margin: 20px 0px 20px 0px;">
            <span class="text-secondary">Laravel Vue Product Operation</span>
        </div>
 
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="collapse navbar-collapse">
                <div class="navbar-nav">
                    <router-link to="/" class="nav-item nav-link">Home</router-link>
                </div>
            </div>
        </nav>
        <br/>
        <router-view></router-view>
        <vue-progress-bar></vue-progress-bar>
    </div>
</template>
 
<script>
    export default {}
</script>