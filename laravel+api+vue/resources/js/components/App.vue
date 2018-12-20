<template>
    <div class="">
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
            <div class="container">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <router-link :to="{name: 'home'}" class="nav-link">Home</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="{name: 'users'}" class="nav-link">Users</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="{name: 'cards'}" class="nav-link">Cards</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="{name: 'cases'}" class="nav-link">Cases</router-link>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container bg-white py-4">
            <router-view></router-view>
        </div>
    </div>
</template>
