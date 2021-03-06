<ul class="page-sidebar-menu {{ !isset($slide_close) ? '' : 'page-sidebar-menu-closed' }}" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
    <li class="start @if(Whole\Core\Http\Helpers\SidebarMenuHelper::isActivePath('admin')) active @endif">
        <a href="{{ route('admin.index') }}">
            <i class="icon-home"></i>
            <span class="title">Yönetim Paneli</span>
        </a>
    </li>
    <li>
        <a target="_blank" href="{{ route('master_page') }}">
            <i class="icon-globe"></i>
            <span class="title">Siteyi Önizle</span>
        </a>
    </li>
    {{--sidebar_menu--}}
    @include('backend::_layouts._sidebar_menu',['sidebar_menus'=>$sidebar_menus,'top_id'=>0])
    <li @if(Whole\Core\Http\Helpers\SidebarMenuHelper::isActivePath('admin/user,admin/user/*,admin/role,admin/role/*,admin/permitted-page,admin/permitted-page/*')) class="active" @endif >
        <a href="javascript:;">
            <i class="icon-users"></i>
            <span class="title">Kullanıcı İşlemleri</span>
            <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
            <li @if(Whole\Core\Http\Helpers\SidebarMenuHelper::isActivePath('admin/user,admin/user/*')) class="active" @endif>
                <a href="{{ route('admin.user.index') }}">
                    Kullanıcılar</a>
            </li>
            <li @if(Whole\Core\Http\Helpers\SidebarMenuHelper::isActivePath('admin/role,admin/role/*')) class="active" @endif>
                <a href="{{ route('admin.role.index') }}">
                    Kullanıcı Grupları</a>
            </li>
            <li @if(Whole\Core\Http\Helpers\SidebarMenuHelper::isActivePath('admin/permitted-page,admin/permitted-page/*')) class="active" @endif>
                <a href="{{ route('admin.permitted_page.index') }}">
                    Sayfa Erişim Seviyeleri</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="{{ route('admin.logout') }}">
            <i class="icon-power"></i>
            <span class="title">Çıkış Yap</span></a>
    </li>
</ul>