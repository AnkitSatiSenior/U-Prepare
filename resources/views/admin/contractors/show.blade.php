
<x-app-layout>
    <div class="container-fluid">
        <!-- Header + Breadcrumb -->
        <x-admin.breadcrumb-header
    icon="fas fa-building text-primary"
    title="Contractor Details"
    :breadcrumbs="[
        ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
        ['label' => 'Admin'],
        ['route' => 'admin.contractors.index', 'label' => 'Contractors'],
        ['label' => 'Details']
    ]"
/>
        <x-admin.contractor-info :contractor="$contractor" />
    </div>
</x-app-layout>
