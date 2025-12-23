<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-info-circle text-info" title="Projects" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            [
                'route' => 'admin.package-projects.index',
                'label' => ' <i class=\'fas fa-project-diagram \'></i> Packages',
            ],
            ['class' => 'active', 'label' => 'Packages'],
        ]" />

        <x-admin.package-card :packageProject="$packageProject" />
        <!-- End: Card  -->
        <x-admin.approval-details :packageProject="$packageProject" />


</x-app-layout>
