<style>
    /* Sidebar Navigation Styling */
    ul.nav.side-menu li a {
        font-size: 15px !important;
        padding: 8px 12px !important;
        display: flex;
        align-items: center;
    }

    ul.nav.child_menu li {
        padding-left: 30px !important;
        font-size: 14px;
    }

    .nav.side-menu i {
        width: 20px;
        text-align: center;
        margin-right: 8px;
    }
</style>

@php
    $user = auth()->user();

    // Generate initials if profile image is missing
    $nameParts = explode(' ', trim($user->name));
    $initials =
        count($nameParts) > 1
            ? strtoupper($nameParts[0][0] . $nameParts[1][0])
            : strtoupper(substr($nameParts[0], 0, 2));
@endphp
<div class="col-md-3 left_col menu_fixed">
    <div class="left_col scroll-view">

        <!-- =======================
             LOGO
        ======================== -->
        <div class="navbar nav_title">
            <a href="{{ route('dashboard') }}" class="site_title">
                <i class="fa fa-institution"></i>
                <span>{{ env('APP_NAME') }}</span>
            </a>
        </div>

        <!-- =======================
             USER PROFILE
        ======================== -->
        <div class="profile clearfix text-center mt-3">
            <!-- Profile Photo -->
            <img id="profileImage" src="{{ $user->profile_photo_url }}" alt="Profile" class="img-circle profile_img"
                style="height:150px; width:auto; margin:0 auto; display:block;"
                onerror="this.style.display='none'; document.getElementById('initialsDiv').style.display='flex';">

            <!-- Initials Fallback -->
            <div id="initialsDiv"
                style="height:50px; width:50px; background-color:#0d6efd; color:#fff;
                       border-radius:50%; font-size:20px; font-weight:bold;
                       display:none; align-items:center; justify-content:center;
                       margin:0 auto; user-select:none;">
                {{ $initials }}
            </div>

            <!-- User Name -->
            <h5 class="mt-2">{{ ucfirst($user->name) }}</h5>
        </div>

        <!-- =======================
             CLEAR CACHE BUTTON
        ======================== -->
        <div class="mx-2">
            <button id="clearCacheBtn" class="btn btn-primary w-100 mt-3">🔄 Clear Cache</button>
            <div id="cacheResult" class="mt-2 text-sm"></div>
        </div>
        <a href="{{ url('/link-storage') }}" class="btn btn-primary w-100 mt-3">
            <span>🔗 Storage Link</span>
        </a>

        <script>
            document.getElementById('clearCacheBtn').addEventListener('click', function() {
                if (!confirm('Are you sure you want to clear the website cache?')) return;

                fetch('{{ route('admin.clear.cache') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        const resultDiv = document.getElementById('cacheResult');
                        resultDiv.style.color = 'white';

                        if (data.status === 'success') {
                            resultDiv.textContent = (data.message || 'Cache cleared successfully.') +
                                ' Page will reload in 2 seconds...';
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            resultDiv.textContent = data.message || 'Something went wrong.';
                        }
                    })
                    .catch(() => {
                        const resultDiv = document.getElementById('cacheResult');
                        resultDiv.textContent = 'Request failed.';
                        resultDiv.style.color = 'red';
                    });
            });
        </script>

        <!-- =======================
             SIDEBAR MENU
        ======================== -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <ul class="nav side-menu">

                    {{-- Dashboard --}}
                    @if (canRoute('admin.dashboard'))
                        <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="fa fa-home"></i> Dashboard
                            </a>
                        </li>
                    @endif
                    @if (canRoute('admin.activity_logs.index'))
                        <li class="{{ request()->routeIs('admin.activity_logs.index') ? 'current-page' : '' }}">
                            <a href="{{ route('admin.activity_logs.index') }}">
                                <i class="fa fa-clipboard-check"></i> Activity Logs
                            </a>
                        </li>
                    @endif
                    @if (canRoute('admin.permission.groups.index'))
                        <li class="{{ request()->routeIs('admin.permission.groups.index') ? 'current-page' : '' }}">
                            <a href="{{ route('admin.permission.groups.index') }}">
                                <i class="fa fa-clipboard-check"></i> Permission Group
                            </a>
                        </li>
                    @endif
                    @if (canRoute('admin.reports.index') ||
                            canRoute('admin.reports.subprojects') ||
                            canRoute('admin.contract-register') ||
                            canRoute('admin.reports.packages-summary') ||
                            canRoute('admin.social-safeguard.dynamic-report') ||
                            canRoute('admin.package-safeguard.dynamic-report'))

                        <li
                            class="{{ request()->routeIs('admin.reports.*') || request()->routeIs('admin.contract-register') || request()->routeIs('admin.reports.packages-summary') ? 'active' : '' }}">
                            <a><i class="fa fa-chart-pie"></i> Reports <span class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                @if (canRoute('admin.reports.index'))
                                    <li class="{{ request()->routeIs('admin.reports.index') ? 'current-page' : '' }}">
                                        <a href="{{ route('admin.reports.index') }}">
                                            <i class="fa fa-file-alt"></i> Package-Wise
                                        </a>
                                    </li>
                                @endif

                                @if (canRoute('admin.reports.subprojects'))
                                    <li
                                        class="{{ request()->routeIs('admin.reports.subprojects') ? 'current-page' : '' }}">
                                        <a href="{{ route('admin.reports.subprojects') }}">
                                            <i class="fa fa-project-diagram"></i> Sub Projects-Wise
                                        </a>
                                    </li>
                                @endif

                                @if (canRoute('admin.social-safeguard.dynamic-report'))
                                    <li
                                        class="{{ request()->routeIs('admin.social-safeguard.dynamic-report') ? 'current-page' : '' }}">
                                        <a href="{{ route('admin.social-safeguard.dynamic-report') }}">
                                            <i class="fa fa-project-diagram"></i> Dynamic Reports
                                        </a>
                                    </li>
                                @endif

                                @if (canRoute('admin.contract-register'))
                                    <li
                                        class="{{ request()->routeIs('admin.contract-register') ? 'current-page' : '' }}">
                                        <a href="{{ route('admin.contract-register') }}">
                                            <i class="fa fa-clipboard-list"></i> Work Contractor Register
                                        </a>
                                    </li>
                                @endif

                                @if (canRoute('admin.reports.packages-summary'))
                                    <li
                                        class="{{ request()->routeIs('admin.reports.packages-summary') ? 'current-page' : '' }}">
                                        <a href="{{ route('admin.reports.packages-summary') }}">
                                            <i class="fa fa-list"></i> Summary Projects-Wise
                                        </a>
                                    </li>
                                @endif
                                @if (canRoute('admin.package-safeguard.dynamic-report'))
                                    <li
                                        class="{{ request()->routeIs('admin.package-safeguard.dynamic-report') ? 'current-page' : '' }}">
                                        <a href="{{ route('admin.package-safeguard.dynamic-report') }}">
                                            <i class="fa fa-list"></i> Overall Compliances
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Packages --}}
                    @if (canRoute('admin.procurement-details.index') ||
                            canRoute('admin.package-projects.index') ||
                            canRoute('admin.procurement-work-programs.index') ||
                            canRoute('admin.contracts.index'))
                        <li
                            class="{{ request()->routeIs('admin.procurement-details.*') || request()->routeIs('admin.package-projects.*') || request()->routeIs('admin.package-project-assignments.*') || request()->routeIs('admin.procurement-work-programs.*') ? 'active' : '' }}">
                            <a><i class="fa fa-box"></i> Packages <span class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                @if (canRoute('admin.package-projects.index'))
                                    <li><a href="{{ route('admin.package-projects.index') }}"><i
                                                class="fa fa-archive"></i> All Packages</a></li>
                                @endif

                                @if (canRoute('admin.procurement-details.index'))
                                    <li><a href="{{ route('admin.procurement-details.index') }}"><i
                                                class="fa fa-list"></i> Procurement Details</a></li>
                                @endif

                                @if (canRoute('admin.contracts.index'))
                                    <li><a href="{{ route('admin.contracts.index') }}"><i
                                                class="fa fa-file-contract"></i> Manage Contracts</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Safeguard --}}
                    @if (canRoute('admin.safeguard-compliances.index') ||
                            canRoute('admin.safeguard_entries.index') ||
                            canRoute('admin.user-safeguard-subpackage.index') ||
                            canRoute('admin.social_safeguard_entries.index'))
                        <li class="{{ request()->routeIs('admin.safeguard*') ? 'active' : '' }}">
                            <a><i class="fa fa-shield-alt"></i> Safeguard <span class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                @if (canRoute('admin.social_safeguard_entries.overview'))
                                    <li><a href="{{ route('admin.social_safeguard_entries.overview') }}"><i
                                                class="fa fa-users"></i> Social/Environment Safeguards</a></li>
                                @endif
                                @if (canRoute('admin.user-safeguard-subpackage.index'))
                                    <li><a href="{{ route('admin.user-safeguard-subpackage.index') }}"><i
                                                class="fa fa-tasks"></i> Compliance Assignments</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Grievances --}}
                    @if (canRoute('admin.grievances.index'))
                        <li class="{{ request()->routeIs('admin.grievances.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.grievances.index') }}"><i class="fa fa-exclamation-triangle"></i>
                                Grievances</a>
                        </li>
                    @endif

                    {{-- Feedback --}}
                    @if (canRoute('admin.feedback.index'))
                        <li class="{{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.feedback.index') }}"><i class="fa fa-comments"></i> Feedback</a>
                        </li>
                    @endif

                    {{-- News & Tenders --}}
                    @if (canRoute('admin.news.index') || canRoute('admin.tenders.index'))
                        <li
                            class="{{ request()->routeIs('admin.news.*') || request()->routeIs('admin.tenders.*') ? 'active' : '' }}">
                            <a><i class="fa fa-newspaper"></i> News & Tenders <span
                                    class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                @if (canRoute('admin.news.index'))
                                    <li><a href="{{ route('admin.news.index') }}"><i class="fa fa-bullhorn"></i>
                                            News</a></li>
                                @endif
                                @if (canRoute('admin.tenders.index'))
                                    <li><a href="{{ route('admin.tenders.index') }}"><i
                                                class="fa fa-file-signature"></i> Tenders</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Progress Updates --}}
                    @if (canRoute('admin.financial-progress-updates.index2') ||
                            canRoute('admin.physical_boq_progress.index') ||
                            canRoute('admin.physical_epc_progress.index'))
                        <li
                            class="{{ request()->routeIs('admin.financial-progress-updates.*') || request()->routeIs('admin.work_progress_data.*') || request()->routeIs('admin.physical_boq_progress.*') || request()->routeIs('admin.physical_epc_progress.*') ? 'active' : '' }}">
                            <a><i class="fa fa-chart-line"></i> Progress Updates <span
                                    class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                @if (canRoute('admin.financial-progress-updates.index2'))
                                    <li><a href="{{ route('admin.financial-progress-updates.index2') }}"><i
                                                class="fa fa-coins"></i> Update Progress</a></li>
                                @endif
                                @if (canRoute('admin.work_progress_data.index'))
                                    <li><a href="{{ route('admin.work_progress_data.index') }}"><i
                                                class="fa fa-chart-line"></i> Work Progress</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- EPC --}}
                    @if (canRoute('admin.epcentry_data.index') ||
                            canRoute('admin.epcentry_data.create') ||
                            canRoute('admin.already_define_epc.index'))
                        <li
                            class="{{ request()->routeIs('admin.epcentry_data.*') || request()->routeIs('admin.already_define_epc.*') ? 'active' : '' }}">
                            <a><i class="fa fa-industry"></i> EPC <span class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                @if (canRoute('admin.already_define_epc.index'))
                                    <li><a href="{{ route('admin.already_define_epc.index') }}"><i
                                                class="fa fa-check"></i> Already Defined EPC</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if (canRoute('admin.already-define-safeguards.index') ||
                            canRoute('admin.already-define-safeguards.edit') ||
                            canRoute('admin.already-define-safeguards.index'))
                        <li
                            class="{{ request()->routeIs('admin.epcentry_data.*') || request()->routeIs('admin.already-define-safeguards.*') ? 'active' : '' }}">
                            <a><i class="fa fa-industry"></i> Safeguard <span class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                @if (canRoute('admin.already-define-safeguards.index'))
                                    <li><a href="{{ route('admin.already-define-safeguards.index') }}"><i
                                                class="fa fa-check"></i> Already Defined Safeguard</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Work Services --}}
                    @if (canRoute('admin.work_services.index'))
                        <li class="{{ request()->routeIs('admin.work_services.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.work_services.index') }}"><i class="fa fa-cogs"></i> Work
                                Services</a>
                        </li>
                    @endif

                    {{-- Contraction Phases --}}
                    @if (canRoute('admin.contraction-phases.index'))
                        <li class="{{ request()->routeIs('admin.contraction-phases.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.contraction-phases.index') }}"><i
                                    class="fa fa-project-diagram"></i> Contraction Phases</a>
                        </li>
                    @endif
                    @if (canRoute('admin.projects.subpackage-index') || canRoute('admin.summary'))
                        <li
                            class="{{ request()->routeIs('admin.projects.subpackage-index') || request()->routeIs('admin.summary') ? 'active' : '' }}">
                            <a><i class="fa fa-boxes"></i> Sub Packages <span class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                @if (canRoute('admin.projects.subpackage-index'))
                                    <li>
                                        <a href="{{ route('admin.projects.subpackage-index') }}">
                                            <i class="fa fa-list"></i> Sub Package Projects
                                        </a>
                                    </li>
                                @endif
                                @if (canRoute('admin.safeguards.list'))
                                    <li>
                                        <a href="{{ route('admin.safeguards.list') }}">
                                            <i class="fa fa-list"></i> View All Safeguards
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Profile --}}
                    <li class="{{ request()->routeIs('profile.show.*') ? 'active' : '' }}">
                        <a href="{{ route('profile.show') }}"><i class="fa fa-user"></i> Profile</a>
                    </li>

                    {{-- Admin Panel --}}
                    @if (canRoute('admin.users.index') ||
                            canRoute('admin.roles.index') ||
                            canRoute('admin.role_routes.index') ||
                            canRoute('admin.departments.index') ||
                            canRoute('admin.sub-departments.index') ||
                            canRoute('admin.designations.index') ||
                            canRoute('admin.projects-category.index') ||
                            canRoute('admin.role_dashboards.index') ||
                            canRoute('admin.contractors.index') ||
                            canRoute('admin.safeguard_entries.index') ||
                            canRoute('admin.safeguard-compliances.index') ||
                            canRoute('admin.already_defined_work_progress.index') ||
                            canRoute('admin.work_progress_data.index'))
                        <li
                            class="{{ request()->routeIs(
                                'admin.users.*',
                                'admin.roles.*',
                                'admin.role_routes.*',
                                'admin.departments.*',
                                'admin.sub-departments.*',
                                'admin.designations.*',
                                'admin.projects-category.*',
                                'admin.role_dashboards.*',
                                'admin.contractors.*',
                                'admin.safeguard_entries.*',
                                'admin.safeguard-compliances.*',
                                'admin.already_defined_work_progress.*',
                            )
                                ? 'active'
                                : '' }}">
                            <a><i class="fa fa-user-shield"></i> Admin Panel <span
                                    class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                {{-- Existing items --}}
                                @if (canRoute('admin.users.index'))
                                    <li><a href="{{ route('admin.users.index') }}"><i class="fa fa-user"></i>
                                            Users</a></li>
                                @endif
                                @if (canRoute('admin.roles.index'))
                                    <li><a href="{{ route('admin.roles.index') }}"><i class="fa fa-user-tag"></i>
                                            Roles</a></li>
                                @endif
                                @if (canRoute('admin.role_routes.index'))
                                    <li><a href="{{ route('admin.role_routes.index') }}"><i class="fa fa-key"></i>
                                            Permission Routes</a></li>
                                @endif
                                @if (canRoute('admin.role_dashboards.index'))
                                    <li><a href="{{ route('admin.role_dashboards.index') }}"><i
                                                class="fa fa-tachometer-alt"></i> Dashboard Access</a></li>
                                @endif
                                @if (canRoute('admin.departments.index'))
                                    <li><a href="{{ route('admin.departments.index') }}"><i
                                                class="fa fa-sitemap"></i> Departments</a></li>
                                @endif
                                @if (canRoute('admin.sub-departments.index'))
                                    <li><a href="{{ route('admin.sub-departments.index') }}"><i
                                                class="fa fa-building"></i> Field PIU</a></li>
                                @endif
                                @if (canRoute('admin.designations.index'))
                                    <li><a href="{{ route('admin.designations.index') }}"><i
                                                class="fa fa-briefcase"></i> Designations</a></li>
                                @endif
                                @if (canRoute('admin.contractors.index'))
                                    <li><a href="{{ route('admin.contractors.index') }}"><i
                                                class="fa fa-user-tie"></i> Contractors</a></li>
                                @endif
                                @if (canRoute('admin.safeguard_entries.index'))
                                    <li><a href="{{ route('admin.safeguard_entries.index') }}"><i
                                                class="fa fa-list"></i> Safeguard Entries</a></li>
                                @endif
                                @if (canRoute('admin.safeguard-compliances.index'))
                                    <li><a href="{{ route('admin.safeguard-compliances.index') }}"><i
                                                class="fa fa-check-circle"></i> Compliance</a></li>
                                @endif

                                {{-- New Work Progress Items --}}
                                @if (canRoute('admin.already_defined_work_progress.index'))
                                    <li><a href="{{ route('admin.already_defined_work_progress.index') }}"><i
                                                class="fa fa-tasks"></i> Work Components</a></li>
                                @endif
                                @if (canRoute('admin.package-project-assignments.index'))
                                    <li><a href="{{ route('admin.package-project-assignments.index') }}"><i
                                                class="fa fa-tasks"></i> Package Assignments</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif


                    {{-- Super Admin --}}
                    @if (canRoute('admin.project.index'))
                        <li
                            class="{{ request()->routeIs('admin.project.*', 'admin.package-components.*', 'admin.package-project-assignments.*', 'admin.safeguard-global.*', 'admin.projects-category.*', 'admin.contract-security-types.*', 'admin.contract-security-forms.*', 'admin.type-of-procurements.*') ? 'active' : '' }}">
                            <a><i class="fa fa-industry"></i> Super Admin <span class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                <li><a href="{{ route('admin.project.index') }}"><i class="fa fa-tasks"></i> Add
                                        Projects</a></li>

                                @if (canRoute('admin.package-components.index'))
                                    <li><a href="{{ route('admin.package-components.index') }}"><i
                                                class="fa fa-cubes"></i> Components</a></li>
                                @endif
                                @if (canRoute('admin.sub_package_project_test_types.index'))
                                    <li><a href="{{ route('admin.sub_package_project_test_types.index') }}"> <i
                                                class="fa fa-vial me-2"></i> Test Types
                                        </a></li>
                                @endif

                                @if (canRoute('admin.safeguard-global.index'))
                                    <li><a href="{{ route('admin.safeguard-global.index') }}"><i
                                                class="fa fa-globe"></i> Safeguard-Global</a></li>
                                @endif
                                @if (canRoute('admin.projects-category.index'))
                                    <li><a href="{{ route('admin.projects-category.index') }}"><i
                                                class="fa fa-folder-open"></i> Procurement Categories</a></li>
                                @endif
                                @if (canRoute('admin.contract-security-types.index'))
                                    <li><a href="{{ route('admin.contract-security-types.index') }}"><i
                                                class="fa fa-key"></i> Security Types</a></li>
                                @endif
                                @if (canRoute('admin.contract-security-forms.index'))
                                    <li><a href="{{ route('admin.contract-security-forms.index') }}"><i
                                                class="fa fa-file-contract"></i> Security Forms</a></li>
                                @endif
                                @if (canRoute('admin.type-of-procurements.index'))
                                    <li><a href="{{ route('admin.type-of-procurements.index') }}"><i
                                                class="fa fa-list-alt"></i> Packages Type</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif



                    {{-- Website Management --}}
                    @if (canRoute('admin.pages.list') ||
                            canRoute('admin.slides.index') ||
                            canRoute('admin.leaders.index') ||
                            canRoute('admin.videos.index') ||
                            canRoute('admin.navbar-items.index'))
                        <li
                            class="{{ request()->routeIs('admin.pages.*') || request()->routeIs('admin.slides.*') || request()->routeIs('admin.leaders.*') || request()->routeIs('admin.videos.*') || request()->routeIs('admin.navbar-items.*') ? 'active' : '' }}">
                            <a><i class="fa fa-globe"></i> Website Management <span
                                    class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                @if (canRoute('admin.pages.list'))
                                    <li><a href="{{ route('admin.pages.list') }}"><i class="fa fa-file-alt"></i>
                                            Pages</a></li>
                                @endif
                                @if (canRoute('admin.media.gallery'))
                                    <li>
                                        <a href="{{ route('admin.media.gallery') }}">
                                            <i class="fa fa-images"></i> Gallery
                                        </a>
                                    </li>
                                @endif

                                @if (canRoute('admin.slides.index'))
                                    <li><a href="{{ route('admin.slides.index') }}"><i class="fa fa-images"></i>
                                            Slides</a></li>
                                @endif
                                @if (canRoute('admin.leaders.index'))
                                    <li><a href="{{ route('admin.leaders.index') }}"><i
                                                class="fa fa-user-friends"></i> Leaders</a></li>
                                @endif
                                @if (canRoute('admin.videos.index'))
                                    <li><a href="{{ route('admin.videos.index') }}"><i class="fa fa-video"></i>
                                            Videos</a></li>
                                @endif
                                @if (canRoute('admin.navbar-items.index'))
                                    <li><a href="{{ route('admin.navbar-items.index') }}"><i class="fa fa-bars"></i>
                                            Navbar Items</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                </ul>
            </div>
        </div>
        <!-- /sidebar menu -->
        <div class="sidebar-footer hidden-small"> <a data-toggle="tooltip" title="Logout" href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> <span
                    class="glyphicon glyphicon-off"></span> </a>
            <form id="logout-form" action="{{ url('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>
</div>
