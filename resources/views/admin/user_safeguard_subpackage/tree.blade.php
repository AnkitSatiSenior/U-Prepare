<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header 
            icon="fas fa-sitemap text-primary" 
            title="User Safeguard Subpackage Tree" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'User Safeguard Subpackages'],
                ['label' => 'Tree View'],
            ]" 
        />

        @php
            // Group assignments by SubPackageProject -> SafeguardCompliance
            $tree = $assignments->groupBy('subPackageProject.name')
                                ->map(function($subProjectGroup) {
                                    return $subProjectGroup->groupBy('safeguardCompliance.name');
                                });
        @endphp

        @if ($assignments->isEmpty())
            <div class="alert alert-warning mt-3">No assignments found.</div>
        @else
            <ul class="tree mt-3">
                @foreach($tree as $subProjectName => $compliances)
                    @php
                        // Get first assignment to fetch package number safely
                        $firstAssignment = $compliances->first()->first();
                        $packageNumber = $firstAssignment->subPackageProject->packageproject?->package_number ?? 'N/A';
                    @endphp

                    <li>
                        <div class="subpackage">
                            {{ $subProjectName }} <br/> 
                            (Package Number: {{ $packageNumber }})
                        </div>
                        <ul>
                            @foreach($compliances as $complianceName => $users)
                                <li>
                                    <div class="compliance">{{ $complianceName }}</div>
                                    <ul>
                                        @foreach($users as $assignment)
                                            <li>
                                                <div class="user">{{ $assignment->user->name ?? 'N/A' }} ({{ $assignment->user->username ?? 'N/A' }})</div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <style>
        body {
            background: white;
            font: normal 13px/1.4 Segoe,"Segoe UI",Calibri,Helmet,FreeSans,Sans-Serif;
        }

        .tree ul {
            margin: 0 0 0 2em;
            padding: 0;
            list-style: none;
            position: relative;
        }

        .tree ul:before {
            content: "";
            display: block;
            width: 1px;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            border-left: 1px solid #369;
        }

        .tree li {
            margin: 0;
            padding: 1.5em 0 0 2em;
            position: relative;
        }

        .tree li:before {
            content: "";
            position: absolute;
            top: 1.5em;
            left: 0;
            width: 2em;
            border-top: 1px solid #369;
        }

        .tree li:last-child:before {
            background: white;
            height: auto;
            top: 1.5em;
        }

        .tree li div {
            border-radius: 5px;
            border: 1px solid #afafaf;
            padding: 0.5em 1em;
            box-shadow: 1px 1px 4px #8F949A;
            font-weight: bold;
        }

        .tree li div.subpackage {
            background-color: #cce5ff; /* light blue */
            border-color: #339af0;
            color: #0b3d91;
        }

        .tree li div.compliance {
            background-color: #d4edda; /* light green */
            border-color: #28a745;
            color: #155724;
        }

        .tree li div.user {
            background-color: #fff3cd; /* light yellow */
            border-color: #ffc107;
            color: #856404;
        }

        .sticky {
            position: sticky;
            top: 0px;
        }
    </style>
</x-app-layout>
