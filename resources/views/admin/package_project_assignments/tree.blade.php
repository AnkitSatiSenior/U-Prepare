<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header 
            icon="fas fa-sitemap text-primary" 
            title="Project Assignment Tree" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Assignments'],
                ['label' => 'Tree View'],
            ]" 
        />

        @if ($projects->isEmpty())
            <div class="alert alert-warning mt-3">No package projects found.</div>
        @else
            <ul class="tree mt-3">
                @foreach($projects as $project)
                    <li>
                        <div class="project">{{ $project->package_name ?? 'N/A' }}<br/> (Package Number: {{ $project->package_number }})</div>
                        @if($project->assignments->isNotEmpty())
                            <ul>
                                @foreach($project->assignments as $assignment)
                                    <li>
                                        <div class="assignee">Assigned To: {{ $assignment->assignee->name ?? 'N/A' }}</div>
                                        <ul>
                                            <li>
                                                <div class="assigner">Assigned By: {{ $assignment->assigner->name ?? 'N/A' }}</div>
                                            </li>
                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <ul>
                                <li><div class="text-warning">No assignments</div></li>
                            </ul>
                        @endif
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

        /* Colorful levels */
        .project {
            background-color: #cce5ff; /* light blue */
            border-color: #339af0;
            color: #0b3d91;
        }

        .assignee {
            background-color: #d4edda; /* light green */
            border-color: #28a745;
            color: #155724;
        }

        .assigner {
            background-color: #fff3cd; /* light yellow */
            border-color: #ffc107;
            color: #856404;
        }

        .sticky {
            position: sticky;
            top: 0px;
            background: #f8f9fa;
        }
    </style>
</x-app-layout>
