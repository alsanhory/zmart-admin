
<h1 class="page-header-title">
    <span class="page-header-icon">
        <img src="{{asset('/public/assets/admin/img/business-setup.png')}}" class="w--20" alt="">
    </span>
    <span>
        {{translate('Page Setup')}}
    </span>
</h1>
<ul class="nav nav-tabs border-0 mb-3">
    <li class="nav-item">
        <a class="nav-link {{Request::is('admin/business-settings/page-setup/about-us')?'active':''}}" href="{{route('admin.business-settings.page-setup.about-us')}}">
            {{translate('About Us')}}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('admin/business-settings/page-setup/terms-and-conditions')?'active':''}}" href="{{route('admin.business-settings.page-setup.terms-and-conditions')}}">
            {{translate('Terms & Conditions')}}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('admin/business-settings/page-setup/privacy-policy')?'active':''}}" href="{{route('admin.business-settings.page-setup.privacy-policy')}}">
            {{translate('Privacy Policy')}}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('admin/business-settings/page-setup/faq')?'active':''}}" href="{{route('admin.business-settings.page-setup.faq')}}">
            {{translate('FAQ')}}
        </a>
    </li>
</ul>
