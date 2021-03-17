<template id="navbar-template" xmlns:v-on="http://www.w3.org/1999/xhtml" xmlns:v-bind="http://www.w3.org/1999/xhtml">
    <nav class="navbar navbar-expand-lg position-fixed w-100" :class="navbar.mainClass" :style="'background: ' + navbar.background">
        <router-link :to="'/'" tag="a" class="navbar-brand p-0">
            <img :src="navbar.brandUrl" height="35" alt="ePsychology Academy">
        </router-link>

        <button class="navbar-toggler" type="button" @click="toggleNavbar()" :style="'border-color: ' + navbar.linkColor">
            <span class="navbar-toggler-icon" :style="'color: ' + navbar.linkColor"></span>
        </button>

        <div class="collapse navbar-collapse mt-2 mt-lg-0" id="navbarRightContent">
            
            
                @if(Auth::guest())
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <router-link :to="'/login'" tag="a" class="nav-link pointer" :style="'color: ' + navbar.linkColor">
                                @{{ trans.messages.navbar.login }}
                            </router-link>
                        </li>
                    </ul>

                    <ul class="navbar-nav mr-2">
                        <li class="nav-item ml-lg-3">
                            <router-link :to="'/register'" tag="a" class="nav-link pointer" :style="'color: ' + navbar.linkColor">
                                @{{ trans.messages.navbar.register }}
                            </router-link>
                        </li>
                    </ul>
                @else
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <router-link :to="'/'" tag="a" class="nav-link pointer" :style="'color: ' + navbar.linkColor">
                                Página principal
                            </router-link>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        <li id="profile-navbar-li" class="nav-item dropdown mt-lg-0 mt-2 ml-lg-3 mb-3 mb-lg-0">
                            <a class="nav-link dropdown-toggle p-0 pointer" id="navbarProfileDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="position: relative; top: -2px">
                                <avatar-component :avatar="user.avatar" :name="user.name" :lastname="null" :width="30" style="margin-bottom: -11px; margin-right: 0.25rem;"></avatar-component>
                                @{{ user.name }} 
                                <span v-if="unreadNotifications > 0" class="badge badge badge-pill badge-warning text-white">@{{ unreadNotifications }}</span>
                                <span v-if="unreadNotifications == 0"><i class="fa fa-angle-down"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarProfileDropdownMenuLink">
                                <router-link :to="'/notifications'" tag="a" class="dropdown-item">
                                    @{{ trans.messages.navbar.notifications }} <span v-if="unreadNotifications > 0" class="badge badge badge-pill badge-warning text-white">@{{ unreadNotifications }}</span>
                                </router-link>
                                <router-link :to="'/profile'" tag="a" class="dropdown-item">
                                    @{{ trans.messages.navbar.edit }}
                                </router-link>
                                <a class="dropdown-item pointer" @click="logout()" >
                                    <i class="fas fa-sign-out-alt"></i> @{{ trans.messages.navbar.exit }}
                                </a>
                            </div>
                        </li>
                    </ul>
                @endif
            </ul>
        </div>
    </nav>
</template>