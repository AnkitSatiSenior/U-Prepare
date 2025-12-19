/* -------------------------------------------------
* 🔹 Safeguard Card Module
* ------------------------------------------------- */
export function initSafeguardCards() {

    const fetchDropdown = (url, target, defaultText = 'Select') => {
        target.disabled = true;
        target.innerHTML = `<option>Loading...</option>`;
        fetch(url)
        .then(res => res.json())
        .then(data => {
            target.innerHTML = `<option value="">${defaultText}</option>`;
            data.forEach(item => {
                const opt = document.createElement("option");
                opt.value = item.id ?? item.user_id ?? item.project_id;
                opt.textContent = item.name ?? item.package_number ?? item.value;
                target.appendChild(opt);
            });
            target.disabled = false;
        })
        .catch(() => {
            target.innerHTML = `<option>Error loading</option>`;
            target.disabled = true;
        });
    };

    document.querySelectorAll('.safeguard-card').forEach(card => {
        const departmentSelect = card.querySelector('.department-select');
        const subDepartmentSelect = card.querySelector('.subdepartment-select');
        const userSelect = card.querySelector('.user-select');
        const projectSelect = card.querySelector('.project-select');
        const safeguardSelect = card.querySelector('.safeguard-select');
        const phaseSelect = card.querySelector('.phase-select');

        if (!departmentSelect) return;

        // Department → Sub-Department + Users + Projects
        departmentSelect.addEventListener('change', () => {
            const deptId = departmentSelect.value;

            // Reset downstream selects
            subDepartmentSelect.innerHTML = '<option>Loading...</option>';
            subDepartmentSelect.disabled = true;

            userSelect.innerHTML = '<option>Select Sub-Department first</option>';
            userSelect.disabled = true;

            projectSelect.innerHTML = '<option>Select User first</option>';
            projectSelect.disabled = true;

            if (!deptId) {
                subDepartmentSelect.innerHTML = '<option>Select Department first</option>';
                return;
            }

            // Fetch sub-departments
            fetchDropdown(`/get-subdepartments/${deptId}`, subDepartmentSelect, 'Select Sub-Department');

            // Fetch users + projects for department
            fetch(`/api/department-users-projects/${deptId}`)
            .then(res => res.json())
            .then(data => {
                // Users
                userSelect.innerHTML = '<option value="">Select User</option>';
                data.users.forEach(u => {
                    const opt = document.createElement("option");
                    opt.value = u.id;
                    opt.textContent = u.name;
                    userSelect.appendChild(opt);
                });
                userSelect.disabled = false;

                // Projects
                projectSelect.innerHTML = '<option value="">Select Project</option>';
                const added = new Set();
                data.projects.forEach(p => {
                    if (!added.has(p.id)) {
                        added.add(p.id);
                        const opt = document.createElement("option");
                        opt.value = p.id;
                        opt.textContent = `${p.package_number} (${p.package_number})`;
                        projectSelect.appendChild(opt);
                    }
                });
                projectSelect.disabled = false;
            });
        });

        // Sub-Department → Filter Users & Projects
        subDepartmentSelect.addEventListener('change', () => {
            const subDeptId = subDepartmentSelect.value;

            userSelect.innerHTML = '<option>Loading...</option>';
            userSelect.disabled = true;

            projectSelect.innerHTML = '<option>Loading...</option>';
            projectSelect.disabled = true;

            if (!subDeptId) {
                userSelect.innerHTML = '<option>Select Sub-Department first</option>';
                projectSelect.innerHTML = '<option>Select Sub-Department first</option>';
                return;
            }

            fetch(`/api/subdepartment-users-projects/${subDeptId}`)
            .then(res => res.json())
            .then(data => {
                // Users
                userSelect.innerHTML = '<option value="">Select User</option>';
                data.users.forEach(u => {
                    const opt = document.createElement("option");
                    opt.value = u.id;
                    opt.textContent = u.name;
                    userSelect.appendChild(opt);
                });
                userSelect.disabled = false;

                // Projects (distinct)
                projectSelect.innerHTML = '<option value="">Select Project</option>';
                const added = new Set();
                data.projects.forEach(p => {
                    if (!added.has(p.id)) {
                        added.add(p.id);
                        const opt = document.createElement("option");
                        opt.value = p.id;
                        opt.textContent = `${p.package_number} (${p.package_number})`;
                        projectSelect.appendChild(opt);
                    }
                });
                projectSelect.disabled = false;
            });
        });

        // User → Projects filtered
        userSelect.addEventListener('change', () => {
            const userId = userSelect.value;

            projectSelect.innerHTML = '<option>Loading...</option>';
            projectSelect.disabled = true;

            if (!userId) {
                projectSelect.innerHTML = '<option>Select User first</option>';
                projectSelect.disabled = true;
                return;
            }

            fetch(`/api/get-package-projects-by-user/${userId}`)
            .then(res => res.json())
            .then(projects => {
                projectSelect.innerHTML = '<option value="">Select Project</option>';
                projects.forEach(p => {
                    const opt = document.createElement("option");
                    opt.value = p.id;
                    opt.textContent = `${p.package_number} (${p.package_number})`;
                    projectSelect.appendChild(opt);
                });
                projectSelect.disabled = false;
            });
        });

        // Safeguard → Phases
        safeguardSelect.addEventListener('change', () => {
            const safeguardId = safeguardSelect.value;

            phaseSelect.innerHTML = '<option>Loading...</option>';
            phaseSelect.disabled = true;

            if (!safeguardId) {
                phaseSelect.innerHTML = '<option>Select Safeguard first</option>';
                return;
            }

            fetchDropdown(`/get-phases/${safeguardId}`, phaseSelect, 'Select Phase');
        });

        // Auto-select first department
        if (departmentSelect.options.length > 1) {
            departmentSelect.selectedIndex = 1;
            departmentSelect.dispatchEvent(new Event('change'));
        }
    });
}
