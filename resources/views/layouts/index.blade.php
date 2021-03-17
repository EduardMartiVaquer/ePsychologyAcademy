<div id="app">
    @include('layouts.navbar')
    <transition name="fade" mode="out-in">
        <router-view :key="$route.fullPath"></router-view>
    </transition>
    <alert></alert>
    @include('mainModal')
</div>