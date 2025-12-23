<script>
document.addEventListener('DOMContentLoaded', function() {
    const yesRadio = document.querySelector('input[name="has_multiple_sub_projects"][value="yes"]');
    const noRadio = document.querySelector('input[name="has_multiple_sub_projects"][value="no"]');
    const countWrapper = document.getElementById('count_sub_project_wrapper');
    const countInput = document.getElementById('count_sub_project');

    function toggleCountField() {
        countWrapper.style.display = yesRadio.checked ? 'block' : 'none';
        countInput.value = yesRadio.checked ? '' : 1;
    }

    if (yesRadio && noRadio) {
        yesRadio.addEventListener('change', toggleCountField);
        noRadio.addEventListener('change', toggleCountField);
        toggleCountField();
    }
});
</script>
