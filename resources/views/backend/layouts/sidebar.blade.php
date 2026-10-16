<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- Dashboard -->
                <li> <a class="waves-effect waves-dark" href="{{route('admin.dashboard')}}"><i class="icon-speedometer"></i><span class="hide-menu">Dashboard</span></a>
                </li>

                <!-- User -->
                <li class="{{ Request::is('admin/users*') ? 'active' : '' }}"> <a class="waves-effect waves-dark" href="{{ route('admin.users.index') }}"><i class="fa fa-users"></i><span class="hide-menu">Users</span></a>
                </li>

                <!-- Withdrawal -->
                <li class="{{ Request::is('admin/withdraws*') ? 'active' : '' }}"> <a class="waves-effect waves-dark" href="{{ route('admin.withdraws.index') }}"><i class="fa fa-hand-holding-usd"></i><span class="hide-menu">Withdrawal Requests</span></a>
                </li>

                <!-- Transactions -->
                <li class="{{ Request::is('admin/transactions*') ? 'active' : '' }}"> <a class="waves-effect waves-dark" href="{{ route('admin.transactions.index') }}"><i class="fa fa-exchange-alt"></i><span class="hide-menu">Transactions</span></a>
                </li>

                <!-- Settings -->
                <li
                    class="{{ Request::is('admin/settings*') ? 'active' : '' }}">
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-cogs"></i>
                        <span class="hide-menu">Settings</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li
                            class="{{ Request::is('admin/settings/general*') ? 'active' : '' }}">
                            <a href="{{ route('admin.setting.general') }}">General Settings</a>
                        </li>
                        <li
                            class="{{ Request::is('admin/logos*') ? 'active' : '' }}">
                            <a href="{{ route('admin.settingtheme') }}">Logo/Fav Setting</a>
                        </li>
                    </ul>
                </li>

                <!-- Logout -->
                <li>
                    <a class="waves-effect waves-dark" href="{{ route('admin.logout') }}">
                        <i class="fa fa-sign-out-alt"></i>
                        <span class="hide-menu">Logout</span>
                    </a>
                </li>
                
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>